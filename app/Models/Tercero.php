<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tercero extends Model
{
    protected $table = 'terceros';
    protected $appends = ['nombre_completo'];

    protected $fillable = [
        'tipo',
        'nombre',
        'apellido',
        'cedula',
        'razon_social',
        'nit',
        'email',
        'celular',
        'direccion',
        'estado'
    ];

    // =========================================
    // RELACIONES
    // =========================================

    public function ajustes()
    {
        return $this->hasMany(Ajuste::class);
    }

    // =========================================
    // ACCESOR (NOMBRE COMPLETO)
    // =========================================

    public function getNombreCompletoAttribute()
    {
        return $this->tipo === 'persona'
            ? "{$this->nombre} {$this->apellido}"
            : $this->razon_social;
    }
}