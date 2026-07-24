<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuracion_contable', function (Blueprint $table) {

            $table->id();

            // Clave única de configuración
            $table->string('clave')->unique();

            // Nombre amigable
            $table->string('nombre');

            // Cuenta asociada
            $table->foreignId('cuenta_contable_id')
                ->nullable()
                ->constrained('cuentas_contables')
                ->nullOnDelete();

            // Descripción
            $table->text('descripcion')->nullable();

            // Estado
            $table->boolean('estado')->default(true);

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracion_contable');
    }
};