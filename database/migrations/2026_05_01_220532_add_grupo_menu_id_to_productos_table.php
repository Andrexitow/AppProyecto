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
        Schema::table('productos', function (Blueprint $table) {
            // 1. Agregamos la columna
            $table->unsignedBigInteger('grupo_menu_id')->after('id')->nullable();

            // 2. Definimos la llave foránea
            $table->foreign('grupo_menu_id')->references('id')->on('grupo_menus')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            // Es importante definir cómo deshacer el cambio
            $table->dropForeign(['grupo_menu_id']);
            $table->dropColumn('grupo_menu_id');
        });
    }
};
