<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Caja extends Model {
    protected $fillable = ['nombre', 'prefijo', 'proximo_numero', 'bodega_id', 'user_id', 'activa'];

    public function bodega() {
        return $this->belongsTo(Bodega::class);
    }

    public function cajero() {
        return $this->belongsTo(User::class, 'user_id');
    }
}