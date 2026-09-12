<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodoContable extends Model
{
    protected $table = 'periodos_contables';
    protected $fillable = ['fecha_inicio', 'fecha_fin', 'nombre', 'estado', 'cerrado_por', 'cerrado_at', 'reabierto_por', 'reabierto_at'];
    protected $casts = ['fecha_inicio' => 'date', 'fecha_fin' => 'date', 'cerrado_at' => 'datetime', 'reabierto_at' => 'datetime'];

    public function cerradoPor()
    {
        return $this->belongsTo(User::class, 'cerrado_por');
    }

    public function reabiertoPor()
    {
        return $this->belongsTo(User::class, 'reabierto_por');
    }
}
