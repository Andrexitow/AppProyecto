<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Descubierto probando contra el sandbox REAL de Factus (2026-09-14):
     * a diferencia de lo que decía la documentación leída antes, Factus SÍ
     * exige `customer.municipality_code` (código DANE/DIVIPOLA) para
     * cualquier cliente que no sea "Consumidor Final" — sin esto, la API
     * responde 422 "El campo código municipio es obligatorio." en cada
     * factura con cliente real.
     */
    public function up(): void
    {
        Schema::table('terceros', function (Blueprint $table) {
            $table->string('codigo_municipio', 10)->nullable()->after('ciudad');
        });
    }

    public function down(): void
    {
        Schema::table('terceros', function (Blueprint $table) {
            $table->dropColumn('codigo_municipio');
        });
    }
};
