<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlantillaContable extends Model
{

    protected $table = 'plantillas_contables';

    protected $fillable = [
        'proceso_contable_id',
        'orden',
        'tipo_movimiento',
        'configuracion_clave',
        'origen_valor',
        'valor_fijo',
        'descripcion',
        'estado',
        'requiere_tercero',
        'requiere_centro_costo',
        'omitir_si_cero'
    ];

    protected $casts = [
        'valor_fijo' => 'decimal:2',
        'estado' => 'boolean'
    ];

    public function proceso()
    {
        return $this->belongsTo(ProcesoContable::class);
    }
}
