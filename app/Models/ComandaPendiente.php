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
        'detalle_ids',
        'estado',
        'finalizado_por',
        'finalizado_at',
        'error_mensaje',
    ];

    protected $casts = [
        'detalle_ids' => 'array',
        'finalizado_at' => 'datetime',
    ];

    public function impresora()
    {
        return $this->belongsTo(Impresora::class, 'impresora_id');
    }

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }
}
