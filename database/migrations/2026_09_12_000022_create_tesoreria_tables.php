<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cuentas_tesoreria', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->enum('tipo', ['CAJA', 'BANCO']);
            $table->foreignId('cuenta_contable_id')->constrained('cuentas_contables');
            $table->string('numero_cuenta', 60)->nullable();
            $table->boolean('activa')->default(true);
            $table->timestamps();
        });

        Schema::create('movimientos_tesoreria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cuenta_tesoreria_id')->constrained('cuentas_tesoreria');
            $table->enum('tipo', ['INGRESO', 'EGRESO', 'TRANSFERENCIA_SALIDA', 'TRANSFERENCIA_ENTRADA']);
            $table->decimal('valor', 14, 2);
            $table->date('fecha');
            $table->string('descripcion', 255);
            $table->foreignId('tercero_id')->nullable()->constrained('terceros')->nullOnDelete();
            $table->foreignId('cuenta_contrapartida_id')->nullable()->constrained('cuentas_contables')->nullOnDelete();
            $table->foreignId('cuenta_tesoreria_relacionada_id')->nullable()->constrained('cuentas_tesoreria')->nullOnDelete();
            $table->foreignId('comprobante_contable_id')->nullable()->constrained('comprobantes_contables')->nullOnDelete();
            $table->foreignId('usuario_id')->constrained('users');
            $table->timestamps();
            $table->index(['cuenta_tesoreria_id', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_tesoreria');
        Schema::dropIfExists('cuentas_tesoreria');
    }
};
