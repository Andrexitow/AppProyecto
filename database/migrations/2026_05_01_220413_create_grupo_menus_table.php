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
        Schema::create('grupo_menus', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // Ej: Coctelería, Parrilla, Entradas

            // Relación con la impresora física
            $table->unsignedBigInteger('impresora_id')->nullable();
            $table->foreign('impresora_id')->references('id')->on('impresoras')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grupo_menus');
    }
};
