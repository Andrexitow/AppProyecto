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

            // De dónde sale el valor. Las 3 CUENTA_* y PROPINA se agregaron
            // después de creada esta tabla (ver migraciones
            // add_cuentas_pago_a_origen_valor_plantillas_contables y
            // add_propina_a_origen_valor_plantillas_contables, que hacen el
            // ALTER equivalente en MySQL ya migrado; aquí también se
            // agregan para que un `migrate:fresh` o el SQLite de los tests,
            // que hornea el enum como CHECK al crear la tabla, reflejen el
            // esquema final sin depender del orden de migraciones).
            $table->enum('origen_valor',[
                'TOTAL',
                'SUBTOTAL',
                'IVA',
                'COSTO',
                'DESCUENTO',
                'RETENCION',
                'VALOR_FIJO',
                'CUENTA_CAJA',
                'CUENTA_BANCO',
                'CUENTA_CLIENTES',
                'PROPINA'
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