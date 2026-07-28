<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntegracionContable extends Model
{
    protected $table = 'integraciones_contables';

    protected $fillable = [
        'codigo',
        'nombre',
        'proceso_contable_id',
        'porcentaje_iva',
        'porcentaje_inc',
        'estado'
    ];

    protected $casts = [
        'estado' => 'boolean'
    ];

    public function procesoContable()
    {
        return $this->belongsTo(
            ProcesoContable::class,
            'proceso_contable_id'
        );
    }

    public function productos()
    {
        return $this->hasMany(
            Producto::class,
            'integracion_contable_id'
        );
    }
}