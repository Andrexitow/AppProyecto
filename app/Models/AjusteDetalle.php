<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Ajuste;

class AjusteDetalle extends Model
{
    protected $fillable = [
        'ajuste_id',
        'producto_id',
        'cantidad',
        'precio'
    ];

    public function ajuste()
    {
        return $this->belongsTo(Ajuste::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
