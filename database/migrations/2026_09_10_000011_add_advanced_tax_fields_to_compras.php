<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('compras', function (Blueprint $table) {
            $table->decimal('retefuente_porcentaje', 8, 4)->default(0)->after('retenciones');
            $table->decimal('reteiva_porcentaje', 8, 4)->default(0)->after('retefuente_porcentaje');
            $table->decimal('reteica_porcentaje', 8, 4)->default(0)->after('reteiva_porcentaje');
            $table->decimal('imp_saludable', 14, 2)->default(0)->after('ico');
        });

        Schema::table('compra_detalles', function (Blueprint $table) {
            $table->decimal('descuento_2_porcentaje', 8, 4)->default(0)->after('descuento_porcentaje');
            $table->decimal('descuento_financiero_porcentaje', 8, 4)->default(0)->after('descuento_2_porcentaje');
            $table->decimal('imp_saludable_porcentaje', 8, 4)->default(0)->after('ico_porcentaje');
            $table->string('unidad', 40)->nullable()->after('cantidad');
            $table->text('observacion')->nullable()->after('otros_cargos');
            $table->boolean('bonificado')->default(false)->after('observacion');
            $table->boolean('entrada_pos')->default(false)->after('bonificado');
        });
    }

    public function down(): void
    {
        Schema::table('compra_detalles', function (Blueprint $table) {
            $table->dropColumn(['descuento_2_porcentaje', 'descuento_financiero_porcentaje', 'imp_saludable_porcentaje', 'unidad', 'observacion', 'bonificado', 'entrada_pos']);
        });

        Schema::table('compras', function (Blueprint $table) {
            $table->dropColumn(['retefuente_porcentaje', 'reteiva_porcentaje', 'reteica_porcentaje', 'imp_saludable']);
        });
    }
};
