<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiquidacionNomina extends Model
{
    protected $table = 'liquidaciones_nomina';

    protected $fillable = [
        'periodo',
        'fecha_pago',
        'total_devengado',
        'total_deducciones',
        'total_neto',
        'total_aportes_patronales',
        'comprobante_contable_id',
        'estado',
        'usuario_id',
        'motivo_anulacion',
    ];

    protected $casts = [
        'fecha_pago' => 'date',
        'total_devengado' => 'decimal:2',
        'total_deducciones' => 'decimal:2',
        'total_neto' => 'decimal:2',
        'total_aportes_patronales' => 'decimal:2',
    ];

    public function detalles()
    {
        return $this->hasMany(DetalleLiquidacionNomina::class);
    }

    public function comprobante()
    {
        return $this->belongsTo(ComprobanteContable::class, 'comprobante_contable_id');
    }
}
