<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('compra_detalles', function (Blueprint $table) {
            $table->decimal('valor_ico', 14, 2)->nullable()->after('ico_porcentaje');
        });
    }

    public function down(): void
    {
        Schema::table('compra_detalles', function (Blueprint $table) {
            $table->dropColumn('valor_ico');
        });
    }
};
