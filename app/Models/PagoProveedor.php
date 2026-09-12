<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoProveedor extends Model
{
    protected $table = 'pagos_proveedor';
    protected $fillable = ['proveedor_id', 'fecha', 'valor', 'metodo_pago_contable_id', 'referencia', 'origen', 'estado', 'usuario_id', 'comprobante_contable_id'];
    protected $casts = ['fecha' => 'date', 'valor' => 'decimal:2'];

    public function proveedor() { return $this->belongsTo(Tercero::class, 'proveedor_id'); }
    public function metodoPago() { return $this->belongsTo(MetodoPagoContable::class, 'metodo_pago_contable_id'); }
    public function comprobante() { return $this->belongsTo(ComprobanteContable::class, 'comprobante_contable_id'); }
    public function aplicaciones() { return $this->hasMany(PagoProveedorAplicacion::class); }
}
