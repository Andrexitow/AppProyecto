<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    protected $table = 'empleados';
    protected $appends = ['nombre_completo'];

    protected $fillable = [
        'codigo',
        'nombre',
        'apellido',
        'cedula',
        'cargo',
        'fecha_ingreso',
        'salario_base',
        'arl_tarifa',
        'email',
        'celular',
        'cuenta_bancaria',
        'estado',
        'fecha_retiro',
    ];

    protected $casts = [
        'fecha_ingreso' => 'date',
        'fecha_retiro' => 'date',
        'salario_base' => 'decimal:2',
        'arl_tarifa' => 'decimal:4',
    ];

    public function getNombreCompletoAttribute(): string
    {
        return trim("{$this->nombre} {$this->apellido}");
    }

    public function detalles()
    {
        return $this->hasMany(DetalleLiquidacionNomina::class);
    }
}
