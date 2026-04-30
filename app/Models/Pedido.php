<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class Pedido extends Model
{
    use HasFactory;

    protected $fillable = [
        'mesa_id',
        'user_id',
        'total',
        'estado'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

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
