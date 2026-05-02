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
        'cliente_id',
        'caja_id',
        'subtotal',
        'impuestos',
        'propina',
        'total',
        'metodo_pago',
        'tipo_tarjeta',
        'banco_destino',
        'referencia_pago',
        'estado'
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

    public function detalles()
    {
        return $this->hasMany(FacturaDetalle::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Tercero::class, 'cliente_id');
    }

    public function caja()
    {
        return $this->belongsTo(Caja::class, 'caja_id');
    }

    /**
     * Obtener el usuario (cajero/mesero) que realizó el cobro.
     */
    public function user() // Usaremos 'user' para que sea estándar
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Obtener la mesa asociada a la factura.
     */
    public function mesa(): BelongsTo
    {
        return $this->belongsTo(Mesa::class, 'mesa_id');
    }
}
