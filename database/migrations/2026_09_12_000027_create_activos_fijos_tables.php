<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activos_fijos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('categoria', 30);

            // Cuentas contables resueltas al momento del registro (no se
            // recalculan luego aunque cambie el mapeo de categorías, para que
            // la depreciación de un activo siempre siga afectando la misma
            // cuenta con la que nació).
            $table->foreignId('cuenta_activo_id')->constrained('cuentas_contables');
            $table->foreignId('cuenta_depreciacion_id')->constrained('cuentas_contables');
            $table->foreignId('cuenta_gasto_id')->constrained('cuentas_contables');

            $table->date('fecha_adquisicion');
            $table->decimal('valor_adquisicion', 15, 2);
            $table->decimal('valor_residual', 15, 2)->default(0);
            $table->unsignedInteger('vida_util_meses');

            // Total acumulado hasta la fecha (denormalizado): evita sumar la
            // tabla de depreciaciones cada vez que se necesita el valor en
            // libros, y sirve de tope para no depreciar más allá del valor
            // depreciable (valor_adquisicion - valor_residual).
            $table->decimal('depreciacion_acumulada', 15, 2)->default(0);

            $table->foreignId('tercero_id')->nullable()->constrained('terceros')->nullOnDelete();
            $table->foreignId('comprobante_alta_id')->nullable()->constrained('comprobantes_contables')->nullOnDelete();

            $table->enum('estado', ['activo', 'de_baja'])->default('activo');
            $table->date('fecha_baja')->nullable();
            $table->foreignId('comprobante_baja_id')->nullable()->constrained('comprobantes_contables')->nullOnDelete();

            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->index('estado');
            $table->index('categoria');
        });

        Schema::create('depreciaciones_activos_fijos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activo_fijo_id')->constrained('activos_fijos')->cascadeOnDelete();
            $table->char('periodo', 7); // 'YYYY-MM'
            $table->decimal('valor', 15, 2);
            $table->foreignId('comprobante_contable_id')->constrained('comprobantes_contables');
            $table->timestamps();

            $table->unique(['activo_fijo_id', 'periodo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('depreciaciones_activos_fijos');
        Schema::dropIfExists('activos_fijos');
    }
};
