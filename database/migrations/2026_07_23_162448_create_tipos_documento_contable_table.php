<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipos_documento_contable', function (Blueprint $table) {

            $table->id();

            // Código corto
            $table->string('codigo',10)->unique();

            // Nombre
            $table->string('nombre',100);

            // Prefijo para numeración
            $table->string('prefijo',10)->nullable();

            // Consecutivo actual
            $table->unsignedBigInteger('consecutivo')->default(1);

            // Longitud del consecutivo
            $table->unsignedTinyInteger('longitud')->default(6);

            // Descripción
            $table->text('descripcion')->nullable();

            // Estado
            $table->boolean('estado')->default(true);

            $table->timestamps();

            $table->index('codigo');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipos_documento_contable');
    }
};