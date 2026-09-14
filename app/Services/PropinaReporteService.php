<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/**
 * Propinas recibidas por vendedor (Factura.user_id — quien cobró la cuenta),
 * para saber cuánto ha recaudado cada mesero/cajero en propinas y qué
 * porcentaje de sus facturas trae propina.
 */
class PropinaReporteService
{
    public function porVendedor(array $filtros): array
    {
        $query = DB::table('facturas as f')
            ->leftJoin('users as u', 'u.id', '=', 'f.user_id')
            ->leftJoin('cajas as caja', 'caja.id', '=', 'f.caja_id')
            ->where('f.estado', '!=', 'anulada')
            ->whereBetween('f.created_at', [$filtros['desde'], $filtros['hasta']])
            ->when($filtros['bodega_id'] ?? null, fn ($q, $v) => $q->where('caja.bodega_id', $v))
            ->when($filtros['caja_id'] ?? null, fn ($q, $v) => $q->where('f.caja_id', $v))
            ->when($filtros['user_id'] ?? null, fn ($q, $v) => $q->where('f.user_id', $v))
            ->select('f.id as factura_id', 'f.numero_factura', 'f.created_at as fecha', 'f.user_id', 'u.name as vendedor', 'f.propina', 'f.subtotal', 'f.total')
            ->get();

        $porVendedor = $query->groupBy('user_id')->map(function ($facturas) {
            $primera = $facturas->first();
            $conPropina = $facturas->where('propina', '>', 0);

            return [
                'user_id' => $primera->user_id,
                'vendedor' => $primera->vendedor ?? 'Sin vendedor asignado',
                'facturas' => $facturas->count(),
                'facturas_con_propina' => $conPropina->count(),
                'total_ventas' => round((float) $facturas->sum('subtotal'), 2),
                'total_propinas' => round((float) $facturas->sum('propina'), 2),
                'propina_promedio' => $conPropina->count() > 0 ? round((float) $conPropina->sum('propina') / $conPropina->count(), 2) : 0.0,
                // Trazabilidad: de qué factura exacta sale cada propina, para
                // poder revisarla — solo se listan las que sí trajeron propina.
                'facturas_detalle' => $conPropina->map(fn ($f) => [
                    'factura_id' => $f->factura_id,
                    'numero_factura' => $f->numero_factura,
                    'fecha' => $f->fecha,
                    'subtotal' => round((float) $f->subtotal, 2),
                    'propina' => round((float) $f->propina, 2),
                ])->sortBy('fecha')->values(),
            ];
        })->sortByDesc('total_propinas')->values();

        return [
            'filas' => $porVendedor,
            'totales' => [
                'vendedores' => $porVendedor->count(),
                'facturas' => $query->count(),
                'total_ventas' => round((float) $porVendedor->sum('total_ventas'), 2),
                'total_propinas' => round((float) $porVendedor->sum('total_propinas'), 2),
            ],
        ];
    }
}
