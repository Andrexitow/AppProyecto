<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GrupoMenu extends Model
{
    use HasFactory;

    protected $table = 'grupo_menus';

    // 💡 Quitamos 'impresora_id' porque ahora los destinos se guardan en la tabla pivote
    protected $fillable = [
        'nombre',
    ];

    /**
     * Relación: Un GrupoMenu pertenece a muchas Impresoras (Multi-punto).
     * Cambiado a plural 'impresoras' para que coincida con el controlador y la vista
     */
    public function impresoras(): BelongsToMany
    {
        return $this->belongsToMany(Impresora::class, 'grupo_menu_impresora')
                    ->withPivot('punto')
                    ->withTimestamps();
    }

    /**
     * Relación: Un GrupoMenu tiene muchos Productos.
     */
    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class, 'grupo_menu_id');
    }
}