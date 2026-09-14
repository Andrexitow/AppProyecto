<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Sin esto, un documento con error se reintentaba cada 5 minutos para
 * siempre (hallazgo de auditoría: sin límite, sin backoff). intentos_dian
 * cuenta los intentos; al pasar el máximo, FacturacionElectronicaService
 * deja de reintentarlo automáticamente (estado_dian pasa a 'fallida', que
 * ya no entra en el filtro pendiente/error del comando programado) y queda
 * a la espera de que alguien lo revise manualmente.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facturas', function (Blueprint $table) {
            $table->unsignedTinyInteger('intentos_dian')->default(0)->after('estado_dian');
        });

        Schema::table('notas_factura', function (Blueprint $table) {
            $table->unsignedTinyInteger('intentos_dian')->default(0)->after('estado_dian');
        });
    }

    public function down(): void
    {
        Schema::table('facturas', function (Blueprint $table) {
            $table->dropColumn('intentos_dian');
        });

        Schema::table('notas_factura', function (Blueprint $table) {
            $table->dropColumn('intentos_dian');
        });
    }
};
