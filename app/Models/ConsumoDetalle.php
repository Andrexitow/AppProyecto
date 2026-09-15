<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsumoDetalle extends Model
{
    protected $fillable = [
        'consumo_id',
        'producto_base_id',
        'producto_ensamblado_id',
        // De qué bodega sale (o debe salir) este insumo — normalmente la de
        // la caja que vendió, salvo que el ensamblado declare su propia
        // bodega_origen_id (ver Producto).
        'bodega_id',
        'cantidad',
        'costo_unitario',
        'subtotal',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'costo_unitario' => 'decimal:4',
        'subtotal' => 'decimal:2',
    ];

    public function consumo()
    {
        return $this->belongsTo(Consumo::class);
    }

    // El insumo real descontado del inventario.
    public function productoBase()
    {
        return $this->belongsTo(Producto::class, 'producto_base_id');
    }

    // El producto ensamblado vendido que originó este renglón.
    public function productoEnsamblado()
    {
        return $this->belongsTo(Producto::class, 'producto_ensamblado_id');
    }

    public function bodega()
    {
        return $this->belongsTo(Bodega::class);
    }
}
