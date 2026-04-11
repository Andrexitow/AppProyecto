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
        Schema::create('ajustes', function (Blueprint $table) {
            $table->id();

            $table->string('prefijo', 5);
            $table->integer('numero');

            $table->date('fecha');

            $table->foreignId('tercero_id')->nullable()->constrained()->nullOnDelete();
            $table->string('contraparte')->nullable();

            $table->text('observaciones')->nullable();

            $table->decimal('total', 12, 2)->default(0);
            $table->boolean('registrado')->default(false);

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->timestamps();

            // 🔥 IMPORTANTE: evitar duplicados
            $table->unique(['prefijo', 'numero']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ajustes');
    }
};
