<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('cierres_caja', function (Blueprint $table) {
            $table->json('resumen')->nullable()->after('denominaciones');
        });
    }

    public function down(): void
    {
        Schema::table('cierres_caja', function (Blueprint $table) {
            $table->dropColumn('resumen');
        });
    }
};
