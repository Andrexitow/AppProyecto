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
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->string('numero_factura')->unique(); // Ej: POS-0001
            $table->foreignId('mesa_id')->constrained();
            $table->foreignId('user_id')->constrained(); // Quién cobró
            $table->integer('subtotal');
            $table->integer('propina')->default(0);
            $table->integer('total');

            // Información del Pago (Lo que pediste)
            $table->enum('metodo_pago', ['efectivo', 'tarjeta', 'transferencia', 'mixto']);
            $table->string('tipo_tarjeta')->nullable(); // Debito, Credito
            $table->string('banco_destino')->nullable(); // Bancolombia, Caja Social, etc.
            $table->string('referencia_pago')->nullable(); // # de comprobante

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};
