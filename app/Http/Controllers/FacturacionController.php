<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\CierreCaja;
use App\Models\ComandaPendiente;
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
use App\Services\LegacyDocumentSyncService;
use App\Services\PrintService;
use App\Services\ContabilidadService;
use App\DataTransferObjects\ContabilidadData;
use App\Services\FacturacionContableService;
use App\Services\AuditoriaService;
use Carbon\Carbon;

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

            $user = Auth::user();
            if (!$user) {
                return response()->json(['status' => 'error', 'message' => 'Debes estar autenticado.'], 401);
            }

            // 1. Buscamos o creamos el pedido
            $pedido = Pedido::firstOrCreate(
                ['mesa_id' => $request->mesa_id, 'estado' => 'pendiente'],
                ['user_id' => $user->id, 'total' => 0, 'cliente_id' => $request->cliente_id ?? 1]
            );

            // Si el pedido ya existía y llega un cliente distinto, actualízalo
            if ($request->cliente_id && $pedido->cliente_id != $request->cliente_id) {
                $pedido->update(['cliente_id' => $request->cliente_id]);
            }

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
                        ->with('producto.grupoMenu.impresoras');
                }
            ])->find($pedido->id);

            // 🌟 DETECTAR EL PUNTO DE IMPRESIÓN BASADO EN LA CAJA DEL MESERO
            $puntoActual = 'RESTAURANTE'; // Punto por defecto para administradores o si no tienen caja

            if ($user->caja_id) {
                $caja = DB::table('cajas')->where('id', $user->caja_id)->first();

                // Evaluamos la condición de tu sistema (por ejemplo, si caja_id es 2, o si tiene bodega_id = 2)
                if ($caja && ($caja->id == 2 || (isset($caja->bodega_id) && $caja->bodega_id == 2))) {
                    $puntoActual = 'DISCOTECA';
                }
            }

            // 6. ENVIAR AL SERVICIO PASANDO EL PUNTO ACTUAL DETECTADO
            // ✅ Ya no imprime directo: PrintService encola en comandas_pendientes
            // y el agente local instalado en el negocio es quien imprime de verdad.
            $resultadoImpresion = $printService->procesarYEnviarComandas($pedidoParaImprimir, $puntoActual);

            DB::commit();

            $productosComandados = $pedidoParaImprimir->detalles
                ->map(fn ($detalle) => $detalle->cantidad . 'x ' . ($detalle->producto?->descripcion ?? 'Producto'))
                ->implode(', ');
            $mesaNumero = $pedidoParaImprimir->mesa?->numero ?? $request->mesa_id;
            $request->attributes->set('auditoria_detallada', true);
            AuditoriaService::registrar(
                $user,
                'Facturación / Comandas',
                'Comanda enviada',
                'Comanda enviada para Mesa ' . $mesaNumero . ': ' . $productosComandados . '.',
                $request,
                'Pedido #' . $pedido->id,
                ['mesa' => $mesaNumero, 'productos' => $productosComandados, 'punto' => $puntoActual]
            );

            return response()->json([
                'status' => 'success',
                'message' => '¡Pedido enviado y comanda en cola de impresión!',
                'impresion' => $resultadoImpresion
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function actualizarClientePedido(Request $request)
    {
        $request->validate([
            'mesa_id' => 'required|exists:mesas,id',
            'cliente_id' => 'required|exists:terceros,id',
        ]);

        $pedido = Pedido::where('mesa_id', $request->mesa_id)
            ->where('estado', 'pendiente')
            ->first();

        if (!$pedido) {
            return response()->json(['status' => 'error', 'message' => 'No hay un pedido pendiente en esta mesa.'], 422);
        }

        $pedido->update(['cliente_id' => $request->cliente_id]);

        return response()->json(['status' => 'success']);
    }


    public function obtenerPedidoPendiente($mesaId)
    {
        $pedido = Pedido::where('mesa_id', $mesaId)
            ->where('estado', 'pendiente')
            ->with(['detalles.producto', 'user', 'cliente'])
            // ->with(['detalles.producto', 'user'])
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

        $nombreCliente = 'Consumidor Final';
        if ($pedido->cliente) {
            $nombreCliente = $pedido->cliente->tipo === 'persona'
                ? trim($pedido->cliente->nombre . ' ' . ($pedido->cliente->apellido ?? ''))
                : ($pedido->cliente->razon_social ?? 'Consumidor Final');
        }

        return response()->json([
            'status'         => 'success',
            'items'          => $items,
            'mesero_nombre'  => $pedido->user->name ?? 'Sin mesero', // ← nuevo
            'cliente_id'     => $pedido->cliente_id,
            'cliente_nombre' => $nombreCliente,
        ]);
    }

    public function eliminarItemPedido(Request $request, PrintService $printService)
    {
        $autorizador = Auth::user();
        if (!$autorizador || !$autorizador->clave_anulacion || !\Illuminate\Support\Facades\Hash::check((string) $request->clave, $autorizador->clave_anulacion)) {
            return response()->json(['status' => 'error', 'message' => 'Clave incorrecta'], 403);
        }

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

            // ✅ ENCOLAR COMANDA DE ANULACIÓN ANTES DE ELIMINAR
            try {
                // 🔄 Cambiado a plural 'grupoMenu.impresoras'
                $producto = $item->producto()->with('grupoMenu.impresoras')->first();
                $mesa     = Mesa::find($request->mesa_id);

                // Verificamos si existen impresoras asignadas en la relación muchos a muchos
                if ($producto && $producto->grupoMenu && $producto->grupoMenu->impresoras->isNotEmpty()) {

                    // 💡 Tomamos la primera impresora del grupo para enviar la notificación de anulación
                    foreach ($producto->grupoMenu->impresoras->where('activa', true) as $impresora) {
                        $printService->imprimirComandaAnulacion(
                            $mesa,
                            $producto,
                            $item->cantidad,
                            $item->observacion ?? '',
                            $impresora,
                            $impresora->nombre
                        );
                    }
                }
            } catch (\Exception $e) {
                Log::error("Error encolando anulación: " . $e->getMessage());
                // No frenamos el proceso si falla la impresora
            }

            // Restar del total solo lo de este item
            $pedido->decrement('total', $item->subtotal);
            $item->delete();

            // Solo borrar el pedido si no quedan items
            $restantes = $pedido->detalles()->count();
            if ($restantes === 0) {
                $pedido->delete();
                Mesa::find($request->mesa_id)?->update(['estado' => 'disponible']);

                return response()->json([
                    'status'           => 'success',
                    'pedido_eliminado' => true
                ]);
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Error eliminar item: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function cerrarMesa(
        Request $request,
        PrintService $printService,
        FacturacionContableService $facturacionContableService
    ) {
        $user = Auth::user();

        if (!in_array($user?->rol?->nombre, ['Administrador', 'Cajero'], true)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Solo un usuario con rol Cajero o Administrador puede cerrar una cuenta.'
            ], 403);
        }

        $request->validate([
            'mesa_id'       => 'required|exists:mesas,id',
            'metodo_pago'   => 'required|in:efectivo,tarjeta,transferencia,mixto,credito',
            'total'         => 'required|numeric|min:0',
            'propina'       => 'nullable|numeric|min:0',
            'tipo_tarjeta'  => 'nullable|string',
            'banco_destino' => 'nullable|string',
            'referencia'    => 'nullable|string',
            'cliente_id'    => 'nullable|integer',
        ]);

        $caja = Caja::with('impresora')->find($user->caja_id);

        if (!$caja || !$caja->activa) {
            Log::warning("Usuario ID " . auth()->id() . " intentó facturar sin caja asignada.");

            return response()->json([
                'status'  => 'error',
                'message' => 'Tu usuario no tiene una caja asignada o activa.'
            ], 403);
        }

        $pedidos = Pedido::where('mesa_id', $request->mesa_id)
            ->where('estado', 'pendiente')
            ->with('detalles.producto.integracionContable.procesoContable')
            ->get();

        if ($pedidos->isEmpty()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No hay pedidos pendientes para facturar en esta mesa.'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $todosLosDetalles = $pedidos->flatMap->detalles;
            $clienteId = $pedidos->first()->cliente_id ?? 1;

            // Una venta a crédito queda como cuenta por cobrar de un cliente real:
            // no se puede dejar a nombre del "Consumidor Final" (id 1) genérico.
            // Se valida sobre $clienteId (el del pedido, que es el que realmente
            // queda en la factura) y no sobre $request->cliente_id, que el
            // frontend puede resetear después de "Enviar pedido".
            if ($request->metodo_pago === 'credito' && (int) $clienteId <= 1) {
                DB::rollBack();

                return response()->json([
                    'status'  => 'error',
                    'message' => 'Para vender a crédito el pedido debe tener un cliente registrado (no "Consumidor Final"). Selecciona el cliente antes de enviar el pedido a cocina.',
                ], 422);
            }

            // 1. Validar stock
            foreach ($todosLosDetalles as $detalle) {
                $producto = $detalle->producto;

                if (!$producto || $producto->afecta_inventario != 1) {
                    continue;
                }

                $inventario = DB::table('inventarios')
                    ->where('producto_id', $producto->id)
                    ->where('bodega_id', $caja->bodega_id)
                    ->first();

                if (!$inventario || $inventario->stock < $detalle->cantidad) {
                    throw new \Exception("Stock insuficiente para: {$producto->descripcion}");
                }
            }

            // 2. Totales fiscales (una sola fuente de verdad: el service)
            $totales = $facturacionContableService->calcularTotales($todosLosDetalles);

            // 3. Numeración de factura
            $ultimaFactura = Factura::where('caja_id', $caja->id)
                ->where('numero_factura', 'LIKE', $caja->prefijo . '-%')
                ->orderBy('id', 'desc')
                ->first();

            $nuevoNumero = $ultimaFactura
                ? intval(explode('-', $ultimaFactura->numero_factura)[1] ?? 0) + 1
                : 1;

            $numeroFactura = $caja->prefijo . '-' . str_pad($nuevoNumero, 5, '0', STR_PAD_LEFT);

            // 4. Crear factura con subtotal/impuestos correctos
            $factura = Factura::create([
                'numero_factura'  => $numeroFactura,
                'mesa_id'         => $request->mesa_id,
                'user_id'         => auth()->id(),
                'cliente_id'      => $clienteId,
                'caja_id'         => $caja->id,
                'subtotal'        => $totales['base'],
                'impuestos'       => $totales['iva'],
                'propina'         => $request->propina ?? 0,
                'total'           => $request->total,
                'metodo_pago'     => $request->metodo_pago,
                'tipo_tarjeta'    => $request->tipo_tarjeta,
                'banco_destino'   => $request->banco_destino,
                'referencia_pago' => $request->referencia,
                'estado'          => 'pagada',
                'estado_pago'     => $request->metodo_pago === 'credito' ? 'pendiente' : 'pagada',
                'total_pagado'    => $request->metodo_pago === 'credito' ? 0 : $request->total,
                'saldo_pendiente' => $request->metodo_pago === 'credito' ? $request->total : 0,
            ]);

            // 5. Detalles + inventario
            foreach ($todosLosDetalles as $detalle) {
                $factura->detalles()->create([
                    'producto_id'     => $detalle->producto_id,
                    'cantidad'        => $detalle->cantidad,
                    'precio_unitario' => $detalle->precio_unitario,
                    'subtotal'        => $detalle->subtotal,
                ]);

                if ($detalle->producto && $detalle->producto->afecta_inventario == 1) {
                    DB::table('inventarios')
                        ->where('producto_id', $detalle->producto_id)
                        ->where('bodega_id', $caja->bodega_id)
                        ->decrement('stock', $detalle->cantidad);
                }
            }

            // 6. Cerrar pedidos y liberar mesa
            app(LegacyDocumentSyncService::class)->factura($factura);
            $pedidos->each->update(['estado' => 'pagado']);

            Mesa::where('id', $request->mesa_id)->update(['estado' => 'disponible']);

            // 7. Contabilizar — el controlador NO sabe de IVA, integraciones ni ContabilidadData
            $facturacionContableService->contabilizar($factura);

            // 8. Imprimir
            if ($caja->impresora) {
                try {
                    $factura->load([
                        'detalles.producto',
                        'user',
                        'mesa.pedidos.mesero',
                        'cliente',
                        'caja'
                    ]);

                    $printService->imprimirFactura($factura, $caja->impresora);
                } catch (\Exception $e) {
                    Log::error("Error de impresora: " . $e->getMessage());
                }
            }

            DB::commit();

            $productosFacturados = $todosLosDetalles
                ->map(fn ($detalle) => $detalle->cantidad . 'x ' . ($detalle->producto?->descripcion ?? 'Producto'))
                ->implode(', ');
            $request->attributes->set('auditoria_detallada', true);
            AuditoriaService::registrar(
                auth()->user(),
                'Facturación',
                'Factura pagada',
                'Factura ' . $factura->numero_factura . ' pagada por $' . number_format($factura->total, 0, ',', '.') . '. Productos: ' . $productosFacturados . '.',
                $request,
                $factura->numero_factura,
                [
                    'mesa' => $factura->mesa_id,
                    'total' => $factura->total,
                    'metodo_pago' => $factura->metodo_pago,
                    'productos' => $productosFacturados,
                ]
            );

            return response()->json([
                'status'     => 'success',
                'message'    => "Venta $numeroFactura registrada y mesa liberada.",
                'factura_id' => $factura->id
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error("Fallo crítico en transacción: " . $e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function imprimirInventarioPos(Request $request)
    {
        try {
            $user = Auth::user();

            // 1. Obtener la caja e impresora
            $caja = DB::table('cajas')->where('id', $user->caja_id)->first();
            if (!$caja) {
                return response()->json(['success' => false, 'message' => 'Caja no encontrada.'], 400);
            }

            $impresora = DB::table('impresoras')->where('id', $caja->impresora_id)->first();
            if (!$impresora) {
                return response()->json(['success' => false, 'message' => 'Impresora no configurada.'], 400);
            }

            // 2. Determinar la Bodega según la caja (Bodega 1 = Restaurante, 2 = Discoteca)
            $bodegaId = $caja->bodega_id;
            $nombreBodega = ($bodegaId == 1) ? "RESTAURANTE" : "DISCOTECA";

            // 3. Consultar la tabla INVENTARIOS con JOIN a PRODUCTOS
            // Esto trae el nombre del producto y el stock específico de esa bodega
            $inventario = DB::table('inventarios')
                ->join('productos', 'inventarios.producto_id', '=', 'productos.id')
                ->where('inventarios.bodega_id', $bodegaId)
                ->select('productos.descripcion', 'productos.und_detal', 'inventarios.stock')
                ->get();

            // 4. Construcción del Formato POS
            $txt = "========================================\n";
            $txt .= "      REVISIÓN DE INVENTARIO POS        \n";
            $txt .= "========================================\n";
            $txt .= "BODEGA: " . $nombreBodega . "\n";
            $txt .= "FECHA : " . date('d/m/Y h:i A') . "\n";
            $txt .= "CAJERO: " . strtoupper($user->name) . "\n";
            $txt .= "----------------------------------------\n";
            $txt .= "PRODUCTO            | STOCK  | CONTEO  \n";
            $txt .= "----------------------------------------\n";

            foreach ($inventario as $item) {
                // Cortar nombre a 19 caracteres para que no se desplace la columna
                $nombre = substr(strtoupper($item->descripcion), 0, 19);
                $nombrePad = str_pad($nombre, 19, " ");

                // Formatear stock (quitando decimales innecesarios .00)
                $stockVal = number_format($item->stock, 0);
                $stockPad = str_pad($stockVal, 6, " ", STR_PAD_LEFT);

                $txt .= "{$nombrePad} | {$stockPad} | _______\n";
            }

            $txt .= "----------------------------------------\n";
            $txt .= "   Favor reportar cualquier descuadre   \n";
            $txt .= "========================================\n";
            $txt .= "\n\n\n\n\n";

            // 5. ✅ ENCOLAR EN VEZ DE IMPRIMIR DIRECTO
            // Antes aquí se abría NetworkPrintConnector($impresora->ip, ...) y se
            // colgaba en Hostinger porque no hay ruta de red hacia la IP local.
            // Ahora se guarda el ticket en la cola y el agente local del negocio
            // lo recoge y lo manda de verdad a la impresora térmica.
            ComandaPendiente::create([
                'tipo'         => 'inventario',
                'impresora_id' => $impresora->id,
                'contenido'    => $txt,
                'estado'       => 'pendiente',
            ]);

            return response()->json(['success' => true, 'bodega' => $nombreBodega]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en impresión: ' . $e->getMessage()
            ], 500);
        }
    }

    public function procesarCierreCaja(Request $request)
    {
        try {

            $user = Auth::user();

            /*
        |--------------------------------------------------------------------------
        | VALIDACIONES
        |--------------------------------------------------------------------------
        */

            if (
                !$request->fecha_inicio ||
                !$request->hora_inicio  ||
                !$request->fecha_fin    ||
                !$request->hora_fin
            ) {
                return response()->json([
                    'success' => false,
                    'message' => 'Faltan fechas u horas del cierre.'
                ], 400);
            }

            $desde = \Carbon\Carbon::parse($request->fecha_inicio . ' ' . $request->hora_inicio);
            $hasta = \Carbon\Carbon::parse($request->fecha_fin    . ' ' . $request->hora_fin);

            /*
        |--------------------------------------------------------------------------
        | CAJA / IMPRESORA
        |--------------------------------------------------------------------------
        */

            $caja = DB::table('cajas')
                ->where('id', $user->caja_id)
                ->first();

            $impresora = DB::table('impresoras')
                ->where('id', $caja->impresora_id ?? 0)
                ->first();

            /*
        |--------------------------------------------------------------------------
        | FACTURAS
        |--------------------------------------------------------------------------
        */

            $facturas = DB::table('facturas')
                ->where('user_id', $user->id)
                ->whereBetween('created_at', [$desde, $hasta])
                ->orderBy('id')
                ->get();

            $cantidadFacturas = $facturas->count();
            $facturaInicial   = $facturas->first();
            $facturaFinal     = $facturas->last();

            /*
        |--------------------------------------------------------------------------
        | VENTAS SIN PROPINA
        |--------------------------------------------------------------------------
        */

            $ventas = DB::table('facturas')
                ->where('user_id', $user->id)
                ->whereBetween('created_at', [$desde, $hasta])
                ->selectRaw("
                SUM(CASE WHEN metodo_pago = 'efectivo'       THEN total - propina ELSE 0 END) as efectivo,
                SUM(CASE WHEN metodo_pago = 'qr'             THEN total - propina ELSE 0 END) as qr,
                SUM(CASE WHEN metodo_pago = 'tarjeta'        THEN total - propina ELSE 0 END) as tarjeta,
                SUM(CASE WHEN metodo_pago = 'transferencia'  THEN total - propina ELSE 0 END) as transferencia,
                SUM(total - propina) as total_ventas
            ")
                ->first();

            /*
        |--------------------------------------------------------------------------
        | PROPINAS
        |--------------------------------------------------------------------------
        */

            $propinas = DB::table('facturas')
                ->where('user_id', $user->id)
                ->whereBetween('created_at', [$desde, $hasta])
                ->selectRaw("
                SUM(CASE WHEN metodo_pago = 'efectivo'       THEN propina ELSE 0 END) as efectivo,
                SUM(CASE WHEN metodo_pago = 'qr'             THEN propina ELSE 0 END) as qr,
                SUM(CASE WHEN metodo_pago = 'tarjeta'        THEN propina ELSE 0 END) as tarjeta,
                SUM(CASE WHEN metodo_pago = 'transferencia'  THEN propina ELSE 0 END) as transferencia,
                SUM(propina) as total_propinas
            ")
                ->first();

            /*
        |--------------------------------------------------------------------------
        | MOVIMIENTOS DE CAJA
        |--------------------------------------------------------------------------
        */

            $movimientos = DB::table('movimientos_caja')
                ->whereBetween('created_at', [$desde, $hasta])
                ->get();

            $totalEntradas = $movimientos->where('tipo', 'entrada')->sum('valor');
            $totalSalidas  = $movimientos->where('tipo', 'salida')->sum('valor');

            /*
        |--------------------------------------------------------------------------
        | ARQUEO
        |--------------------------------------------------------------------------
        */

            $arqueoEfectivo      = ($ventas->efectivo      ?? 0) + ($propinas->efectivo      ?? 0);
            $arqueoQr            = ($ventas->qr            ?? 0) + ($propinas->qr            ?? 0);
            $arqueoTarjeta       = ($ventas->tarjeta       ?? 0) + ($propinas->tarjeta       ?? 0);
            $arqueoTransferencia = ($ventas->transferencia ?? 0) + ($propinas->transferencia ?? 0);

            /*
        |--------------------------------------------------------------------------
        | CONTEO FISICO
        |--------------------------------------------------------------------------
        */

            $m100   = intval($request->m100    ?? 0) * 100;
            $m200   = intval($request->m200    ?? 0) * 200;
            $m500   = intval($request->m500    ?? 0) * 500;
            $m1000  = intval($request->m1000   ?? 0) * 1000;

            $b2000   = intval($request->b2000   ?? 0) * 2000;
            $b5000   = intval($request->b5000   ?? 0) * 5000;
            $b10000  = intval($request->b10000  ?? 0) * 10000;
            $b20000  = intval($request->b20000  ?? 0) * 20000;
            $b50000  = intval($request->b50000  ?? 0) * 50000;
            $b100000 = intval($request->b100000 ?? 0) * 100000;

            $totalFisico =
                $m100 + $m200 + $m500 + $m1000 +
                $b2000 + $b5000 + $b10000 + $b20000 + $b50000 + $b100000;

            /*
        |--------------------------------------------------------------------------
        | TOTAL ESPERADO
        |--------------------------------------------------------------------------
        */

            $baseInicial = floatval($request->base_caja ?? 0);

            $efectivoEsperado =
                $baseInicial +
                $arqueoEfectivo +
                $totalEntradas -
                $totalSalidas;

            $diferencia = $totalFisico - $efectivoEsperado;

            /*
        |--------------------------------------------------------------------------
        | HELPERS DE IMPRESION (ahora generan texto plano, no objetos $printer)
        |--------------------------------------------------------------------------
        */

            $W = 40;

            // Dos columnas alineadas: texto izquierda, valor derecha
            $col = function ($izq, $der) use ($W) {
                $espacios = $W - mb_strlen($izq) - mb_strlen($der);
                return $izq . str_repeat(' ', max(1, $espacios)) . $der . "\n";
            };

            $lineaDoble  = str_repeat('=', $W) . "\n";
            $lineaSimple = str_repeat('-', $W) . "\n";
            $lineaPuntos = str_repeat('. ', (int) ($W / 2)) . "\n";

            // Título de sección centrado
            $seccion = function ($titulo) use ($W, $lineaPuntos) {
                $label = '[ ' . $titulo . ' ]';
                $pad   = str_repeat(' ', max(0, (int) floor(($W - mb_strlen($label)) / 2)));
                return $lineaPuntos
                    . $pad . $label . "\n"
                    . $lineaPuntos;
            };

            /*
        |--------------------------------------------------------------------------
        | CONSTRUCCIÓN DEL TICKET COMO TEXTO (antes eran llamadas a $printer->...)
        |--------------------------------------------------------------------------
        */

            $txt = '';

            if ($impresora) {

                // ── ENCABEZADO ───────────────────────────────────────────
                $txt .= "APPSYSTEM\n";
                $txt .= "NIT: 901.456.789-1\n";
                $txt .= $lineaDoble;
                $txt .= "** CIERRE DE CAJA **\n";
                $txt .= $lineaDoble;

                // ── INFO GENERAL ─────────────────────────────────────────
                $txt .= $col('Desde:',   $desde->format('d/m/Y H:i'));
                $txt .= $col('Hasta:',   $hasta->format('d/m/Y H:i'));
                $txt .= $col('Cajero:',  strtoupper($user->name));
                $txt .= $col('Caja:',    $caja->nombre ?? 'PRINCIPAL');
                $txt .= $col('Fac. ini:', $facturaInicial->numero_factura ?? 'N/A');
                $txt .= $col('Fac. fin:', $facturaFinal->numero_factura ?? 'N/A');
                $txt .= $col('Cant. facturas:', (string) $cantidadFacturas);
                $txt .= $col('Impreso:', now()->format('d/m/Y H:i:s'));

                // ── VENTAS ───────────────────────────────────────────────
                $txt .= $seccion('VENTAS');
                $txt .= $col('  Efectivo:',      '$ ' . number_format($ventas->efectivo      ?? 0, 0, ',', '.'));
                $txt .= $col('  QR:',            '$ ' . number_format($ventas->qr            ?? 0, 0, ',', '.'));
                $txt .= $col('  Tarjeta:',       '$ ' . number_format($ventas->tarjeta       ?? 0, 0, ',', '.'));
                $txt .= $col('  Transferencia:', '$ ' . number_format($ventas->transferencia ?? 0, 0, ',', '.'));
                $txt .= $lineaSimple;
                $txt .= $col('TOTAL VENTAS:', '$ ' . number_format($ventas->total_ventas ?? 0, 0, ',', '.'));

                // ── PROPINAS ─────────────────────────────────────────────
                $txt .= $seccion('PROPINAS');
                $txt .= $col('  Efectivo:',      '$ ' . number_format($propinas->efectivo      ?? 0, 0, ',', '.'));
                $txt .= $col('  QR:',            '$ ' . number_format($propinas->qr            ?? 0, 0, ',', '.'));
                $txt .= $col('  Tarjeta:',       '$ ' . number_format($propinas->tarjeta       ?? 0, 0, ',', '.'));
                $txt .= $col('  Transferencia:', '$ ' . number_format($propinas->transferencia ?? 0, 0, ',', '.'));
                $txt .= $lineaSimple;
                $txt .= $col('TOTAL PROPINAS:', '$ ' . number_format($propinas->total_propinas ?? 0, 0, ',', '.'));

                // ── MOVIMIENTOS ──────────────────────────────────────────
                $txt .= $seccion('MOV. DE CAJA');
                foreach ($movimientos as $mov) {
                    $icono = strtolower($mov->tipo) === 'entrada' ? '+ ' : '- ';
                    $txt .= $col('  ' . $icono . ucfirst($mov->concepto), '$ ' . number_format($mov->valor, 0, ',', '.'));
                }
                $txt .= $lineaSimple;
                $txt .= $col('Entradas:', '$ ' . number_format($totalEntradas, 0, ',', '.'));
                $txt .= $col('Salidas:',  '$ ' . number_format($totalSalidas,  0, ',', '.'));

                // ── ARQUEO ───────────────────────────────────────────────
                $txt .= $seccion('ARQUEO');
                $txt .= $col('  Base inicial:',  '$ ' . number_format($baseInicial,        0, ',', '.'));
                $txt .= $col('  Efectivo:',      '$ ' . number_format($arqueoEfectivo,      0, ',', '.'));
                $txt .= $col('  QR:',            '$ ' . number_format($arqueoQr,            0, ',', '.'));
                $txt .= $col('  Tarjeta:',       '$ ' . number_format($arqueoTarjeta,       0, ',', '.'));
                $txt .= $col('  Transferencia:', '$ ' . number_format($arqueoTransferencia, 0, ',', '.'));

                // ── CONTEO FÍSICO ────────────────────────────────────────
                $txt .= $seccion('CONTEO FISICO');
                $denominaciones = [
                    ['Moneda $100',      intval($request->m100 ?? 0), 100],
                    ['Moneda $200',      intval($request->m200 ?? 0), 200],
                    ['Moneda $500',      intval($request->m500 ?? 0), 500],
                    ['Moneda $1.000',    intval($request->m1000 ?? 0), 1000],
                    ['Billete $2.000',   intval($request->b2000 ?? 0), 2000],
                    ['Billete $5.000',   intval($request->b5000 ?? 0), 5000],
                    ['Billete $10.000',  intval($request->b10000 ?? 0), 10000],
                    ['Billete $20.000',  intval($request->b20000 ?? 0), 20000],
                    ['Billete $50.000',  intval($request->b50000 ?? 0), 50000],
                    ['Billete $100.000', intval($request->b100000 ?? 0), 100000],
                ];
                foreach ($denominaciones as [$label, $cantidad, $valor]) {
                    $detalle = $label . ' (' . $cantidad . ' x)';
                    $subtotal = '$ ' . number_format($cantidad * $valor, 0, ',', '.');
                    $txt .= $col('  ' . $detalle, $subtotal);
                }
                $txt .= $lineaSimple;
                $txt .= $col('TOTAL FISICO:', '$ ' . number_format($totalFisico, 0, ',', '.'));

                // ── RESULTADO FINAL ──────────────────────────────────────
                $txt .= $lineaDoble;
                $txt .= $col('Efectivo esperado:', '$ ' . number_format($efectivoEsperado, 0, ',', '.'));
                $txt .= $col('Total fisico:',      '$ ' . number_format($totalFisico,      0, ',', '.'));
                $txt .= $lineaSimple;

                if ($diferencia == 0) {
                    $txt .= "CAJA CUADRADA\n";
                } elseif ($diferencia > 0) {
                    $txt .= "SOBRANTE\n";
                    $txt .= "+ $ " . number_format($diferencia, 0, ',', '.') . "\n";
                } else {
                    $txt .= "!!! FALTANTE !!!\n";
                    $txt .= "- $ " . number_format(abs($diferencia), 0, ',', '.') . "\n";
                }

                $txt .= $lineaDoble;
                $txt .= "-- Documento interno --\n";
                $txt .= "No valido como factura\n";
                $txt .= "\n\n\n\n";

                // ✅ ENCOLAR EN VEZ DE IMPRIMIR DIRECTO
                // Antes aquí se abría NetworkPrintConnector($impresora->ip, ...).
                // Ahora se guarda el ticket en texto y el agente local del negocio
                // lo recoge y lo manda de verdad a la impresora térmica.
                ComandaPendiente::create([
                    'tipo'         => 'cierre_caja',
                    'impresora_id' => $impresora->id,
                    'contenido'    => $txt,
                    'estado'       => 'pendiente',
                ]);
            }

            CierreCaja::create([
                'caja_id' => $caja->id, 'user_id' => $user->id,
                'fecha_inicio' => $desde, 'fecha_fin' => $hasta,
                'factura_inicial' => $facturaInicial->numero_factura ?? null,
                'factura_final' => $facturaFinal->numero_factura ?? null,
                'cantidad_facturas' => $cantidadFacturas, 'base_inicial' => $baseInicial,
                'efectivo_esperado' => $efectivoEsperado, 'total_fisico' => $totalFisico,
                'diferencia' => $diferencia,
                'denominaciones' => $request->only(['m100','m200','m500','m1000','b2000','b5000','b10000','b20000','b50000','b100000']),
                'resumen' => [
                    'ventas' => [
                        'bruta' => (float) ($ventas->total_ventas ?? 0),
                        'efectivo' => (float) ($ventas->efectivo ?? 0),
                        'qr' => (float) ($ventas->qr ?? 0),
                        'tarjeta' => (float) ($ventas->tarjeta ?? 0),
                        'transferencia' => (float) ($ventas->transferencia ?? 0),
                    ],
                    'propinas' => [
                        'total' => (float) ($propinas->total_propinas ?? 0),
                        'efectivo' => (float) ($propinas->efectivo ?? 0),
                        'qr' => (float) ($propinas->qr ?? 0),
                        'tarjeta' => (float) ($propinas->tarjeta ?? 0),
                        'transferencia' => (float) ($propinas->transferencia ?? 0),
                    ],
                    'movimientos' => [
                        'entradas' => (float) $totalEntradas,
                        'salidas' => (float) $totalSalidas,
                    ],
                ],
            ]);

            return response()->json([
                'success'       => true,
                'estado_cuadre' => $diferencia == 0
                    ? 'CUADRADO'
                    : ($diferencia > 0 ? 'SOBRANTE' : 'FALTANTE'),
                'diferencia' => number_format(abs($diferencia), 0, ',', '.')
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage() . ' linea ' . $e->getLine()
            ], 500);
        }
    }
}
