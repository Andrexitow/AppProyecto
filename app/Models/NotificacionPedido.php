<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificacionPedido extends Model
{
    protected $table = 'notificaciones_pedidos';

    protected $fillable = [
        'user_id',
        'pedido_id',
        'comanda_pendiente_id',
        'tipo',
        'mensaje',
        'leida_at',
    ];

    protected $casts = [
        'leida_at' => 'datetime',
    ];
}
