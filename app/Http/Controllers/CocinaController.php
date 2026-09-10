<?php

namespace App\Http\Controllers;

use App\Models\ComandaPendiente;
use App\Models\DetallePedido;
use App\Models\NotificacionPedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CocinaController extends Controller
{
    public function index(Request $request)
    {
        $this->autorizar($request);

        return view('cocina.index');
    }

    public function comandas(Request $request)
    {
        $this->autorizar($request);

        $comandas = ComandaPendiente::query()
            ->with(['pedido.mesa.zona', 'pedido.mesero', 'impresora'])
            ->where('tipo', 'comanda')
            ->whereIn('estado', ['pendiente', 'impreso'])
            ->whereHas('impresora', fn ($query) => $query->whereRaw('LOWER(nombre) LIKE ?', ['%cocina%']))
            ->orderBy('created_at')
            ->get()
            ->map(function (ComandaPendiente $comanda) {
                $items = DetallePedido::query()
                    ->with('producto:id,descripcion')
                    ->whereIn('id', $comanda->detalle_ids ?? [])
                    ->orderBy('id')
                    ->get()
                    ->map(fn (DetallePedido $detalle) => [
                        'cantidad' => $detalle->cantidad,
                        'producto' => $detalle->producto?->descripcion ?? 'Producto eliminado',
                        'observacion' => $detalle->observacion,
                    ]);

                return [
                    'id' => $comanda->id,
                    'pedido_id' => $comanda->pedido_id,
                    'mesa' => $comanda->pedido?->mesa?->numero ?? 'Sin mesa',
                    'zona' => $comanda->pedido?->mesa?->zona?->nombre ?? null,
                    'mesero' => $comanda->pedido?->mesero?->name ?? 'Sin asignar',
                    'impresora' => $comanda->impresora?->nombre ?? 'Cocina',
                    'creado_en' => $comanda->created_at?->toIso8601String(),
                    'items' => $items,
                    // Comandas anteriores a este módulo no guardaban detalle_ids.
                    'contenido_respaldo' => $items->isEmpty() ? $comanda->contenido : null,
                ];
            });

        return response()
            ->json(['data' => $comandas->values()->all()])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }

    public function finalizar(Request $request, int $id)
    {
        $this->autorizar($request);

        DB::transaction(function () use ($request, $id) {
            $comanda = ComandaPendiente::lockForUpdate()->find($id);
            if (!$comanda) {
                abort(422, 'La comanda ya no existe. Actualiza la pantalla de cocina.');
            }
            if ($comanda->tipo !== 'comanda' || !in_array($comanda->estado, ['pendiente', 'impreso'], true)) {
                abort(422, 'Esta comanda ya fue finalizada o no está disponible.');
            }

            $pedido = $comanda->pedido()->with('mesa')->first();
            $comanda->update([
                'estado' => 'finalizado',
                'finalizado_por' => $request->user()->id,
                'finalizado_at' => now(),
            ]);

            if ($pedido) {
                NotificacionPedido::create([
                    'user_id' => $pedido->user_id,
                    'pedido_id' => $pedido->id,
                    'comanda_pendiente_id' => $comanda->id,
                    'tipo' => 'pedido_listo',
                    'mensaje' => 'Mesa ' . ($pedido->mesa?->numero ?? 'sin número') . ': tu pedido está listo para entregar.',
                ]);
            }
        });

        return response()->json(['message' => 'Comanda marcada como lista correctamente.']);
    }

    private function autorizar(Request $request): void
    {
        abort_unless(in_array($request->user()?->rol?->nombre, ['Cocina', 'Administrador'], true), 403);
    }
}
