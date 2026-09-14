<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoCaja extends Model
{
    protected $table = 'movimientos_caja';

    protected $fillable = [
        'tipo',
        'concepto',
        'concepto_caja_id',
        'tercero_id',
        'valor',
        'user_id',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
    ];

    public function conceptoCaja()
    {
        return $this->belongsTo(ConceptoCaja::class, 'concepto_caja_id');
    }

    public function tercero()
    {
        return $this->belongsTo(Tercero::class, 'tercero_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
