<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Un producto ensamblado (ej. Hamburguesa) consume un insumo (Carne)
     * que a veces vive en una bodega distinta a la de la caja que vende
     * (ej. se vende desde la caja de Discoteca, pero la carne está en la
     * bodega de Cocina). Sin esto, el sistema siempre revisaba/descontaba
     * el insumo de la bodega de la caja vendedora, así que un insumo que
     * de verdad vivía en otra bodega siempre parecía "sin stock".
     */
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->foreignId('bodega_origen_id')->nullable()->after('factor_consumo')
                ->constrained('bodegas')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('bodega_origen_id');
        });
    }
};
