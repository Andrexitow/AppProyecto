<?php

namespace App\Services;

use App\Models\Inventario;
use App\Models\MovimientoInventario;
use Illuminate\Support\Facades\DB;

/**
 * Registra el historial de movimientos de inventario (kardex) y mantiene
 * el costo promedio ponderado por producto/bodega.
 *
 * Deliberadamente NO toca `inventarios.stock`: cada controlador ya valida y
 * actualiza el stock con su propia lógica (bloqueos, reversos, etc.). Este
 * servicio solo necesita el stock antes/después (que el llamador ya conoce)
 * para calcular el costo y dejar la fila histórica, evitando duplicar o
 * arriesgar la lógica de stock que ya está en producción.
 */
class KardexService
{
    /**
     * @param  string  $tipo  'ENTRADA' o 'SALIDA'
     * @param  float|null  $costoUnitarioEntrada  Costo real de la entrada (compra, ajuste con precio, etc.).
     *                                             Si es null se usa el costo promedio actual (p. ej. traslados).
     */
    public function registrar(
        int $documentoId,
        ?int $documentoDetalleId,
        int $productoId,
        int $bodegaId,
        string $tipo,
        float $cantidad,
        float $stockAnterior,
        float $stockNuevo,
        ?float $costoUnitarioEntrada,
        int $userId,
        $fecha = null
    ): MovimientoInventario {
        return DB::transaction(function () use ($documentoId, $documentoDetalleId, $productoId, $bodegaId, $tipo, $cantidad, $stockAnterior, $stockNuevo, $costoUnitarioEntrada, $userId, $fecha) {
            // El inventario debería existir ya (los controladores lo crean antes de
            // mover el stock), pero se autocompleta por robustez si no es el caso.
            $inventario = Inventario::firstOrCreate(
                ['producto_id' => $productoId, 'bodega_id' => $bodegaId],
                ['stock' => 0, 'costo_promedio' => 0]
            );
            $inventario = Inventario::where('id', $inventario->id)->lockForUpdate()->first();

            $costoPromedioAnterior = (float) ($inventario->costo_promedio ?? 0);

            if ($tipo === 'ENTRADA') {
                $costoUnitario = $costoUnitarioEntrada ?? $costoPromedioAnterior;
                $valorAnterior = $stockAnterior * $costoPromedioAnterior;
                $valorEntrada = $cantidad * $costoUnitario;
                $costoPromedioNuevo = $stockNuevo > 0.00001
                    ? ($valorAnterior + $valorEntrada) / $stockNuevo
                    : 0.0;
                $valorMovimiento = $valorEntrada;
            } else {
                // SALIDA: sale al costo promedio vigente; el promedio no cambia.
                $costoUnitario = $costoPromedioAnterior;
                $costoPromedioNuevo = $costoPromedioAnterior;
                $valorMovimiento = $cantidad * $costoUnitario;
            }

            $inventario->update(['costo_promedio' => round($costoPromedioNuevo, 4)]);

            return MovimientoInventario::create([
                'documento_id' => $documentoId,
                'documento_detalle_id' => $documentoDetalleId,
                'producto_id' => $productoId,
                'bodega_id' => $bodegaId,
                'tipo' => $tipo,
                'cantidad' => $cantidad,
                'stock_anterior' => $stockAnterior,
                'stock_nuevo' => $stockNuevo,
                'costo_unitario' => round($costoUnitario, 4),
                'costo_promedio_nuevo' => round($costoPromedioNuevo, 4),
                'valor_movimiento' => round($valorMovimiento, 2),
                'fecha' => $fecha ?? now(),
                'user_id' => $userId,
            ]);
        });
    }
}
