<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Factura extends Model
{
    use HasFactory;

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'numero_factura',
        'mesa_id',
        'user_id',
        'subtotal',
        'propina',
        'total',
        'metodo_pago',
        'tipo_tarjeta',
        'banco_destino',
        'referencia_pago',
    ];

    /**
     * Conversión de tipos automática.
     */
    protected $casts = [
        'subtotal' => 'integer',
        'propina'  => 'integer',
        'total'    => 'integer',
        'created_at' => 'datetime',
    ];

    /**
     * Obtener el usuario (cajero/mesero) que realizó el cobro.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Obtener la mesa asociada a la factura.
     */
    public function mesa(): BelongsTo
    {
        return $this->belongsTo(Mesa::class);
    }
}