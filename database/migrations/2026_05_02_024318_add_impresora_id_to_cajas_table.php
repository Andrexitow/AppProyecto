<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cajas', function (Blueprint $table) {
            // Relacionamos la caja con una impresora de tu tabla 'impresoras'
            $table->unsignedBigInteger('impresora_id')->nullable()->after('bodega_id');
            $table->foreign('impresora_id')->references('id')->on('impresoras');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cajas', function (Blueprint $table) {
            $table->dropForeign(['impresora_id']);
            $table->dropColumn('impresora_id');
        });
    }
};
