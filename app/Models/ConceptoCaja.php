<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConceptoCaja extends Model
{
    protected $table = 'conceptos_caja';

    protected $fillable = [
        'nombre',
        'tipo',
        'descripcion',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    /** Valores válidos para el selector de tipo. */
    public const TIPOS = [
        'ingreso' => 'Solo ingresos',
        'salida' => 'Solo salidas',
        'ambos' => 'Ingresos y salidas',
    ];

    public function movimientos()
    {
        return $this->hasMany(MovimientoCaja::class, 'concepto_caja_id');
    }
}
