<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {

            $table->dropColumn('integracion_contable');

            $table->foreignId('integracion_contable_id')
                ->nullable()
                ->after('valor_imp_saludable')
                ->constrained('integraciones_contables')
                ->cascadeOnUpdate()
                ->nullOnDelete();

        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {

            $table->dropForeign(['integracion_contable_id']);

            $table->dropColumn('integracion_contable_id');

            $table->string('integracion_contable')->nullable();

        });
    }
};