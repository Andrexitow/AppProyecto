<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * origen_valor de plantillas_contables necesita 3 valores nuevos
 * (CUENTA_CAJA/CUENTA_BANCO/CUENTA_CLIENTES) para que
 * FacturacionContableService pueda repartir el ingreso de una venta entre
 * las 3 cuentas de pago posibles en vez de una sola línea a Caja con
 * override (ver PlantillaContableSeeder). Solo aplica el ALTER en MySQL: el
 * enum se emula como CHECK/TEXT en SQLite (usado en tests), donde cualquier
 * string ya es válido y no hace falta tocar nada.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE plantillas_contables MODIFY origen_valor ENUM(
            'TOTAL','SUBTOTAL','IVA','COSTO','DESCUENTO','RETENCION','VALOR_FIJO',
            'CUENTA_CAJA','CUENTA_BANCO','CUENTA_CLIENTES'
        )");
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE plantillas_contables MODIFY origen_valor ENUM(
            'TOTAL','SUBTOTAL','IVA','COSTO','DESCUENTO','RETENCION','VALOR_FIJO'
        )");
    }
};
