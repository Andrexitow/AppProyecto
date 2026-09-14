<?php

namespace App\Services;

use App\Models\ComprobanteContable;
use App\Models\CuentaContable;
use App\Models\DetalleLiquidacionNomina;
use App\Models\Empleado;
use App\Models\LiquidacionNomina;
use App\Models\MovimientoContable;
use App\Models\ParametroNomina;
use App\Models\ProcesoContable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Liquidación mensual de nómina: calcula devengados, deducciones y aportes
 * patronales de cada empleado activo, y los contabiliza en un único
 * comprobante (gasto de personal + pasivos laborales por pagar). No genera
 * ni envía nómina electrónica a la DIAN — eso requiere un proveedor
 * tecnológico autorizado que este sistema no tiene contratado; esto cubre la
 * liquidación interna y su registro contable.
 */
class NominaService
{
    /** Calcula (sin persistir) todos los conceptos de un empleado para un período. */
    public function calcular(Empleado $empleado, ParametroNomina $p): array
    {
        $salario = (float) $empleado->salario_base;
        $auxTransporte = $salario <= (float) $p->smmlv * 2 ? (float) $p->auxilio_transporte : 0.0;
        $baseCesantiasPrima = $salario + $auxTransporte;

        $cesantias = round($baseCesantiasPrima * (float) $p->cesantias_pct / 100, 2);
        $interesesCesantias = round($cesantias * (float) $p->intereses_cesantias_pct / 100, 2);
        $prima = round($baseCesantiasPrima * (float) $p->prima_pct / 100, 2);
        $vacaciones = round($salario * (float) $p->vacaciones_pct / 100, 2);

        $saludEmpleado = round($salario * (float) $p->salud_empleado_pct / 100, 2);
        $pensionEmpleado = round($salario * (float) $p->pension_empleado_pct / 100, 2);
        $saludPatronal = round($salario * (float) $p->salud_patronal_pct / 100, 2);
        $pensionPatronal = round($salario * (float) $p->pension_patronal_pct / 100, 2);
        $arl = round($salario * (float) $empleado->arl_tarifa / 100, 2);
        $sena = round($salario * (float) $p->sena_pct / 100, 2);
        $icbf = round($salario * (float) $p->icbf_pct / 100, 2);
        $cajaCompensacion = round($salario * (float) $p->caja_compensacion_pct / 100, 2);

        $netoPagado = round($salario + $auxTransporte - $saludEmpleado - $pensionEmpleado, 2);

        return compact(
            'salario', 'auxTransporte', 'cesantias', 'interesesCesantias', 'prima', 'vacaciones',
            'saludEmpleado', 'pensionEmpleado', 'saludPatronal', 'pensionPatronal', 'arl', 'sena',
            'icbf', 'cajaCompensacion', 'netoPagado'
        );
    }

    public function liquidarPeriodo(string $periodo, string $fechaPago, int $usuarioId): LiquidacionNomina
    {
        return DB::transaction(function () use ($periodo, $fechaPago, $usuarioId) {
            if (LiquidacionNomina::where('periodo', $periodo)->where('estado', 'REGISTRADA')->exists()) {
                throw ValidationException::withMessages(['periodo' => "Ya existe una liquidación registrada para {$periodo}. Anúlela primero si necesita rehacerla."]);
            }

            $empleados = Empleado::where('estado', 'activo')->get();
            if ($empleados->isEmpty()) {
                throw ValidationException::withMessages(['empleados' => 'No hay empleados activos para liquidar.']);
            }

            $parametros = ParametroNomina::vigente();
            $cuentas = $this->cuentasNomina();

            $comprobante = $this->crearComprobante('NOMINA_MENSUAL', 'NOMINA_MENSUAL', $fechaPago, $usuarioId, "Liquidación de nómina — período {$periodo}");

            $acumulado = [
                'devengado' => 0.0, 'deducciones' => 0.0, 'neto' => 0.0, 'patronales' => 0.0,
                'gasto' => array_fill_keys(array_keys($cuentas['gasto']), 0.0),
                'pasivo' => array_fill_keys(array_keys($cuentas['pasivo']), 0.0),
            ];

            $liquidacion = LiquidacionNomina::create([
                'periodo' => $periodo,
                'fecha_pago' => $fechaPago,
                'comprobante_contable_id' => $comprobante->id,
                'usuario_id' => $usuarioId,
                'estado' => 'REGISTRADA',
            ]);

            foreach ($empleados as $empleado) {
                $c = $this->calcular($empleado, $parametros);

                DetalleLiquidacionNomina::create([
                    'liquidacion_nomina_id' => $liquidacion->id,
                    'empleado_id' => $empleado->id,
                    'salario_devengado' => $c['salario'],
                    'auxilio_transporte' => $c['auxTransporte'],
                    'cesantias' => $c['cesantias'],
                    'intereses_cesantias' => $c['interesesCesantias'],
                    'prima' => $c['prima'],
                    'vacaciones' => $c['vacaciones'],
                    'salud_empleado' => $c['saludEmpleado'],
                    'pension_empleado' => $c['pensionEmpleado'],
                    'salud_patronal' => $c['saludPatronal'],
                    'pension_patronal' => $c['pensionPatronal'],
                    'arl' => $c['arl'],
                    'sena' => $c['sena'],
                    'icbf' => $c['icbf'],
                    'caja_compensacion' => $c['cajaCompensacion'],
                    'neto_pagado' => $c['netoPagado'],
                ]);

                $acumulado['gasto']['sueldos'] += $c['salario'];
                $acumulado['gasto']['auxilio_transporte'] += $c['auxTransporte'];
                $acumulado['gasto']['cesantias'] += $c['cesantias'];
                $acumulado['gasto']['intereses_cesantias'] += $c['interesesCesantias'];
                $acumulado['gasto']['prima'] += $c['prima'];
                $acumulado['gasto']['vacaciones'] += $c['vacaciones'];
                $acumulado['gasto']['arl'] += $c['arl'];
                $acumulado['gasto']['eps_patronal'] += $c['saludPatronal'];
                $acumulado['gasto']['pension_patronal'] += $c['pensionPatronal'];
                $acumulado['gasto']['parafiscales'] += $c['sena'] + $c['icbf'] + $c['cajaCompensacion'];

                $acumulado['pasivo']['salarios_por_pagar'] += $c['netoPagado'];
                $acumulado['pasivo']['cesantias'] += $c['cesantias'];
                $acumulado['pasivo']['intereses_cesantias'] += $c['interesesCesantias'];
                $acumulado['pasivo']['prima'] += $c['prima'];
                $acumulado['pasivo']['vacaciones'] += $c['vacaciones'];
                $acumulado['pasivo']['eps'] += $c['saludEmpleado'] + $c['saludPatronal'];
                $acumulado['pasivo']['pension'] += $c['pensionEmpleado'] + $c['pensionPatronal'];
                $acumulado['pasivo']['arl'] += $c['arl'];
                $acumulado['pasivo']['sena'] += $c['sena'];
                $acumulado['pasivo']['icbf'] += $c['icbf'];
                $acumulado['pasivo']['caja_compensacion'] += $c['cajaCompensacion'];

                $acumulado['devengado'] += $c['salario'] + $c['auxTransporte'];
                $acumulado['deducciones'] += $c['saludEmpleado'] + $c['pensionEmpleado'];
                $acumulado['neto'] += $c['netoPagado'];
                $acumulado['patronales'] += $c['saludPatronal'] + $c['pensionPatronal'] + $c['arl'] + $c['sena'] + $c['icbf'] + $c['cajaCompensacion'];
            }

            foreach ($acumulado['gasto'] as $clave => $valor) {
                $this->movimiento($comprobante, $cuentas['gasto'][$clave]->id, round($valor, 2), 0, null, 'Nómina ' . $periodo);
            }
            foreach ($acumulado['pasivo'] as $clave => $valor) {
                $this->movimiento($comprobante, $cuentas['pasivo'][$clave]->id, 0, round($valor, 2), null, 'Nómina ' . $periodo);
            }

            $comprobante->update(['estado' => 'CONTABILIZADO']);

            $liquidacion->update([
                'total_devengado' => round($acumulado['devengado'], 2),
                'total_deducciones' => round($acumulado['deducciones'], 2),
                'total_neto' => round($acumulado['neto'], 2),
                'total_aportes_patronales' => round($acumulado['patronales'], 2),
            ]);

            return $liquidacion->fresh(['detalles.empleado']);
        });
    }

    public function anular(LiquidacionNomina $liquidacion, string $motivo, int $usuarioId): void
    {
        if ($liquidacion->estado !== 'REGISTRADA') {
            throw ValidationException::withMessages(['liquidacion' => 'Esta liquidación ya está anulada.']);
        }

        DB::transaction(function () use ($liquidacion, $motivo, $usuarioId) {
            // El update() masivo no dispara los eventos de Eloquent (a diferencia
            // de $modelo->update()), así que el bloqueo de período cerrado no se
            // activaría solo: se valida aquí explícitamente, igual que en
            // AjusteContableService::anular().
            if ($liquidacion->comprobante) {
                app(PeriodoContableService::class)->assertAbierto($liquidacion->comprobante->fecha->toDateString());
                ComprobanteContable::where('id', $liquidacion->comprobante_contable_id)->where('estado', 'CONTABILIZADO')->update(['estado' => 'ANULADO']);
            }
            $liquidacion->update(['estado' => 'ANULADA', 'motivo_anulacion' => $motivo]);
        });
    }

    /** Resuelve (y cachea en memoria de request) las cuentas contables usadas por la nómina. */
    private function cuentasNomina(): array
    {
        $codigo = fn (string $c) => CuentaContable::where('codigo', $c)->firstOrFail();

        return [
            'gasto' => [
                'sueldos' => $codigo('510506'),
                'auxilio_transporte' => $codigo('510527'),
                'cesantias' => $codigo('510530'),
                'intereses_cesantias' => $codigo('510533'),
                'prima' => $codigo('510536'),
                'vacaciones' => $codigo('510539'),
                'arl' => $codigo('510568'),
                'eps_patronal' => $codigo('510569'),
                'pension_patronal' => $codigo('510570'),
                'parafiscales' => $codigo('510572'),
            ],
            'pasivo' => [
                'salarios_por_pagar' => $codigo('250505'),
                'cesantias' => $codigo('251005'),
                'intereses_cesantias' => $codigo('251505'),
                'prima' => $codigo('252005'),
                'vacaciones' => $codigo('252505'),
                'eps' => $codigo('253505'),
                'pension' => $codigo('253510'),
                'arl' => $codigo('253515'),
                'sena' => $codigo('254005'),
                'icbf' => $codigo('254010'),
                'caja_compensacion' => $codigo('254015'),
            ],
        ];
    }

    private function crearComprobante(string $procesoCodigo, string $origen, string $fecha, int $usuarioId, string $observacion): ComprobanteContable
    {
        $proceso = ProcesoContable::with('tipoDocumento')->where('codigo', $procesoCodigo)->where('estado', true)->firstOrFail();
        $tipo = $proceso->tipoDocumento;
        if (!$tipo) throw ValidationException::withMessages(['contabilidad' => "El proceso {$procesoCodigo} no tiene tipo de comprobante."]);
        $tipo = $tipo->newQuery()->lockForUpdate()->findOrFail($tipo->id);
        $numero = $tipo->prefijo . str_pad($tipo->consecutivo, $tipo->longitud, '0', STR_PAD_LEFT);
        $comprobante = ComprobanteContable::create([
            'tipo_documento_contable_id' => $tipo->id,
            'proceso_contable_id' => $proceso->id,
            'numero' => $numero,
            'fecha' => $fecha,
            'observacion' => $observacion,
            'usuario_id' => $usuarioId,
            'documento_origen' => $origen,
            'documento_origen_id' => null,
            'estado' => 'BORRADOR',
        ]);
        $tipo->increment('consecutivo');

        return $comprobante;
    }

    private function movimiento(ComprobanteContable $comprobante, int $cuentaContableId, float $debito, float $credito, ?int $terceroId, string $detalle): void
    {
        if (round($debito + $credito, 2) <= 0) return;
        MovimientoContable::create([
            'comprobante_contable_id' => $comprobante->id,
            'cuenta_contable_id' => $cuentaContableId,
            'tercero_id' => $terceroId,
            'referencia' => $comprobante->documento_origen,
            'detalle' => $detalle,
            'debito' => $debito,
            'credito' => $credito,
        ]);
    }
}
