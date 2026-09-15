<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Services\FacturacionContableService;
use App\Services\FacturacionElectronicaService;
use App\Services\PrintService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FacturaController extends Controller
{
    public function index()
    {
        return view('facturas.index');
    }

    /**
     * JSON para el listado (lo consume el fetch('/facturas') de la vista)
     */
    public function data()
    {
        $facturas = DB::table('facturas')
            ->leftJoin('terceros', 'terceros.id', '=', 'facturas.cliente_id')
            ->leftJoin('users', 'users.id', '=', 'facturas.user_id')
            ->select(
                'facturas.id',
                'facturas.numero_factura',
                'facturas.subtotal',
                'facturas.impuestos',
                'facturas.propina',      // NUEVO
                'facturas.total',
                'facturas.estado',
                'facturas.estado_dian',
                'facturas.metodo_pago',
                'facturas.created_at',
                'terceros.nombre as tercero_nombre',
                'terceros.apellido as tercero_apellido',
                'terceros.razon_social',
                'users.name as usuario_nombre'
            )
            ->orderByDesc('facturas.id')
            ->get()
            ->map(function ($f) {
                $prefijo = 'FR';
                $numero  = $f->numero_factura;
                if (str_contains($f->numero_factura, '-')) {
                    [$prefijo, $numero] = explode('-', $f->numero_factura, 2);
                }

                $cliente = $f->razon_social
                    ?: trim(($f->tercero_nombre ?? '') . ' ' . ($f->tercero_apellido ?? ''))
                    ?: 'Consumidor Final';

                return [
                    'id'              => $f->id,
                    'prefijo'         => $prefijo,
                    'numero'          => $numero,
                    'fecha'           => $f->created_at ? date('Y-m-d', strtotime($f->created_at)) : null,
                    'hora'            => $f->created_at ? date('H:i', strtotime($f->created_at)) : null,
                    'cliente'         => $cliente,
                    'subtotal'        => (float) $f->subtotal,   // NUEVO
                    'impuestos'       => (float) $f->impuestos,  // NUEVO
                    'propina'         => (float) $f->propina,    // NUEVO
                    'total'           => (float) $f->total,
                    // Toda venta pagada o anulada ya afecta caja, inventario y contabilidad.
                    'registrada'      => in_array($f->estado, ['pagada', 'anulada'], true),
                    'anulada'         => $f->estado === 'anulada',
                    'usuario'         => $f->usuario_nombre ?? '—',
                    // 'no_aplica' (sin proveedor DIAN configurado, el caso de
                    // hoy) | 'pendiente' | 'enviada' | 'aceptada' | 'rechazada' | 'error'
                    'estado_dian'     => $f->estado_dian ?? 'no_aplica',
                    'doc_electronico' => $f->estado_dian === 'aceptada',
                ];
            });

        return response()->json(['data' => $facturas]);
    }

    /**
     * Detalle de una factura con sus ítems (lo usa verFactura en el JS)
     */
    public function show($id)
    {
        $factura = DB::table('facturas')
            ->leftJoin('terceros', 'terceros.id', '=', 'facturas.cliente_id')
            ->leftJoin('users', 'users.id', '=', 'facturas.user_id')
            ->where('facturas.id', $id)
            ->select(
                'facturas.*',
                'terceros.nombre as tercero_nombre',
                'terceros.apellido as tercero_apellido',
                'terceros.razon_social',
                'users.name as usuario_nombre'
            )
            ->first();

        if (!$factura) {
            return response()->json(['message' => 'Factura no encontrada'], 404);
        }

        $items = DB::table('factura_detalles')
            ->join('productos', 'productos.id', '=', 'factura_detalles.producto_id')
            ->where('factura_detalles.factura_id', $id)
            ->select(
                'factura_detalles.id as detalle_id',
                'productos.descripcion as producto',
                'factura_detalles.cantidad',
                'factura_detalles.precio_unitario as precio',
                'productos.iva_ventas as iva'
            )
            ->get()
            ->map(function ($it) {
                return [
                    'id'        => $it->detalle_id, // usado por Notas Crédito/Débito
                    'producto'  => $it->producto,
                    'cantidad'  => $it->cantidad,
                    'precio'    => (float) $it->precio,
                    'descuento' => 0,
                    'iva'       => (float) ($it->iva ?? 0),
                ];
            });

        return response()->json([
            'id'        => $factura->id,
            'numero'    => $factura->numero_factura,
            'subtotal'  => (float) $factura->subtotal,   // NUEVO
            'impuestos' => (float) $factura->impuestos,  // NUEVO
            'propina'   => (float) $factura->propina,    // NUEVO
            'total'     => (float) $factura->total,
            'estado'    => $factura->estado,
            'anulada'   => $factura->estado === 'anulada', // antes faltaba: el modal "ver factura" nunca mostraba el aviso de anulada
            'estado_dian'     => $factura->estado_dian ?? 'no_aplica',
            'doc_electronico' => $factura->estado_dian === 'aceptada', // antes faltaba: el aviso "pendiente ante la DIAN" salía siempre, sin importar el estado real
            'cliente'   => $factura->razon_social ?: trim($factura->tercero_nombre . ' ' . $factura->tercero_apellido),
            'items'     => $items,
        ]);
    }

    /**
     * Catálogos para llenar los <select> del modal de factura
     */
    public function catalogoTerceros()
    {
        $terceros = DB::table('terceros')
            ->where('estado', 1)
            ->select('id', 'nombre', 'apellido', 'razon_social', 'nit')
            ->orderBy('nombre')
            ->get()
            ->map(function ($t) {
                return [
                    'id'     => $t->id,
                    'nombre' => $t->razon_social ?: trim($t->nombre . ' ' . $t->apellido),
                    'nit'    => $t->nit,
                ];
            });

        return response()->json(['data' => $terceros]);
    }

    public function catalogoCajas()
    {
        $cajas = DB::table('cajas')
            ->where('activa', 1)
            ->select('id', 'nombre')
            ->orderBy('nombre')
            ->get();

        return response()->json(['data' => $cajas]);
    }

    public function catalogoBodegas()
    {
        $bodegas = DB::table('bodegas')
            ->select('id', 'descripcion as nombre') // tu tabla no tiene columna "nombre", tiene "descripcion"
            ->orderBy('descripcion')
            ->get();

        return response()->json(['data' => $bodegas]);
    }

    public function catalogoProductos()
    {
        $productos = DB::table('productos')
            ->where('activo', 1)
            ->where('inactivo', 0)
            ->select('id', 'codigo', 'descripcion as nombre', 'precio', 'iva_ventas')
            ->orderBy('descripcion')
            ->get();

        return response()->json(['data' => $productos]);
    }

    public function anular($id, FacturacionContableService $facturacionContableService)
    {
        $factura = Factura::find($id);

        if (!$factura) {
            return response()->json(['message' => 'Factura no encontrada'], 404);
        }

        if ($factura->estado === 'anulada') {
            return response()->json(['message' => 'Esta factura ya se encuentra anulada.'], 422);
        }

        if (in_array($factura->estado_dian, ['aceptada', 'enviada'], true)) {
            return response()->json([
                'message' => 'No se puede anular: el documento electrónico ya fue transmitido a la DIAN. Debes hacer una nota crédito.'
            ], 422);
        }

        if ($factura->notas()->exists()) {
            return response()->json([
                'message' => 'No se puede anular: esta factura ya tiene notas crédito/débito emitidas. Revisa el historial de notas antes de anularla.'
            ], 422);
        }

        $bodegaId = DB::table('cajas')->where('id', $factura->caja_id)->value('bodega_id');
        if (!$bodegaId) {
            return response()->json(['message' => 'La caja de esta factura no tiene una bodega asignada.'], 422);
        }

        // Nota: no se envuelve en try/catch — igual que en el resto de la app
        // (ver CompraAvanzadaController), una ValidationException lanzada aquí
        // (p. ej. período contable cerrado) la formatea automáticamente el
        // manejador de excepciones de Laravel como 422 con sus mensajes.
        DB::transaction(function () use ($factura, $bodegaId, $facturacionContableService) {
            if ($factura->pagosCliente()->whereHas('pago', fn ($q) => $q->where('estado', 'registrado'))->exists()) {
                throw \Illuminate\Validation\ValidationException::withMessages(['factura'=>'Anule primero los abonos del cliente antes de anular la factura.']);
            }
            if ($factura->documento_id) {
                app(\App\Services\ReversionInventarioService::class)->ejecutar($factura->documento_id);
            }

            // Antes esto dejaba el/los comprobantes contables en CONTABILIZADO
            // para siempre: la factura quedaba "anulada" pero sus asientos
            // (ingreso, IVA, costo de ventas) seguían activos, igual al bug ya
            // corregido en CompraContableService::prepararReversion().
            $facturacionContableService->anular($factura);

            $factura->update(['estado' => 'anulada']);
        });

        return response()->json([
            'success' => true,
            'message' => "Factura {$factura->numero_factura} anulada correctamente."
        ]);
    }

    /**
     * Recupera manualmente una factura 'fallida' ante la DIAN (agotó los 5
     * reintentos automáticos) — antes no existía forma de reactivarla; se
     * quedaba invisible para procesarPendientes() para siempre. Se usa
     * después de corregir lo que la bloqueaba (completar el emisor, revisar
     * el cliente, etc.).
     */
    public function reintentarDian($id, FacturacionElectronicaService $facturacionElectronicaService)
    {
        $factura = Factura::find($id);

        if (!$factura) {
            return response()->json(['message' => 'Factura no encontrada'], 404);
        }

        if ($factura->estado_dian !== 'fallida') {
            return response()->json(['message' => 'Solo se puede reintentar una factura que haya quedado en estado \'fallida\'.'], 422);
        }

        $facturacionElectronicaService->reintentarManualmente($factura);

        return response()->json([
            'success' => true,
            'message' => "Factura {$factura->numero_factura} lista para reintentar — se transmitirá en el próximo ciclo programado.",
        ]);
    }

    public function revertirAnulacion($id, FacturacionContableService $facturacionContableService)
    {
        $factura = Factura::find($id);

        if (!$factura) {
            return response()->json(['message' => 'Factura no encontrada'], 404);
        }

        if ($factura->estado !== 'anulada') {
            return response()->json([
                'message' => 'Solo se puede revertir la anulación de una factura que esté anulada.'
            ], 422);
        }

        try {
            DB::transaction(function () use ($factura, $facturacionContableService) {
                if ($factura->documento_id) {
                    app(\App\Services\ReversionInventarioService::class)->ejecutar($factura->documento_id, true);
                }

                // Igual que al cerrar la mesa originalmente: 'credito' queda
                // pendiente de cobro, cualquier otro método queda pagado.
                $factura->update([
                    'estado'          => 'pagada',
                    'estado_pago'     => $factura->metodo_pago === 'credito' ? 'pendiente' : 'pagada',
                    'total_pagado'    => $factura->metodo_pago === 'credito' ? 0 : $factura->total,
                    'saldo_pendiente' => $factura->metodo_pago === 'credito' ? $factura->total : 0,
                ]);

                // El comprobante anterior quedó ANULADO (no borrado) al anular
                // la factura, así que contabilizar() puede crear uno nuevo sin
                // chocar con el chequeo de "ya está contabilizado".
                $facturacionContableService->contabilizar($factura);
            });
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'success' => true,
            'message' => "Factura {$factura->numero_factura} restaurada correctamente."
        ]);
    }

    public function imprimir($id, PrintService $printService)
    {
        $factura = Factura::with([
            'detalles.producto',
            'caja.impresora',
            'user',
            'cliente',
            'mesa.pedidos.mesero',
        ])->find($id);

        if (!$factura) {
            return response()->json(['message' => 'Factura no encontrada'], 404);
        }

        if ($factura->estado === 'anulada') {
            return response()->json(['message' => 'No se puede imprimir una factura anulada'], 422);
        }

        $impresora = $factura->caja?->impresora;
        if (!$impresora) {
            return response()->json(['message' => 'La caja de esta factura no tiene impresora asignada'], 422);
        }

        $resultado = $printService->imprimirFactura($factura, $impresora);
        if (($resultado['status'] ?? 'error') !== 'success') {
            return response()->json(['message' => $resultado['message'] ?? 'No fue posible encolar la impresión'], 422);
        }

        return response()->json(['success' => true, 'message' => 'Factura enviada a la cola de impresión']);
    }
}
