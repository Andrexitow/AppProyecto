<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// Importa el trait si usas factories, si no, déjalo así
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Producto extends Model
{
    protected $table = 'productos';

    protected $fillable = [
        'codigo',
        'codigo_barras',
        'referencia',
        'descripcion',
        'caracteristicas',
        'und_detal',
        'und_mayor',
        'und_adicional',
        'factor_mayor',
        'factor_adicional',
        'categoria',
        'categoria2',
        'linea',
        'grupo_menu_id',
        'afecta_inventario',
        'iva_ventas',
        'ico_ventas',
        'valor_ico_ventas',
        'imp_saludable',
        'integracion_contable_id',
        'integracion_contable',
        'iva_compras',
        'ico_compras',
        'precio',
        'inactivo',
        // Producto ensamblado: al venderse no descuenta su propio
        // inventario sino el del producto base (ver Consumo).
        'es_ensamblado',
        'producto_base_id',
        'factor_consumo',
        // Si el insumo real de este ensamblado vive en una bodega distinta
        // a la de la caja que lo vende (ej. la carne de una hamburguesa
        // vendida en Discoteca en realidad está en la bodega de Cocina).
        // Null = se sigue revisando/descontando de la bodega de la caja.
        'bodega_origen_id',
        // Acompañamiento: en vez de un solo insumo fijo, el mesero reparte
        // el máximo del grupo entre varias opciones al comandar (ver
        // AcompanamientoGrupo).
        'acompanamiento_grupo_id',
    ];

    protected $casts = [
        'afecta_inventario' => 'boolean',
        'inactivo' => 'boolean',
        'precio' => 'decimal:2',
        'iva_ventas' => 'decimal:2',
        'ico_ventas' => 'decimal:2',
        'valor_ico_ventas' => 'decimal:2',
        'imp_saludable' => 'decimal:2',
        'valor_imp_saludable' => 'decimal:2',
        'iva_compras' => 'decimal:2',
        'ico_compras' => 'decimal:2',
        'es_ensamblado' => 'boolean',
        'factor_consumo' => 'decimal:2',
    ];

    // RELACIÓN CON EL GRUPO (NUEVA)
    public function grupoMenu()
    {
        return $this->belongsTo(GrupoMenu::class, 'grupo_menu_id');
    }

    public function inventarios()
    {
        return $this->hasMany(Inventario::class);
    }

    public function integracionContable()
    {
        return $this->belongsTo(
            IntegracionContable::class,
            'integracion_contable_id'
        );
    }

    /**
     * El insumo real que se descuenta cuando este producto (ensamblado)
     * se vende. Ej: "Cubetazo Poker" -> producto base "Poker".
     */
    public function productoBase()
    {
        return $this->belongsTo(Producto::class, 'producto_base_id');
    }

    /**
     * Productos ensamblados que usan a este producto como insumo base.
     */
    public function productosEnsamblados()
    {
        return $this->hasMany(Producto::class, 'producto_base_id');
    }

    /**
     * Bodega donde de verdad vive el insumo de este ensamblado, si es
     * distinta a la de la caja que lo vende (ver bodega_origen_id).
     */
    public function bodegaOrigen()
    {
        return $this->belongsTo(Bodega::class, 'bodega_origen_id');
    }

    /**
     * Grupo de acompañamiento del que este producto reparte unidades al
     * comandarse (ej. "Cubetazo Mix" -> "Servicio de Cubetazo").
     */
    public function acompanamientoGrupo()
    {
        return $this->belongsTo(AcompanamientoGrupo::class, 'acompanamiento_grupo_id');
    }
}
