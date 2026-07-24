<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('procesos_contables', function (Blueprint $table) {

            $table->foreignId('tipo_documento_contable_id')
                ->nullable()
                ->after('nombre')
                ->constrained('tipos_documento_contable')
                ->restrictOnDelete();

        });
    }

    public function down(): void
    {
        Schema::table('procesos_contables', function (Blueprint $table) {

            $table->dropForeign(['tipo_documento_contable_id']);

            $table->dropColumn('tipo_documento_contable_id');

        });
    }
};