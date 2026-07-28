<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetodoPagoContable extends Model
{
    protected $table = 'metodos_pago_contables';

    protected $fillable = [
        'metodo_pago',
        'configuracion_clave',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];
}