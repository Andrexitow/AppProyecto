<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * El reparto real que el mesero eligió para una línea de pedido concreta
 * (ej. esta comanda de "Cubetazo Mix" llevó 4 Poker + 3 Águila + 3 Costeña).
 * Vive del lado del pedido para sobrevivir mientras la mesa sigue abierta;
 * al facturar, cada línea se convierte en un ConsumoDetalle (ver
 * FacturacionController::cerrarMesa()).
 */
class DetallePedidoAcompanamiento extends Model
{
    protected $fillable = [
        'detalle_pedido_id',
        'producto_id',
        'cantidad',
    ];

    protected $casts = [
        'cantidad' => 'integer',
    ];

    public function detallePedido()
    {
        return $this->belongsTo(DetallePedido::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
