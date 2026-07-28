<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comprobantes_contables', function (Blueprint $table) {
            $table->foreignId('proceso_contable_id')
                ->nullable()
                ->after('tipo_documento_contable_id')
                ->constrained('procesos_contables');
        });
    }

    public function down(): void
    {
        Schema::table('comprobantes_contables', function (Blueprint $table) {
            $table->dropConstrainedForeignId('proceso_contable_id');
        });
    }
};