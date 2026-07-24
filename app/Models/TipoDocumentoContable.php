<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoDocumentoContable extends Model
{
    protected $table = 'tipos_documento_contable';

    protected $fillable = [
        'codigo',
        'nombre',
        'prefijo',
        'consecutivo',
        'longitud',
        'descripcion',
        'estado'
    ];

    protected $casts = [
        'estado' => 'boolean'
    ];
}