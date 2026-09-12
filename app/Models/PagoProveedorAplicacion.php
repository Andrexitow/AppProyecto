<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoProveedorAplicacion extends Model
{
    protected $table = 'pago_proveedor_aplicaciones';
    protected $fillable = ['pago_proveedor_id', 'compra_id', 'valor'];
    protected $casts = ['valor' => 'decimal:2'];

    public function pago() { return $this->belongsTo(PagoProveedor::class, 'pago_proveedor_id'); }
    public function compra() { return $this->belongsTo(Compra::class); }
}
