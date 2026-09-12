<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bodega extends Model
{
    protected $fillable = ['descripcion'];

    public function inventarios()
    {
        return $this->hasMany(Inventario::class);
    }
}
