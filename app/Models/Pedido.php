<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pedido extends Model
{
    use HasFactory;

    protected $fillable = [
        'mesa_id',
        'user_id',
        'total',
        'estado'
    ];

    // Relación: Un pedido pertenece a una mesa
    public function mesa()
    {
        return $this->belongsTo(Mesa::class);
    }

    // Relación: Un pedido fue tomado por un usuario (mesero)
    public function mesero()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relación: Un pedido tiene muchos detalles (productos)
    public function detalles()
    {
        return $this->hasMany(DetallePedido::class);
    }
}
