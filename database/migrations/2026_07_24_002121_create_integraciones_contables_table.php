<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('integraciones_contables', function (Blueprint $table) {

            $table->id();

            $table->string('codigo')->unique();

            $table->string('nombre');

            $table->foreignId('proceso_contable_id')
                ->constrained('procesos_contables')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->decimal('porcentaje_iva', 5, 2)->default(0);

            $table->decimal('porcentaje_inc', 5, 2)->default(0);

            $table->boolean('estado')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('integraciones_contables');
    }
};