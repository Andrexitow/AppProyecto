<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotaFacturaDetalle extends Model
{
    protected $fillable = [
        'nota_factura_id',
        'factura_detalle_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
    ];

    public function nota()
    {
        return $this->belongsTo(NotaFactura::class, 'nota_factura_id');
    }

    public function facturaDetalle()
    {
        return $this->belongsTo(FacturaDetalle::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
