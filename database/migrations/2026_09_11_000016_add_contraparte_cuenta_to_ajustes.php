<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ajustes', function (Blueprint $table) {
            $table->foreignId('contraparte_cuenta_id')->nullable()->after('contraparte')
                ->constrained('cuentas_contables')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ajustes', function (Blueprint $table) {
            $table->dropForeign(['contraparte_cuenta_id']);
            $table->dropColumn('contraparte_cuenta_id');
        });
    }
};
