<?php

namespace App\Http\Controllers;

use App\Models\ComandaPendiente;
use App\Models\DetallePedido;
use App\Models\NotificacionPedido;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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

        // Las comandas 'anulacion' ya NO se muestran como ficha aparte: eso
        // era lo que hacía que un solo item cancelado apareciera 2 o 3
        // veces en pantalla (una por cada impresora del grupo). Ahora el
        // ítem cancelado se marca (DetallePedido::cancelado_at) y sigue
        // viviendo DENTRO de su comanda original, tachado — ver el mapeo
        // de items más abajo. La comanda 'anulacion' solo sirve para el
        // ticket físico del agente de impresión (routes/web.php: /agente).
        // Se incluye 'error' (el agente de impresión no pudo conectar con la
        // impresora física — apagada, sin papel, sin red) a propósito: antes
        // esas comandas desaparecían de la pantalla para siempre (ni acá, ni
        // en el Historial, ni se podían finalizar) aunque la venta sí se
        // hubiera hecho — el cocinero se quedaba sin saber que había un
        // pedido. Ahora sigue viendo el pedido igual, con un aviso de que el
        // ticket físico no salió, para que alguien revise la impresora aparte.
        $comandas = ComandaPendiente::query()
            ->with(['pedido.mesa.zona', 'pedido.mesero', 'impresora'])
            ->where('tipo', 'comanda')
            ->whereIn('estado', ['pendiente', 'impreso', 'error'])
            ->whereHas('impresora', fn ($query) => $query->whereRaw('LOWER(nombre) LIKE ?', ['%cocina%']))
            ->orderBy('created_at')
            ->get()
            ->map(function (ComandaPendiente $comanda) {
                $items = DetallePedido::query()
                    ->with(['producto:id,descripcion', 'canceladoPor:id,name'])
                    ->whereIn('id', $comanda->detalle_ids ?? [])
                    ->orderBy('id')
                    ->get()
                    ->map(fn (DetallePedido $detalle) => [
                        'cantidad' => $detalle->cantidad,
                        'producto' => $detalle->producto?->descripcion ?? 'Producto eliminado',
                        'observacion' => $detalle->observacion,
                        'cancelado' => $detalle->cancelado_at !== null,
                        'cancelado_por' => $detalle->canceladoPor?->name,
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
                    'error_impresion' => $comanda->estado === 'error',
                    'error_mensaje' => $comanda->estado === 'error' ? $comanda->error_mensaje : null,
                    // Comandas anteriores a este módulo no guardaban detalle_ids.
                    'contenido_respaldo' => $items->isEmpty() ? $comanda->contenido : null,
                ];
            });

        return response()
            ->json(['data' => $comandas->values()->all()])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }

    public function historial(Request $request)
    {
        $this->autorizar($request);

        return view('cocina.historial');
    }

    public function historialDatos(Request $request)
    {
        $this->autorizar($request);

        $filtros = $request->validate([
            'desde' => ['nullable', 'date_format:Y-m-d'],
            'hasta' => ['nullable', 'date_format:Y-m-d'],
        ]);

        $desde = Carbon::createFromFormat('Y-m-d', $filtros['desde'] ?? now()->toDateString())->startOfDay();
        $hasta = Carbon::createFromFormat('Y-m-d', $filtros['hasta'] ?? now()->toDateString())->endOfDay();

        if ($desde->gt($hasta)) {
            return response()->json(['message' => 'La fecha inicial no puede ser posterior a la fecha final.'], 422);
        }

        $comandas = ComandaPendiente::query()
            ->with(['pedido.mesa.zona', 'pedido.mesero', 'impresora'])
            ->where('tipo', 'comanda')
            ->where('estado', 'finalizado')
            ->whereBetween('finalizado_at', [$desde, $hasta])
            ->whereHas('impresora', fn ($query) => $query->whereRaw('LOWER(nombre) LIKE ?', ['%cocina%']))
            ->orderByDesc('finalizado_at')
            ->get();

        $detalleIds = $comandas
            ->flatMap(fn (ComandaPendiente $comanda) => $comanda->detalle_ids ?? [])
            ->filter()
            ->unique()
            ->values();

        $detalles = DetallePedido::query()
            ->with(['producto:id,descripcion', 'canceladoPor:id,name'])
            ->whereIn('id', $detalleIds)
            ->get()
            ->keyBy('id');

        $productos = [];
        $historial = $comandas->map(function (ComandaPendiente $comanda) use ($detalles, &$productos) {
            $items = collect($comanda->detalle_ids ?? [])
                ->map(fn ($id) => $detalles->get($id))
                ->filter();

            $unidades = 0;
            foreach ($items as $detalle) {
                // Un ítem cancelado no cuenta como preparado, aunque siga
                // visible en el historial para trazabilidad.
                if ($detalle->cancelado_at) {
                    continue;
                }

                $cantidad = (float) $detalle->cantidad;
                $unidades += $cantidad;
                $clave = $detalle->producto_id ?: 'eliminado-' . $detalle->id;

                if (!isset($productos[$clave])) {
                    $productos[$clave] = [
                        'producto' => $detalle->producto?->descripcion ?? 'Producto eliminado',
                        'cantidad' => 0,
                    ];
                }

                $productos[$clave]['cantidad'] += $cantidad;
            }

            return [
                'id' => $comanda->id,
                'mesa' => $comanda->pedido?->mesa?->numero ?? 'Sin mesa',
                'zona' => $comanda->pedido?->mesa?->zona?->nombre,
                'mesero' => $comanda->pedido?->mesero?->name ?? 'Sin asignar',
                'finalizado_en' => $comanda->finalizado_at?->toIso8601String(),
                'unidades' => $unidades,
                'items' => $items->map(fn (DetallePedido $detalle) => [
                    'producto' => $detalle->producto?->descripcion ?? 'Producto eliminado',
                    'cantidad' => $detalle->cantidad,
                    'cancelado' => $detalle->cancelado_at !== null,
                    'cancelado_por' => $detalle->canceladoPor?->name,
                ])->values(),
                'minutos_preparacion' => $comanda->created_at && $comanda->finalizado_at
                    ? $comanda->created_at->diffInMinutes($comanda->finalizado_at)
                    : null,
            ];
        });

        $tiempos = $historial->pluck('minutos_preparacion')->filter(fn ($minutos) => $minutos !== null);
        $productosOrdenados = collect($productos)->sortByDesc('cantidad')->values();

        return response()
            ->json([
                'resumen' => [
                    'comandas_finalizadas' => $historial->count(),
                    'unidades_preparadas' => $historial->sum('unidades'),
                    'productos_distintos' => $productosOrdenados->count(),
                    'minutos_promedio' => $tiempos->isNotEmpty() ? round($tiempos->avg()) : 0,
                ],
                'productos' => $productosOrdenados,
                'historial' => $historial->take(50)->values(),
            ])
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
            // 'error' se puede finalizar igual que 'pendiente'/'impreso': que
            // el ticket físico no haya salido no significa que el pedido no
            // se preparó — la cocina lo ve y lo marca listo desde la pantalla.
            if (!in_array($comanda->tipo, ['comanda', 'anulacion'], true) || !in_array($comanda->estado, ['pendiente', 'impreso', 'error'], true)) {
                abort(422, 'Esta comanda ya fue gestionada o no está disponible.');
            }

            $pedido = $comanda->pedido()->with('mesa')->first();
            $comanda->update([
                'estado' => 'finalizado',
                'finalizado_por' => $request->user()->id,
                'finalizado_at' => now(),
            ]);

            if ($comanda->tipo === 'comanda' && $pedido) {
                NotificacionPedido::create([
                    'user_id' => $pedido->user_id,
                    'pedido_id' => $pedido->id,
                    'comanda_pendiente_id' => $comanda->id,
                    'tipo' => 'pedido_listo',
                    'mensaje' => 'Mesa ' . ($pedido->mesa?->numero ?? 'sin número') . ': tu pedido está listo para entregar.',
                ]);
            }
        });

        return response()->json(['message' => 'Comanda actualizada correctamente.']);
    }

    private function autorizar(Request $request): void
    {
        abort_unless(in_array($request->user()?->rol?->nombre, ['Cocina', 'Administrador'], true), 403);
    }
}
