<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Mesa extends Model
{
    use HasFactory;

    protected $fillable = ['zona_id', 'numero', 'capacidad', 'estado'];

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
}
