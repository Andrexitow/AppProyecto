<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Grupo de acompañamiento: un máximo de unidades a repartir libremente
 * entre sus opciones (ej. "Servicio de Cubetazo" = hasta 10 unidades entre
 * Poker/Águila/Costeña). Ver AcompanamientoOpcion y Producto::acompanamientoGrupo().
 */
class AcompanamientoGrupo extends Model
{
    protected $fillable = [
        'codigo',
        'descripcion',
        'cantidad_maxima',
    ];

    protected $casts = [
        'cantidad_maxima' => 'integer',
    ];

    public function opciones()
    {
        return $this->hasMany(AcompanamientoOpcion::class);
    }

    // Productos de venta (ej. "Cubetazo Mix") que usan este grupo.
    public function productos()
    {
        return $this->hasMany(Producto::class, 'acompanamiento_grupo_id');
    }
}
