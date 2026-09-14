<?php

namespace App\Services;

use App\Models\ComprobanteContable;
use App\Models\ConfiguracionContable;
use App\Models\Factura;
use App\Models\MovimientoContable;
use App\Models\ProcesoContable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Provisión (deterioro) de cartera: castiga contablemente, mes a mes, el
 * riesgo de que la cartera vieja nunca se cobre — sin esperar a que se
 * confirme la pérdida. Se calcula por antigüedad de cada factura a crédito
 * pendiente y solo se contabiliza el AJUSTE contra el saldo de provisión que
 * ya existe (nunca el valor total de nuevo), para no duplicar el gasto mes
 * tras mes.
 */
class ProvisionCarteraService
{
    /** Política de provisión por antigüedad (días de mora → % a provisionar). */
    private const TRAMOS = [
        ['desde' => 0, 'hasta' => 30, 'porcentaje' => 0],
        ['desde' => 31, 'hasta' => 60, 'porcentaje' => 10],
        ['desde' => 61, 'hasta' => 90, 'porcentaje' => 20],
        ['desde' => 91, 'hasta' => 180, 'porcentaje' => 50],
        ['desde' => 181, 'hasta' => null, 'porcentaje' => 100],
    ];

    public function __construct(private ReporteContableService $reportes)
    {
    }

    /** Calcula (sin contabilizar) la provisión requerida a una fecha de corte. */
    public function calcular(string $fecha): array
    {
        $facturas = Factura::where('metodo_pago', 'credito')
            ->where('saldo_pendiente', '>', 0)
            ->with('cliente:id,razon_social,nombre,apellido')
            ->get();

        $detalle = [];
        $totalProvision = 0.0;
        $totalCartera = 0.0;

        foreach ($facturas as $factura) {
            // Cálculo directo por timestamps (en vez de Carbon::diffInDays) para
            // no depender de la convención de signo de esa función según la
            // dirección de la comparación — aquí solo interesan los días
            // transcurridos entre la creación de la factura y la fecha de corte.
            $dias = max(0, (int) floor((strtotime($fecha) - strtotime($factura->created_at)) / 86400));
            $tramo = $this->tramoPara($dias);
            $saldo = (float) $factura->saldo_pendiente;
            $provision = round($saldo * $tramo['porcentaje'] / 100, 2);

            $totalCartera += $saldo;
            $totalProvision += $provision;

            if ($provision > 0) {
                $detalle[] = [
                    'factura' => $factura->numero_factura,
                    'cliente' => $factura->cliente?->razon_social ?: trim(($factura->cliente?->nombre ?? '') . ' ' . ($factura->cliente?->apellido ?? '')),
                    'dias_mora' => $dias,
                    'saldo' => $saldo,
                    'porcentaje' => $tramo['porcentaje'],
                    'provision' => $provision,
                ];
            }
        }

        return [
            'fecha' => $fecha,
            'total_cartera' => round($totalCartera, 2),
            'total_provision_requerida' => round($totalProvision, 2),
            'detalle' => $detalle,
        ];
    }

    private function tramoPara(int $diasMora): array
    {
        foreach (self::TRAMOS as $tramo) {
            if ($diasMora >= $tramo['desde'] && ($tramo['hasta'] === null || $diasMora <= $tramo['hasta'])) {
                return $tramo;
            }
        }
        return end(self::TRAMOS);
    }

    /** Saldo actual (crédito - débito) de la cuenta de provisión, a la fecha dada. */
    public function saldoProvisionActual(string $fecha): float
    {
        $cuentaId = ConfiguracionContable::where('clave', 'CUENTA_PROVISION_CARTERA')->value('cuenta_contable_id');
        if (!$cuentaId) return 0.0;

        $fila = DB::table('movimientos_contables as m')
            ->join('comprobantes_contables as c', 'c.id', '=', 'm.comprobante_contable_id')
            ->where('m.cuenta_contable_id', $cuentaId)
            ->whereIn('c.estado', ['REGISTRADO', 'CONTABILIZADO'])
            // DATE(...) normaliza la comparación cuando 'fecha' trae hora (el
            // cast 'datetime' del modelo la guarda con hora incluso en una
            // columna DATE bajo SQLite): sin esto, un comprobante fechado el
            // mismo día que $fecha quedaba fuera del corte por comparación de
            // texto ("...12 00:00:00" no es <= "...12").
            ->whereRaw('DATE(c.fecha) <= ?', [$fecha])
            ->selectRaw('COALESCE(SUM(m.credito),0) as cr, COALESCE(SUM(m.debito),0) as db')
            ->first();

        return round((float) $fila->cr - (float) $fila->db, 2);
    }

    /**
     * Contabiliza el ajuste (aumento o disminución) entre la provisión
     * requerida hoy y la que ya está contabilizada. Si no hay diferencia
     * material, no crea comprobante (nada que ajustar).
     */
    public function contabilizarAjuste(string $fecha, int $usuarioId): array
    {
        return DB::transaction(function () use ($fecha, $usuarioId) {
            $calculo = $this->calcular($fecha);
            $actual = $this->saldoProvisionActual($fecha);
            $ajuste = round($calculo['total_provision_requerida'] - $actual, 2);

            if (abs($ajuste) <= 0.004) {
                return ['ajuste' => 0.0, 'comprobante' => null, 'calculo' => $calculo];
            }

            $cuentaProvision = $this->configuracion('CUENTA_PROVISION_CARTERA');
            $cuentaGasto = $this->configuracion('CUENTA_GASTO_PROVISION_CARTERA');

            $comprobante = $this->crearComprobante($fecha, $usuarioId, $ajuste > 0
                ? 'Provisión de cartera — aumento del período'
                : 'Provisión de cartera — reversión del período');

            if ($ajuste > 0) {
                $this->movimiento($comprobante, $cuentaGasto->cuenta_contable_id, $ajuste, 0, 'Aumento de provisión de cartera');
                $this->movimiento($comprobante, $cuentaProvision->cuenta_contable_id, 0, $ajuste, 'Aumento de provisión de cartera');
            } else {
                $valor = abs($ajuste);
                $this->movimiento($comprobante, $cuentaProvision->cuenta_contable_id, $valor, 0, 'Reversión de provisión de cartera');
                $this->movimiento($comprobante, $cuentaGasto->cuenta_contable_id, 0, $valor, 'Reversión de provisión de cartera');
            }

            $comprobante->update(['estado' => 'CONTABILIZADO']);

            return ['ajuste' => $ajuste, 'comprobante' => $comprobante->fresh(), 'calculo' => $calculo];
        });
    }

    private function configuracion(string $clave): ConfiguracionContable
    {
        $c = ConfiguracionContable::where('clave', $clave)->where('estado', true)->first();
        if (!$c?->cuenta_contable_id) throw ValidationException::withMessages(['contabilidad' => "La configuración contable {$clave} no tiene una cuenta asignada."]);
        return $c;
    }

    private function crearComprobante(string $fecha, int $usuarioId, string $observacion): ComprobanteContable
    {
        $proceso = ProcesoContable::with('tipoDocumento')->where('codigo', 'AJUSTE_INVENTARIO')->where('estado', true)->first();
        // Se reutiliza el tipo de documento de Ajustes (AJ) — la provisión de
        // cartera es, en esencia, un ajuste contable más; no amerita un tipo
        // de comprobante ni un proceso nuevos solo para esto.
        $tipo = $proceso?->tipoDocumento ?? \App\Models\TipoDocumentoContable::where('codigo', 'AJ')->firstOrFail();
        $tipo = $tipo->newQuery()->lockForUpdate()->findOrFail($tipo->id);
        $numero = $tipo->prefijo . str_pad($tipo->consecutivo, $tipo->longitud, '0', STR_PAD_LEFT);
        $comprobante = ComprobanteContable::create([
            'tipo_documento_contable_id' => $tipo->id,
            'numero' => $numero,
            'fecha' => $fecha,
            'observacion' => $observacion,
            'usuario_id' => $usuarioId,
            'documento_origen' => 'PROVISION_CARTERA',
            'documento_origen_id' => null,
            'estado' => 'BORRADOR',
        ]);
        $tipo->increment('consecutivo');

        return $comprobante;
    }

    private function movimiento(ComprobanteContable $comprobante, int $cuentaContableId, float $debito, float $credito, string $detalle): void
    {
        if (round($debito + $credito, 2) <= 0) return;
        MovimientoContable::create([
            'comprobante_contable_id' => $comprobante->id,
            'cuenta_contable_id' => $cuentaContableId,
            'referencia' => $comprobante->documento_origen,
            'detalle' => $detalle,
            'debito' => $debito,
            'credito' => $credito,
        ]);
    }
}
