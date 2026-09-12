<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoCliente extends Model
{
    protected $table = 'pagos_cliente';
    protected $fillable = ['cliente_id', 'fecha', 'valor', 'metodo_pago_contable_id', 'referencia', 'origen', 'estado', 'usuario_id', 'comprobante_contable_id'];
    protected $casts = ['fecha' => 'date', 'valor' => 'decimal:2'];

    public function cliente() { return $this->belongsTo(Tercero::class, 'cliente_id'); }
    public function metodoPago() { return $this->belongsTo(MetodoPagoContable::class, 'metodo_pago_contable_id'); }
    public function comprobante() { return $this->belongsTo(ComprobanteContable::class, 'comprobante_contable_id'); }
    public function aplicaciones() { return $this->hasMany(PagoClienteAplicacion::class); }
}
