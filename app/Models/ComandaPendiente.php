<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComandaPendiente extends Model
{
    protected $table = 'comandas_pendientes';

    protected $fillable = [
        'pedido_id',
        'factura_id',
        'tipo',
        'impresora_id',
        'contenido',
        'estado',
        'error_mensaje',
    ];

    public function impresora()
    {
        return $this->belongsTo(Impresora::class, 'impresora_id');
    }
}
