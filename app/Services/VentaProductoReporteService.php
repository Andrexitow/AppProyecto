<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/**
 * Informe de ventas por producto (POS), con todos los filtros que una
 * contadora o el dueño del negocio necesita para cruzar información:
 * bodega, vendedor, cliente, caja, prefijo de factura, categoría, grupo de
 * menú, producto y rango de fecha/hora. El IVA no se filtra: se muestra u
 * oculta del valor de cada línea según el checkbox 'con_iva', porque el
 * precio ya cobrado (factura_detalles.subtotal) siempre incluye IVA — igual
 * que hace FacturacionContableService al contabilizar.
 */
class VentaProductoReporteService
{
    public function ventaPorProducto(array $filtros): array
    {
        $query = DB::table('factura_detalles as fd')
            ->join('facturas as f', 'f.id', '=', 'fd.factura_id')
            ->join('productos as p', 'p.id', '=', 'fd.producto_id')
            ->leftJoin('cajas as caja', 'caja.id', '=', 'f.caja_id')
            ->leftJoin('grupo_menus as gm', 'gm.id', '=', 'p.grupo_menu_id')
            ->where('f.estado', '!=', 'anulada')
            ->whereBetween('f.created_at', [$filtros['desde'], $filtros['hasta']])
            // La bodega de una venta es la de la CAJA que la cobró (misma
            // fuente que usa FacturacionController para descontar stock),
            // no la de la mesa/zona: zonas.bodega_id existe en el esquema
            // pero nunca se ha llenado, así que filtrar por ahí no encontraría nada.
            ->when($filtros['bodega_id'] ?? null, fn ($q, $v) => $q->where('caja.bodega_id', $v))
            ->when($filtros['user_id'] ?? null, fn ($q, $v) => $q->where('f.user_id', $v))
            ->when($filtros['cliente_id'] ?? null, fn ($q, $v) => $q->where('f.cliente_id', $v))
            ->when($filtros['caja_id'] ?? null, fn ($q, $v) => $q->where('f.caja_id', $v))
            ->when($filtros['prefijo'] ?? null, fn ($q, $v) => $q->where('f.numero_factura', 'like', $v . '-%'))
            ->when($filtros['categoria'] ?? null, fn ($q, $v) => $q->where('p.categoria', $v))
            ->when($filtros['grupo_menu_id'] ?? null, fn ($q, $v) => $q->where('p.grupo_menu_id', $v))
            ->when($filtros['producto_id'] ?? null, fn ($q, $v) => $q->where('p.id', $v))
            ->select(
                'fd.factura_id', 'fd.producto_id', 'fd.cantidad', 'fd.subtotal',
                'p.codigo', 'p.descripcion', 'p.categoria', 'p.iva_ventas',
                'gm.nombre as grupo_menu',
                'f.numero_factura', 'f.created_at as fecha_venta'
            );

        $filas = $query->get();
        $conIva = (bool) ($filtros['con_iva'] ?? true);

        $porProducto = $filas->groupBy('producto_id')->map(function ($lineas) use ($conIva) {
            $primera = $lineas->first();
            $cantidad = 0.0;
            $valor = 0.0;

            // Detalle por factura (no por línea): si el mismo producto aparece
            // dos veces en una factura se suma en una sola fila — es la
            // trazabilidad que pide una contadora ("¿de qué factura sale
            // esto?"), no una fila repetida para el mismo número.
            $porFactura = [];

            foreach ($lineas as $linea) {
                $subtotal = (float) $linea->subtotal;
                $ivaPct = (float) ($linea->iva_ventas ?? 0);
                $ivaLinea = $ivaPct > 0 ? round($subtotal * $ivaPct / (100 + $ivaPct), 2) : 0.0;
                $valorLinea = $conIva ? $subtotal : ($subtotal - $ivaLinea);

                $valor += $valorLinea;
                $cantidad += (float) $linea->cantidad;

                if (!isset($porFactura[$linea->factura_id])) {
                    $porFactura[$linea->factura_id] = [
                        'factura_id' => $linea->factura_id,
                        'numero_factura' => $linea->numero_factura,
                        'fecha' => $linea->fecha_venta,
                        'cantidad' => 0.0,
                        'valor' => 0.0,
                    ];
                }
                $porFactura[$linea->factura_id]['cantidad'] += (float) $linea->cantidad;
                $porFactura[$linea->factura_id]['valor'] += $valorLinea;
            }

            $facturasDetalle = collect($porFactura)->map(function ($f) {
                $f['cantidad'] = round($f['cantidad'], 3);
                $f['valor'] = round($f['valor'], 2);
                return $f;
            })->sortBy('fecha')->values();

            return [
                'producto_id' => $primera->producto_id,
                'codigo' => $primera->codigo,
                'descripcion' => $primera->descripcion,
                'categoria' => $primera->categoria,
                'grupo_menu' => $primera->grupo_menu,
                'cantidad_vendida' => round($cantidad, 3),
                'valor_total' => round($valor, 2),
                'valor_promedio_unitario' => $cantidad > 0 ? round($valor / $cantidad, 2) : 0.0,
                'facturas' => $facturasDetalle->count(),
                'facturas_detalle' => $facturasDetalle,
            ];
        })->sortByDesc('valor_total')->values();

        $facturasUnicas = $filas->pluck('factura_id')->unique()->count();

        return [
            'filas' => $porProducto,
            'totales' => [
                'productos' => $porProducto->count(),
                'cantidad' => round((float) $porProducto->sum('cantidad_vendida'), 3),
                'valor' => round((float) $porProducto->sum('valor_total'), 2),
                'facturas' => $facturasUnicas,
            ],
            'con_iva' => $conIva,
        ];
    }
}
