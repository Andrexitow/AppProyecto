<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrasladoBodega extends Model
{
    protected $table = 'traslados_bodega';

    protected $fillable = [
        'prefijo',
        'consecutivo',
        'fecha',
        'bodega_origen_id',
        'bodega_destino_id',
        'observaciones',
        'estado',
        'user_id',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function origen()
    {
        return $this->belongsTo(Bodega::class, 'bodega_origen_id');
    }

    public function destino()
    {
        return $this->belongsTo(Bodega::class, 'bodega_destino_id');
    }

    public function detalles()
    {
        return $this->hasMany(TrasladoBodegaDetalle::class, 'traslado_bodega_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
