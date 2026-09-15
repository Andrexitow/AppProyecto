<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * "Producto ensamblado": un producto que al venderse no descuenta su
     * propio inventario sino el de otro producto base (el insumo real).
     * Ej: "Cubetazo Poker" (ensamblado) consume 6 x "Poker" (base) por
     * cada unidad vendida — ver FacturacionController::cerrarMesa() y
     * el nuevo módulo de Consumos.
     */
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->boolean('es_ensamblado')->default(false)->after('inactivo');

            $table->foreignId('producto_base_id')
                ->nullable()
                ->after('es_ensamblado')
                ->constrained('productos')
                ->nullOnDelete();

            // Cuántas unidades del producto base consume UNA unidad vendida
            // del producto ensamblado (ej: 6 para un cubetazo de 6 cervezas).
            $table->decimal('factor_consumo', 10, 2)->nullable()->after('producto_base_id');
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropForeign(['producto_base_id']);
            $table->dropColumn(['es_ensamblado', 'producto_base_id', 'factor_consumo']);
        });
    }
};
