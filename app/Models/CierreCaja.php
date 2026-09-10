<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CierreCaja extends Model
{
    protected $table = 'cierres_caja';
    protected $guarded = [];
    protected $casts = ['fecha_inicio' => 'datetime', 'fecha_fin' => 'datetime', 'denominaciones' => 'array', 'resumen' => 'array'];
    public function caja() { return $this->belongsTo(Caja::class); }
    public function usuario() { return $this->belongsTo(User::class, 'user_id'); }
}
