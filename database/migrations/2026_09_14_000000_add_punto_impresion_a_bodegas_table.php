<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Antes, FacturacionController decidía el punto de impresión de las
     * comandas comparando `$caja->id == 2` o `$caja->bodega_id == 2` a lo
     * bruto — se rompía apenas se creaba una caja/bodega nueva o cambiaban
     * los IDs. Ahora cada bodega declara explícitamente su punto.
     */
    public function up(): void
    {
        Schema::table('bodegas', function (Blueprint $table) {
            $table->string('punto_impresion')->default('RESTAURANTE')->after('descripcion');
        });

        // Backfill: preserva el comportamiento actual (bodega_id == 2 era
        // "Discoteca") antes de que el código deje de mirar el ID a mano.
        DB::table('bodegas')->where('descripcion', 'like', '%discoteca%')->update(['punto_impresion' => 'DISCOTECA']);
    }

    public function down(): void
    {
        Schema::table('bodegas', function (Blueprint $table) {
            $table->dropColumn('punto_impresion');
        });
    }
};
