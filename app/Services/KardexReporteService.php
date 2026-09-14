<?php

namespace App\Services;

use App\Models\Bodega;
use App\Models\Producto;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Consultas de lectura sobre el kardex (historial de movimientos_inventario)
 * y la valorización de existencias, en el mismo espíritu que ReporteContableService.
 */
class KardexReporteService
{
    public function movimientos(int $productoId, ?int $bodegaId, string $desde, string $hasta): array
    {
        $producto = Producto::findOrFail($productoId);

        if ($bodegaId !== null) {
            return $this->movimientosDeUnaBodega($producto, Bodega::findOrFail($bodegaId), $desde, $hasta);
        }

        return $this->movimientosDeTodasLasBodegas($producto, $desde, $hasta);
    }

    private function movimientosDeUnaBodega(Producto $producto, Bodega $bodega, string $desde, string $hasta): array
    {
        $anterior = DB::table('movimientos_inventario')
            ->where('producto_id', $producto->id)
            ->where('bodega_id', $bodega->id)
            ->where('fecha', '<', $desde . ' 00:00:00')
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->first();

        $saldoInicial = [
            'cantidad' => (float) ($anterior->stock_nuevo ?? 0),
            'costo_promedio' => (float) ($anterior->costo_promedio_nuevo ?? 0),
        ];
        $saldoInicial['valor'] = round($saldoInicial['cantidad'] * $saldoInicial['costo_promedio'], 2);

        $movimientos = DB::table('movimientos_inventario as m')
            ->leftJoin('documentos as d', 'd.id', '=', 'm.documento_id')
            ->leftJoin('tipos_documento as td', 'td.id', '=', 'd.tipo_documento_id')
            ->where('m.producto_id', $producto->id)
            ->where('m.bodega_id', $bodega->id)
            ->whereBetween('m.fecha', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
            ->orderBy('m.fecha')
            ->orderBy('m.id')
            ->select('m.*', 'td.nombre as documento_tipo', 'd.numero')
            ->get();

        $ultimo = $movimientos->last();
        $saldoFinal = $ultimo
            ? ['cantidad' => (float) $ultimo->stock_nuevo, 'costo_promedio' => (float) $ultimo->costo_promedio_nuevo]
            : $saldoInicial;
        $saldoFinal['valor'] = round($saldoFinal['cantidad'] * $saldoFinal['costo_promedio'], 2);

        return [
            'producto' => $producto,
            'bodega' => $bodega,
            'saldo_inicial' => $saldoInicial,
            'movimientos' => $movimientos,
            'saldo_final' => $saldoFinal,
        ];
    }

    /**
     * Consolida el kardex del producto en TODAS las bodegas donde se ha
     * movido. El costo promedio ponderado se calcula por separado en cada
     * bodega (así lo hace KardexService), así que aquí no se puede sumar un
     * "costo promedio" directamente: se reconstruye como valor total /
     * cantidad total, y cada línea del detalle conserva su propia bodega para
     * que nunca se mezclen cantidades o costos de dos bodegas distintas.
     */
    private function movimientosDeTodasLasBodegas(Producto $producto, string $desde, string $hasta): array
    {
        $bodegaIds = DB::table('movimientos_inventario')->where('producto_id', $producto->id)->distinct()->pluck('bodega_id');

        $cantidadInicial = 0.0;
        $valorInicial = 0.0;
        $cantidadFinal = 0.0;
        $valorFinal = 0.0;

        foreach ($bodegaIds as $bodegaId) {
            $antesDesde = DB::table('movimientos_inventario')
                ->where('producto_id', $producto->id)->where('bodega_id', $bodegaId)
                ->where('fecha', '<', $desde . ' 00:00:00')
                ->orderByDesc('fecha')->orderByDesc('id')->first();
            $cantidadInicial += (float) ($antesDesde->stock_nuevo ?? 0);
            $valorInicial += (float) ($antesDesde->stock_nuevo ?? 0) * (float) ($antesDesde->costo_promedio_nuevo ?? 0);

            $antesHasta = DB::table('movimientos_inventario')
                ->where('producto_id', $producto->id)->where('bodega_id', $bodegaId)
                ->where('fecha', '<=', $hasta . ' 23:59:59')
                ->orderByDesc('fecha')->orderByDesc('id')->first();
            $cantidadFinal += (float) ($antesHasta->stock_nuevo ?? 0);
            $valorFinal += (float) ($antesHasta->stock_nuevo ?? 0) * (float) ($antesHasta->costo_promedio_nuevo ?? 0);
        }

        $saldoInicial = [
            'cantidad' => round($cantidadInicial, 4),
            'costo_promedio' => $cantidadInicial > 0.0001 ? round($valorInicial / $cantidadInicial, 4) : 0.0,
            'valor' => round($valorInicial, 2),
        ];
        $saldoFinal = [
            'cantidad' => round($cantidadFinal, 4),
            'costo_promedio' => $cantidadFinal > 0.0001 ? round($valorFinal / $cantidadFinal, 4) : 0.0,
            'valor' => round($valorFinal, 2),
        ];

        $movimientos = DB::table('movimientos_inventario as m')
            ->leftJoin('documentos as d', 'd.id', '=', 'm.documento_id')
            ->leftJoin('tipos_documento as td', 'td.id', '=', 'd.tipo_documento_id')
            ->join('bodegas as b', 'b.id', '=', 'm.bodega_id')
            ->where('m.producto_id', $producto->id)
            ->whereBetween('m.fecha', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
            ->orderBy('m.fecha')
            ->orderBy('m.id')
            ->select('m.*', 'td.nombre as documento_tipo', 'd.numero', 'b.descripcion as bodega_nombre')
            ->get();

        return [
            'producto' => $producto,
            'bodega' => null,
            'saldo_inicial' => $saldoInicial,
            'movimientos' => $movimientos,
            'saldo_final' => $saldoFinal,
        ];
    }

    public function valorizacion(?int $bodegaId = null): array
    {
        $filas = DB::table('inventarios as i')
            ->join('productos as p', 'p.id', '=', 'i.producto_id')
            ->join('bodegas as b', 'b.id', '=', 'i.bodega_id')
            ->where('i.stock', '<>', 0)
            ->when($bodegaId, fn ($q) => $q->where('i.bodega_id', $bodegaId))
            ->orderBy('b.descripcion')
            ->orderBy('p.descripcion')
            ->select('p.id as producto_id', 'p.codigo', 'p.descripcion', 'b.id as bodega_id', 'b.descripcion as bodega', 'i.stock', 'i.costo_promedio')
            ->get()
            ->map(function ($fila) {
                $fila->stock = (float) $fila->stock;
                $fila->costo_promedio = (float) $fila->costo_promedio;
                $fila->valor_total = round($fila->stock * $fila->costo_promedio, 2);

                return $fila;
            });

        return [
            'filas' => $filas,
            'total_unidades' => round($filas->sum('stock'), 3),
            'total_valorizado' => round($filas->sum('valor_total'), 2),
        ];
    }

    public function catalogoProductosConMovimiento(): Collection
    {
        return DB::table('movimientos_inventario as m')
            ->join('productos as p', 'p.id', '=', 'm.producto_id')
            ->select('p.id', 'p.codigo', 'p.descripcion')
            ->distinct()
            ->orderBy('p.descripcion')
            ->get();
    }
}
