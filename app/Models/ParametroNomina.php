<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParametroNomina extends Model
{
    protected $table = 'parametros_nomina';

    protected $fillable = [
        'smmlv',
        'auxilio_transporte',
        'salud_empleado_pct',
        'pension_empleado_pct',
        'salud_patronal_pct',
        'pension_patronal_pct',
        'cesantias_pct',
        'intereses_cesantias_pct',
        'prima_pct',
        'vacaciones_pct',
        'sena_pct',
        'icbf_pct',
        'caja_compensacion_pct',
    ];

    /**
     * Único registro vigente (se crea con valores por defecto si no existe).
     * Los porcentajes se listan aquí explícitamente en vez de confiar en los
     * ->default(...) de la migración: firstOrCreate() devuelve el objeto que
     * acaba de construir en memoria, no una fila releída de la BD, así que
     * cualquier columna omitida aquí llegaría como null (y (float) null = 0)
     * en el mismo cálculo que la creó — un cero silencioso en la primera
     * liquidación que se haga antes de abrir la pantalla de Parámetros.
     */
    public static function vigente(): self
    {
        return self::firstOrCreate([], [
            'smmlv' => 1300000,
            'auxilio_transporte' => 162000,
            'salud_empleado_pct' => 4.0,
            'pension_empleado_pct' => 4.0,
            'salud_patronal_pct' => 8.5,
            'pension_patronal_pct' => 12.0,
            'cesantias_pct' => 8.33,
            'intereses_cesantias_pct' => 1.0,
            'prima_pct' => 8.33,
            'vacaciones_pct' => 4.17,
            'sena_pct' => 2.0,
            'icbf_pct' => 3.0,
            'caja_compensacion_pct' => 4.0,
        ]);
    }
}
