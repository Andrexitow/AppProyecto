<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('compras', function (Blueprint $table) {
            $table->string('estado_pago', 24)->default('pendiente')->after('estado');
            $table->decimal('total_pagado', 14, 2)->default(0)->after('total');
            $table->decimal('saldo_pendiente', 14, 2)->default(0)->after('total_pagado');
            $table->timestamp('contabilizado_at')->nullable()->after('registrado_at');
            $table->index(['estado', 'estado_pago']);
        });
        DB::table('compras')->where('estado', 'confirmada')->update(['saldo_pendiente' => DB::raw('total')]);

        Schema::create('pagos_proveedor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proveedor_id')->constrained('terceros');
            $table->date('fecha');
            $table->decimal('valor', 14, 2);
            $table->foreignId('metodo_pago_contable_id')->constrained('metodos_pago_contables');
            $table->string('referencia', 120)->nullable();
            $table->string('origen', 20)->default('abono');
            $table->string('estado', 20)->default('registrado');
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('comprobante_contable_id')->nullable()->constrained('comprobantes_contables')->nullOnDelete();
            $table->timestamps();
            $table->index(['proveedor_id', 'estado']);
        });

        Schema::create('pago_proveedor_aplicaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pago_proveedor_id')->constrained('pagos_proveedor')->cascadeOnDelete();
            $table->foreignId('compra_id')->constrained('compras');
            $table->decimal('valor', 14, 2);
            $table->timestamps();
            $table->unique(['pago_proveedor_id', 'compra_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pago_proveedor_aplicaciones');
        Schema::dropIfExists('pagos_proveedor');
        Schema::table('compras', function (Blueprint $table) {
            $table->dropIndex(['estado', 'estado_pago']);
            $table->dropColumn(['estado_pago', 'total_pagado', 'saldo_pendiente', 'contabilizado_at']);
        });
    }
};
