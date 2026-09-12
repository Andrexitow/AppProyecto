<?php

namespace App\Services;

use App\Models\PeriodoContable;
use Illuminate\Validation\ValidationException;

/**
 * Apertura/cierre de períodos contables. Un período CERRADO bloquea crear o
 * modificar comprobantes con fecha dentro de su rango, sin importar desde
 * qué módulo se originen (ventas, compras, ajustes, tesorería, etc.).
 */
class PeriodoContableService
{
    public function estaCerrado(string|\DateTimeInterface $fecha): bool
    {
        $fecha = $this->normalizar($fecha);

        // whereDate() (no where() a secas) porque el cast 'date' del modelo
        // guarda fecha_inicio/fecha_fin con hora "00:00:00" incluida: una
        // comparación de string plana rompía la igualdad justo en los límites
        // del rango (ej. el propio primer día del período).
        return PeriodoContable::where('estado', 'CERRADO')
            ->whereDate('fecha_inicio', '<=', $fecha)
            ->whereDate('fecha_fin', '>=', $fecha)
            ->exists();
    }

    /** @throws ValidationException si la fecha cae en un período cerrado. */
    public function assertAbierto(string|\DateTimeInterface $fecha): void
    {
        if ($this->estaCerrado($fecha)) {
            throw ValidationException::withMessages([
                'periodo' => 'El período contable que contiene la fecha ' . $this->normalizar($fecha) . ' está cerrado. Reábralo desde Períodos Contables antes de registrar o modificar comprobantes ahí.',
            ]);
        }
    }

    public function cerrarPeriodo(string $desde, string $hasta, string $nombre, int $usuarioId): PeriodoContable
    {
        if ($desde > $hasta) {
            throw ValidationException::withMessages(['fecha_fin' => 'La fecha final debe ser posterior o igual a la inicial.']);
        }

        $solapa = PeriodoContable::whereDate('fecha_inicio', '<=', $hasta)->whereDate('fecha_fin', '>=', $desde)->exists();
        if ($solapa) {
            throw ValidationException::withMessages(['periodo' => 'Ya existe un período contable que se solapa con este rango de fechas.']);
        }

        return PeriodoContable::create([
            'fecha_inicio' => $desde,
            'fecha_fin' => $hasta,
            'nombre' => $nombre,
            'estado' => 'CERRADO',
            'cerrado_por' => $usuarioId,
            'cerrado_at' => now(),
        ]);
    }

    public function reabrirPeriodo(PeriodoContable $periodo, int $usuarioId): PeriodoContable
    {
        if ($periodo->estado !== 'CERRADO') {
            throw ValidationException::withMessages(['periodo' => 'Este período ya está abierto.']);
        }

        $periodo->update(['estado' => 'ABIERTO', 'reabierto_por' => $usuarioId, 'reabierto_at' => now()]);

        return $periodo->fresh();
    }

    private function normalizar(string|\DateTimeInterface $fecha): string
    {
        return $fecha instanceof \DateTimeInterface ? $fecha->format('Y-m-d') : substr($fecha, 0, 10);
    }
}
