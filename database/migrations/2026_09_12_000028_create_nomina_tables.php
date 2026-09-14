<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empleados', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('nombre');
            $table->string('apellido');
            $table->string('cedula')->unique();
            $table->string('cargo')->nullable();
            $table->date('fecha_ingreso');
            $table->decimal('salario_base', 15, 2);
            $table->decimal('arl_tarifa', 6, 4)->default(0.522); // % nivel de riesgo I por defecto
            $table->string('email')->nullable();
            $table->string('celular')->nullable();
            $table->string('cuenta_bancaria')->nullable();
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->date('fecha_retiro')->nullable();
            $table->timestamps();

            $table->index('estado');
        });

        // Fila única de parámetros legales de nómina. Se guardan aquí (en vez de
        // hardcodearlos) porque el SMMLV y el auxilio de transporte cambian cada
        // año por decreto — la contadora debe poder actualizarlos sin tocar código.
        Schema::create('parametros_nomina', function (Blueprint $table) {
            $table->id();
            $table->decimal('smmlv', 12, 2);
            $table->decimal('auxilio_transporte', 12, 2);
            $table->decimal('salud_empleado_pct', 6, 4)->default(4.0);
            $table->decimal('pension_empleado_pct', 6, 4)->default(4.0);
            $table->decimal('salud_patronal_pct', 6, 4)->default(8.5);
            $table->decimal('pension_patronal_pct', 6, 4)->default(12.0);
            $table->decimal('cesantias_pct', 6, 4)->default(8.33);
            $table->decimal('intereses_cesantias_pct', 6, 4)->default(1.0); // mensual
            $table->decimal('prima_pct', 6, 4)->default(8.33);
            $table->decimal('vacaciones_pct', 6, 4)->default(4.17);
            $table->decimal('sena_pct', 6, 4)->default(2.0);
            $table->decimal('icbf_pct', 6, 4)->default(3.0);
            $table->decimal('caja_compensacion_pct', 6, 4)->default(4.0);
            $table->timestamps();
        });

        Schema::create('liquidaciones_nomina', function (Blueprint $table) {
            $table->id();
            $table->char('periodo', 7); // 'YYYY-MM'
            $table->date('fecha_pago');
            $table->decimal('total_devengado', 15, 2)->default(0);
            $table->decimal('total_deducciones', 15, 2)->default(0);
            $table->decimal('total_neto', 15, 2)->default(0);
            $table->decimal('total_aportes_patronales', 15, 2)->default(0);
            $table->foreignId('comprobante_contable_id')->nullable()->constrained('comprobantes_contables')->nullOnDelete();
            $table->enum('estado', ['REGISTRADA', 'ANULADA'])->default('REGISTRADA');
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('motivo_anulacion')->nullable();
            $table->timestamps();

            // Sin unique() a nivel de BD: si una liquidación se anula, debe poder
            // volver a liquidarse el mismo período (quedaría más de una fila con
            // el mismo periodo, una ANULADA y otra REGISTRADA). La regla real
            // — solo una liquidación REGISTRADA vigente por período — la aplica
            // NominaService::liquidarPeriodo() antes de crear la fila.
            $table->index('periodo');
        });

        Schema::create('detalle_liquidaciones_nomina', function (Blueprint $table) {
            $table->id();
            $table->foreignId('liquidacion_nomina_id')->constrained('liquidaciones_nomina')->cascadeOnDelete();
            $table->foreignId('empleado_id')->constrained('empleados');
            $table->decimal('salario_devengado', 15, 2);
            $table->decimal('auxilio_transporte', 15, 2)->default(0);
            $table->decimal('cesantias', 15, 2)->default(0);
            $table->decimal('intereses_cesantias', 15, 2)->default(0);
            $table->decimal('prima', 15, 2)->default(0);
            $table->decimal('vacaciones', 15, 2)->default(0);
            $table->decimal('salud_empleado', 15, 2)->default(0);
            $table->decimal('pension_empleado', 15, 2)->default(0);
            $table->decimal('salud_patronal', 15, 2)->default(0);
            $table->decimal('pension_patronal', 15, 2)->default(0);
            $table->decimal('arl', 15, 2)->default(0);
            $table->decimal('sena', 15, 2)->default(0);
            $table->decimal('icbf', 15, 2)->default(0);
            $table->decimal('caja_compensacion', 15, 2)->default(0);
            $table->decimal('neto_pagado', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_liquidaciones_nomina');
        Schema::dropIfExists('liquidaciones_nomina');
        Schema::dropIfExists('parametros_nomina');
        Schema::dropIfExists('empleados');
    }
};
