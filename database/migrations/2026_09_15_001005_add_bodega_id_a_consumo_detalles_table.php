<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Antes se asumía que el insumo siempre vivía en la bodega de la caja
     * que vendió — ahora un producto ensamblado puede declarar su propia
     * bodega_origen (ver Producto::bodega_origen_id), así que cada línea
     * de consumo debe guardar de qué bodega salió/debe salir, para poder
     * descontarla del lugar correcto al registrarla.
     */
    public function up(): void
    {
        Schema::table('consumo_detalles', function (Blueprint $table) {
            $table->foreignId('bodega_id')->nullable()->after('producto_ensamblado_id')->constrained('bodegas');
        });

        // Los consumos existentes se descontaron de la bodega de la caja de
        // su factura — se backfillea con ese mismo dato para no dejar filas
        // huérfanas sin bodega. Se hace fila por fila (no con un UPDATE...
        // JOIN) para que la migración corra igual en MySQL y en SQLite
        // (motor de las pruebas).
        DB::table('consumo_detalles as cd')
            ->join('consumos as c', 'c.id', '=', 'cd.consumo_id')
            ->join('facturas as f', 'f.id', '=', 'c.factura_id')
            ->join('cajas as ca', 'ca.id', '=', 'f.caja_id')
            ->whereNull('cd.bodega_id')
            ->select('cd.id as consumo_detalle_id', 'ca.bodega_id')
            ->get()
            ->each(fn ($fila) => DB::table('consumo_detalles')->where('id', $fila->consumo_detalle_id)->update(['bodega_id' => $fila->bodega_id]));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consumo_detalles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('bodega_id');
        });
    }
};
