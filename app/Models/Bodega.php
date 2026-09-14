<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bodega extends Model
{
    protected $fillable = ['descripcion', 'punto_impresion'];

    public function inventarios()
    {
        return $this->hasMany(Inventario::class);
    }
}
