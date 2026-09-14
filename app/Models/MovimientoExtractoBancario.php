<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoExtractoBancario extends Model
{
    protected $table = 'movimientos_extracto_bancario';

    protected $fillable = [
        'cuenta_tesoreria_id',
        'fecha',
        'descripcion',
        'valor',
        'movimiento_contable_id',
        'conciliado',
        'usuario_id',
    ];

    protected $casts = [
        'fecha' => 'date',
        'valor' => 'decimal:2',
        'conciliado' => 'boolean',
    ];

    public function cuentaTesoreria()
    {
        return $this->belongsTo(CuentaTesoreria::class);
    }

    public function movimientoContable()
    {
        return $this->belongsTo(MovimientoContable::class);
    }
}
