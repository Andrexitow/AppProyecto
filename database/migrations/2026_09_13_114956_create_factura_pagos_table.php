<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Desglose de formas de pago de una factura (mismo patrón que compra_pagos).
 * Antes, 'mixto' era una opción válida en el cierre de mesa pero el sistema
 * no capturaba ningún dato de cuánto fue efectivo/tarjeta/etc — se
 * contabilizaba todo como Caja por defecto, silenciosamente. Con esta tabla
 * el desglose queda explícito y auditable, y FacturacionContableService lo
 * usa para repartir el ingreso entre Caja/Banco/Clientes.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('factura_pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factura_id')->constrained('facturas')->cascadeOnDelete();
            $table->string('metodo_pago', 30);
            $table->decimal('valor', 14, 2);
            $table->string('referencia')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('factura_pagos');
    }
};
