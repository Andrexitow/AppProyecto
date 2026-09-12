<?php

namespace App\Http\Controllers;

use App\Models\Bodega;
use App\Models\Inventario;
use App\Models\TrasladoBodega;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Services\LegacyDocumentSyncService;

class TrasladoBodegaController extends Controller
{
    public function index()
    {
        return view('traslados-bodega.index', [
            'bodegas' => Bodega::orderBy('descripcion')->get(['id', 'descripcion']),
        ]);
    }

    public function data(Request $request)
    {
        $traslados = TrasladoBodega::query()
            ->with(['origen:id,descripcion', 'destino:id,descripcion', 'usuario:id,name'])
            ->withCount('detalles')
            ->when($request->filled('buscar'), function ($query) use ($request) {
                $buscar = trim((string) $request->string('buscar'));
                $query->where(function ($subquery) use ($buscar) {
                    $subquery->where('prefijo', 'like', '%' . $buscar . '%')
                        ->orWhere('consecutivo', 'like', '%' . $buscar . '%');
                });
            })
            ->when($request->filled('estado'), fn ($query) => $query->where('estado', (string) $request->string('estado')))
            ->latest('id')
            ->paginate(30);

        $traslados->getCollection()->transform(function (TrasladoBodega $traslado) {
            $traslado->fecha_formateada = $traslado->fecha?->format('d/m/Y');

            return $traslado;
        });

        return response()->json($traslados);
    }

    public function siguienteConsecutivo(Request $request)
    {
        $prefijo = strtoupper(trim((string) $request->string('prefijo', 'TR')));
        $ultimo = TrasladoBodega::where('prefijo', $prefijo)->max('consecutivo');

        return response()->json(['consecutivo' => ($ultimo ?? 0) + 1]);
    }

    public function productos(Request $request)
    {
        $datos = $request->validate([
            'bodega_id' => ['required', 'exists:bodegas,id'],
            'buscar' => ['nullable', 'string', 'max:100'],
        ]);

        $buscar = trim($datos['buscar'] ?? '');

        $productos = DB::table('inventarios')
            ->join('productos', 'productos.id', '=', 'inventarios.producto_id')
            ->where('inventarios.bodega_id', $datos['bodega_id'])
            ->where('inventarios.stock', '>', 0)
            ->where('productos.inactivo', 0)
            ->when($buscar !== '', function ($query) use ($buscar) {
                $query->where(function ($subquery) use ($buscar) {
                    $subquery->where('productos.descripcion', 'like', '%' . $buscar . '%')
                        ->orWhere('productos.codigo', 'like', '%' . $buscar . '%')
                        ->orWhere('productos.codigo_barras', 'like', '%' . $buscar . '%');
                });
            })
            ->orderBy('productos.descripcion')
            ->limit(20)
            ->get([
                'productos.id',
                'productos.descripcion',
                'productos.codigo',
                'inventarios.stock',
            ]);

        return response()->json(['data' => $productos]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'prefijo' => ['required', 'string', 'max:10'],
            'consecutivo' => ['required', 'integer', 'min:1'],
            'fecha' => ['required', 'date'],
            'bodega_origen_id' => ['required', 'exists:bodegas,id', 'different:bodega_destino_id'],
            'bodega_destino_id' => ['required', 'exists:bodegas,id'],
            'observaciones' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.producto_id' => ['required', 'distinct', 'exists:productos,id'],
            'items.*.cantidad' => ['required', 'numeric', 'gt:0'],
        ]);

        $datos['prefijo'] = strtoupper(trim($datos['prefijo']));

        $traslado = DB::transaction(function () use ($datos, $request) {
            if (TrasladoBodega::where('prefijo', $datos['prefijo'])
                ->where('consecutivo', $datos['consecutivo'])
                ->lockForUpdate()
                ->exists()) {
                throw ValidationException::withMessages([
                    'consecutivo' => ['Ya existe un traslado con este prefijo y consecutivo.'],
                ]);
            }

            $traslado = TrasladoBodega::create([
                'prefijo' => $datos['prefijo'],
                'consecutivo' => $datos['consecutivo'],
                'fecha' => $datos['fecha'],
                'bodega_origen_id' => $datos['bodega_origen_id'],
                'bodega_destino_id' => $datos['bodega_destino_id'],
                'observaciones' => $datos['observaciones'] ?? null,
                'estado' => 'borrador',
                'user_id' => $request->user()->id,
            ]);

            foreach ($datos['items'] as $item) {
                $traslado->detalles()->create([
                    'producto_id' => $item['producto_id'],
                    'cantidad' => $item['cantidad'],
                ]);
            }

            return $traslado;
        });

        return response()->json([
            'success' => true,
            'message' => 'Traslado guardado como borrador. Regístralo cuando estés listo para mover el inventario.',
            'data' => $traslado->load(['origen', 'destino', 'detalles.producto']),
        ], 201);
    }

    public function registrar(TrasladoBodega $traslado)
    {
        DB::transaction(function () use ($traslado) {
            $traslado = TrasladoBodega::query()
                ->with('detalles.producto')
                ->lockForUpdate()
                ->findOrFail($traslado->id);

            if ($traslado->estado !== 'borrador') {
                throw ValidationException::withMessages([
                    'traslado' => ['Solo se pueden registrar traslados en borrador.'],
                ]);
            }

            foreach ($traslado->detalles as $detalle) {
                $origen = Inventario::query()
                    ->where('producto_id', $detalle->producto_id)
                    ->where('bodega_id', $traslado->bodega_origen_id)
                    ->lockForUpdate()
                    ->first();

                if (!$origen || (float) $origen->stock < (float) $detalle->cantidad) {
                    throw ValidationException::withMessages([
                        'traslado' => ['Stock insuficiente para registrar: ' . ($detalle->producto?->descripcion ?? 'un producto') . '.'],
                    ]);
                }
            }

            foreach ($traslado->detalles as $detalle) {
                Inventario::query()
                    ->where('producto_id', $detalle->producto_id)
                    ->where('bodega_id', $traslado->bodega_origen_id)
                    ->decrement('stock', $detalle->cantidad);

                $destino = Inventario::firstOrCreate(
                    ['producto_id' => $detalle->producto_id, 'bodega_id' => $traslado->bodega_destino_id],
                    ['stock' => 0]
                );
                $destino->increment('stock', $detalle->cantidad);
            }

            $traslado->update(['estado' => 'confirmado']);
            app(LegacyDocumentSyncService::class)->operativo($traslado->fresh('detalles.producto'), 'TRASLADO', $traslado->prefijo . '-' . $traslado->consecutivo, $traslado->bodega_origen_id, $traslado->detalles, $traslado->bodega_destino_id);
        });

        return response()->json(['success' => true, 'message' => 'Traslado registrado y existencias actualizadas.']);
    }

    public function revertir(TrasladoBodega $traslado)
    {
        DB::transaction(function () use ($traslado) {
            $traslado = TrasladoBodega::query()
                ->with('detalles.producto')
                ->lockForUpdate()
                ->findOrFail($traslado->id);

            if ($traslado->estado !== 'confirmado') {
                throw ValidationException::withMessages([
                    'traslado' => ['Solo se pueden revertir traslados registrados.'],
                ]);
            }

            foreach ($traslado->detalles as $detalle) {
                $destino = Inventario::query()
                    ->where('producto_id', $detalle->producto_id)
                    ->where('bodega_id', $traslado->bodega_destino_id)
                    ->lockForUpdate()
                    ->first();

                if (!$destino || (float) $destino->stock < (float) $detalle->cantidad) {
                    throw ValidationException::withMessages([
                        'traslado' => ['No se puede revertir: en la bodega destino ya no hay existencias suficientes de ' . ($detalle->producto?->descripcion ?? 'un producto') . '.'],
                    ]);
                }
            }

            foreach ($traslado->detalles as $detalle) {
                Inventario::query()
                    ->where('producto_id', $detalle->producto_id)
                    ->where('bodega_id', $traslado->bodega_destino_id)
                    ->decrement('stock', $detalle->cantidad);

                $origen = Inventario::firstOrCreate(
                    ['producto_id' => $detalle->producto_id, 'bodega_id' => $traslado->bodega_origen_id],
                    ['stock' => 0]
                );
                $origen->increment('stock', $detalle->cantidad);
            }

            $traslado->update(['estado' => 'borrador']);
        });

        return response()->json(['success' => true, 'message' => 'Traslado revertido. El inventario volvió a la bodega de origen.']);
    }

    public function destroy(TrasladoBodega $traslado)
    {
        if ($traslado->estado !== 'borrador') {
            return response()->json([
                'message' => 'Primero revierte el traslado para devolver las existencias y luego podrás eliminar el borrador.',
            ], 422);
        }

        $traslado->delete();

        return response()->json(['success' => true, 'message' => 'Borrador de traslado eliminado.']);
    }
}
