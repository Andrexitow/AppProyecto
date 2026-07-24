<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos_contables', function (Blueprint $table) {

            $table->id();

            // Comprobante al que pertenece
            $table->foreignId('comprobante_contable_id')
                ->constrained('comprobantes_contables')
                ->cascadeOnDelete();

            // Cuenta contable
            $table->foreignId('cuenta_contable_id')
                ->constrained('cuentas_contables');

            // Cliente / proveedor (opcional)
            $table->unsignedBigInteger('tercero_id')->nullable();

            // Centro de costo (para futuras versiones)
            $table->unsignedBigInteger('centro_costo_id')->nullable();

            // Referencia del documento
            $table->string('referencia',100)->nullable();

            // Detalle del movimiento
            $table->text('detalle')->nullable();

            // Valores
            $table->decimal('debito',18,2)->default(0);

            $table->decimal('credito',18,2)->default(0);

            $table->timestamps();

            $table->index('comprobante_contable_id');
            $table->index('cuenta_contable_id');
            $table->index('tercero_id');
            $table->index('centro_costo_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_contables');
    }
};