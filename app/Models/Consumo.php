<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Consumo de materia prima generado automáticamente cuando una factura
 * incluye un producto ensamblado (ver FacturacionController::cerrarMesa()).
 * numero_factura es deliberadamente EL MISMO de la factura que lo originó:
 * no es un consecutivo propio.
 */
class Consumo extends Model
{
    protected $fillable = [
        'numero_factura',
        'factura_id',
        'fecha',
        'observacion',
        'total',
        'user_id',
        // 'registrado' (se descontó el inventario ya) o 'no_registrado'
        // (falló el stock de algún insumo derivado — ver
        // FacturacionController::cerrarMesa() — y queda esperando a que un
        // administrador ajuste el inventario o borre la línea problemática).
        'estado',
        'registrado_por',
        'registrado_at',
    ];

    protected $casts = [
        'fecha' => 'date',
        'total' => 'decimal:2',
        'registrado_at' => 'datetime',
    ];

    public function factura()
    {
        return $this->belongsTo(Factura::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function registradoPor()
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    public function detalles()
    {
        return $this->hasMany(ConsumoDetalle::class);
    }
}
