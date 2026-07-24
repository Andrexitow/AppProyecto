<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcesoContable extends Model
{
    protected $table = 'procesos_contables';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'estado'
    ];

    public function plantillas()
    {
        return $this->hasMany(PlantillaContable::class);
    }

    public function tipoDocumento()
    {
        return $this->belongsTo(
            TipoDocumentoContable::class,
            'tipo_documento_contable_id',
            'id'
        );
    }
}
