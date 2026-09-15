<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * El consecutivo de factura se calculaba como "última factura de ESTA
     * caja + 1" — pero el prefijo no es exclusivo de una caja (nada impide,
     * y de hecho ya pasa en esta base de datos, que dos cajas compartan el
     * mismo prefijo). Eso podía generar un numero_factura que otra caja con
     * el mismo prefijo ya hubiera usado, sin necesidad de concurrencia.
     *
     * Este contador vive en el prefijo (no en la caja), y se lee con
     * lockForUpdate() dentro de la transacción de cerrarMesa() — eso cierra
     * tanto el choque entre cajas con el mismo prefijo como la carrera entre
     * dos cajeros facturando al mismo tiempo.
     */
    public function up(): void
    {
        Schema::table('prefijos', function (Blueprint $table) {
            $table->unsignedInteger('proximo_numero')->default(1)->after('codigo');
        });

        // Backfill: que el contador arranque después del número más alto ya
        // usado por facturas existentes con ese prefijo, para no repetir
        // ninguno ya emitido.
        foreach (DB::table('prefijos')->get(['id', 'codigo']) as $prefijo) {
            $maximo = DB::table('facturas')
                ->where('numero_factura', 'like', $prefijo->codigo . '-%')
                ->pluck('numero_factura')
                ->map(fn ($numero) => (int) (explode('-', $numero)[1] ?? 0))
                ->max() ?? 0;

            DB::table('prefijos')->where('id', $prefijo->id)->update(['proximo_numero' => $maximo + 1]);
        }
    }

    public function down(): void
    {
        Schema::table('prefijos', function (Blueprint $table) {
            $table->dropColumn('proximo_numero');
        });
    }
};
