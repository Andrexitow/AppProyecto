<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plantillas_contables', function (Blueprint $table) {

            $table->id();

            $table->foreignId('proceso_contable_id')
                ->constrained('procesos_contables')
                ->cascadeOnDelete();

            // Orden de ejecución
            $table->unsignedTinyInteger('orden');

            // DÉBITO o CRÉDITO
            $table->enum('tipo_movimiento',[
                'DEBITO',
                'CREDITO'
            ]);

            // Clave de la configuración
            $table->string('configuracion_clave');

            // De dónde sale el valor
            $table->enum('origen_valor',[
                'TOTAL',
                'SUBTOTAL',
                'IVA',
                'COSTO',
                'DESCUENTO',
                'RETENCION',
                'VALOR_FIJO'
            ]);

            // Solo si origen_valor = VALOR_FIJO
            $table->decimal('valor_fijo',18,2)
                ->nullable();

            // Descripción
            $table->string('descripcion')
                ->nullable();

            $table->boolean('estado')
                ->default(true);

            $table->timestamps();

            $table->index('proceso_contable_id');
            $table->index('configuracion_clave');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plantillas_contables');
    }
};