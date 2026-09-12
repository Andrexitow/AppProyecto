<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoClienteAplicacion extends Model
{
    protected $table = 'pago_cliente_aplicaciones';
    protected $fillable = ['pago_cliente_id', 'factura_id', 'valor'];
    protected $casts = ['valor' => 'decimal:2'];

    public function pago() { return $this->belongsTo(PagoCliente::class, 'pago_cliente_id'); }
    public function factura() { return $this->belongsTo(Factura::class); }
}
