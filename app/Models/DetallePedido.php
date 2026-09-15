<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetallePedido extends Model
{
    use HasFactory;

    // Es vital que el nombre de la tabla coincida con tu migración
    protected $table = 'detalle_pedidos';

    protected $fillable = [
        'pedido_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
        'observacion',
        'cancelado_at',
        'cancelado_por',
    ];

    protected $casts = [
        'cancelado_at' => 'datetime',
    ];

    // Relación: El detalle pertenece a un pedido
    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    // Relación: El detalle pertenece a un producto específico
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    // Quién autorizó/canceló esta línea después de enviada a cocina (ver
    // FacturacionController::eliminarItemPedido) — null mientras no esté
    // cancelada.
    public function canceladoPor()
    {
        return $this->belongsTo(User::class, 'cancelado_por');
    }

    // Reparto de acompañamiento elegido para esta línea (si el producto
    // tiene un acompanamiento_grupo_id) — ver DetallePedidoAcompanamiento.
    public function acompanamientos()
    {
        return $this->hasMany(DetallePedidoAcompanamiento::class);
    }
}
