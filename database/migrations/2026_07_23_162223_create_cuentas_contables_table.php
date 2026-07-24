<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuentas_contables', function (Blueprint $table) {

            $table->id();

            // Código PUC
            $table->string('codigo',20)->unique();

            // Nombre de la cuenta
            $table->string('nombre',150);

            // Nivel dentro del árbol
            $table->unsignedTinyInteger('nivel');

            // Cuenta padre
            $table->foreignId('cuenta_padre_id')
                ->nullable()
                ->constrained('cuentas_contables')
                ->nullOnDelete();

            // Clasificación
            $table->enum('clasificacion',[
                'ACTIVO',
                'PASIVO',
                'PATRIMONIO',
                'INGRESO',
                'COSTO',
                'GASTO',
                'ORDEN'
            ]);

            // Naturaleza
            $table->enum('naturaleza',[
                'DEBITO',
                'CREDITO'
            ]);

            // Tipo de cuenta
            $table->enum('tipo',[
                'AGRUPADORA',
                'DETALLE'
            ]);

            // ¿Permite movimientos?
            $table->boolean('permite_movimientos')->default(false);

            // Estado
            $table->boolean('estado')->default(true);

            $table->timestamps();

            // Índices
            $table->index('codigo');
            $table->index('clasificacion');
            $table->boolean('es_auxiliar')->default(false);
            $table->index('cuenta_padre_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuentas_contables');
    }
};