<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // El enum nativo solo existe en MySQL (en SQLite, que usan los tests,
        // la columna ya es un varchar libre sin CHECK, así que no hace falta
        // tocarla ahí). No se usa Blueprint::change() para no depender de
        // doctrine/dbal, que el proyecto no tiene instalado.
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE facturas MODIFY metodo_pago ENUM('efectivo','tarjeta','transferencia','mixto','credito') NOT NULL");
        }

        Schema::table('facturas', function (Blueprint $table) {
            $table->string('estado_pago', 24)->default('pagada')->after('metodo_pago');
            $table->decimal('total_pagado', 14, 2)->default(0)->after('total');
            $table->decimal('saldo_pendiente', 14, 2)->default(0)->after('total_pagado');
        });

        // Todas las facturas históricas se cobraron de contado en el momento de la venta.
        DB::table('facturas')->update([
            'estado_pago' => 'pagada',
            'total_pagado' => DB::raw('total'),
            'saldo_pendiente' => 0,
        ]);

        Schema::create('pagos_cliente', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('terceros');
            $table->date('fecha');
            $table->decimal('valor', 14, 2);
            $table->foreignId('metodo_pago_contable_id')->constrained('metodos_pago_contables');
            $table->string('referencia', 120)->nullable();
            $table->string('origen', 20)->default('abono');
            $table->string('estado', 20)->default('registrado');
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('comprobante_contable_id')->nullable()->constrained('comprobantes_contables')->nullOnDelete();
            $table->timestamps();
            $table->index(['cliente_id', 'estado']);
        });

        Schema::create('pago_cliente_aplicaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pago_cliente_id')->constrained('pagos_cliente')->cascadeOnDelete();
            $table->foreignId('factura_id')->constrained('facturas');
            $table->decimal('valor', 14, 2);
            $table->timestamps();
            $table->unique(['pago_cliente_id', 'factura_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pago_cliente_aplicaciones');
        Schema::dropIfExists('pagos_cliente');
        Schema::table('facturas', function (Blueprint $table) {
            $table->dropColumn(['estado_pago', 'total_pagado', 'saldo_pendiente']);
        });
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE facturas MODIFY metodo_pago ENUM('efectivo','tarjeta','transferencia','mixto') NOT NULL");
        }
    }
};
