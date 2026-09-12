<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('traslados_bodega', function (Blueprint $table) {
            $table->id();
            $table->string('prefijo', 10);
            $table->unsignedInteger('consecutivo');
            $table->date('fecha');
            $table->foreignId('bodega_origen_id')->constrained('bodegas');
            $table->foreignId('bodega_destino_id')->constrained('bodegas');
            $table->text('observaciones')->nullable();
            $table->string('estado', 20)->default('confirmado');
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();

            $table->unique(['prefijo', 'consecutivo']);
            $table->index(['fecha', 'estado']);
        });

        Schema::create('traslado_bodega_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('traslado_bodega_id')->constrained('traslados_bodega')->cascadeOnDelete();
            $table->foreignId('producto_id')->constrained('productos');
            $table->decimal('cantidad', 14, 3);
            $table->timestamps();

            $table->unique(['traslado_bodega_id', 'producto_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('traslado_bodega_detalles');
        Schema::dropIfExists('traslados_bodega');
    }
};
