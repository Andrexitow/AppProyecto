<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepreciacionActivoFijo extends Model
{
    protected $table = 'depreciaciones_activos_fijos';

    protected $fillable = [
        'activo_fijo_id',
        'periodo',
        'valor',
        'comprobante_contable_id',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
    ];

    public function activoFijo()
    {
        return $this->belongsTo(ActivoFijo::class);
    }

    public function comprobante()
    {
        return $this->belongsTo(ComprobanteContable::class, 'comprobante_contable_id');
    }
}
