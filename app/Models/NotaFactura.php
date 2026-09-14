<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotaFactura extends Model
{
    protected $table = 'notas_factura';

    protected $fillable = [
        'factura_id',
        'tipo',
        'numero',
        'fecha',
        'motivo',
        'subtotal',
        'iva',
        'total',
        'restaura_inventario',
        'user_id',
        'cufe',
        'numero_proveedor',
        'xml_url',
        'pdf_url',
        'qr_texto',
        'estado_dian',
        'mensaje_dian',
        'intentos_dian',
        'fecha_transmision_dian',
    ];

    protected $casts = [
        'fecha' => 'date',
        'restaura_inventario' => 'boolean',
        'fecha_transmision_dian' => 'datetime',
    ];

    public function factura()
    {
        return $this->belongsTo(Factura::class);
    }

    public function detalles()
    {
        return $this->hasMany(NotaFacturaDetalle::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
