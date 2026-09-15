<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Antes, el plazo de crédito que se le mandaba a Factus era un "30 días"
     * fijo en el código, sin relación con ningún acuerdo real. Ahora:
     * - terceros.dias_credito: plazo pactado con ESE cliente (opcional).
     * - facturas.fecha_vencimiento: fotografía del vencimiento calculado al
     *   momento de la venta (fecha_venta + dias_credito, o la política
     *   general si el cliente no tiene uno configurado) — así, si el plazo
     *   del cliente cambia después, las facturas ya emitidas no se alteran.
     */
    public function up(): void
    {
        Schema::table('terceros', function (Blueprint $table) {
            $table->unsignedSmallInteger('dias_credito')->nullable()->after('regimen_tributario');
        });

        Schema::table('facturas', function (Blueprint $table) {
            $table->date('fecha_vencimiento')->nullable()->after('estado_pago');
        });
    }

    public function down(): void
    {
        Schema::table('terceros', function (Blueprint $table) {
            $table->dropColumn('dias_credito');
        });

        Schema::table('facturas', function (Blueprint $table) {
            $table->dropColumn('fecha_vencimiento');
        });
    }
};
