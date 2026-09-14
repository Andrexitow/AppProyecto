<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Catálogo de conceptos para movimientos de caja (ingresos/salidas), ej.
     * "Pago turno meseros", "Compras generales", "Propinas repartidas", etc.
     * Se usa como selector obligatorio al registrar un movimiento, en vez de
     * texto libre, para poder reportar y auditar por concepto.
     */
    public function up(): void
    {
        Schema::create('conceptos_caja', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            // A qué tipo de movimiento aplica este concepto en el selector.
            $table->enum('tipo', ['ingreso', 'salida', 'ambos'])->default('ambos');
            $table->string('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conceptos_caja');
    }
};
