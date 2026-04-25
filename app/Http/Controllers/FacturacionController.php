<?php

namespace App\Http\Controllers;

use App\Models\DetallePedido;
use App\Models\Mesa;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Zona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        // 3. Validar permisos de acceso
        if ($nombreRol !== 'Mesero' && $nombreRol !== 'Administrador') {
            abort(403, 'No tienes acceso a la zona de facturación');
        }

        /** * LÓGICA DE AUTO-REVERSIÓN (Limpieza de seguridad)
         * Buscamos mesas que estén 'seleccionada' pero que NO se hayan actualizado 
         * en los últimos 2 minutos. Las volvemos a poner 'disponible'.
         */
        Mesa::where('estado', 'seleccionada')
            ->where('updated_at', '<', now()->subMinutes(2))
            ->update(['estado' => 'disponible']);

        // 4. Traer datos para la vista
        // Cargamos mesas con su zona para evitar múltiples consultas (Eager Loading)
        $mesas = Mesa::with(['zona', 'pedidos' => function ($query) {
            $query->where('estado', 'pendiente');
        }])->get();
        $zonas = Zona::all();

        // Optimizamos la carga de productos y categorías
        $productos = Producto::where('activo', 1)->orderBy('categoria')->get();
        $categorias = $productos->pluck('categoria')->unique();

        return view('facturacion.index', compact('productos', 'categorias', 'mesas', 'zonas'));
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
        // Limpieza de seguridad antes de mostrar (reversión de 2 min)
        Mesa::where('estado', 'seleccionada')
            ->where('updated_at', '<', now()->subMinutes(2))
            ->update(['estado' => 'disponible']);

        $mesas = Mesa::with('zona')->get();

        // Devolvemos solo el contenido de las mesas (crearemos este archivo ahora)
        return view('facturacion.partials.mesas_grid', compact('mesas'));
    }

    public function guardarPedido(Request $request)
    {
        try {
            DB::beginTransaction();

            $userId = Auth::id();

            if (!$userId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Debes estar autenticado para enviar pedidos.'
                ], 401);
            }

            $pedido = Pedido::firstOrCreate(
                ['mesa_id' => $request->mesa_id, 'estado' => 'pendiente'],
                ['user_id' => $userId, 'total' => 0]
            );
            $nuevoSubtotal = 0;

            // 2. Guardamos cada item del ticket
            foreach ($request->items as $item) {
                $subtotalItem = $item['precio'] * $item['cantidad'];

                DetallePedido::create([
                    'pedido_id' => $pedido->id,
                    'producto_id' => $item['id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio'],
                    'subtotal' => $item['precio'] * $item['cantidad'],
                    'observacion' => $item['observacion'] ?? null, // <--- GUARDAR NOTA
                ]);

                $nuevoSubtotal += $subtotalItem;
            }

            // 3. Actualizamos el total del pedido
            $pedido->increment('total', $nuevoSubtotal);

            // 4. CAMBIO CLAVE: Pasamos la mesa a ocupada
            $mesa = Mesa::find($request->mesa_id);
            $mesa->update(['estado' => 'ocupada']);

            DB::commit();
            return response()->json(['status' => 'success', 'message' => '¡Pedido enviado!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function obtenerPedidoPendiente($mesaId)
    {
        $pedido = Pedido::where('mesa_id', $mesaId)
            ->where('estado', 'pendiente')
            ->where('user_id', auth()->id())
            ->with('detalles.producto')
            ->first();

        if (!$pedido) {
            return response()->json(['status' => 'error', 'message' => 'No hay pedido'], 404);
        }

        $items = $pedido->detalles->map(function ($detalle) {
            return [
                'producto_id' => $detalle->producto_id,
                'nombre_producto' => $detalle->producto->descripcion,
                'precio' => $detalle->precio_unitario,
                'cantidad' => $detalle->cantidad,
                'observacion' => $detalle->observacion ?? ""

            ];
        });

        return response()->json([
            'status' => 'success',
            'items' => $items
        ]);
    }

    public function eliminarItemPedido(Request $request)
    {
        // Validamos la SuperClave también en el servidor por seguridad
        if ($request->clave !== '1234') {
            return response()->json(['status' => 'error', 'message' => 'Clave incorrecta'], 403);
        }

        try {
            // Buscamos el pedido pendiente de esa mesa
            $pedido = Pedido::where('mesa_id', $request->mesa_id)
                ->where('estado', 'pendiente')
                ->first();

            if ($pedido) {
                // Borramos el producto específico de los detalles
                $item = $pedido->detalles()->where('producto_id', $request->producto_id)->first();

                if ($item) {
                    $subtotalCudado = $item->subtotal;
                    $item->delete();

                    // Actualizamos el total del pedido restando lo eliminado
                    $pedido->decrement('total', $subtotalCudado);

                    // Si el pedido se quedó sin productos, opcionalmente puedes borrar el pedido
                    if ($pedido->detalles()->count() == 0) {
                        $pedido->delete();
                        // Y volvemos la mesa a disponible
                        Mesa::find($request->mesa_id)->update(['estado' => 'disponible']);
                    }
                }
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
