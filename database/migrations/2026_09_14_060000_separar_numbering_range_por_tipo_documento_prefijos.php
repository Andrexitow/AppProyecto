<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Confirmado contra el sandbox real de Factus (2026-09-14): facturas y
 * notas NO comparten el mismo espacio de numbering_range_id. La cuenta de
 * prueba tiene un solo rango activo de "Factura de Venta" (por eso omitir
 * numbering_range_id funcionaba para facturas), pero DOS rangos activos de
 * "Nota Crédito" — Factus responde 422 "El campo id rango de numeración es
 * obligatorio" si no se especifica cuál usar. Un solo campo
 * (numbering_range_id_factus) no alcanza: si se llenaba con el id de la
 * nota, las facturas de ese mismo prefijo habrían empezado a fallar.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prefijos', function (Blueprint $table) {
            $table->unsignedInteger('numbering_range_id_nota_credito_factus')->nullable()->after('numbering_range_id_factus');
            $table->unsignedInteger('numbering_range_id_nota_debito_factus')->nullable()->after('numbering_range_id_nota_credito_factus');
        });
    }

    public function down(): void
    {
        Schema::table('prefijos', function (Blueprint $table) {
            $table->dropColumn(['numbering_range_id_nota_credito_factus', 'numbering_range_id_nota_debito_factus']);
        });
    }
};
