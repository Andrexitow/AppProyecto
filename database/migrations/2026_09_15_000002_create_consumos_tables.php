<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Registro de consumo de materia prima: se genera automáticamente
     * cuando una factura incluye un producto ensamblado (ver
     * FacturacionController::cerrarMesa()). numero_factura queda igual
     * al de la factura que lo originó a propósito — no es un consecutivo
     * propio, es la misma numeración de esa venta.
     */
    public function up(): void
    {
        Schema::create('consumos', function (Blueprint $table) {
            $table->id();
            $table->string('numero_factura');
            $table->foreignId('factura_id')->nullable()->constrained('facturas')->nullOnDelete();
            $table->date('fecha');
            $table->string('observacion');
            $table->decimal('total', 14, 2)->default(0);
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('numero_factura');
        });

        Schema::create('consumo_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consumo_id')->constrained('consumos')->cascadeOnDelete();
            // El insumo real que se descontó del inventario.
            $table->foreignId('producto_base_id')->constrained('productos');
            // El producto ensamblado vendido que originó este consumo.
            $table->foreignId('producto_ensamblado_id')->constrained('productos');
            $table->decimal('cantidad', 12, 2);
            $table->decimal('costo_unitario', 14, 4)->default(0);
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consumo_detalles');
        Schema::dropIfExists('consumos');
    }
};
