<?php

namespace App\Services;

use App\Models\CuentaTesoreria;
use App\Models\MovimientoExtractoBancario;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Concilia el extracto bancario (lo que dice el banco) contra el mayor de la
 * cuenta de tesorería (lo que dice el sistema). No inventa un "saldo real del
 * banco" derivado por fórmula — eso requeriría el saldo final que solo trae
 * el extracto real del banco; en cambio, muestra lado a lado qué coincide y
 * qué queda pendiente en cada lado, que es lo que la contadora necesita para
 * decidir qué le falta registrar o qué el banco no ha procesado todavía.
 */
class ConciliacionBancariaService
{
    public function __construct(private TesoreriaService $tesoreria)
    {
    }

    public function agregarLinea(array $datos, int $usuarioId): MovimientoExtractoBancario
    {
        return MovimientoExtractoBancario::create([
            'cuenta_tesoreria_id' => $datos['cuenta_tesoreria_id'],
            'fecha' => $datos['fecha'],
            'descripcion' => $datos['descripcion'],
            'valor' => $datos['valor'],
            'usuario_id' => $usuarioId,
        ]);
    }

    public function eliminarLinea(MovimientoExtractoBancario $linea): void
    {
        $linea->delete();
    }

    /** Movimientos del sistema (mayor de la cuenta contable ligada) en el rango, con su estado de conciliación. */
    public function movimientosSistema(CuentaTesoreria $cuenta, string $desde, string $hasta): array
    {
        $conciliados = MovimientoExtractoBancario::where('cuenta_tesoreria_id', $cuenta->id)
            ->whereNotNull('movimiento_contable_id')
            ->pluck('movimiento_contable_id')
            ->all();

        $movimientos = DB::table('movimientos_contables as m')
            ->join('comprobantes_contables as c', 'c.id', '=', 'm.comprobante_contable_id')
            ->where('m.cuenta_contable_id', $cuenta->cuenta_contable_id)
            ->whereIn('c.estado', ['REGISTRADO', 'CONTABILIZADO'])
            ->whereBetween('c.fecha', [$desde, $hasta])
            ->select('m.id', 'c.fecha', 'c.numero', 'm.detalle', 'm.debito', 'm.credito')
            ->orderBy('c.fecha')
            ->get()
            ->map(function ($m) use ($conciliados) {
                $m->valor = round((float) $m->debito - (float) $m->credito, 2);
                $m->conciliado = in_array($m->id, $conciliados, true);
                return $m;
            });

        return $movimientos->all();
    }

    /** Líneas del extracto bancario cargadas para el rango. */
    public function lineasExtracto(CuentaTesoreria $cuenta, string $desde, string $hasta): array
    {
        return MovimientoExtractoBancario::where('cuenta_tesoreria_id', $cuenta->id)
            ->whereBetween('fecha', [$desde, $hasta])
            ->orderBy('fecha')
            ->get()
            ->all();
    }

    /**
     * Sugiere emparejamientos automáticos: mismo valor exacto, no conciliados
     * todavía, y la fecha más cercana entre sí (hasta 5 días de diferencia).
     * No concilia nada por sí solo — solo propone; el usuario confirma cada
     * sugerencia desde la pantalla.
     */
    public function sugerirCoincidencias(CuentaTesoreria $cuenta, string $desde, string $hasta): array
    {
        $movimientos = collect($this->movimientosSistema($cuenta, $desde, $hasta))->where('conciliado', false)->values();
        $lineas = collect($this->lineasExtracto($cuenta, $desde, $hasta))->where('conciliado', false)->values();

        $sugerencias = [];
        $usados = [];

        foreach ($lineas as $linea) {
            $mejor = null;
            $mejorDiferenciaDias = null;

            foreach ($movimientos as $mov) {
                if (in_array($mov->id, $usados, true)) continue;
                if (abs((float) $mov->valor - (float) $linea->valor) > 0.01) continue;

                $dias = abs(strtotime($linea->fecha->toDateString()) - strtotime($mov->fecha)) / 86400;
                if ($dias > 5) continue;
                if ($mejorDiferenciaDias === null || $dias < $mejorDiferenciaDias) {
                    $mejor = $mov;
                    $mejorDiferenciaDias = $dias;
                }
            }

            if ($mejor) {
                $usados[] = $mejor->id;
                $sugerencias[] = ['linea_extracto_id' => $linea->id, 'movimiento_contable_id' => $mejor->id, 'valor' => (float) $linea->valor];
            }
        }

        return $sugerencias;
    }

    public function conciliar(MovimientoExtractoBancario $linea, int $movimientoContableId): void
    {
        $yaUsado = MovimientoExtractoBancario::where('movimiento_contable_id', $movimientoContableId)->where('id', '!=', $linea->id)->exists();
        if ($yaUsado) {
            throw ValidationException::withMessages(['movimiento_contable_id' => 'Ese movimiento del sistema ya está conciliado con otra línea del extracto.']);
        }

        $linea->update(['movimiento_contable_id' => $movimientoContableId, 'conciliado' => true]);
    }

    public function desconciliar(MovimientoExtractoBancario $linea): void
    {
        $linea->update(['movimiento_contable_id' => null, 'conciliado' => false]);
    }

    public function resumen(CuentaTesoreria $cuenta, string $desde, string $hasta): array
    {
        $movimientos = $this->movimientosSistema($cuenta, $desde, $hasta);
        $lineas = $this->lineasExtracto($cuenta, $desde, $hasta);

        $totalLibros = round(array_sum(array_map(fn ($m) => $m->valor, $movimientos)), 2);
        $totalExtracto = round(array_sum(array_map(fn ($l) => (float) $l->valor, $lineas)), 2);
        $pendientesSistema = array_values(array_filter($movimientos, fn ($m) => !$m->conciliado));
        $pendientesExtracto = array_values(array_filter($lineas, fn ($l) => !$l->conciliado));

        return [
            'saldo_libros_al_corte' => $this->tesoreria->saldo($cuenta, $hasta),
            'total_movimientos_libros' => $totalLibros,
            'total_lineas_extracto' => $totalExtracto,
            'pendientes_sistema' => $pendientesSistema,
            'pendientes_extracto' => $pendientesExtracto,
            'total_pendientes_sistema' => round(array_sum(array_map(fn ($m) => $m->valor, $pendientesSistema)), 2),
            'total_pendientes_extracto' => round(array_sum(array_map(fn ($l) => (float) $l->valor, $pendientesExtracto)), 2),
        ];
    }
}
