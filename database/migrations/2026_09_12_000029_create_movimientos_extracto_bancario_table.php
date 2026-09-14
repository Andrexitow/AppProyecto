<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos_extracto_bancario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cuenta_tesoreria_id')->constrained('cuentas_tesoreria')->cascadeOnDelete();
            $table->date('fecha');
            $table->string('descripcion');
            // Positivo = consignación/abono del banco; negativo = cargo/retiro.
            $table->decimal('valor', 15, 2);
            $table->foreignId('movimiento_contable_id')->nullable()->constrained('movimientos_contables')->nullOnDelete();
            $table->boolean('conciliado')->default(false);
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['cuenta_tesoreria_id', 'conciliado'], 'mov_extracto_cuenta_conciliado_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_extracto_bancario');
    }
};
