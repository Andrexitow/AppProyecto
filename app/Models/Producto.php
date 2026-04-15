<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'grupo_menu',
        'afecta_inventario',
        'iva_ventas',
        'ico_ventas',
        'valor_ico_ventas',
        'imp_saludable',
        'valor_imp_saludable',
        'integracion_contable',
        'iva_compras',
        'ico_compras',
        'precio',
        'inactivo'
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
    ];

    public function inventarios()
    {
        return $this->hasMany(Inventario::class);
    }
}
