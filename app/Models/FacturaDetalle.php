<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacturaDetalle extends Model
{
    protected $table = 'factura_detalles';

    protected $fillable = [
        'factura_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
        'subtotal'
    ];

    public function factura()
    {
        return $this->belongsTo(Factura::class);
    }

    // public function producto()
    // {
    //     return $this->belongsTo(Producto::class);
    // }

    public function producto()
    {
        return $this->belongsTo(
            Producto::class,
            'producto_id'
        );
    }

    /** Líneas de notas crédito/débito que ya corrigieron esta línea. */
    public function notaDetalles()
    {
        return $this->hasMany(NotaFacturaDetalle::class);
    }
}
