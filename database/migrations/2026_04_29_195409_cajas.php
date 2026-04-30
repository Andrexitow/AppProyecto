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
        Schema::create('cajas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // Ej: Caja Barra Principal
            $table->string('prefijo'); // Ej: FB o 00
            $table->foreignId('bodega_id')->constrained('bodegas'); // De dónde descuenta
            $table->foreignId('user_id')->nullable()->constrained('users'); // Cajero asignado
            $table->boolean('activa')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cajas');
    }
};
