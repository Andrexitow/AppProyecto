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
        Schema::create('comandas_pendientes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pedido_id')->nullable();
            $table->unsignedBigInteger('factura_id')->nullable();
            $table->string('tipo'); // comanda | anulacion | factura | cierre_caja | inventario
            $table->unsignedBigInteger('impresora_id');
            $table->longText('contenido'); // texto plano ya formateado, listo para enviar a la térmica
            $table->string('estado')->default('pendiente'); // pendiente | impreso | error
            $table->text('error_mensaje')->nullable();
            $table->timestamps();
 
            $table->index(['estado', 'impresora_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comandas_pendientes');
    }
};
