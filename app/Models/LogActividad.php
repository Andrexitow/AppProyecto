<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogActividad extends Model
{
    protected $table = 'logs_actividad';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'rol',
        'modulo',
        'accion',
        'descripcion',
        'metodo',
        'ruta',
        'referencia',
        'ip',
        'metadata',
        'created_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
