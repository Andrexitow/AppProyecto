<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    protected $fillable = [
        'producto_id',
        'bodega_id',
        'stock',
        'costo_promedio',
    ];

    // No se castea 'stock' a propósito: se deja igual que antes (tal cual lo
    // devuelve el driver) para no alterar el tipo que ya consume el resto del
    // código. Solo el nuevo campo de costo promedio lleva cast.
    protected $casts = [
        'costo_promedio' => 'decimal:4',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function bodega()
    {
        return $this->belongsTo(Bodega::class);
    }
}
