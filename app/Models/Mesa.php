<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Mesa extends Model
{
    use HasFactory;

    protected $fillable = ['zona_id', 'numero', 'capacidad', 'estado', 'bloqueada_por', 'bloqueada_at'];

    protected $casts = [
        'bloqueada_at' => 'datetime',
    ];

    // Una mesa pertenece a una zona
    public function zona()
    {
        return $this->belongsTo(Zona::class);
    }

    public function pedidos()
    {
        // Una mesa puede tener muchos pedidos a lo largo del tiempo
        return $this->hasMany(Pedido::class);
    }

    /** Quién la tomó mientras arma el pedido (antes de que exista un Pedido real). */
    public function bloqueadaPor()
    {
        return $this->belongsTo(User::class, 'bloqueada_por');
    }
}
