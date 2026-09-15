<?php

namespace App\Services;

use App\Models\{Documento, Inventario, MovimientoInventario};
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/** Reversa trazable al costo del movimiento original, dentro de la operación atómica. */
class ReversionInventarioService
{
    public function ejecutar(int $documentoId, bool $restaurar = false): void
    {
        DB::transaction(function () use ($documentoId, $restaurar) {
            $doc = Documento::lockForUpdate()->findOrFail($documentoId);
            if (($doc->estado === 'anulado') !== $restaurar) {
                throw ValidationException::withMessages(['documento'=>'El documento no está en el estado requerido para esta reversión.']);
            }
            app(PeriodoContableService::class)->assertAbierto($doc->fecha);
            $movimientos = MovimientoInventario::where('documento_id', $doc->id)
                ->whereNotIn('id', MovimientoInventario::whereNotNull('reversa_de_id')->select('reversa_de_id'))
                ->orderByDesc('id')->get();
            foreach ($movimientos as $m) {
                $profundidad = 0;
                $original = $m;
                while ($original->reversa_de_id) {
                    $original = MovimientoInventario::findOrFail($original->reversa_de_id);
                    $profundidad++;
                }
                // Un ciclo ya compensado no vuelve a afectar existencias cuando el documento se registra de nuevo.
                if (!$restaurar && $profundidad % 2 === 1) continue;
                $inv = Inventario::where('producto_id',$m->producto_id)->where('bodega_id',$m->bodega_id)->lockForUpdate()->firstOrFail();
                $entrada = $m->tipo === 'SALIDA';
                $anterior = (float)$inv->stock;
                $nuevo = round($anterior + ($entrada ? 1 : -1) * (float)$m->cantidad, 4);
                if ($nuevo < -0.0001) throw ValidationException::withMessages(['inventario'=>'No hay existencias suficientes para revertir el documento.']);
                $valor = $anterior * (float)$inv->costo_promedio + ($entrada ? 1 : -1) * (float)$m->valor_movimiento;
                if ($valor < -0.02) throw ValidationException::withMessages(['inventario'=>'La reversión dejaría una valoración negativa; revise los movimientos posteriores.']);
                $promedio = $nuevo > 0.0001 ? max(0, round($valor/$nuevo,4)) : 0;
                $inv->update(['stock'=>max(0,$nuevo),'costo_promedio'=>$promedio]);
                $rev = new MovimientoInventario();
                $rev->forceFill(['documento_id'=>$doc->id,'documento_detalle_id'=>$m->documento_detalle_id,'producto_id'=>$m->producto_id,'bodega_id'=>$m->bodega_id,'tipo'=>$entrada?'ENTRADA':'SALIDA','cantidad'=>$m->cantidad,'stock_anterior'=>$anterior,'stock_nuevo'=>$nuevo,'costo_unitario'=>$m->costo_unitario,'valor_movimiento'=>$m->valor_movimiento,'costo_promedio_nuevo'=>$promedio,'fecha'=>now(),'user_id'=>auth()->id() ?? $doc->user_id,'reversa_de_id'=>$m->id])->save();
            }
            $doc->update(['estado'=>$restaurar?'registrado':'anulado','anulado_at'=>$restaurar?null:now()]);
        });
    }
}
