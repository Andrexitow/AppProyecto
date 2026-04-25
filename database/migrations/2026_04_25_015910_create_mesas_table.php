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
        Schema::create('mesas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zona_id')->constrained('zonas'); // Vincula la mesa a una zona
            $table->string('numero');
            $table->integer('capacidad')->default(4);
            $table->enum('estado', ['disponible', 'ocupada', 'cuenta_pedida', 'seleccionada'])->default('disponible');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mesas');
    }
};
