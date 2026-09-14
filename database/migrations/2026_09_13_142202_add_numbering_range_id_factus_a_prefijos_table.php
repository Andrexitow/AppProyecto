<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * El ID del rango de numeración en Factus (`numbering_range_id`) es propio
 * de Factus, no de la resolución DIAN en sí — solo hace falta si el negocio
 * tiene más de un rango activo allá (p. ej. un rango por bodega/caja, como
 * este negocio con FR/FD). Si se deja vacío, Factus usa su único rango
 * disponible por defecto (así lo documentan ellos mismos).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prefijos', function (Blueprint $table) {
            $table->unsignedInteger('numbering_range_id_factus')->nullable()->after('clave_tecnica');
        });
    }

    public function down(): void
    {
        Schema::table('prefijos', function (Blueprint $table) {
            $table->dropColumn('numbering_range_id_factus');
        });
    }
};
