<?php

namespace App\Http\Controllers;

use App\Models\Factura;
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
                'facturas.doc_electronico',
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
                    'doc_electronico' => (bool) $f->doc_electronico,
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
                'productos.descripcion as producto',
                'factura_detalles.cantidad',
                'factura_detalles.precio_unitario as precio',
                'productos.iva_ventas as iva'
            )
            ->get()
            ->map(function ($it) {
                return [
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

    public function anular($id)
    {
        $factura = DB::table('facturas')->where('id', $id)->first();

        if (!$factura) {
            return response()->json(['message' => 'Factura no encontrada'], 404);
        }

        if ($factura->estado === 'anulada') {
            return response()->json(['message' => 'Esta factura ya se encuentra anulada.'], 422);
        }

        if ($factura->doc_electronico) {
            return response()->json([
                'message' => 'No se puede anular: el documento electrónico ya fue generado ante la DIAN. Debes hacer una nota crédito.'
            ], 422);
        }

        $bodegaId = DB::table('cajas')->where('id', $factura->caja_id)->value('bodega_id');
        if (!$bodegaId) {
            return response()->json(['message' => 'La caja de esta factura no tiene una bodega asignada.'], 422);
        }

        DB::transaction(function () use ($id, $bodegaId) {
            // Reponer stock de cada producto que afecte inventario
            $detalles = DB::table('factura_detalles')
                ->join('productos', 'productos.id', '=', 'factura_detalles.producto_id')
                ->where('factura_detalles.factura_id', $id)
                ->where('productos.afecta_inventario', 1)
                ->select('factura_detalles.producto_id', 'factura_detalles.cantidad')
                ->get();

            foreach ($detalles as $d) {
                $inventario = DB::table('inventarios')
                    ->where('producto_id', $d->producto_id)
                    ->where('bodega_id', $bodegaId)
                    ->lockForUpdate()
                    ->first();

                if ($inventario) {
                    DB::table('inventarios')->where('id', $inventario->id)->increment('stock', $d->cantidad);
                    continue;
                }

                DB::table('inventarios')->insert([
                    'producto_id' => $d->producto_id,
                    'bodega_id' => $bodegaId,
                    'stock' => $d->cantidad,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('facturas')->where('id', $id)->update([
                'estado'     => 'anulada',
                'updated_at' => now(),
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => "Factura {$factura->numero_factura} anulada correctamente."
        ]);
    }

    public function revertirAnulacion($id)
    {
        $factura = DB::table('facturas')->where('id', $id)->first();

        if (!$factura) {
            return response()->json(['message' => 'Factura no encontrada'], 404);
        }

        if ($factura->estado !== 'anulada') {
            return response()->json([
                'message' => 'Solo se puede revertir la anulación de una factura que esté anulada.'
            ], 422);
        }

        try {
            DB::transaction(function () use ($id, $factura) {
                $detalles = DB::table('factura_detalles')
                    ->join('productos', 'productos.id', '=', 'factura_detalles.producto_id')
                    ->where('factura_detalles.factura_id', $id)
                    ->where('productos.afecta_inventario', 1)
                    ->select(
                        'factura_detalles.producto_id',
                        'factura_detalles.cantidad',
                        'productos.descripcion'
                    )
                    ->get();

                $bodegaId = DB::table('cajas')->where('id', $factura->caja_id)->value('bodega_id');

                // Validar que haya stock suficiente para volver a descontarlo
                foreach ($detalles as $d) {
                    $stockActual = DB::table('inventarios')
                        ->where('producto_id', $d->producto_id)
                        ->where('bodega_id', $bodegaId)
                        ->value('stock');

                    if ($stockActual === null || $stockActual < $d->cantidad) {
                        throw new \Exception("Stock insuficiente para revertir: {$d->descripcion}");
                    }
                }

                foreach ($detalles as $d) {
                    DB::table('inventarios')
                        ->where('producto_id', $d->producto_id)
                        ->where('bodega_id', $bodegaId)
                        ->decrement('stock', $d->cantidad);
                }

                DB::table('facturas')->where('id', $id)->update([
                    'estado'     => 'pagada',
                    'updated_at' => now(),
                ]);
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
