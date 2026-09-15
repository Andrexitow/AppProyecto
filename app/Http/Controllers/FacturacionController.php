<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\CierreCaja;
use App\Models\ComandaPendiente;
use App\Models\ConceptoCaja;
use App\Models\Consumo;
use App\Models\DetallePedido;
use App\Models\Factura;
use App\Models\Impresora;
use App\Models\Mesa;
use App\Models\MovimientoCaja;
use App\Models\Pedido;
use App\Models\Prefijo;
use App\Models\Producto;
use App\Models\Tercero;
use App\Models\User;
use App\Models\Zona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Services\LegacyDocumentSyncService;
use App\Services\PrintService;
use App\Services\ContabilidadService;
use App\DataTransferObjects\ContabilidadData;
use App\Services\FacturacionContableService;
use App\Services\FacturacionElectronicaService;
use App\Services\AuditoriaService;
use Carbon\Carbon;

class FacturacionController extends Controller
{
    /**
     * Plazo de crédito que se usa cuando el cliente no tiene uno propio
     * configurado (Tercero::dias_credito). Antes esto era un "30" oculto
     * solo dentro de FactusFacturaElectronicaProvider; ahora es la política
     * general explícita, y la factura misma guarda el vencimiento ya
     * calculado (fecha_vencimiento) en vez de que cada consumidor lo
     * recalcule por su cuenta.
     */
    public const DIAS_CREDITO_POR_DEFECTO = 30;

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

        $this->liberarMesasVencidas();

        // 4. Traer datos para la vista
        $mesas = $this->mesasVisiblesPara($user)->with(['zona', 'pedidos' => function ($query) {
            $query->where('estado', 'pendiente')->with('user');
        }])->get();

        $zonas    = $this->zonasVisiblesPara($user);
        // 'activo' es una columna heredada que nadie actualiza: el toggle
        // real de ProductoController usa 'inactivo'. Filtrar por 'activo'
        // (siempre 1) dejaba ver en el POS productos que sí se habían
        // desactivado desde el catálogo.
        $productos = Producto::where('inactivo', 0)->orderBy('categoria')->get();
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

    /**
     * Un mesero/cajero de Discoteca no debe ver ni poder comandar las
     * mesas del Restaurante (y viceversa) — antes el POS traía TODAS las
     * mesas de TODAS las zonas sin importar a qué caja/bodega pertenecía
     * quien tenía la sesión abierta. Un Administrador sigue viendo todo.
     *
     * El "venue" de cada zona lo da zonas.bodega_id (asignado desde
     * Mesas y Zonas); el del usuario, la bodega de su caja. Una zona sin
     * bodega asignada todavía, o un usuario sin caja, se deja visible
     * para todos — mejor mostrar de más en ese caso que dejar el POS
     * vacío por una configuración a medias.
     */
    private function mesasVisiblesPara($user)
    {
        $query = Mesa::query();

        if ($user->rol->nombre === 'Administrador') {
            return $query;
        }

        $bodegaId = $user->caja?->bodega_id;
        if (!$bodegaId) {
            return $query;
        }

        return $query->whereHas('zona', fn ($z) => $z->whereNull('bodega_id')->orWhere('bodega_id', $bodegaId));
    }

    private function zonasVisiblesPara($user)
    {
        if ($user->rol->nombre === 'Administrador') {
            return Zona::all();
        }

        $bodegaId = $user->caja?->bodega_id;
        if (!$bodegaId) {
            return Zona::all();
        }

        return Zona::whereNull('bodega_id')->orWhere('bodega_id', $bodegaId)->get();
    }

    /**
     * Revierte a 'disponible' las mesas 'seleccionada' hace más de 2
     * minutos — pero solo si de verdad no tienen un pedido pendiente detrás
     * (antes esto se asumía por construcción; ahora queda explícito, para
     * que no importe si algún día otro flujo deja una mesa 'seleccionada'
     * con un pedido real sin querer).
     */
    private function liberarMesasVencidas(): void
    {
        Mesa::where('estado', 'seleccionada')
            ->where('updated_at', '<', now()->subMinutes(2))
            ->whereDoesntHave('pedidos', fn ($query) => $query->where('estado', 'pendiente'))
            ->update(['estado' => 'disponible', 'bloqueada_por' => null, 'bloqueada_at' => null]);
    }

    /**
     * Para un renglón de venta (DetallePedido), determina qué producto(s)
     * hay que descontar del inventario y en qué cantidad. Normalmente es
     * solo el mismo producto vendido, pero hay dos formas de que sea otra
     * cosa:
     *
     *  - "Ensamblado" (ej. Cubetazo Poker, ver Producto::es_ensamblado): se
     *    descuenta su producto base (Poker) multiplicado por el factor de
     *    consumo — un único insumo, cantidad fija.
     *  - "Acompañamiento" (ej. Cubetazo Mix, ver Producto::acompanamiento_grupo_id):
     *    el mesero repartió libremente el máximo del grupo entre varias
     *    opciones al comandar (ver DetallePedidoAcompanamiento) — puede ser
     *    varios insumos distintos, cada uno con su propia cantidad.
     *
     * En ambos casos el producto vendido en sí NO carga su propio
     * inventario. Devuelve una lista de renglones
     * [Producto $productoADescontar, float $cantidad, bool $esDerivado,
     * ?int $bodegaOrigenId] (vacía si esta línea no debe afectar
     * inventario).
     *
     * $esDerivado = true significa que la cantidad viene de una RECETA
     * (ensamblado/acompañamiento), no de una venta directa de ese producto
     * — eso es lo que permite, más adelante en cerrarMesa(), que un
     * faltante de stock quede como "consumo pendiente" en vez de bloquear
     * toda la factura (el dinero ya entró). Una venta DIRECTA nunca se
     * difiere: si no hay stock, se rechaza como siempre.
     *
     * $bodegaOrigenId es la bodega donde vive de verdad el insumo si es
     * distinta a la de la caja que vendió (ver Producto::bodega_origen_id
     * — ej. la carne de una hamburguesa vendida en Discoteca vive en la
     * bodega de Cocina). Null = usar la bodega de la caja, como siempre.
     */
    private function resolverDescuentosInventario($detalle): array
    {
        $producto = $detalle->producto;
        if (!$producto) {
            return [];
        }

        if ($producto->acompanamiento_grupo_id) {
            $pares = [];
            foreach ($detalle->acompanamientos as $reparto) {
                $elegido = $reparto->producto;
                if ($elegido && $elegido->afecta_inventario) {
                    $pares[] = [$elegido, (float) $reparto->cantidad, true, $elegido->bodega_origen_id];
                }
            }
            return $pares;
        }

        if ($producto->es_ensamblado && $producto->producto_base_id) {
            $base = $producto->productoBase;
            if (!$base || !$base->afecta_inventario) {
                return [];
            }

            $factor = (float) ($producto->factor_consumo ?? 1);

            return [[$base, (float) $detalle->cantidad * $factor, true, $producto->bodega_origen_id]];
        }

        if (!$producto->afecta_inventario) {
            return [];
        }

        return [[$producto, (float) $detalle->cantidad, false, null]];
    }

    public function bloquearMesa($id)
    {
        $user = Auth::user();
        $rolNombre = $user?->rol?->nombre;

        if (!in_array($rolNombre, ['Mesero', 'Administrador', 'Cajero'], true)) {
            return response()->json(['status' => 'error', 'message' => 'No tienes acceso a la zona de facturación.'], 403);
        }

        return DB::transaction(function () use ($id, $user) {
            // lockForUpdate() bloquea la fila hasta que esta transacción
            // termine: si dos meseros piden la misma mesa al mismo tiempo,
            // el segundo espera, relee el estado ya actualizado por el
            // primero, y recibe el 403 de "ya no está disponible" — antes
            // ambos podían leer 'disponible' a la vez y tomarla los dos.
            $mesa = Mesa::where('id', $id)->lockForUpdate()->first();

            if (!$mesa) {
                return response()->json(['status' => 'error', 'message' => 'Mesa no encontrada'], 404);
            }

            // No basta con ocultarla en el listado (mesasVisiblesPara): el
            // id de la mesa es adivinable/consultable, así que hay que
            // rechazar aquí también que un mesero/cajero de otra sede la
            // tome directamente por URL.
            if (!$this->mesasVisiblesPara($user)->where('id', $mesa->id)->exists()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Esta mesa pertenece a otra sede/zona.',
                ], 403);
            }

            if ($mesa->estado !== 'disponible') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'La mesa ya no está disponible'
                ], 403);
            }

            $mesa->update([
                'estado' => 'seleccionada',
                'bloqueada_por' => Auth::id(),
                'bloqueada_at' => now(),
            ]);

            return response()->json(['status' => 'success']);
        });
    }

    public function liberarMesa($id, Request $request)
    {
        try {
            $user = Auth::user();
            $rolNombre = $user?->rol?->nombre;

            if (!in_array($rolNombre, ['Mesero', 'Administrador', 'Cajero'], true)) {
                return response()->json(['status' => 'error', 'message' => 'No tienes acceso a la zona de facturación.'], 403);
            }

            $mesa = Mesa::findOrFail($id);
            $pedido = Pedido::where('mesa_id', $id)->where('estado', 'pendiente')->first();

            if ($pedido) {
                // Solo quien abrió el pedido (o un Administrador) puede
                // liberar la mesa — antes cualquier autenticado podía
                // cancelar el pedido de otro mesero/cajero.
                if ($pedido->user_id !== $user->id && $rolNombre !== 'Administrador') {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Esta mesa la tiene abierta otro usuario; no puedes liberarla tú.',
                    ], 403);
                }

                // Si la mesa ya está "ocupada" significa que al menos una
                // comanda salió hacia cocina/barra — borrar el pedido acá
                // perdería ese pedido ya en preparación. Antes se borraba
                // siempre, sin mirar esto. Un Administrador puede forzarlo
                // (p. ej. para corregir un error), quedando auditado.
                if ($mesa->estado === 'ocupada' && $rolNombre !== 'Administrador') {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Esta mesa ya tiene productos enviados a cocina/barra; no se puede cancelar así. Ciérrala como venta o pide a un administrador que la libere.',
                    ], 422);
                }

                $forzado = $mesa->estado === 'ocupada';

                $pedido->detalles()->delete();
                $pedido->delete();

                if ($forzado) {
                    AuditoriaService::registrar(
                        $user,
                        'Facturación',
                        'Mesa liberada forzosamente',
                        "Mesa {$mesa->numero} tenía un pedido con productos ya enviados a cocina y fue liberada/borrada por un administrador.",
                        $request,
                        'Pedido #' . $pedido->id
                    );
                }
            } elseif ($mesa->bloqueada_por && $mesa->bloqueada_por !== $user->id && $rolNombre !== 'Administrador') {
                // Sin pedido todavía (solo la tomó para empezar a armar el
                // pedido): antes cualquiera podía liberar esta reserva
                // ajena, aunque no hubiera nada más que borrar.
                return response()->json([
                    'status' => 'error',
                    'message' => 'Esta mesa la tomó otro usuario; no puedes liberarla tú.',
                ], 403);
            }

            $mesa->estado = 'disponible';
            $mesa->bloqueada_por = null;
            $mesa->bloqueada_at = null;
            $mesa->save();

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
        $this->liberarMesasVencidas();

        // Mismo filtro por venue que index() — si no, el polling automático
        // de esta pantalla (cada pocos segundos) le devolvía a un mesero de
        // Discoteca las mesas del Restaurante que index() ya le escondía.
        $mesas = $this->mesasVisiblesPara(Auth::user())->with(['zona', 'pedidos' => function ($query) {
            $query->where('estado', 'pendiente')->with('user');
        }])->get();

        return view('facturacion.partials.mesas_grid', compact('mesas'));
    }

    public function guardarPedido(Request $request, PrintService $printService)
    {
        $request->validate([
            'mesa_id' => 'required|exists:mesas,id',
            'cliente_id' => 'nullable|exists:terceros,id',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|integer',
            'items.*.cantidad' => 'required|integer|min:1',
            'items.*.observacion' => 'nullable|string|max:255',
            // Reparto de acompañamiento (ej. Cubetazo Mix: 4 Poker + 3
            // Águila + 3 Costeña) — se valida a fondo más abajo, contra las
            // opciones y el máximo reales del grupo.
            'items.*.acompanamiento' => 'nullable|array',
            'items.*.acompanamiento.*.producto_id' => 'required_with:items.*.acompanamiento|integer',
            'items.*.acompanamiento.*.cantidad' => 'required_with:items.*.acompanamiento|integer|min:1',
        ]);

        // El precio y la disponibilidad del producto se resuelven aquí, en
        // el servidor — antes se guardaba item.precio tal cual llegaba del
        // navegador, así que un usuario autenticado podía manipular el
        // payload y facturar cualquier precio que quisiera.
        $productoIds = collect($request->items)->pluck('id')->unique();
        $productos = Producto::whereIn('id', $productoIds)->where('inactivo', 0)->with('acompanamientoGrupo.opciones')->get()->keyBy('id');

        $faltantes = $productoIds->diff($productos->keys());
        if ($faltantes->isNotEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Uno o más productos ya no están disponibles. Refresca el catálogo e intenta de nuevo.',
            ], 422);
        }

        // Un producto con acompañamiento (ej. Cubetazo Mix) trae consigo el
        // reparto que el mesero eligió en el modal — se valida acá, en el
        // servidor, contra el máximo y las opciones REALES del grupo (nunca
        // se confía en lo que mande el navegador).
        foreach ($request->items as $item) {
            $producto = $productos->get((int) $item['id']);
            if (!$producto->acompanamiento_grupo_id) {
                continue;
            }

            if ((int) $item['cantidad'] !== 1) {
                return response()->json([
                    'status' => 'error',
                    'message' => "\"{$producto->descripcion}\" es un producto con acompañamiento: agrégalo como líneas separadas, no aumentes su cantidad.",
                ], 422);
            }

            $grupo = $producto->acompanamientoGrupo;
            $opcionesValidas = $grupo->opciones->pluck('producto_id')->all();
            $reparto = collect($item['acompanamiento'] ?? []);

            if ($reparto->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Falta el reparto de acompañamiento para \"{$producto->descripcion}\".",
                ], 422);
            }

            foreach ($reparto as $linea) {
                if (!in_array((int) ($linea['producto_id'] ?? 0), $opcionesValidas, true)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => "Uno de los productos elegidos ya no pertenece al grupo \"{$grupo->descripcion}\". Refresca e intenta de nuevo.",
                    ], 422);
                }
            }

            $totalRepartido = $reparto->sum(fn ($l) => (int) ($l['cantidad'] ?? 0));
            if ($totalRepartido < 1 || $totalRepartido > $grupo->cantidad_maxima) {
                return response()->json([
                    'status' => 'error',
                    'message' => "El reparto de \"{$producto->descripcion}\" debe sumar entre 1 y {$grupo->cantidad_maxima} unidades (llegó a {$totalRepartido}).",
                ], 422);
            }
        }

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

            // 2. Guardamos cada item, con el precio oficial del producto
            // (nunca el que venga en el request)
            foreach ($request->items as $item) {
                $producto = $productos->get((int) $item['id']);
                $precioOficial = (float) $producto->precio;
                $subtotalItem = round($precioOficial * $item['cantidad'], 2);

                $detalle = DetallePedido::create([
                    'pedido_id' => $pedido->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $precioOficial,
                    'subtotal' => $subtotalItem,
                    'observacion' => $item['observacion'] ?? null,
                ]);

                if ($producto->acompanamiento_grupo_id) {
                    foreach ($item['acompanamiento'] as $linea) {
                        $detalle->acompanamientos()->create([
                            'producto_id' => $linea['producto_id'],
                            'cantidad' => $linea['cantidad'],
                        ]);
                    }
                }

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

            // Punto de impresión según la bodega de la caja del mesero.
            // Antes se comparaba caja_id == 2 / bodega_id == 2 a lo bruto,
            // así que crear una caja o bodega nueva (o reordenar IDs) lo
            // rompía en silencio. Ahora cada bodega declara su propio punto
            // (columna bodegas.punto_impresion).
            $puntoActual = 'RESTAURANTE'; // Punto por defecto para administradores o si no tienen caja

            if ($user->caja_id) {
                $caja = Caja::with('bodega')->find($user->caja_id);
                $puntoActual = $caja?->bodega?->punto_impresion ?? 'RESTAURANTE';
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

        // Los ítems cancelados (ver eliminarItemPedido) ya no se borran de
        // la base de datos, así que hay que excluirlos aquí explícitamente
        // para que no vuelvan a aparecer en el ticket del mesero/cajero.
        $items = $pedido->detalles->whereNull('cancelado_at')->map(function ($detalle) {
            return [
                'producto_id'     => $detalle->producto_id,
                'nombre_producto' => $detalle->producto->descripcion,
                'precio'          => $detalle->precio_unitario,
                'cantidad'        => $detalle->cantidad,
                'observacion'     => $detalle->observacion ?? '',
            ];
        })->values();

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
        $request->validate([
            'mesa_id' => ['required', 'exists:mesas,id'],
            'producto_id' => ['required', 'exists:productos,id'],
            'clave' => ['required', 'string', 'min:4', 'max:50'],
        ]);

        $autorizador = $this->resolverAutorizadorAnulacion((string) $request->input('clave'));
        if (!$autorizador) {
            return response()->json(['status' => 'error', 'message' => 'Clave incorrecta'], 403);
        }

        Log::info('Eliminación de ítem de pedido autorizada', [
            'mesa_id' => $request->mesa_id,
            'producto_id' => $request->producto_id,
            'autorizado_por' => $autorizador->id,
            'solicitado_por' => Auth::id(),
        ]);

        try {
            $pedido = Pedido::where('mesa_id', $request->mesa_id)
                ->where('estado', 'pendiente')
                ->first();

            if (!$pedido) {
                return response()->json(['status' => 'error', 'message' => 'Pedido no encontrado'], 404);
            }

            $item = $pedido->detalles()
                ->where('producto_id', $request->producto_id)
                ->whereNull('cancelado_at')
                ->first();

            if (!$item) {
                return response()->json(['status' => 'error', 'message' => 'Item no encontrado'], 404);
            }

            // ✅ ENCOLAR COMANDA DE ANULACIÓN ANTES DE CANCELAR (ticket físico
            // para cocina, por si el cocinero ya empezó a preparar el plato).
            try {
                // 🔄 Cambiado a plural 'grupoMenu.impresoras'
                $producto = $item->producto()->with('grupoMenu.impresoras')->first();
                $mesa     = Mesa::find($request->mesa_id);

                // Verificamos si existen impresoras asignadas en la relación muchos a muchos.
                // unique('id'): el mismo grupo puede tener varias filas hacia
                // la MISMA impresora (una por punto RESTAURANTE/DISCOTECA/
                // KARAOKE) — sin deduplicar, esto imprimía y mostraba la
                // misma anulación 2 o 3 veces ("se multiplica").
                if ($producto && $producto->grupoMenu && $producto->grupoMenu->impresoras->isNotEmpty()) {
                    foreach ($producto->grupoMenu->impresoras->where('activa', true)->unique('id') as $impresora) {
                        $printService->imprimirComandaAnulacion(
                            $mesa,
                            $producto,
                            $item->cantidad,
                            $item->observacion ?? '',
                            $impresora,
                            $impresora->nombre,
                            $pedido->id,
                            $autorizador->name
                        );
                    }
                }
            } catch (\Exception $e) {
                Log::error("Error encolando anulación: " . $e->getMessage());
                // No frenamos el proceso si falla la impresora
            }

            // Restar del total solo lo de este item
            $pedido->decrement('total', $item->subtotal);

            // No se borra: se marca como cancelado para que Cocina siga
            // mostrándolo (tachado/deshabilitado) dentro de su misma
            // comanda, indicando quién lo canceló — ver
            // CocinaController::comandas().
            $item->update([
                'cancelado_at' => now(),
                'cancelado_por' => $autorizador->id,
            ]);

            // Solo borrar el pedido si no quedan items activos (todos
            // cancelados o ya no había más).
            $restantes = $pedido->detalles()->whereNull('cancelado_at')->count();
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

    /**
     * Acepta la clave de anulación del usuario que está operando o de un
     * administrador activo. Así un cajero puede pedir la superclave al
     * administrador sin compartir la contraseña de inicio de sesión.
     */
    private function resolverAutorizadorAnulacion(string $clave): ?User
    {
        $actual = Auth::user();

        $candidatos = User::query()
            ->where('activo', true)
            ->where(function ($query) use ($actual) {
                $query->whereHas('rol', fn ($rol) => $rol->where('nombre', 'Administrador'));

                if ($actual) {
                    $query->orWhere('id', $actual->id);
                }
            })
            ->whereNotNull('clave_anulacion')
            ->get();

        return $candidatos->first(
            fn (User $usuario) => Hash::check($clave, $usuario->clave_anulacion)
        );
    }

    /**
     * Registra un ingreso o salida de caja. Las salidas exigen un concepto
     * del catálogo y el tercero que recibe el dinero, y encolan un
     * comprobante imprimible para que esa persona lo firme.
     */
    public function guardarMovimiento(Request $request, PrintService $printService)
    {
        $user = Auth::user();

        $datos = $request->validate([
            'tipo' => 'required|in:ingreso,salida',
            'monto' => 'required|numeric|min:0.01',
            'concepto_caja_id' => 'required|exists:conceptos_caja,id',
            'tercero_id' => 'nullable|exists:terceros,id|required_if:tipo,salida',
            'nota' => 'nullable|string|max:255',
        ], [
            'concepto_caja_id.required' => 'Selecciona el concepto del movimiento.',
            'tercero_id.required_if' => 'Selecciona quién recibe el dinero.',
        ]);

        $concepto = ConceptoCaja::find($datos['concepto_caja_id']);
        if ($concepto->tipo !== 'ambos' && $concepto->tipo !== $datos['tipo']) {
            return response()->json([
                'success' => false,
                'message' => 'Ese concepto no aplica para este tipo de movimiento.',
            ], 422);
        }

        $nota = $datos['nota'] ?? null;
        $etiquetaConcepto = trim($concepto->nombre . ($nota ? ' — ' . $nota : ''));

        $movimiento = MovimientoCaja::create([
            'tipo' => $datos['tipo'] === 'salida' ? 'salida' : 'entrada',
            'concepto' => $etiquetaConcepto,
            'concepto_caja_id' => $concepto->id,
            'tercero_id' => $datos['tercero_id'] ?? null,
            'valor' => $datos['monto'],
            'user_id' => $user->id,
        ]);

        $impresionEncolada = false;
        try {
            $caja = Caja::find($user->caja_id);
            $impresora = $caja?->impresora_id ? Impresora::find($caja->impresora_id) : null;

            if ($impresora) {
                $movimiento->load('conceptoCaja', 'tercero');
                $printService->imprimirComprobanteMovimiento($movimiento, $impresora, $user);
                $impresionEncolada = true;
            }
        } catch (\Exception $e) {
            Log::error('Error encolando comprobante de movimiento de caja: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'data' => $movimiento,
            'comprobante_impreso' => $impresionEncolada,
            'message' => $datos['tipo'] === 'salida' ? 'Salida de caja registrada.' : 'Ingreso de caja registrado.',
        ]);
    }

    public function cerrarMesa(
        Request $request,
        PrintService $printService,
        FacturacionContableService $facturacionContableService,
        FacturacionElectronicaService $facturacionElectronicaService
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
            // 'mixto' exige el desglose real de formas de pago: antes se
            // aceptaba sin pedir este dato y se contabilizaba todo como Caja.
            // No incluye 'credito': una venta mixta con una porción a crédito
            // necesitaría integrarse con cartera/cuentas por cobrar, que es
            // un frente aparte todavía no construido para este caso.
            'pagos'                 => 'required_if:metodo_pago,mixto|array|min:2',
            'pagos.*.metodo_pago'   => 'required_with:pagos|in:efectivo,tarjeta,transferencia,nequi,daviplata',
            'pagos.*.valor'         => 'required_with:pagos|numeric|gt:0',
            'pagos.*.referencia'    => 'nullable|string',
        ]);

        if ($request->metodo_pago === 'mixto') {
            $sumaPagos = round(collect($request->pagos)->sum('valor'), 2);
            if (abs($sumaPagos - round((float) $request->total, 2)) > 0.01) {
                return response()->json([
                    'status'  => 'error',
                    'message' => "La suma de las formas de pago (\${$sumaPagos}) debe ser igual al total de la factura.",
                ], 422);
            }
        }

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
            ->with('detalles.producto.integracionContable.procesoContable', 'detalles.producto.productoBase', 'detalles.acompanamientos.producto')
            ->get();

        if ($pedidos->isEmpty()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No hay pedidos pendientes para facturar en esta mesa.'
            ], 422);
        }

        try {
            DB::beginTransaction();

            // whereNull('cancelado_at'): un ítem cancelado (eliminarItemPedido)
            // ya no se borra de la base de datos, solo se marca — así que
            // hay que excluirlo aquí explícitamente para que no se facture
            // ni descuente inventario de algo que el cliente no recibió.
            $todosLosDetalles = $pedidos->flatMap->detalles->whereNull('cancelado_at')->values();
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

            // 1. Toda venta debe poder contabilizarse. Antes, un producto sin
            // integración contable (o con una integración sin proceso
            // contable válido) no bloqueaba nada: contabilizar() lo saltaba
            // con un warning en el log y esa parte de la venta quedaba
            // fuera de la contabilidad sin que nadie se enterara en el
            // momento — el cajero veía "venta exitosa" igual.
            $detalleSinIntegracion = $todosLosDetalles->first(
                fn ($detalle) => !$detalle->producto?->integracionContable || !$detalle->producto->integracionContable->procesoContable
            );

            if ($detalleSinIntegracion) {
                DB::rollBack();

                return response()->json([
                    'status'  => 'error',
                    'message' => "El producto '{$detalleSinIntegracion->producto->descripcion}' no tiene una integración contable configurada correctamente. Pide a un administrador que la asigne en Productos antes de facturarlo.",
                ], 422);
            }

            // 2. Validar stock — agrupado por PRODUCTO A DESCONTAR y con
            // lockForUpdate(). Antes se validaba línea por línea: si el
            // mismo producto aparecía en dos líneas del pedido (agregado en
            // momentos distintos), cada línea comparaba su propia cantidad
            // contra el stock SIN restar lo que la otra línea ya iba a
            // consumir — con stock=1 y dos líneas de 1, ambas "pasaban" y
            // luego se descontaban las dos (stock -1). Agrupar y sumar antes
            // de comparar cierra eso. lockForUpdate() además bloquea la fila
            // hasta que esta transacción termine, para que dos cajeros no
            // lean el mismo stock "suficiente" a la vez.
            //
            // "Producto a descontar" no siempre es el producto vendido: si
            // es un ensamblado (ej. Cubetazo Poker) o tiene acompañamiento
            // (ej. Cubetazo Mix), lo que de verdad hay que descontar es su
            // insumo(s) real(es) — ver resolverDescuentosInventario(). Y no
            // siempre es de la bodega de esta caja: un insumo puede
            // declarar su propia bodega_origen_id (ej. la carne vive en
            // Cocina aunque la hamburguesa se venda desde Discoteca).
            //
            // Se agrupa por [producto, bodega] en vez de solo producto:
            // el mismo insumo podría en teoría resolverse contra bodegas
            // distintas según de dónde venga cada línea.
            $cantidadPorClave = [];
            $productoEfectivoPorClave = [];
            $bodegaPorClave = [];
            $esPuraDerivadaPorClave = [];
            foreach ($todosLosDetalles as $detalle) {
                foreach ($this->resolverDescuentosInventario($detalle) as [$productoEfectivo, $cantidad, $esDerivado, $bodegaOrigenId]) {
                    $bodegaId = $bodegaOrigenId ?? $caja->bodega_id;
                    $clave = $productoEfectivo->id . ':' . $bodegaId;

                    $productoEfectivoPorClave[$clave] = $productoEfectivo;
                    $bodegaPorClave[$clave] = $bodegaId;
                    $cantidadPorClave[$clave] = ($cantidadPorClave[$clave] ?? 0) + $cantidad;
                    // Solo se puede diferir si TODAS las líneas que aportan a
                    // esta clave vienen de una receta — si alguna es una
                    // venta directa de ese producto, no se difiere nunca.
                    $esPuraDerivadaPorClave[$clave] = ($esPuraDerivadaPorClave[$clave] ?? true) && $esDerivado;
                }
            }

            // Si algún insumo DERIVADO (nunca uno vendido directamente) no
            // alcanza, la factura de todas formas debe pasar — el dinero ya
            // entró. En ese caso el Consumo completo de esta venta queda
            // "no_registrado": NINGÚN insumo derivado se descuenta todavía
            // (ni siquiera los que sí alcanzaban), hasta que un
            // administrador ajuste el inventario o elimine la línea
            // problemática y lo registre manualmente (ver ConsumoController).
            $consumoPendiente = false;

            foreach ($cantidadPorClave as $clave => $cantidadTotal) {
                $producto = $productoEfectivoPorClave[$clave];
                $bodegaId = $bodegaPorClave[$clave];

                $inventario = DB::table('inventarios')
                    ->where('producto_id', $producto->id)
                    ->where('bodega_id', $bodegaId)
                    ->lockForUpdate()
                    ->first();

                if (!$inventario || $inventario->stock < $cantidadTotal) {
                    if (!$esPuraDerivadaPorClave[$clave]) {
                        throw new \Exception("Stock insuficiente para: {$producto->descripcion}");
                    }

                    $consumoPendiente = true;
                }
            }

            // 3. Totales fiscales (una sola fuente de verdad: el service)
            $totales = $facturacionContableService->calcularTotales($todosLosDetalles);

            // El total que llega del navegador debe coincidir con lo calculado
            // server-side (productos + IVA + propina) — sin este chequeo, un
            // valor manipulado o desincronizado del lado del cliente se
            // guardaba tal cual, sin comparar contra nada.
            $totalEsperado = round($totales['base'] + $totales['iva'] + (float) ($request->propina ?? 0), 2);
            if (abs((float) $request->total - $totalEsperado) > 1) {
                DB::rollBack();

                return response()->json([
                    'status'  => 'error',
                    'message' => "El total recibido (\${$request->total}) no coincide con el calculado a partir del pedido (\${$totalEsperado}). Refresca la mesa e intenta de nuevo.",
                ], 422);
            }

            // 4. Numeración de factura — consecutivo centralizado por
            // PREFIJO, no por caja: dos cajas pueden compartir el mismo
            // prefijo (ya pasa hoy en esta base de datos), así que "última
            // factura DE ESTA CAJA + 1" podía generar un número que otra
            // caja con el mismo prefijo ya hubiera usado, sin que hiciera
            // falta ninguna concurrencia real. Prefijo::siguienteNumero()
            // usa lockForUpdate(), así que también cierra la carrera entre
            // dos cajeros facturando al mismo tiempo con el mismo prefijo.
            $nuevoNumero = Prefijo::siguienteNumero($caja->prefijo);

            $numeroFactura = $caja->prefijo . '-' . str_pad($nuevoNumero, 5, '0', STR_PAD_LEFT);

            // El vencimiento se calcula UNA VEZ, aquí, y queda fotografiado
            // en la factura — si el plazo del cliente cambia después, esta
            // factura ya emitida no se ve afectada. dias_credito del
            // cliente manda; si no está configurado, se usa la política
            // general (antes esto era un "30" fijo solo dentro del
            // adaptador de Factus, invisible para el resto del sistema).
            $fechaVencimiento = null;
            if ($request->metodo_pago === 'credito') {
                $diasCredito = Tercero::find($clienteId)?->dias_credito ?? self::DIAS_CREDITO_POR_DEFECTO;
                $fechaVencimiento = now()->addDays($diasCredito)->toDateString();
            }

            // 5. Crear factura con subtotal/impuestos correctos
            $factura = Factura::create([
                'numero_factura'    => $numeroFactura,
                'mesa_id'           => $request->mesa_id,
                'user_id'           => auth()->id(),
                'cliente_id'        => $clienteId,
                'caja_id'           => $caja->id,
                'subtotal'          => $totales['base'],
                'impuestos'         => $totales['iva'],
                'propina'           => $request->propina ?? 0,
                'total'             => $request->total,
                'metodo_pago'       => $request->metodo_pago,
                'tipo_tarjeta'      => $request->tipo_tarjeta,
                'banco_destino'     => $request->banco_destino,
                'referencia_pago'   => $request->referencia,
                'estado'            => 'pagada',
                'estado_pago'       => $request->metodo_pago === 'credito' ? 'pendiente' : 'pagada',
                'total_pagado'      => $request->metodo_pago === 'credito' ? 0 : $request->total,
                'saldo_pendiente'   => $request->metodo_pago === 'credito' ? $request->total : 0,
                'fecha_vencimiento' => $fechaVencimiento,
            ]);

            if ($request->metodo_pago === 'mixto') {
                foreach ($request->pagos as $pago) {
                    $factura->pagos()->create([
                        'metodo_pago' => $pago['metodo_pago'],
                        'valor'       => $pago['valor'],
                        'referencia'  => $pago['referencia'] ?? null,
                    ]);
                }
            }

            // 6. Detalles + inventario (+ consumo de materia prima si el
            // producto vendido es un ensamblado o tiene acompañamiento,
            // ej. Cubetazo Poker / Cubetazo Mix).
            $lineasConsumo = [];
            foreach ($todosLosDetalles as $detalle) {
                $factura->detalles()->create([
                    'producto_id'     => $detalle->producto_id,
                    'cantidad'        => $detalle->cantidad,
                    'precio_unitario' => $detalle->precio_unitario,
                    'subtotal'        => $detalle->subtotal,
                ]);

                $producto = $detalle->producto;
                $descuentos = $this->resolverDescuentosInventario($detalle);
                $esConsumoDeInsumo = $producto && ($producto->es_ensamblado || $producto->acompanamiento_grupo_id);

                foreach ($descuentos as [$productoEfectivo, $cantidad, $esDerivado, $bodegaOrigenId]) {
                    $bodegaId = $bodegaOrigenId ?? $caja->bodega_id;

                    // Si esta factura quedó con algún faltante de insumo
                    // derivado, NINGÚN insumo derivado se descuenta todavía
                    // (ver validación arriba) — el Consumo nace
                    // "no_registrado" y un administrador lo registra
                    // manualmente después. Una venta directa (no derivada)
                    // ya pasó la validación estricta, así que sí se
                    // descuenta de una vez, como siempre.
                    if (!($esDerivado && $consumoPendiente)) {
                        DB::table('inventarios')
                            ->where('producto_id', $productoEfectivo->id)
                            ->where('bodega_id', $bodegaId)
                            ->decrement('stock', $cantidad);
                    }

                    if ($esConsumoDeInsumo) {
                        $costoUnitario = (float) (DB::table('inventarios')
                            ->where('producto_id', $productoEfectivo->id)
                            ->where('bodega_id', $bodegaId)
                            ->value('costo_promedio') ?? 0);

                        $lineasConsumo[] = [
                            'producto_base_id' => $productoEfectivo->id,
                            'producto_ensamblado_id' => $producto->id,
                            'bodega_id' => $bodegaId,
                            'cantidad' => $cantidad,
                            'costo_unitario' => $costoUnitario,
                            'subtotal' => round($cantidad * $costoUnitario, 2),
                        ];
                    }
                }
            }

            if (!empty($lineasConsumo)) {
                $consumo = Consumo::create([
                    'numero_factura' => $factura->numero_factura,
                    'factura_id' => $factura->id,
                    'fecha' => now()->toDateString(),
                    'observacion' => 'Consumo de materia prima de venta ' . now()->format('d/m/Y'),
                    'total' => array_sum(array_column($lineasConsumo, 'subtotal')),
                    'user_id' => auth()->id(),
                    'estado' => $consumoPendiente ? 'no_registrado' : 'registrado',
                    'registrado_por' => $consumoPendiente ? null : auth()->id(),
                    'registrado_at' => $consumoPendiente ? null : now(),
                ]);

                foreach ($lineasConsumo as $linea) {
                    $consumo->detalles()->create($linea);
                }
            }

            // 7. Cerrar pedidos y liberar mesa
            app(LegacyDocumentSyncService::class)->factura($factura);
            $pedidos->each->update(['estado' => 'pagado']);

            Mesa::where('id', $request->mesa_id)->update(['estado' => 'disponible']);

            // 8. Contabilizar — el controlador NO sabe de IVA, integraciones ni ContabilidadData
            $facturacionContableService->contabilizar($factura);

            // 8.1 Facturación electrónica: no hace nada mientras no haya
            // proveedor configurado (ver FacturacionElectronicaService).
            $facturacionElectronicaService->encolarFactura($factura);

            // 9. Imprimir
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
            $caja = Caja::with('bodega')->find($user->caja_id);
            if (!$caja) {
                return response()->json(['success' => false, 'message' => 'Caja no encontrada.'], 400);
            }

            $impresora = DB::table('impresoras')->where('id', $caja->impresora_id)->first();
            if (!$impresora) {
                return response()->json(['success' => false, 'message' => 'Impresora no configurada.'], 400);
            }

            // 2. Determinar la Bodega según la caja — antes asumía
            // "bodega_id == 1 es Restaurante, cualquier otra es Discoteca".
            $bodegaId = $caja->bodega_id;
            $nombreBodega = $caja->bodega?->punto_impresion ?? 'RESTAURANTE';

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

            if (!$caja || $hasta->lt($desde)) {
                return response()->json(['message'=>'Caja o rango de cierre inválido.'], 422);
            }
            $facturas = \App\Models\Factura::with('pagos')
                ->where('caja_id', $caja->id)->where('user_id', $user->id)
                ->where('estado', 'pagada')->whereBetween('created_at', [$desde, $hasta])->orderBy('id')->get();
            $cantidadFacturas = $facturas->count();
            $facturaInicial = $facturas->first();
            $facturaFinal = $facturas->last();
            $ventas = (object) ['efectivo'=>0,'qr'=>0,'tarjeta'=>0,'transferencia'=>0,'credito'=>0,'total_ventas'=>0];
            $propinas = (object) ['efectivo'=>0,'qr'=>0,'tarjeta'=>0,'transferencia'=>0,'credito'=>0,'total_propinas'=>0];
            foreach ($facturas as $factura) {
                $restanteVenta = round((float)$factura->total - (float)$factura->propina, 2);
                $ventas->total_ventas += $restanteVenta;
                $propinas->total_propinas += (float)$factura->propina;
                foreach (app(FacturacionContableService::class)->resolverPagos($factura) as $pago) {
                    $medio = in_array($pago['metodo_pago'], ['nequi','daviplata','qr']) ? 'qr' : $pago['metodo_pago'];
                    if (!property_exists($ventas,$medio)) continue;
                    $venta = min($restanteVenta, (float)$pago['valor']);
                    $ventas->$medio += $venta;
                    $propinas->$medio += (float)$pago['valor'] - $venta;
                    $restanteVenta = round($restanteVenta - $venta, 2);
                }
            }
            $movimientos = DB::table('movimientos_caja')->where('user_id', $user->id)
                ->whereBetween('created_at', [$desde, $hasta])->get();
            $totalEntradas = $movimientos->where('tipo', 'entrada')->sum('valor');
            $totalSalidas = $movimientos->where('tipo', 'salida')->sum('valor');

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
                        'credito' => (float) $ventas->credito,
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
