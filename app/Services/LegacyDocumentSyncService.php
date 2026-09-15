<?php

namespace App\Services;

use App\Models\Compra;
use App\Models\Consumo;
use App\Models\Documento;
use App\Models\Factura;
use App\Models\TipoDocumento;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Traduce los documentos "legacy" (Compra, Factura, Ajuste, TrasladoBodega)
 * al modelo genérico de Documento, y deja el historial de kardex
 * (movimientos_inventario con costo promedio) para cada uno.
 */
class LegacyDocumentSyncService
{
    public function __construct(private KardexService $kardex)
    {
    }

    public function compra(Compra $compra): Documento
    {
        $existente = $compra->documento_id ? Documento::findOrFail($compra->documento_id) : null;
        if ($existente && $existente->estado !== 'anulado') return $existente;

        $usuario = $compra->user_id ?: User::query()->value('id');
        $compra->loadMissing('detalles.producto');
        $tipo = TipoDocumento::where('codigo', 'COMPRA')->firstOrFail();

        $doc = $existente ?? new Documento();
        $doc->fill([
            'tipo_documento_id' => $tipo->id,
            'prefijo' => $compra->prefijo,
            'numero' => $compra->numero_factura,
            'consecutivo' => $compra->consecutivo,
            'fecha' => $compra->fecha,
            'hora' => now()->format('H:i:s'),
            'tercero_id' => $compra->proveedor_id,
            'user_id' => $usuario,
            'subtotal' => $compra->subtotal,
            'descuento' => $compra->descuentos,
            'impuestos' => (float) $compra->iva + (float) $compra->ico + (float) $compra->imp_saludable,
            'retenciones' => $compra->retenciones,
            'total' => $compra->total,
            'observaciones' => $compra->observaciones,
            'estado' => 'registrado',
            'registrado_at' => $compra->registrado_at ?: now(),
            'registrado_por' => $usuario,
            'referencia_externa' => 'COMPRA:' . $compra->id,
        ])->save();

        $saldos = [];
        foreach ($compra->detalles->groupBy(fn ($d) => $d->producto_id.':'.$d->bodega_id) as $clave => $lineas) {
            $linea = $lineas->first();
            $saldos[$clave] = (float) DB::table('inventarios')->where(['producto_id'=>$linea->producto_id,'bodega_id'=>$linea->bodega_id])->value('stock') - (float)$lineas->sum('cantidad');
        }

        foreach ($compra->detalles as $linea) {
            $detalle = $doc->detalles()->create([
                'producto_id' => $linea->producto_id,
                'descripcion' => $linea->producto?->descripcion ?? 'Producto',
                'cantidad' => $linea->cantidad,
                'precio_unitario' => $linea->costo_unitario,
                'descuento' => 0,
                'impuesto' => (float) $linea->total - (float) $linea->subtotal,
                'subtotal' => $linea->subtotal,
                'total' => $linea->total,
                'tipo_movimiento_inventario' => 'ENTRADA',
                'bodega_destino_id' => $linea->bodega_id,
            ]);

            $clave = $linea->producto_id.':'.$linea->bodega_id;
            $stock = $saldos[$clave] + (float)$linea->cantidad;
            $saldos[$clave] = $stock;
            $this->kardex->registrar(
                $doc->id,
                $detalle->id,
                $linea->producto_id,
                $linea->bodega_id,
                'ENTRADA',
                (float) $linea->cantidad,
                $stock - (float) $linea->cantidad,
                $stock,
                (float) $linea->costo_unitario,
                $usuario,
                $compra->registrado_at ?: now()
            );
        }

        $compra->update(['documento_id' => $doc->id]);

        return $doc;
    }

    public function factura(Factura $factura): Documento
    {
        if ($factura->documento_id) {
            return Documento::findOrFail($factura->documento_id);
        }

        $factura->loadMissing('detalles.producto');
        $tipo = TipoDocumento::where('codigo', 'VENTA')->firstOrFail();
        [$prefijo, $numero] = str_contains($factura->numero_factura, '-')
            ? explode('-', $factura->numero_factura, 2)
            : ['FV', $factura->numero_factura];

        $doc = Documento::create([
            'tipo_documento_id' => $tipo->id,
            'prefijo' => $prefijo,
            'numero' => $factura->numero_factura,
            'fecha' => $factura->created_at->toDateString(),
            'hora' => $factura->created_at->format('H:i:s'),
            'tercero_id' => $factura->cliente_id,
            'caja_id' => $factura->caja_id,
            'user_id' => $factura->user_id,
            'subtotal' => $factura->subtotal,
            'impuestos' => $factura->impuestos,
            'total' => $factura->total,
            'estado' => 'registrado',
            'registrado_at' => $factura->created_at,
            'registrado_por' => $factura->user_id,
            'referencia_externa' => 'FACTURA:' . $factura->id,
        ]);

        $pendientes = [];
        foreach ($factura->detalles as $linea) {
            if ($linea->producto?->afecta_inventario && $factura->caja?->bodega_id) {
                $clave = $linea->producto_id.':'.$factura->caja->bodega_id;
                $pendientes[$clave] = ($pendientes[$clave] ?? 0) + (float)$linea->cantidad;
            }
        }
        foreach (Consumo::where('factura_id',$factura->id)->where('estado','registrado')->with('detalles')->get()->flatMap->detalles as $linea) {
            $clave = $linea->producto_base_id.':'.$linea->bodega_id;
            $pendientes[$clave] = ($pendientes[$clave] ?? 0) + (float)$linea->cantidad;
        }
        foreach ($factura->detalles as $linea) {
            $detalle = $doc->detalles()->create([
                'producto_id' => $linea->producto_id,
                'descripcion' => $linea->producto?->descripcion ?? 'Producto',
                'cantidad' => $linea->cantidad,
                'precio_unitario' => $linea->precio_unitario,
                'subtotal' => $linea->subtotal,
                'total' => $linea->subtotal,
                'tipo_movimiento_inventario' => 'SALIDA',
                'bodega_origen_id' => $factura->caja?->bodega_id,
            ]);

            // Igual que FacturacionController::cerrarMesa() al descontar el stock real:
            // sin este chequeo, un producto con afecta_inventario=false (ej. un plato
            // de cocina cuyo insumo se controla aparte, no la unidad vendida) terminaba
            // con movimientos de kardex fantasma y una fila de inventarios creada de la
            // nada en 0, aunque el stock real de ese producto nunca se tocó.
            if ($factura->caja?->bodega_id && $linea->producto?->afecta_inventario == 1) {
                $clave = $linea->producto_id.':'.$factura->caja->bodega_id;
                $pendientes[$clave] -= (float)$linea->cantidad;
                $stock = (float) DB::table('inventarios')->where(['producto_id' => $linea->producto_id, 'bodega_id' => $factura->caja->bodega_id])->value('stock') + $pendientes[$clave];
                $this->kardex->registrar(
                    $doc->id,
                    $detalle->id,
                    $linea->producto_id,
                    $factura->caja->bodega_id,
                    'SALIDA',
                    (float) $linea->cantidad,
                    $stock + (float) $linea->cantidad,
                    $stock,
                    null,
                    $factura->user_id,
                    $factura->created_at
                );
            }
        }

        // Un producto ensamblado o con acompañamiento (ej. "Cubetazo Águila")
        // tiene afecta_inventario=false — el que de verdad se descuenta es su
        // insumo real (Águila), y ese descuento ya quedó registrado en
        // Consumo/ConsumoDetalle por FacturacionController::cerrarMesa().
        // Sin este bloque esas ventas generaban Consumo pero NUNCA dejaban
        // rastro en el kardex: el stock del insumo bajaba de verdad, pero
        // ningún MovimientoInventario lo explicaba.
        //
        // Un Consumo 'no_registrado' (falta de stock de algún insumo, ver
        // cerrarMesa()) NO mueve inventario todavía — nada que kardexear
        // hasta que un administrador lo registre manualmente (ver
        // ConsumoController::registrar(), que llama a este mismo kardex).
        //
        // Cada línea guarda su propia bodega (Producto::bodega_origen_id
        // puede ser distinta a la de la caja que vendió, ej. la carne de
        // una hamburguesa vendida en Discoteca vive en Cocina), así que se
        // agrupa por [insumo, bodega] y no solo por insumo.
        $detallesPorInsumoYBodega = Consumo::where('factura_id', $factura->id)
            ->where('estado', 'registrado')
            ->with('detalles')
            ->get()
            ->flatMap->detalles
            ->groupBy(fn ($detalle) => $detalle->producto_base_id . ':' . $detalle->bodega_id);

        foreach ($detallesPorInsumoYBodega as $clave => $lineas) {
            [$productoId, $bodegaId] = explode(':', $clave);
            $productoId = (int) $productoId;
            $bodegaId = (int) $bodegaId;

            // Si la factura tiene 2+ líneas del mismo insumo (ej. dos rondas
            // de "Cubetazo Águila" en el mismo pedido), no se puede leer el
            // stock actual para cada una por separado: como ya bajó de
            // verdad ANTES de llegar aquí, las dos leerían el mismo stock
            // final y mostrarían un stock_anterior idéntico y equivocado.
            // Se reconstruye la secuencia real hacia atrás, partiendo del
            // stock actual (que ya refleja TODAS las líneas).
            $stockActual = (float) DB::table('inventarios')
                ->where(['producto_id' => $productoId, 'bodega_id' => $bodegaId])
                ->value('stock');

            // El stock ANTES de esta factura es el actual más todo lo que
            // ella descontó de este insumo; desde ahí se recorren las
            // líneas en orden y se va bajando, para que cada una quede con
            // su propio par anterior/nuevo correcto.
            $stockAntesDeLaFactura = $stockActual + (float) $lineas->sum('cantidad');

            $stockAnterior = $stockAntesDeLaFactura;
            foreach ($lineas as $consumoDetalle) {
                $cantidad = (float) $consumoDetalle->cantidad;
                $stockNuevo = $stockAnterior - $cantidad;

                $this->kardex->registrar(
                    $doc->id,
                    null,
                    $productoId,
                    $bodegaId,
                    'SALIDA',
                    $cantidad,
                    $stockAnterior,
                    $stockNuevo,
                    null,
                    $factura->user_id,
                    $factura->created_at
                );

                $stockAnterior = $stockNuevo;
            }
        }

        $factura->update(['documento_id' => $doc->id]);

        return $doc;
    }

    /**
     * Crea el Documento "espejo" para Ajustes y Traslados de bodega, y deja
     * el historial de kardex correspondiente (con costo promedio) para cada
     * línea. $bodega es la bodega única (Ajuste) o la de origen (Traslado);
     * $bodegaDestino solo aplica a Traslado.
     */
    public function operativo($origen, string $codigo, string $numero, $bodega, $detalles, $bodegaDestino = null): Documento
    {
        if ($origen->documento_id) {
            return Documento::findOrFail($origen->documento_id);
        }

        $tipo = TipoDocumento::where('codigo', $codigo)->firstOrFail();
        $usuario = $origen->user_id;
        $fecha = $origen->fecha ?? now();

        $doc = Documento::create([
            'tipo_documento_id' => $tipo->id,
            'prefijo' => $origen->prefijo,
            'numero' => $numero,
            'consecutivo' => $origen->numero ?? $origen->consecutivo,
            'fecha' => $origen->fecha,
            'bodega_id' => $bodega,
            'user_id' => $usuario,
            'total' => $origen->total ?? 0,
            'observaciones' => $origen->observaciones,
            'estado' => 'registrado',
            'registrado_at' => now(),
            'registrado_por' => $usuario,
        ]);

        foreach ($detalles as $linea) {
            $detalle = $doc->detalles()->create([
                'producto_id' => $linea->producto_id,
                'descripcion' => $linea->producto?->descripcion ?? 'Producto',
                'cantidad' => $linea->cantidad,
                'precio_unitario' => $linea->precio ?? 0,
                'subtotal' => ($linea->precio ?? 0) * $linea->cantidad,
                'total' => ($linea->precio ?? 0) * $linea->cantidad,
            ]);

            if ($codigo === 'TRASLADO' && $bodegaDestino) {
                $stockOrigen = (float) DB::table('inventarios')->where(['producto_id' => $linea->producto_id, 'bodega_id' => $bodega])->value('stock');
                $movSalida = $this->kardex->registrar(
                    $doc->id,
                    $detalle->id,
                    $linea->producto_id,
                    $bodega,
                    'SALIDA',
                    (float) $linea->cantidad,
                    $stockOrigen + (float) $linea->cantidad,
                    $stockOrigen,
                    null,
                    $usuario,
                    $fecha
                );

                $stockDestino = (float) DB::table('inventarios')->where(['producto_id' => $linea->producto_id, 'bodega_id' => $bodegaDestino])->value('stock');
                $this->kardex->registrar(
                    $doc->id,
                    $detalle->id,
                    $linea->producto_id,
                    $bodegaDestino,
                    'ENTRADA',
                    (float) $linea->cantidad,
                    $stockDestino - (float) $linea->cantidad,
                    $stockDestino,
                    (float) $movSalida->costo_unitario,
                    $usuario,
                    $fecha
                );
            } elseif ($codigo === 'AJUSTE') {
                $tipoMov = strtoupper((string) ($linea->tipo ?? 'salida')) === 'ENTRADA' ? 'ENTRADA' : 'SALIDA';
                $stockActual = (float) DB::table('inventarios')->where(['producto_id' => $linea->producto_id, 'bodega_id' => $bodega])->value('stock');
                $stockAnterior = $tipoMov === 'ENTRADA' ? $stockActual - (float) $linea->cantidad : $stockActual + (float) $linea->cantidad;

                $this->kardex->registrar(
                    $doc->id,
                    $detalle->id,
                    $linea->producto_id,
                    $bodega,
                    $tipoMov,
                    (float) $linea->cantidad,
                    $stockAnterior,
                    $stockActual,
                    $tipoMov === 'ENTRADA' ? (float) ($linea->precio ?? 0) : null,
                    $usuario,
                    $fecha
                );
            }
        }

        $origen->documento_id = $doc->id;
        $origen->save();

        return $doc;
    }
}
