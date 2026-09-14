<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Antes un movimiento de caja solo guardaba un texto libre en "concepto".
     * Ahora se relaciona con el catálogo de conceptos_caja y, para las
     * salidas, con el tercero que recibe el dinero (para poder imprimir el
     * comprobante que esa persona firma).
     */
    public function up(): void
    {
        Schema::table('movimientos_caja', function (Blueprint $table) {
            $table->foreignId('concepto_caja_id')
                ->nullable()
                ->after('concepto')
                ->constrained('conceptos_caja')
                ->nullOnDelete();

            $table->foreignId('tercero_id')
                ->nullable()
                ->after('concepto_caja_id')
                ->constrained('terceros')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('movimientos_caja', function (Blueprint $table) {
            $table->dropConstrainedForeignId('concepto_caja_id');
            $table->dropConstrainedForeignId('tercero_id');
        });
    }
};
