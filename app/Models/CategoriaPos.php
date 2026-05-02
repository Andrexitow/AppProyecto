<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaPos extends Model
{
    protected $table    = 'categorias_pos';
    protected $fillable = ['nombre', 'icono', 'orden'];

    public function productos()
    {
        // Relaciona por el campo texto 'categoria' del producto
        return $this->hasMany(Producto::class, 'categoria', 'nombre');
    }

}