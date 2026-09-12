<?php

namespace App\Services;

use App\Models\CuentaContable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Motor de informes contables. Toda consulta parte de `movimientos_contables`
 * unida a `comprobantes_contables`, filtrando siempre por comprobantes
 * REGISTRADO/CONTABILIZADO (los BORRADOR no son contabilidad real todavía,
 * y los ANULADO ya vienen neteados por su propia reversión).
 */
class ReporteContableService
{
    private const ESTADOS_VALIDOS = ['REGISTRADO', 'CONTABILIZADO'];

    /**
     * Suma neta de movimientos hasta (o entre) fechas, por cuenta.
     * Devuelve un query base reutilizable por los demás métodos.
     */
    private function baseMovimientos()
    {
        return DB::table('movimientos_contables as m')
            ->join('comprobantes_contables as c', 'c.id', '=', 'm.comprobante_contable_id')
            ->whereIn('c.estado', self::ESTADOS_VALIDOS);
    }

    /* ════════════════════════════════════════════════
       BALANCE DE PRUEBA
    ════════════════════════════════════════════════ */
    public function balancePrueba(string $desde, string $hasta): array
    {
        $saldosIniciales = $this->baseMovimientos()
            ->where('c.fecha', '<', $desde)
            ->select('m.cuenta_contable_id', DB::raw('SUM(m.debito) as debito'), DB::raw('SUM(m.credito) as credito'))
            ->groupBy('m.cuenta_contable_id')
            ->get()
            ->keyBy('cuenta_contable_id');

        $movimientosPeriodo = $this->baseMovimientos()
            ->whereBetween('c.fecha', [$desde, $hasta])
            ->select('m.cuenta_contable_id', DB::raw('SUM(m.debito) as debito'), DB::raw('SUM(m.credito) as credito'))
            ->groupBy('m.cuenta_contable_id')
            ->get()
            ->keyBy('cuenta_contable_id');

        $cuentaIds = $saldosIniciales->keys()->merge($movimientosPeriodo->keys())->unique();

        $cuentas = CuentaContable::whereIn('id', $cuentaIds)->orderBy('codigo')->get()->keyBy('id');

        $filas = $cuentaIds->map(function ($id) use ($cuentas, $saldosIniciales, $movimientosPeriodo) {
            $cuenta = $cuentas->get($id);
            if (!$cuenta) return null;

            $siDeb = (float) ($saldosIniciales->get($id)->debito ?? 0);
            $siCre = (float) ($saldosIniciales->get($id)->credito ?? 0);
            $mpDeb = (float) ($movimientosPeriodo->get($id)->debito ?? 0);
            $mpCre = (float) ($movimientosPeriodo->get($id)->credito ?? 0);

            $netoInicial = round($siDeb - $siCre, 2);
            $netoFinal = round($siDeb + $mpDeb - $siCre - $mpCre, 2);

            return [
                'cuenta_id' => $cuenta->id,
                'codigo' => $cuenta->codigo,
                'nombre' => $cuenta->nombre,
                'clasificacion' => $cuenta->clasificacion,
                'naturaleza' => $cuenta->naturaleza,
                'saldo_inicial_debito' => $netoInicial > 0 ? $netoInicial : 0,
                'saldo_inicial_credito' => $netoInicial < 0 ? -$netoInicial : 0,
                'movimiento_debito' => round($mpDeb, 2),
                'movimiento_credito' => round($mpCre, 2),
                'saldo_final_debito' => $netoFinal > 0 ? $netoFinal : 0,
                'saldo_final_credito' => $netoFinal < 0 ? -$netoFinal : 0,
            ];
        })->filter()->sortBy('codigo')->values();

        return [
            'filas' => $filas,
            'totales' => [
                'saldo_inicial_debito' => round($filas->sum('saldo_inicial_debito'), 2),
                'saldo_inicial_credito' => round($filas->sum('saldo_inicial_credito'), 2),
                'movimiento_debito' => round($filas->sum('movimiento_debito'), 2),
                'movimiento_credito' => round($filas->sum('movimiento_credito'), 2),
                'saldo_final_debito' => round($filas->sum('saldo_final_debito'), 2),
                'saldo_final_credito' => round($filas->sum('saldo_final_credito'), 2),
            ],
        ];
    }

    /* ════════════════════════════════════════════════
       LIBRO DIARIO
    ════════════════════════════════════════════════ */
    public function libroDiario(string $desde, string $hasta, ?string $tipo = null): Collection
    {
        $query = $this->baseMovimientos()
            ->whereBetween('c.fecha', [$desde, $hasta])
            ->join('cuentas_contables as cc', 'cc.id', '=', 'm.cuenta_contable_id')
            ->leftJoin('terceros as t', 't.id', '=', 'm.tercero_id')
            ->select(
                'c.id as comprobante_id',
                'c.tipo',
                'c.prefijo',
                'c.numero',
                'c.fecha',
                'c.descripcion',
                'c.observacion',
                'cc.codigo as cuenta_codigo',
                'cc.nombre as cuenta_nombre',
                'm.detalle',
                'm.debito',
                'm.credito',
                't.razon_social', 't.nombre as tercero_nombre', 't.apellido as tercero_apellido'
            );

        if ($tipo) {
            $query->where('c.tipo', $tipo);
        }

        return $query->orderBy('c.fecha')->orderBy('c.id')->orderBy('m.id')->get()->each(function ($m) {
            $m->tercero = $this->nombreTercero($m);
        });
    }

    /** Concatena el nombre del tercero en PHP (portable entre MySQL/SQLite, a diferencia de CONCAT en SQL crudo). */
    private function nombreTercero(object $fila): ?string
    {
        if (!empty($fila->razon_social)) return $fila->razon_social;
        $nombre = trim(($fila->tercero_nombre ?? '') . ' ' . ($fila->tercero_apellido ?? ''));
        return $nombre !== '' ? $nombre : null;
    }

    /* ════════════════════════════════════════════════
       LIBRO MAYOR (una cuenta, o todas resumidas)
    ════════════════════════════════════════════════ */
    public function libroMayor(string $desde, string $hasta, int $cuentaId): array
    {
        $cuenta = CuentaContable::findOrFail($cuentaId);

        $saldoInicial = $this->baseMovimientos()
            ->where('c.fecha', '<', $desde)
            ->where('m.cuenta_contable_id', $cuentaId)
            ->selectRaw('SUM(m.debito) as debito, SUM(m.credito) as credito')
            ->first();

        $netoInicial = round((float) ($saldoInicial->debito ?? 0) - (float) ($saldoInicial->credito ?? 0), 2);

        $movimientos = $this->baseMovimientos()
            ->where('m.cuenta_contable_id', $cuentaId)
            ->whereBetween('c.fecha', [$desde, $hasta])
            ->leftJoin('terceros as t', 't.id', '=', 'm.tercero_id')
            ->select(
                'c.id as comprobante_id',
                'c.tipo',
                'c.prefijo',
                'c.numero',
                'c.fecha',
                'm.detalle',
                'm.debito',
                'm.credito',
                't.razon_social', 't.nombre as tercero_nombre', 't.apellido as tercero_apellido'
            )
            ->orderBy('c.fecha')->orderBy('c.id')->orderBy('m.id')
            ->get();

        $saldo = $netoInicial;
        $filas = $movimientos->map(function ($m) use (&$saldo) {
            $saldo = round($saldo + (float) $m->debito - (float) $m->credito, 2);
            $m->saldo = $saldo;
            $m->tercero = $this->nombreTercero($m);
            return $m;
        });

        return [
            'cuenta' => $cuenta,
            'saldo_inicial' => $netoInicial,
            'movimientos' => $filas,
            'total_debito' => round($filas->sum('debito'), 2),
            'total_credito' => round($filas->sum('credito'), 2),
            'saldo_final' => $saldo,
        ];
    }

    /* ════════════════════════════════════════════════
       LIBRO AUXILIAR (una cuenta + opcional un tercero)
    ════════════════════════════════════════════════ */
    public function libroAuxiliar(string $desde, string $hasta, int $cuentaId, ?int $terceroId = null): array
    {
        $cuenta = CuentaContable::findOrFail($cuentaId);

        $baseInicial = $this->baseMovimientos()
            ->where('c.fecha', '<', $desde)
            ->where('m.cuenta_contable_id', $cuentaId);
        if ($terceroId) $baseInicial->where('m.tercero_id', $terceroId);
        $saldoInicial = $baseInicial->selectRaw('SUM(m.debito) as debito, SUM(m.credito) as credito')->first();
        $netoInicial = round((float) ($saldoInicial->debito ?? 0) - (float) ($saldoInicial->credito ?? 0), 2);

        $query = $this->baseMovimientos()
            ->where('m.cuenta_contable_id', $cuentaId)
            ->whereBetween('c.fecha', [$desde, $hasta])
            ->leftJoin('terceros as t', 't.id', '=', 'm.tercero_id')
            ->leftJoin('centros_costo as cco', 'cco.id', '=', 'm.centro_costo_id')
            ->select(
                'c.id as comprobante_id',
                'c.tipo',
                'c.prefijo',
                'c.numero',
                'c.fecha',
                'm.detalle',
                'm.referencia',
                'm.documento_referencia',
                'm.debito',
                'm.credito',
                'm.tercero_id',
                't.razon_social', 't.nombre as tercero_nombre', 't.apellido as tercero_apellido',
                'cco.nombre as centro_costo'
            );

        if ($terceroId) $query->where('m.tercero_id', $terceroId);

        $movimientos = $query->orderBy('c.fecha')->orderBy('c.id')->orderBy('m.id')->get();

        $saldo = $netoInicial;
        $filas = $movimientos->map(function ($m) use (&$saldo) {
            $saldo = round($saldo + (float) $m->debito - (float) $m->credito, 2);
            $m->saldo = $saldo;
            $m->tercero = $this->nombreTercero($m);
            return $m;
        });

        return [
            'cuenta' => $cuenta,
            'saldo_inicial' => $netoInicial,
            'movimientos' => $filas,
            'total_debito' => round($filas->sum('debito'), 2),
            'total_credito' => round($filas->sum('credito'), 2),
            'saldo_final' => $saldo,
        ];
    }

    /* ════════════════════════════════════════════════
       ESTADO DE RESULTADOS (INGRESO / COSTO / GASTO)
    ════════════════════════════════════════════════ */
    public function estadoResultados(string $desde, string $hasta): array
    {
        $movimientos = $this->baseMovimientos()
            ->whereBetween('c.fecha', [$desde, $hasta])
            ->join('cuentas_contables as cc', 'cc.id', '=', 'm.cuenta_contable_id')
            ->whereIn('cc.clasificacion', ['INGRESO', 'COSTO', 'GASTO'])
            ->select('cc.id', 'cc.codigo', 'cc.nombre', 'cc.clasificacion', DB::raw('SUM(m.debito) as debito'), DB::raw('SUM(m.credito) as credito'))
            ->groupBy('cc.id', 'cc.codigo', 'cc.nombre', 'cc.clasificacion')
            ->orderBy('cc.codigo')
            ->get();

        $grupo = function (string $clasificacion, bool $creditoEsPositivo) use ($movimientos) {
            $cuentas = $movimientos->where('clasificacion', $clasificacion)->map(function ($m) use ($creditoEsPositivo) {
                $valor = $creditoEsPositivo
                    ? round((float) $m->credito - (float) $m->debito, 2)
                    : round((float) $m->debito - (float) $m->credito, 2);
                return ['codigo' => $m->codigo, 'nombre' => $m->nombre, 'valor' => $valor];
            })->values();
            return ['cuentas' => $cuentas, 'total' => round($cuentas->sum('valor'), 2)];
        };

        $ingresos = $grupo('INGRESO', true);
        $costos = $grupo('COSTO', false);
        $gastos = $grupo('GASTO', false);

        $utilidadBruta = round($ingresos['total'] - $costos['total'], 2);
        $utilidadNeta = round($utilidadBruta - $gastos['total'], 2);

        return compact('ingresos', 'costos', 'gastos', 'utilidadBruta', 'utilidadNeta');
    }

    /* ════════════════════════════════════════════════
       BALANCE GENERAL (a una fecha de corte)
    ════════════════════════════════════════════════ */
    public function balanceGeneral(string $hasta): array
    {
        $saldos = $this->baseMovimientos()
            ->where('c.fecha', '<=', $hasta)
            ->join('cuentas_contables as cc', 'cc.id', '=', 'm.cuenta_contable_id')
            ->select('cc.id', 'cc.codigo', 'cc.nombre', 'cc.clasificacion', DB::raw('SUM(m.debito) as debito'), DB::raw('SUM(m.credito) as credito'))
            ->groupBy('cc.id', 'cc.codigo', 'cc.nombre', 'cc.clasificacion')
            ->orderBy('cc.codigo')
            ->get();

        $grupo = function (string $clasificacion, bool $creditoEsPositivo) use ($saldos) {
            $cuentas = $saldos->where('clasificacion', $clasificacion)->map(function ($m) use ($creditoEsPositivo) {
                $valor = $creditoEsPositivo
                    ? round((float) $m->credito - (float) $m->debito, 2)
                    : round((float) $m->debito - (float) $m->credito, 2);
                return ['codigo' => $m->codigo, 'nombre' => $m->nombre, 'valor' => $valor];
            })->filter(fn ($c) => abs($c['valor']) > 0.004)->values();
            return ['cuentas' => $cuentas, 'total' => round($cuentas->sum('valor'), 2)];
        };

        $activo = $grupo('ACTIVO', false);
        $pasivo = $grupo('PASIVO', true);
        $patrimonio = $grupo('PATRIMONIO', true);

        // Resultado acumulado del ejercicio (ingresos - costos - gastos, histórico hasta la fecha de corte).
        // Al no existir todavía cierres de período, esta utilidad/pérdida se muestra como una
        // línea de patrimonio para que la ecuación contable siempre cuadre.
        $resultado = $this->estadoResultados('0001-01-01', $hasta)['utilidadNeta'];

        $totalPasivoPatrimonio = round($pasivo['total'] + $patrimonio['total'] + $resultado, 2);
        $diferencia = round($activo['total'] - $totalPasivoPatrimonio, 2);

        return [
            'activo' => $activo,
            'pasivo' => $pasivo,
            'patrimonio' => $patrimonio,
            'resultadoEjercicio' => $resultado,
            'totalPasivoPatrimonio' => $totalPasivoPatrimonio,
            'cuadrado' => abs($diferencia) < 0.01,
            'diferencia' => $diferencia,
        ];
    }
}
