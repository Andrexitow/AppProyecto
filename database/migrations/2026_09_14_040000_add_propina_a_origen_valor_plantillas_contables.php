<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * origen_valor de plantillas_contables necesita un valor nuevo (PROPINA)
 * para poder contabilizar la propina como ingreso propio del negocio (ver
 * FacturacionContableService::contabilizarPropina() y el proceso
 * VENTA_PROPINA en PlantillaContableSeeder). Antes la propina no se
 * contabilizaba en ninguna cuenta — el efectivo entraba a caja, pero no
 * había registro contable de cuánto se había cobrado en propinas.
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
            'CUENTA_CAJA','CUENTA_BANCO','CUENTA_CLIENTES','PROPINA'
        )");
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE plantillas_contables MODIFY origen_valor ENUM(
            'TOTAL','SUBTOTAL','IVA','COSTO','DESCUENTO','RETENCION','VALOR_FIJO',
            'CUENTA_CAJA','CUENTA_BANCO','CUENTA_CLIENTES'
        )");
    }
};
