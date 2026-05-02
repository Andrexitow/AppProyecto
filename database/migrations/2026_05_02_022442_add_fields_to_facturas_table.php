<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('facturas', function (Blueprint $table) {
            $table->unsignedBigInteger('cliente_id')->nullable()->after('user_id');
            $table->unsignedBigInteger('caja_id')->after('cliente_id');
            $table->decimal('impuestos', 12, 2)->default(0)->after('subtotal');
            $table->enum('estado', ['pagada', 'anulada'])->default('pagada')->after('referencia_pago');

            // Definición de llaves foráneas con tus tablas existentes
            $table->foreign('cliente_id')->references('id')->on('terceros');
            $table->foreign('caja_id')->references('id')->on('cajas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facturas', function (Blueprint $table) {
            $table->dropForeign(['cliente_id']);
            $table->dropForeign(['caja_id']);
            $table->dropColumn(['cliente_id', 'caja_id', 'impuestos', 'estado']);
        });
    }
};
