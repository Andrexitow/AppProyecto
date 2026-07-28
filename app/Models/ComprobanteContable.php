<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComprobanteContable extends Model
{
    protected $table = 'comprobantes_contables';

    protected $fillable = [
        'tipo_documento_contable_id',
        'numero',
        'fecha',
        'observacion',
        'payload',
        'usuario_id',
        'documento_origen',
        'documento_origen_id',
        'proceso_contable_id',
        'referencia_grupo',
        'estado'
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'payload' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function tipoDocumento()
    {
        return $this->belongsTo(TipoDocumentoContable::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoContable::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Métodos útiles
    |--------------------------------------------------------------------------
    */

    public function getTotalDebitoAttribute()
    {
        return $this->movimientos()->sum('debito');
    }

    public function getTotalCreditoAttribute()
    {
        return $this->movimientos()->sum('credito');
    }

    public function estaCuadrado(): bool
    {
        return round($this->total_debito, 2) === round($this->total_credito, 2);
    }

    public function procesoContable()
    {
        return $this->belongsTo(ProcesoContable::class, 'proceso_contable_id');
    }
}
