<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcompanamientoOpcion extends Model
{
    // Nombre en español no coincide con el plural en inglés que adivina
    // Eloquent por defecto ("acompanamiento_opcions").
    protected $table = 'acompanamiento_opciones';

    protected $fillable = [
        'acompanamiento_grupo_id',
        'producto_id',
    ];

    public function grupo()
    {
        return $this->belongsTo(AcompanamientoGrupo::class, 'acompanamiento_grupo_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
