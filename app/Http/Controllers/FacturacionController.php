<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\DetallePedido;
use App\Models\Factura;
use App\Models\Mesa;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Zona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Services\PrintService;

class FacturacionController extends Controller
{
    public function index()
    {
        // 1. Obtenemos el usuario de forma segura
        $user = Auth::user();

        // 2. Verificamos sesión y rol
        if (!$user || !$user->rol) {
            abort(403, 'Sesión no válida o usuario sin rol asignado.');
        }

        $nombreRol = $user->rol->nombre;

        // 3. VALIDAR PERMISOS DE ACCESO
        if (!in_array($nombreRol, ['Mesero', 'Administrador', 'Cajero'])) {
            abort(403, 'No tienes acceso a la zona de facturación');
        }

        // LÓGICA DE AUTO-REVERSIÓN
        Mesa::where('estado', 'seleccionada')
            ->where('updated_at', '<', now()->subMinutes(2))
            ->update(['estado' => 'disponible']);

        // 4. Traer datos para la vista
        $mesas = Mesa::with(['zona', 'pedidos' => function ($query) {
            $query->where('estado', 'pendiente')->with('user');
        }])->get();

        $zonas    = Zona::all();
        $productos = Producto::where('activo', 1)->orderBy('categoria')->get();
        $categorias = $productos->pluck('categoria')->unique();

        // ← Agregar esta línea
        $categorias_pos = \App\Models\CategoriaPos::orderBy('orden')->get();

        return view('facturacion.index', compact(
            'productos',
            'categorias',
            'mesas',
            'zonas',
            'categorias_pos' // ← y esta
        ));
    }

    public function bloquearMesa($id)
    {
        $mesa = Mesa::findOrFail($id);

        // Si alguien más ya la cambió de estado en ese milisegundo
        if ($mesa->estado !== 'disponible') {
            return response()->json([
                'status' => 'error',
                'message' => 'La mesa ya no está disponible'
            ], 403);
        }

        // Cambiamos a 'seleccionada' (el estado de espera)
        $mesa->update(['estado' => 'seleccionada']);

        return response()->json(['status' => 'success']);
    }

    public function liberarMesa($id)
    {
        try {
            $mesa = Mesa::findOrFail($id);

            // 1. Cambiamos el estado a disponible
            $mesa->estado = 'disponible';

            // OJO: Si usas una columna para saber quién bloqueó la mesa (ej: usuario_id), 
            // asegúrate de limpiarla también aquí.
            // $mesa->usuario_id = null; 

            $mesa->save();

            // 2. Buscamos el pedido pendiente para borrar sus detalles primero (evita errores de integridad)
            $pedido = Pedido::where('mesa_id', $id)->where('estado', 'pendiente')->first();

            if ($pedido) {
                // Borramos los detalles y luego el pedido
                $pedido->detalles()->delete();
                $pedido->delete();
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Mesa liberada correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al liberar: ' . $e->getMessage()
            ], 500);
        }
    }

    public function obtenerEstadoMesas()
    {
        Mesa::where('estado', 'seleccionada')
            ->where('updated_at', '<', now()->subMinutes(2))
            ->update(['estado' => 'disponible']);

        $mesas = Mesa::with(['zona', 'pedidos' => function ($query) {
            $query->where('estado', 'pendiente')->with('user');
        }])->get();

        return view('facturacion.partials.mesas_grid', compact('mesas'));
    }

    public function guardarPedido(Request $request, PrintService $printService)
    {
        try {
            DB::beginTransaction();

            $userId = Auth::id();
            if (!$userId) {
                return response()->json(['status' => 'error', 'message' => 'Debes estar autenticado.'], 401);
            }

            // 1. Buscamos o creamos el pedido
            $pedido = Pedido::firstOrCreate(
                ['mesa_id' => $request->mesa_id, 'estado' => 'pendiente'],
                ['user_id' => $userId, 'total' => 0]
            );

            $nuevoSubtotal = 0;
            $itemsNuevosIds = [];

            // 2. Guardamos cada item
            foreach ($request->items as $item) {
                $subtotalItem = $item['precio'] * $item['cantidad'];
                $detalle = DetallePedido::create([
                    'pedido_id' => $pedido->id,
                    'producto_id' => $item['id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio'],
                    'subtotal' => $subtotalItem,
                    'observacion' => $item['observacion'] ?? null,
                ]);
                $itemsNuevosIds[] = $detalle->id;
                $nuevoSubtotal += $subtotalItem;
            }

            $pedido->increment('total', $nuevoSubtotal);
            Mesa::where('id', $request->mesa_id)->update(['estado' => 'ocupada']);

            // 5. CARGAR DATOS PARA IMPRESIÓN
            $pedidoParaImprimir = Pedido::with([
                'mesa.zona',
                'mesero',
                'detalles' => function ($query) use ($itemsNuevosIds) {
                    $query->whereIn('id', $itemsNuevosIds)
                        ->with('producto.grupoMenu.impresora');
                }
            ])->find($pedido->id);

            // 6. ENVIAR AL SERVICIO (CAMBIO AQUÍ)
            // Usamos el método que separa por impresora automáticamente
            $resultadoImpresion = $printService->procesarYEnviarComandas($pedidoParaImprimir);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => '¡Pedido enviado y comanda impresa!',
                'impresion' => $resultadoImpresion
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // public function guardarPedido(Request $request)
    // {
    //     try {
    //         DB::beginTransaction();

    //         $userId = Auth::id();

    //         if (!$userId) {
    //             return response()->json([
    //                 'status' => 'error',
    //                 'message' => 'Debes estar autenticado para enviar pedidos.'
    //             ], 401);
    //         }

    //         $pedido = Pedido::firstOrCreate(
    //             ['mesa_id' => $request->mesa_id, 'estado' => 'pendiente'],
    //             ['user_id' => $userId, 'total' => 0]
    //         );
    //         $nuevoSubtotal = 0;

    //         // 2. Guardamos cada item del ticket
    //         foreach ($request->items as $item) {
    //             $subtotalItem = $item['precio'] * $item['cantidad'];

    //             DetallePedido::create([
    //                 'pedido_id' => $pedido->id,
    //                 'producto_id' => $item['id'],
    //                 'cantidad' => $item['cantidad'],
    //                 'precio_unitario' => $item['precio'],
    //                 'subtotal' => $item['precio'] * $item['cantidad'],
    //                 'observacion' => $item['observacion'] ?? null, // <--- GUARDAR NOTA
    //             ]);

    //             $nuevoSubtotal += $subtotalItem;
    //         }

    //         // 3. Actualizamos el total del pedido
    //         $pedido->increment('total', $nuevoSubtotal);

    //         // 4. CAMBIO CLAVE: Pasamos la mesa a ocupada
    //         $mesa = Mesa::find($request->mesa_id);
    //         $mesa->update(['estado' => 'ocupada']);

    //         DB::commit();
    //         return response()->json(['status' => 'success', 'message' => '¡Pedido enviado!']);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    //     }
    // }

    public function obtenerPedidoPendiente($mesaId)
    {
        $pedido = Pedido::where('mesa_id', $mesaId)
            ->where('estado', 'pendiente')
            // ← quitamos ->where('user_id', auth()->id())
            ->with(['detalles.producto', 'user'])
            ->first();

        if (!$pedido) {
            return response()->json(['status' => 'error', 'message' => 'No hay pedido'], 404);
        }

        $items = $pedido->detalles->map(function ($detalle) {
            return [
                'producto_id'     => $detalle->producto_id,
                'nombre_producto' => $detalle->producto->descripcion,
                'precio'          => $detalle->precio_unitario,
                'cantidad'        => $detalle->cantidad,
                'observacion'     => $detalle->observacion ?? '',
            ];
        });

        return response()->json([
            'status'         => 'success',
            'items'          => $items,
            'mesero_nombre'  => $pedido->user->name ?? 'Sin mesero', // ← nuevo
        ]);
    }

    public function eliminarItemPedido(Request $request)
    {
        if ($request->clave !== '1234') {
            return response()->json(['status' => 'error', 'message' => 'Clave incorrecta'], 403);
        }

        // Agrega este log temporal para ver qué llega
        Log::info('Eliminar item:', $request->all());

        try {
            $pedido = Pedido::where('mesa_id', $request->mesa_id)
                ->where('estado', 'pendiente')
                ->first();

            if (!$pedido) {
                return response()->json(['status' => 'error', 'message' => 'Pedido no encontrado'], 404);
            }

            $item = $pedido->detalles()
                ->where('producto_id', $request->producto_id)
                ->first();

            if (!$item) {
                return response()->json(['status' => 'error', 'message' => 'Item no encontrado'], 404);
            }

            // Restar del total solo lo de este item
            $pedido->decrement('total', $item->subtotal);
            $item->delete();

            // Solo borrar el pedido si explícitamente no quedan items
            $restantes = $pedido->detalles()->count();
            if ($restantes === 0) {
                $pedido->delete();
                Mesa::find($request->mesa_id)?->update(['estado' => 'disponible']);

                return response()->json([
                    'status'           => 'success',
                    'pedido_eliminado' => true  // ✅ avisa al JS que el pedido ya no existe
                ]);
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Error eliminar item: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function cerrarMesa(Request $request, PrintService $printService)
    {
        // 1. VALIDACIÓN DE ENTRADA
        $request->validate([
            'mesa_id'       => 'required|exists:mesas,id',
            'metodo_pago'   => 'required|in:efectivo,tarjeta,transferencia,mixto',
            'total'         => 'required|numeric|min:0',
            'tipo_tarjeta'  => 'nullable|string',
            'banco_destino' => 'nullable|string',
            'referencia'    => 'nullable|string',
            'cliente_id'    => 'nullable|integer',
        ]);

        // 2. OBTENER CAJA E IMPRESORA DEL USUARIO
        $caja = Caja::where('user_id', auth()->id())->with('impresora')->first();

        // DIAGNÓSTICO DE CAJA
        if (!$caja) {
            Log::warning("Diagnóstico: Usuario ID " . auth()->id() . " intentó facturar pero NO tiene caja asignada.");
            return response()->json([
                'status'  => 'error',
                'message' => 'Tu usuario no tiene una caja asignada o activa.'
            ], 403);
        }

        Log::info("Diagnóstico: Facturando con Caja ID: {$caja->id} - Nombre: {$caja->nombre}");

        // 3. OBTENER PEDIDOS PENDIENTES
        $pedidos = Pedido::where('mesa_id', $request->mesa_id)
            ->where('estado', 'pendiente')
            ->with('detalles.producto')
            ->get();

        if ($pedidos->isEmpty()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No hay pedidos pendientes para facturar en esta mesa.'
            ], 422);
        }

        try {
            DB::beginTransaction();

            // 4. VALIDACIÓN PREVIA DE STOCK
            foreach ($pedidos as $pedido) {
                foreach ($pedido->detalles as $detalle) {
                    $producto = $detalle->producto;
                    if (!$producto || $producto->afecta_inventario != 1) continue;

                    $inventario = DB::table('inventarios')
                        ->where('producto_id', $producto->id)
                        ->where('bodega_id', $caja->bodega_id)
                        ->first();

                    if (!$inventario || $inventario->stock < $detalle->cantidad) {
                        throw new \Exception("Stock insuficiente para: {$producto->descripcion}");
                    }
                }
            }

            // 5. GENERAR NÚMERO DE FACTURA
            $ultimaFactura = Factura::where('caja_id', $caja->id)
                ->where('numero_factura', 'LIKE', $caja->prefijo . '-%')
                ->orderBy('id', 'desc')
                ->first();

            $nuevoNumero = $ultimaFactura
                ? intval(explode('-', $ultimaFactura->numero_factura)[1] ?? 0) + 1
                : 1;

            $numeroFactura = $caja->prefijo . '-' . str_pad($nuevoNumero, 5, '0', STR_PAD_LEFT);

            // 6. CREAR CABECERA DE FACTURA
            $factura = Factura::create([
                'numero_factura'  => $numeroFactura,
                'mesa_id'         => $request->mesa_id,
                'user_id'         => auth()->id(),
                'cliente_id'      => $request->cliente_id ?? 1,
                'caja_id'         => $caja->id,
                'subtotal'        => $request->total,
                'impuestos'       => 0,
                'total'           => $request->total,
                'metodo_pago'     => $request->metodo_pago,
                'tipo_tarjeta'    => $request->tipo_tarjeta,
                'banco_destino'   => $request->banco_destino,
                'referencia_pago' => $request->referencia,
                'estado'          => 'pagada'
            ]);

            // 7. PROCESAR DETALLES, INVENTARIO Y PEDIDOS
            foreach ($pedidos as $pedido) {
                foreach ($pedido->detalles as $detalle) {
                    $factura->detalles()->create([
                        'producto_id'     => $detalle->producto_id,
                        'cantidad'        => $detalle->cantidad,
                        'precio_unitario' => $detalle->precio_unitario,
                        'subtotal'        => $detalle->subtotal
                    ]);

                    if ($detalle->producto && $detalle->producto->afecta_inventario == 1) {
                        DB::table('inventarios')
                            ->where('producto_id', $detalle->producto_id)
                            ->where('bodega_id', $caja->bodega_id)
                            ->decrement('stock', $detalle->cantidad);
                    }
                }
                $pedido->update(['estado' => 'pagado']);
            }

            // 8. LIBERAR MESA
            Mesa::where('id', $request->mesa_id)->update(['estado' => 'disponible']);

            // 9. DIAGNÓSTICO DE IMPRESIÓN
            if ($caja->impresora) {
                try {
                    // CARGA CRÍTICA DE RELACIONES
                    $factura->load([
                        'detalles.producto',
                        'user',                // Relación con el Cajero
                        'mesa.pedidos.mesero', // Relación para sacar el Mesero
                        'cliente',
                        'caja'
                    ]);

                    $printService->imprimirFactura($factura, $caja->impresora);
                } catch (\Exception $e) {
                    Log::error("Error de impresora: " . $e->getMessage());
                }
            }

            DB::commit();
            Log::info('--- CHECKPOINT POST-COMMIT ---');
            Log::info('Caja ID: ' . $caja->id);
            Log::info('Impresora raw: ' . json_encode($caja->getRelationValue('impresora')));
            Log::info('impresora_id en caja: ' . ($caja->impresora_id ?? 'NULL'));

            return response()->json([
                'status'     => 'success',
                'message'    => "Venta $numeroFactura registrada y mesa liberada.",
                'factura_id' => $factura->id
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Diagnóstico: Fallo crítico en transacción: " . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
