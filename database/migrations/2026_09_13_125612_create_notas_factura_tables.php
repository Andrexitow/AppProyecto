<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Notas crédito/débito de factura (hallazgo #4 de la auditoría DIAN):
 * NOTA_CREDITO/NOTA_DEBITO ya existían parametrizadas contablemente, pero no
 * había ningún flujo real para emitirlas ni vincularlas a la factura que
 * corrigen — FacturaController::anular() incluso le decía al usuario "haz
 * una nota crédito" para un caso que no se podía hacer.
 *
 * Alcance de esta primera versión: corrección contable/de inventario interna
 * (sin transmisión DIAN, que requiere la integración con un proveedor
 * tecnológico habilitado — ver auditoría, hallazgo #3).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notas_factura', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factura_id')->constrained('facturas')->cascadeOnDelete();
            $table->enum('tipo', ['credito', 'debito']);
            $table->string('numero')->nullable(); // número del comprobante contable generado (p. ej. NC000001)
            $table->date('fecha');
            $table->text('motivo');
            $table->decimal('subtotal', 14, 2);
            $table->decimal('iva', 14, 2);
            $table->decimal('total', 14, 2);
            // Solo aplica a notas crédito: si se devuelve el producto a la
            // bodega o si fue una corrección de valor sin devolución física.
            $table->boolean('restaura_inventario')->default(false);
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
        });

        Schema::create('nota_factura_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nota_factura_id')->constrained('notas_factura')->cascadeOnDelete();
            $table->foreignId('factura_detalle_id')->constrained('factura_detalles');
            $table->foreignId('producto_id')->constrained('productos');
            $table->decimal('cantidad', 12, 3);
            $table->decimal('precio_unitario', 14, 2);
            $table->decimal('subtotal', 14, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nota_factura_detalles');
        Schema::dropIfExists('notas_factura');
    }
};
