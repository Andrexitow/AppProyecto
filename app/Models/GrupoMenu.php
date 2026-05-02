<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrupoMenu extends Model
{
    use HasFactory;

    // Nombre de la tabla (opcional si sigue la convención)
    protected $table = 'grupo_menus';

    // Campos que se pueden llenar masivamente
    protected $fillable = [
        'nombre',
        'impresora_id'
    ];

    /**
     * Relación: Un GrupoMenu pertenece a una Impresora.
     * Esto te permite hacer: $grupo->impresora->ip
     */
    public function impresora()
    {
        return $this->belongsTo(Impresora::class, 'impresora_id');
    }

    /**
     * Relación: Un GrupoMenu tiene muchos Productos.
     * Esto te permite hacer: $grupo->productos
     */
    public function productos()
    {
        return $this->hasMany(Producto::class, 'grupo_menu_id');
    }
}