<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cierres_caja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caja_id')->constrained();
            $table->foreignId('user_id')->constrained('users');
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin');
            $table->string('factura_inicial')->nullable();
            $table->string('factura_final')->nullable();
            $table->unsignedInteger('cantidad_facturas')->default(0);
            $table->decimal('base_inicial', 14, 2)->default(0);
            $table->decimal('efectivo_esperado', 14, 2)->default(0);
            $table->decimal('total_fisico', 14, 2)->default(0);
            $table->decimal('diferencia', 14, 2)->default(0);
            $table->json('denominaciones');
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('cierres_caja'); }
};
