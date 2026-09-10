<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comandas_pendientes', function (Blueprint $table) {
            $table->json('detalle_ids')->nullable()->after('contenido');
            $table->foreignId('finalizado_por')->nullable()->after('estado')->constrained('users')->nullOnDelete();
            $table->timestamp('finalizado_at')->nullable()->after('finalizado_por');
            $table->index(['tipo', 'estado']);
        });

        Schema::create('notificaciones_pedidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('pedido_id')->constrained('pedidos')->cascadeOnDelete();
            $table->foreignId('comanda_pendiente_id')->nullable()->constrained('comandas_pendientes')->nullOnDelete();
            $table->string('tipo', 40);
            $table->string('mensaje', 500);
            $table->timestamp('leida_at')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'leida_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificaciones_pedidos');
        Schema::table('comandas_pendientes', function (Blueprint $table) {
            $table->dropIndex(['tipo', 'estado']);
            $table->dropConstrainedForeignId('finalizado_por');
            $table->dropColumn(['detalle_ids', 'finalizado_at']);
        });
    }
};
