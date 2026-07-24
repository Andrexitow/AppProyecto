<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoContable extends Model
{
    protected $table = 'movimientos_contables';

    protected $fillable = [
        'comprobante_contable_id',
        'cuenta_contable_id',
        'tercero_id',
        'centro_costo_id',
        'referencia',
        'detalle',
        'debito',
        'credito'
    ];

    protected $casts = [
        'debito' => 'decimal:2',
        'credito' => 'decimal:2',
    ];

    public function comprobante()
    {
        return $this->belongsTo(ComprobanteContable::class);
    }

    public function cuenta()
    {
        return $this->belongsTo(CuentaContable::class, 'cuenta_contable_id');
    }
}