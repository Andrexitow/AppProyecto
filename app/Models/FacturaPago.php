<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacturaPago extends Model
{
    protected $guarded = [];

    public function factura()
    {
        return $this->belongsTo(Factura::class);
    }
}
