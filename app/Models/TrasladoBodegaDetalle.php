<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrasladoBodegaDetalle extends Model
{
    protected $table = 'traslado_bodega_detalles';

    protected $fillable = [
        'traslado_bodega_id',
        'producto_id',
        'cantidad',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
