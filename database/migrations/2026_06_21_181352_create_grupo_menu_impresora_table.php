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
        Schema::create('grupo_menu_impresora', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grupo_menu_id')->constrained('grupo_menus')->onDelete('cascade');
            $table->foreignId('impresora_id')->constrained('impresoras')->onDelete('cascade');
            $table->string('punto')->nullable(); // Ejemplo: 'RESTAURANTE', 'DISCOTECA', 'PISO_1'
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grupo_menu_impresora');
    }
};
