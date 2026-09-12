<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CuentaTesoreria extends Model
{
    protected $table = 'cuentas_tesoreria';
    protected $fillable = ['nombre', 'tipo', 'cuenta_contable_id', 'numero_cuenta', 'activa'];
    protected $casts = ['activa' => 'boolean'];

    public function cuentaContable()
    {
        return $this->belongsTo(CuentaContable::class, 'cuenta_contable_id');
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoTesoreria::class);
    }
}
