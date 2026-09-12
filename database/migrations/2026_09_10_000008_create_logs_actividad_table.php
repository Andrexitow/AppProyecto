<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logs_actividad', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('rol', 80)->nullable();
            $table->string('modulo', 100);
            $table->string('accion', 80);
            $table->string('descripcion', 500);
            $table->string('metodo', 10)->nullable();
            $table->string('ruta', 255)->nullable();
            $table->string('referencia', 255)->nullable();
            $table->string('ip', 45)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['created_at', 'modulo']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logs_actividad');
    }
};
