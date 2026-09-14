<?php

namespace App\Services;

use App\Models\ConfiguracionContable;
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

        // El sistema no tiene (todavía) un asiento formal de cierre de ejercicio que
        // traslade la utilidad de un año a "Utilidades Acumuladas" — pero como este
        // motor ya calcula el Estado de Resultados por rango de fechas (no por saldo
        // arrastrado), no hace falta: basta con partir el cálculo en dos rangos para
        // que una contadora vea la utilidad del AÑO EN CURSO separada de lo acumulado
        // en años anteriores, tal como exige la presentación de un balance real.
        // Si alguna vez se registran saldos iniciales u otro ajuste directo sobre la
        // cuenta 3705 (Utilidades Acumuladas), esa suma se incluye aparte para no
        // duplicar ni perder ese valor.
        $inicioAnioActual = substr($hasta, 0, 4) . '-01-01';
        $finAnioAnterior = date('Y-m-d', strtotime($inicioAnioActual . ' -1 day'));

        $utilidadEjercicioActual = $this->estadoResultados($inicioAnioActual, $hasta)['utilidadNeta'];
        $utilidadEjerciciosAnteriores = $finAnioAnterior >= '0001-01-01'
            ? $this->estadoResultados('0001-01-01', $finAnioAnterior)['utilidadNeta']
            : 0.0;

        $saldoDirectoUtilidadesAcumuladas = (float) ($saldos->where('codigo', '370505')->first()->credito ?? 0)
            - (float) ($saldos->where('codigo', '370505')->first()->debito ?? 0);
        $utilidadesAcumuladas = round($utilidadEjerciciosAnteriores + $saldoDirectoUtilidadesAcumuladas, 2);
        $utilidadEjercicioActual = round($utilidadEjercicioActual, 2);

        $resultado = round($utilidadEjercicioActual + $utilidadesAcumuladas, 2);
        $totalPasivoPatrimonio = round($pasivo['total'] + $patrimonio['total'] + $resultado, 2);
        $diferencia = round($activo['total'] - $totalPasivoPatrimonio, 2);

        return [
            'activo' => $activo,
            'pasivo' => $pasivo,
            'patrimonio' => $patrimonio,
            'utilidadEjercicioActual' => $utilidadEjercicioActual,
            'utilidadesAcumuladas' => $utilidadesAcumuladas,
            // Se mantiene por compatibilidad: ahora es la suma de las dos líneas de arriba.
            'resultadoEjercicio' => $resultado,
            'totalPasivoPatrimonio' => $totalPasivoPatrimonio,
            'cuadrado' => abs($diferencia) < 0.01,
            'diferencia' => $diferencia,
        ];
    }

    /* ════════════════════════════════════════════════
       IVA DEL PERÍODO (para el Formulario 300)
    ════════════════════════════════════════════════ */
    public function ivaPeriodo(string $desde, string $hasta): array
    {
        $generado = $this->saldoCuentaClave('CUENTA_IVA_GENERADO', $desde, $hasta, true);
        $descontable = $this->saldoCuentaClave('CUENTA_IVA_DESCONTABLE', $desde, $hasta, false);
        $neto = round($generado - $descontable, 2);

        return [
            'ivaGenerado' => $generado,
            'ivaDescontable' => $descontable,
            'neto' => $neto,
            'aPagar' => $neto > 0.004,
            'saldoAFavor' => $neto < -0.004,
            'valorAbsoluto' => round(abs($neto), 2),
        ];
    }

    /** Suma neta de una cuenta configurada (por clave) en un rango de fechas. */
    private function saldoCuentaClave(string $clave, string $desde, string $hasta, bool $creditoEsPositivo): float
    {
        $cuentaId = ConfiguracionContable::where('clave', $clave)->value('cuenta_contable_id');
        if (!$cuentaId) return 0.0;

        $fila = $this->baseMovimientos()
            ->where('m.cuenta_contable_id', $cuentaId)
            ->whereBetween('c.fecha', [$desde, $hasta])
            ->selectRaw('COALESCE(SUM(m.debito),0) as debito, COALESCE(SUM(m.credito),0) as credito')
            ->first();

        return $creditoEsPositivo
            ? round((float) $fila->credito - (float) $fila->debito, 2)
            : round((float) $fila->debito - (float) $fila->credito, 2);
    }

    /* ════════════════════════════════════════════════
       RETENCIONES PRACTICADAS (para el Formulario 350 mensual)
    ════════════════════════════════════════════════ */
    public function retencionesPracticadas(string $desde, string $hasta): array
    {
        $tipos = [
            'retefuente' => ['clave' => 'CUENTA_RETEFUENTE', 'nombre' => 'Retención en la Fuente'],
            'reteiva' => ['clave' => 'CUENTA_RETEIVA', 'nombre' => 'Retención de IVA'],
            'reteica' => ['clave' => 'CUENTA_RETEICA', 'nombre' => 'Retención de ICA'],
        ];

        $resumen = [];
        $totalGeneral = 0.0;

        foreach ($tipos as $codigo => $info) {
            $cuentaId = ConfiguracionContable::where('clave', $info['clave'])->value('cuenta_contable_id');
            $porTercero = collect();

            if ($cuentaId) {
                $porTercero = $this->baseMovimientos()
                    ->where('m.cuenta_contable_id', $cuentaId)
                    ->whereBetween('c.fecha', [$desde, $hasta])
                    ->leftJoin('terceros as t', 't.id', '=', 'm.tercero_id')
                    ->select('m.tercero_id', 't.razon_social', 't.nombre as tercero_nombre', 't.apellido as tercero_apellido', 't.cedula', 't.nit', DB::raw('SUM(m.credito) - SUM(m.debito) as valor'))
                    ->groupBy('m.tercero_id', 't.razon_social', 't.nombre', 't.apellido', 't.cedula', 't.nit')
                    ->havingRaw('SUM(m.credito) - SUM(m.debito) > 0.004')
                    ->get()
                    ->map(function ($fila) {
                        $fila->tercero = $this->nombreTercero($fila) ?? 'Sin tercero asociado';
                        $fila->valor = round((float) $fila->valor, 2);
                        return $fila;
                    })
                    ->sortByDesc('valor')
                    ->values();
            }

            $total = round($porTercero->sum('valor'), 2);
            $resumen[$codigo] = ['nombre' => $info['nombre'], 'total' => $total, 'porTercero' => $porTercero];
            $totalGeneral += $total;
        }

        return ['tipos' => $resumen, 'totalGeneral' => round($totalGeneral, 2)];
    }

    /* ════════════════════════════════════════════════
       INDICADORES FINANCIEROS
       Se calculan a partir de los totales que ya arman balanceGeneral() y
       estadoResultados() — sin clasificación de activo/pasivo corriente vs.
       no corriente en el PUC, se reportan las razones que sí se pueden
       calcular de forma confiable con los datos disponibles (rentabilidad y
       endeudamiento), en vez de fingir una liquidez corriente que requeriría
       datos que el plan de cuentas actual no distingue.
    ════════════════════════════════════════════════ */
    public function indicadoresFinancieros(string $desde, string $hasta): array
    {
        $balance = $this->balanceGeneral($hasta);
        $resultados = $this->estadoResultados($desde, $hasta);

        $activoTotal = (float) $balance['activo']['total'];
        $pasivoTotal = (float) $balance['pasivo']['total'];
        $patrimonioTotal = (float) $balance['patrimonio']['total'] + (float) $balance['resultadoEjercicio'];
        $ingresos = (float) $resultados['ingresos']['total'];
        $utilidadBruta = (float) $resultados['utilidadBruta'];
        $utilidadNeta = (float) $resultados['utilidadNeta'];

        $pct = fn (float $num, float $den): ?float => $den > 0.004 ? round($num / $den * 100, 2) : null;

        return [
            'activoTotal' => round($activoTotal, 2),
            'pasivoTotal' => round($pasivoTotal, 2),
            'patrimonioTotal' => round($patrimonioTotal, 2),
            'ingresos' => round($ingresos, 2),
            'utilidadBruta' => round($utilidadBruta, 2),
            'utilidadNeta' => round($utilidadNeta, 2),
            'margenBruto' => $pct($utilidadBruta, $ingresos),
            'margenNeto' => $pct($utilidadNeta, $ingresos),
            'endeudamiento' => $pct($pasivoTotal, $activoTotal),
            'roa' => $pct($utilidadNeta, $activoTotal),
            'roe' => $pct($utilidadNeta, $patrimonioTotal),
        ];
    }
}
