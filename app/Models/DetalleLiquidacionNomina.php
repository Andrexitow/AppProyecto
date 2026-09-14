<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleLiquidacionNomina extends Model
{
    protected $table = 'detalle_liquidaciones_nomina';

    protected $fillable = [
        'liquidacion_nomina_id',
        'empleado_id',
        'salario_devengado',
        'auxilio_transporte',
        'cesantias',
        'intereses_cesantias',
        'prima',
        'vacaciones',
        'salud_empleado',
        'pension_empleado',
        'salud_patronal',
        'pension_patronal',
        'arl',
        'sena',
        'icbf',
        'caja_compensacion',
        'neto_pagado',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }

    public function liquidacion()
    {
        return $this->belongsTo(LiquidacionNomina::class, 'liquidacion_nomina_id');
    }
}
