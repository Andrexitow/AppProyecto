<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CuentaContable extends Model
{
    protected $table = 'cuentas_contables';

    protected $fillable = [
        'codigo',
        'nombre',
        'nivel',
        'cuenta_padre_id',
        'clasificacion',
        'naturaleza',
        'tipo',
        'permite_movimientos',
        'estado'
    ];

    protected $casts = [
        'permite_movimientos' => 'boolean',
        'estado' => 'boolean'
    ];

    public function padre()
    {
        return $this->belongsTo(CuentaContable::class, 'cuenta_padre_id');
    }

    public function hijos()
    {
        return $this->hasMany(CuentaContable::class, 'cuenta_padre_id')
            ->orderBy('codigo');
    }
}