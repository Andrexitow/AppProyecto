<?php

namespace App\Services;

use App\Models\Compra;
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
        if ($compra->documento_id) {
            return Documento::findOrFail($compra->documento_id);
        }

        $usuario = $compra->user_id ?: User::query()->value('id');
        $compra->loadMissing('detalles.producto');
        $tipo = TipoDocumento::where('codigo', 'COMPRA')->firstOrFail();

        $doc = Documento::create([
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
        ]);

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

            $stock = (float) DB::table('inventarios')->where(['producto_id' => $linea->producto_id, 'bodega_id' => $linea->bodega_id])->value('stock');
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
                $stock = (float) DB::table('inventarios')->where(['producto_id' => $linea->producto_id, 'bodega_id' => $factura->caja->bodega_id])->value('stock');
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
