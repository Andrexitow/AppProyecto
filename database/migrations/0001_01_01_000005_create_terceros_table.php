<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('terceros', function (Blueprint $table) {
            $table->id();

            // Tipo de tercero
            $table->enum('tipo', ['persona', 'empresa']);

            // Datos comunes
            $table->string('email')->nullable();
            $table->string('celular')->nullable();
            $table->string('direccion')->nullable();

            // Persona natural
            $table->string('nombre')->nullable();
            $table->string('apellido')->nullable();
            $table->string('cedula')->nullable()->unique();

            // Empresa
            $table->string('razon_social')->nullable();
            $table->string('nit')->nullable()->unique();

            // Control
            $table->boolean('estado')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('terceros');
    }
};
