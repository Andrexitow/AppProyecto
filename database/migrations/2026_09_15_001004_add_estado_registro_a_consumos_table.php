<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Antes, cerrarMesa() rechazaba TODA la factura si le faltaba stock a
     * un insumo de un ensamblado/acompañamiento — pero el dinero ya entró,
     * así que la factura debe pasar sí o sí. Ahora, cuando falta stock de
     * un insumo "derivado" (nunca de un producto vendido directamente), el
     * Consumo completo de esa factura queda 'no_registrado': NINGUNA de
     * sus líneas descuenta inventario todavía, hasta que un administrador
     * ajuste el inventario o elimine la línea problemática y lo registre
     * manualmente (ver ConsumoController).
     */
    public function up(): void
    {
        Schema::table('consumos', function (Blueprint $table) {
            $table->enum('estado', ['registrado', 'no_registrado'])->default('registrado')->after('total');
            $table->foreignId('registrado_por')->nullable()->after('estado')->constrained('users')->nullOnDelete();
            $table->timestamp('registrado_at')->nullable()->after('registrado_por');
        });

        // Todo lo que ya existía se asume registrado (así se comportaba el
        // sistema antes de esta migración: se descontaba de inmediato).
        DB::table('consumos')->update([
            'estado' => 'registrado',
            'registrado_por' => DB::raw('user_id'),
            'registrado_at' => DB::raw('created_at'),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consumos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('registrado_por');
            $table->dropColumn(['estado', 'registrado_at']);
        });
    }
};
