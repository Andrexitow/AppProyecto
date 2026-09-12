<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoTesoreria extends Model
{
    protected $table = 'movimientos_tesoreria';
    protected $guarded = [];
    protected $casts = ['fecha' => 'date', 'valor' => 'decimal:2'];

    public function cuentaTesoreria()
    {
        return $this->belongsTo(CuentaTesoreria::class, 'cuenta_tesoreria_id');
    }

    public function cuentaRelacionada()
    {
        return $this->belongsTo(CuentaTesoreria::class, 'cuenta_tesoreria_relacionada_id');
    }

    public function cuentaContrapartida()
    {
        return $this->belongsTo(CuentaContable::class, 'cuenta_contrapartida_id');
    }

    public function tercero()
    {
        return $this->belongsTo(Tercero::class);
    }

    public function comprobante()
    {
        return $this->belongsTo(ComprobanteContable::class, 'comprobante_contable_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
