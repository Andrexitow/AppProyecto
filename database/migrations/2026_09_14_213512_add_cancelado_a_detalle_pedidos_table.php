<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Antes, cancelar un ítem de un pedido ya enviado a cocina lo borraba
     * (DetallePedido::delete()): el cocinero nunca veía que fue cancelado
     * ni quién lo hizo, solo dejaba de aparecer. Estas columnas permiten
     * dejar el registro marcado como cancelado en vez de borrarlo, para
     * que Cocina lo muestre tachado/deshabilitado dentro de la misma
     * comanda — ver FacturacionController::eliminarItemPedido().
     */
    public function up(): void
    {
        Schema::table('detalle_pedidos', function (Blueprint $table) {
            $table->timestamp('cancelado_at')->nullable()->after('observacion');
            $table->foreignId('cancelado_por')->nullable()->after('cancelado_at')
                ->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detalle_pedidos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('cancelado_por');
            $table->dropColumn('cancelado_at');
        });
    }
};
