<?php

namespace App\Http\Controllers;

use App\Models\Consumo;
use App\Models\ConsumoDetalle;
use App\Services\KardexService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Consumo de materia prima: se registra solo (no hay "crear consumo" a
 * mano) cada vez que se vende un producto ensamblado — ver
 * FacturacionController::cerrarMesa(). Casi toda esta vista es de solo
 * consulta, salvo los consumos 'no_registrado' (les faltó stock a algún
 * insumo al momento de la venta): esos sí se pueden editar/registrar
 * desde aquí — ver actualizarDetalle()/eliminarDetalle()/registrar().
 */
class ConsumoController extends Controller
{
    public const POR_PAGINA = 50;

    public function __construct(private KardexService $kardex)
    {
    }

    public function index(Request $request)
    {
        $query = Consumo::with('user:id,name')
            ->when($request->filled('buscar'), fn ($q) => $q->where('numero_factura', 'like', '%' . $request->buscar . '%'))
            ->when($request->filled('desde'), fn ($q) => $q->whereDate('fecha', '>=', $request->desde))
            ->when($request->filled('hasta'), fn ($q) => $q->whereDate('fecha', '<=', $request->hasta))
            ->when($request->filled('estado') && $request->estado !== 'todos', fn ($q) => $q->where('estado', $request->estado))
            ->orderByDesc('id');

        $consumos = $query->paginate(self::POR_PAGINA)->withQueryString();

        if ($request->ajax()) {
            return view('consumos.partials.tabla', compact('consumos'));
        }

        $metricas = [
            'total' => Consumo::count(),
            'valor_total' => (float) Consumo::sum('total'),
            'este_mes' => (float) Consumo::whereMonth('fecha', now()->month)
                ->whereYear('fecha', now()->year)
                ->sum('total'),
            'pendientes' => Consumo::where('estado', 'no_registrado')->count(),
        ];

        return view('consumos.index', compact('consumos', 'metricas'));
    }

    public function show(Consumo $consumo)
    {
        $consumo->load([
            'user:id,name',
            'registradoPor:id,name',
            'factura:id,numero_factura',
            'detalles.productoBase:id,codigo,descripcion,und_detal',
            'detalles.productoEnsamblado:id,codigo,descripcion',
            'detalles.bodega:id,descripcion',
        ]);

        // Para un consumo pendiente, el admin necesita ver YA cuánto hay
        // disponible ahora mismo (pudo cambiar desde la venta) para saber
        // si ya puede registrar o todavía falta ajustar inventario.
        if ($consumo->estado === 'no_registrado') {
            $consumo->detalles->each(function (ConsumoDetalle $detalle) {
                $detalle->stock_disponible = (float) (DB::table('inventarios')
                    ->where('producto_id', $detalle->producto_base_id)
                    ->where('bodega_id', $detalle->bodega_id)
                    ->value('stock') ?? 0);
            });
        }

        return response()->json($consumo);
    }

    /**
     * Corrige la cantidad de una línea de un consumo TODAVÍA no
     * registrado — ej. la receta en realidad usa 150gr, no 160gr.
     */
    public function actualizarDetalle(Request $request, ConsumoDetalle $detalle)
    {
        $consumo = $detalle->consumo;
        if ($consumo->factura?->estado === 'anulada') {
            throw ValidationException::withMessages(['consumo'=>'No se puede modificar ni registrar el consumo de una factura anulada.']);
        }
        $this->exigirNoRegistrado($consumo);

        $datos = $request->validate(['cantidad' => 'required|numeric|min:0.01']);

        $detalle->update([
            'cantidad' => $datos['cantidad'],
            'subtotal' => round($datos['cantidad'] * (float) $detalle->costo_unitario, 2),
        ]);

        $consumo->update(['total' => $consumo->detalles()->sum('subtotal')]);

        return response()->json([
            'success' => true,
            'message' => 'Cantidad actualizada.',
            'data' => $consumo->fresh('detalles'),
        ]);
    }

    /**
     * Descarta una línea problemática de un consumo pendiente (ej. la
     * hamburguesa no debía contarse) SIN descontar inventario por ella.
     * Si no queda ninguna línea, el consumo se cierra solo (nada que
     * registrar).
     */
    public function eliminarDetalle(ConsumoDetalle $detalle)
    {
        $consumo = $detalle->consumo;
        if ($consumo->factura?->estado === 'anulada') {
            throw ValidationException::withMessages(['consumo'=>'No se puede modificar ni registrar el consumo de una factura anulada.']);
        }
        $this->exigirNoRegistrado($consumo);

        $detalle->delete();
        $consumo->update(['total' => $consumo->detalles()->sum('subtotal')]);

        if ($consumo->detalles()->count() === 0) {
            $consumo->update([
                'estado' => 'registrado',
                'registrado_por' => auth()->id(),
                'registrado_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Línea eliminada del consumo.',
            'data' => $consumo->fresh('detalles'),
        ]);
    }

    /**
     * El administrador ya ajustó el inventario (o eliminó la línea
     * problemática): revalida el stock de TODO lo que quede y, si
     * alcanza, descuenta cada línea y deja su rastro en el kardex — recién
     * ahí el consumo pasa a 'registrado'. Todo o nada: si a una línea
     * todavía le falta stock, no se registra ninguna.
     */
    public function registrar(Consumo $consumo)
    {
        if ($consumo->factura?->estado === 'anulada') {
            throw ValidationException::withMessages(['consumo'=>'No se puede modificar ni registrar el consumo de una factura anulada.']);
        }
        $this->exigirNoRegistrado($consumo);
        $consumo->load('detalles.productoBase:id,descripcion');

        if ($consumo->detalles->isEmpty()) {
            $consumo->update(['estado' => 'registrado', 'registrado_por' => auth()->id(), 'registrado_at' => now()]);

            return response()->json(['success' => true, 'message' => 'Consumo cerrado (no le quedaban líneas por descontar).']);
        }

        DB::transaction(function () use ($consumo) {
            $bloqueados = [];
            foreach ($consumo->detalles as $detalle) {
                $bloqueados[$detalle->id] = DB::table('inventarios')
                    ->where('producto_id', $detalle->producto_base_id)
                    ->where('bodega_id', $detalle->bodega_id)
                    ->lockForUpdate()
                    ->first();

                $disponible = (float) ($bloqueados[$detalle->id]->stock ?? 0);
                if ($disponible < (float) $detalle->cantidad) {
                    throw ValidationException::withMessages([
                        'inventario' => "Todavía no hay stock suficiente de \"{$detalle->productoBase->descripcion}\" (necesita {$detalle->cantidad}, hay {$disponible} disponibles). Ajusta el inventario o elimina esa línea antes de registrar.",
                    ]);
                }
            }

            $documentoId = $consumo->factura?->documento_id;

            foreach ($consumo->detalles as $detalle) {
                DB::table('inventarios')
                    ->where('producto_id', $detalle->producto_base_id)
                    ->where('bodega_id', $detalle->bodega_id)
                    ->decrement('stock', $detalle->cantidad);

                $stockNuevo = (float) DB::table('inventarios')
                    ->where('producto_id', $detalle->producto_base_id)
                    ->where('bodega_id', $detalle->bodega_id)
                    ->value('stock');

                $this->kardex->registrar(
                    $documentoId,
                    null,
                    $detalle->producto_base_id,
                    $detalle->bodega_id,
                    'SALIDA',
                    (float) $detalle->cantidad,
                    $stockNuevo + (float) $detalle->cantidad,
                    $stockNuevo,
                    null,
                    auth()->id(),
                    now()
                );
            }

            $consumo->update([
                'estado' => 'registrado',
                'registrado_por' => auth()->id(),
                'registrado_at' => now(),
            ]);
        });

        return response()->json(['success' => true, 'message' => 'Consumo registrado: inventario descontado correctamente.']);
    }

    private function exigirNoRegistrado(Consumo $consumo): void
    {
        if ($consumo->estado !== 'no_registrado') {
            throw ValidationException::withMessages([
                'consumo' => 'Este consumo ya está registrado — no se puede editar.',
            ]);
        }
    }
}
