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
        Schema::create('inventarios', function (Blueprint $table) {
            $table->id();

            $table->foreignId('producto_id')->constrained()->cascadeOnDelete();
            $table->foreignId('bodega_id')->constrained()->cascadeOnDelete();

            $table->decimal('stock', 15, 2)->default(0);

            $table->timestamps();

            // 🔥 IMPORTANTE: evitar duplicados
            $table->unique(['producto_id', 'bodega_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventarios');
    }
};
