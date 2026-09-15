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
        ,'documento_id'
        ,'estado_pago'
        ,'total_pagado'
        ,'saldo_pendiente'
        ,'cufe'
        ,'numero_proveedor'
        ,'xml_url'
        ,'pdf_url'
        ,'qr_texto'
        ,'estado_dian'
        ,'mensaje_dian'
        ,'intentos_dian'
        ,'fecha_transmision_dian'
        ,'fecha_vencimiento'
    ];

    /**
     * Conversión de tipos automática.
     */
    protected $casts = [
        'subtotal' => 'integer',
        'impuestos' => 'integer',
        'propina'  => 'integer',
        'total'    => 'integer',
        'created_at' => 'datetime',
        'fecha_vencimiento' => 'date',
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

    public function pagosCliente()
    {
        return $this->hasMany(PagoClienteAplicacion::class);
    }

    /** Desglose de formas de pago cuando metodo_pago = 'mixto'. */
    public function pagos()
    {
        return $this->hasMany(FacturaPago::class);
    }

    /** Notas crédito/débito emitidas contra esta factura. */
    public function notas()
    {
        return $this->hasMany(NotaFactura::class);
    }
}
