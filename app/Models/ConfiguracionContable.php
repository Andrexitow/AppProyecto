<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionContable extends Model
{
    protected $table = 'configuracion_contable';

    protected $fillable = [
        'clave',
        'nombre',
        'cuenta_contable_id',
        'descripcion',
        'estado'
    ];

    protected $casts = [
        'estado' => 'boolean'
    ];

    public function cuenta()
    {
        return $this->belongsTo(CuentaContable::class, 'cuenta_contable_id');
    }

}
