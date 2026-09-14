<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivoFijo extends Model
{
    protected $table = 'activos_fijos';
    protected $appends = ['valor_depreciable', 'depreciacion_mensual', 'valor_libros'];

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'categoria',
        'cuenta_activo_id',
        'cuenta_depreciacion_id',
        'cuenta_gasto_id',
        'fecha_adquisicion',
        'valor_adquisicion',
        'valor_residual',
        'vida_util_meses',
        'depreciacion_acumulada',
        'tercero_id',
        'comprobante_alta_id',
        'estado',
        'fecha_baja',
        'comprobante_baja_id',
        'observaciones',
    ];

    protected $casts = [
        'fecha_adquisicion' => 'date',
        'fecha_baja' => 'date',
        'valor_adquisicion' => 'decimal:2',
        'valor_residual' => 'decimal:2',
        'depreciacion_acumulada' => 'decimal:2',
    ];

    public const CATEGORIAS = [
        'mueble_enseres' => ['nombre' => 'Muebles y Enseres', 'activo' => '152405', 'depreciacion' => '159205', 'gasto' => '516005'],
        'equipo_computo' => ['nombre' => 'Equipo de Cómputo', 'activo' => '152805', 'depreciacion' => '159210', 'gasto' => '516010'],
        'vehiculo' => ['nombre' => 'Vehículos', 'activo' => '154005', 'depreciacion' => '159215', 'gasto' => '516015'],
        'maquinaria_equipo' => ['nombre' => 'Maquinaria y Equipo', 'activo' => '152005', 'depreciacion' => '159220', 'gasto' => '516020'],
        'equipo_cocina' => ['nombre' => 'Equipo de Cocina y Hotelería', 'activo' => '159605', 'depreciacion' => '159225', 'gasto' => '516025'],
        'edificacion' => ['nombre' => 'Construcciones y Edificaciones', 'activo' => '150405', 'depreciacion' => '159230', 'gasto' => '516030'],
    ];

    public function cuentaActivo()
    {
        return $this->belongsTo(CuentaContable::class, 'cuenta_activo_id');
    }

    public function cuentaDepreciacion()
    {
        return $this->belongsTo(CuentaContable::class, 'cuenta_depreciacion_id');
    }

    public function cuentaGasto()
    {
        return $this->belongsTo(CuentaContable::class, 'cuenta_gasto_id');
    }

    public function tercero()
    {
        return $this->belongsTo(Tercero::class);
    }

    public function comprobanteAlta()
    {
        return $this->belongsTo(ComprobanteContable::class, 'comprobante_alta_id');
    }

    public function comprobanteBaja()
    {
        return $this->belongsTo(ComprobanteContable::class, 'comprobante_baja_id');
    }

    public function depreciaciones()
    {
        return $this->hasMany(DepreciacionActivoFijo::class);
    }

    /** Valor depreciable total (lo que sí se puede depreciar, sin tocar el valor de salvamento). */
    public function getValorDepreciableAttribute(): float
    {
        return round((float) $this->valor_adquisicion - (float) $this->valor_residual, 2);
    }

    /** Cuota mensual en línea recta. */
    public function getDepreciacionMensualAttribute(): float
    {
        if ($this->vida_util_meses <= 0) return 0.0;
        return round($this->valor_depreciable / $this->vida_util_meses, 2);
    }

    /** Valor en libros = costo - depreciación acumulada. */
    public function getValorLibrosAttribute(): float
    {
        return round((float) $this->valor_adquisicion - (float) $this->depreciacion_acumulada, 2);
    }
}
