<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Zona extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'bodega_id'];

    // Una zona tiene muchas mesas
    public function mesas()
    {
        return $this->hasMany(Mesa::class);
    }

    // La bodega de la que se descuenta inventario / a la que apunta la
    // impresión de cocina para los pedidos de esta zona (ver punto_impresion).
    public function bodega()
    {
        return $this->belongsTo(Bodega::class);
    }
}
