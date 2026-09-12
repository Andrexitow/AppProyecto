<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('inventarios', function (Blueprint $table) {
            $table->decimal('costo_promedio', 18, 4)->default(0)->after('stock');
        });

        Schema::table('movimientos_inventario', function (Blueprint $table) {
            $table->decimal('costo_unitario', 18, 4)->default(0)->after('stock_nuevo');
            $table->decimal('costo_promedio_nuevo', 18, 4)->default(0)->after('costo_unitario');
            $table->decimal('valor_movimiento', 18, 2)->default(0)->after('costo_promedio_nuevo');
        });
    }

    public function down(): void
    {
        Schema::table('movimientos_inventario', function (Blueprint $table) {
            $table->dropColumn(['costo_unitario', 'costo_promedio_nuevo', 'valor_movimiento']);
        });
        Schema::table('inventarios', function (Blueprint $table) {
            $table->dropColumn('costo_promedio');
        });
    }
};
