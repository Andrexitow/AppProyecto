<?php

namespace App\Services;

use App\Models\Ajuste;
use App\Models\ComprobanteContable;
use App\Models\ConfiguracionContable;
use App\Models\CuentaContable;
use App\Models\MovimientoContable;
use App\Models\ProcesoContable;
use Illuminate\Validation\ValidationException;

class AjusteContableService
{
    public function contabilizar(Ajuste $ajuste): ?ComprobanteContable
    {
        $entradas = (float) $ajuste->detalles->where('tipo', 'entrada')->sum(fn ($d) => $d->cantidad * $d->precio);
        $salidas = (float) $ajuste->detalles->where('tipo', 'salida')->sum(fn ($d) => $d->cantidad * $d->precio);
        if (round($entradas + $salidas, 2) <= 0) return null;
        if (!$ajuste->contraparte_cuenta_id) throw ValidationException::withMessages(['contraparte_cuenta_id' => 'Seleccione la cuenta contable de contrapartida antes de registrar un ajuste con valor.']);

        $proceso = ProcesoContable::with('tipoDocumento')->where('codigo', 'AJUSTE_INVENTARIO')->where('estado', true)->firstOrFail();
        $existente = ComprobanteContable::where('documento_origen', 'AJUSTE')->where('documento_origen_id', $ajuste->id)->where('proceso_contable_id', $proceso->id)->where('estado', 'CONTABILIZADO')->first();
        if ($existente) return $existente;

        $inventario = $this->cuentaConfigurada('CUENTA_INVENTARIO');
        $contraparte = CuentaContable::whereKey($ajuste->contraparte_cuenta_id)->where('estado', true)->where('permite_movimientos', true)->first();
        if (!$contraparte) throw ValidationException::withMessages(['contraparte_cuenta_id' => 'La cuenta de contrapartida no está activa o no permite movimientos.']);
        $tipo = $proceso->tipoDocumento?->newQuery()->lockForUpdate()->find($proceso->tipo_documento_contable_id);
        if (!$tipo) throw ValidationException::withMessages(['contabilidad' => 'El proceso de ajuste no tiene tipo de comprobante configurado.']);

        $numero = $tipo->prefijo . str_pad($tipo->consecutivo, $tipo->longitud, '0', STR_PAD_LEFT);
        $comprobante = ComprobanteContable::create(['tipo_documento_contable_id' => $tipo->id, 'proceso_contable_id' => $proceso->id, 'numero' => $numero, 'fecha' => $ajuste->fecha, 'observacion' => "Ajuste {$ajuste->prefijo}-{$ajuste->numero}", 'usuario_id' => $ajuste->user_id, 'documento_origen' => 'AJUSTE', 'documento_origen_id' => $ajuste->id, 'estado' => 'BORRADOR']);
        $tipo->increment('consecutivo');

        $this->movimiento($comprobante, $inventario, $entradas, 0, $ajuste->tercero_id, 'Entrada por ajuste de inventario');
        $this->movimiento($comprobante, $contraparte, 0, $entradas, $ajuste->tercero_id, 'Contrapartida de entrada por ajuste');
        $this->movimiento($comprobante, $contraparte, $salidas, 0, $ajuste->tercero_id, 'Contrapartida de salida por ajuste');
        $this->movimiento($comprobante, $inventario, 0, $salidas, $ajuste->tercero_id, 'Salida por ajuste de inventario');
        $comprobante->update(['estado' => 'CONTABILIZADO']);
        return $comprobante;
    }

    public function anular(Ajuste $ajuste): void
    {
        // El update() masivo de abajo no dispara los eventos de Eloquent
        // (a diferencia de $modelo->update()), así que el bloqueo de período
        // cerrado del modelo no se activaría solo: se valida aquí explícitamente.
        app(\App\Services\PeriodoContableService::class)->assertAbierto($ajuste->fecha);
        ComprobanteContable::where('documento_origen', 'AJUSTE')->where('documento_origen_id', $ajuste->id)->where('estado', 'CONTABILIZADO')->update(['estado' => 'ANULADO']);
    }

    private function cuentaConfigurada(string $clave): CuentaContable
    {
        $cuenta = ConfiguracionContable::with('cuenta')->where('clave', $clave)->where('estado', true)->first()?->cuenta;
        if (!$cuenta) throw ValidationException::withMessages(['contabilidad' => "La configuración {$clave} no tiene una cuenta asignada."]);
        return $cuenta;
    }

    private function movimiento(ComprobanteContable $comprobante, CuentaContable $cuenta, float $debito, float $credito, ?int $terceroId, string $detalle): void
    {
        if (round($debito + $credito, 2) <= 0) return;
        MovimientoContable::create(['comprobante_contable_id' => $comprobante->id, 'cuenta_contable_id' => $cuenta->id, 'tercero_id' => $terceroId, 'referencia' => $comprobante->documento_origen, 'detalle' => $detalle, 'debito' => $debito, 'credito' => $credito]);
    }
}
