<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Catálogo único de prefijos de numeración, para dejar de escribirlos como
 * texto libre en cada módulo (Cajas hoy, y cualquier otro documento después)
 * y en cambio elegirlos de una sola lista centralizada.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prefijos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 10)->unique();
            $table->string('nombre', 100);
            $table->string('descripcion', 255)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prefijos');
    }
};
