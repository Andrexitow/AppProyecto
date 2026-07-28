<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('metodos_pago_contables', function (Blueprint $table) {
            $table->id();
            $table->string('metodo_pago')->unique(); // efectivo, tarjeta, transferencia, nequi, daviplata...
            $table->string('configuracion_clave');   // CUENTA_CAJA o CUENTA_BANCO
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('metodos_pago_contables');
    }
};