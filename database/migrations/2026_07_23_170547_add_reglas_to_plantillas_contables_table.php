<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plantillas_contables', function (Blueprint $table) {

            $table->boolean('requiere_tercero')
                ->default(false)
                ->after('descripcion');

            $table->boolean('requiere_centro_costo')
                ->default(false)
                ->after('requiere_tercero');

            $table->boolean('omitir_si_cero')
                ->default(true)
                ->after('requiere_centro_costo');

        });
    }

    public function down(): void
    {
        Schema::table('plantillas_contables', function (Blueprint $table) {

            $table->dropColumn([
                'requiere_tercero',
                'requiere_centro_costo',
                'omitir_si_cero'
            ]);

        });
    }
};