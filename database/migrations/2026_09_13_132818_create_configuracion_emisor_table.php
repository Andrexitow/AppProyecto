<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Datos del emisor (tu propia empresa) que CUALQUIER proveedor de
 * facturación electrónica va a pedir para poder transmitir a la DIAN, sin
 * importar cuál se contrate — es información del negocio, no del proveedor.
 * Tabla de una sola fila (como configuracion_sistema, pero con columnas
 * tipadas en vez de clave/valor porque son campos fijos y conocidos).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuracion_emisor', function (Blueprint $table) {
            $table->id();
            $table->string('razon_social');
            $table->string('nit', 20);
            $table->char('dv', 1)->nullable();
            $table->enum('tipo_persona', ['natural', 'juridica'])->default('juridica');
            $table->string('regimen_tributario')->nullable();
            $table->string('direccion')->nullable();
            $table->string('ciudad')->nullable();
            $table->string('departamento')->nullable();
            $table->string('codigo_postal', 10)->nullable();
            $table->string('telefono', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('matricula_mercantil', 30)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracion_emisor');
    }
};
