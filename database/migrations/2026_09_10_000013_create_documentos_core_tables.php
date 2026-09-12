<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tipos_documento', function (Blueprint $table) {
            $table->id(); $table->string('codigo', 40)->unique(); $table->string('nombre');
            $table->string('prefijo_default', 10)->nullable(); $table->boolean('afecta_inventario')->default(false);
            $table->boolean('afecta_caja')->default(false); $table->boolean('afecta_contabilidad')->default(false);
            $table->enum('naturaleza', ['ingreso','egreso','neutro'])->default('neutro'); $table->boolean('activo')->default(true); $table->timestamps();
        });
        Schema::create('documento_consecutivos', function (Blueprint $table) {
            $table->id(); $table->foreignId('tipo_documento_id')->constrained('tipos_documento'); $table->string('prefijo', 10);
            $table->unsignedBigInteger('siguiente_numero')->default(1); $table->timestamps(); $table->unique(['tipo_documento_id','prefijo']);
        });
        Schema::create('documentos', function (Blueprint $table) {
            $table->id(); $table->foreignId('tipo_documento_id')->constrained('tipos_documento'); $table->string('prefijo',10); $table->string('numero',50); $table->unsignedBigInteger('consecutivo')->nullable();
            $table->date('fecha'); $table->time('hora')->nullable(); $table->foreignId('tercero_id')->nullable()->constrained('terceros')->nullOnDelete(); $table->foreignId('bodega_id')->nullable()->constrained('bodegas')->nullOnDelete(); $table->foreignId('caja_id')->nullable()->constrained('cajas')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users'); $table->decimal('subtotal',18,2)->default(0); $table->decimal('descuento',18,2)->default(0); $table->decimal('impuestos',18,2)->default(0); $table->decimal('retenciones',18,2)->default(0); $table->decimal('total',18,2)->default(0);
            $table->text('observaciones')->nullable(); $table->enum('estado',['borrador','registrado','anulado'])->default('borrador'); $table->timestamp('registrado_at')->nullable(); $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete(); $table->timestamp('anulado_at')->nullable(); $table->foreignId('anulado_por')->nullable()->constrained('users')->nullOnDelete(); $table->foreignId('documento_origen_id')->nullable()->constrained('documentos')->nullOnDelete(); $table->string('referencia_externa',100)->nullable(); $table->timestamps();
            $table->unique(['tipo_documento_id','prefijo','numero']); $table->index(['fecha','estado']);
        });
        Schema::create('documento_detalles', function (Blueprint $table) {
            $table->id(); $table->foreignId('documento_id')->constrained('documentos')->cascadeOnDelete(); $table->foreignId('producto_id')->nullable()->constrained('productos')->nullOnDelete(); $table->string('descripcion'); $table->decimal('cantidad',14,3)->default(0); $table->decimal('precio_unitario',18,2)->default(0); $table->decimal('descuento',18,2)->default(0); $table->decimal('impuesto',18,2)->default(0); $table->decimal('subtotal',18,2)->default(0); $table->decimal('total',18,2)->default(0); $table->string('tipo_movimiento_inventario',20)->nullable(); $table->foreignId('bodega_origen_id')->nullable()->constrained('bodegas')->nullOnDelete(); $table->foreignId('bodega_destino_id')->nullable()->constrained('bodegas')->nullOnDelete(); $table->timestamps();
        });
        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id(); $table->foreignId('documento_id')->constrained('documentos'); $table->foreignId('documento_detalle_id')->nullable()->constrained('documento_detalles')->nullOnDelete(); $table->foreignId('producto_id')->constrained('productos'); $table->foreignId('bodega_id')->constrained('bodegas'); $table->enum('tipo',['ENTRADA','SALIDA']); $table->decimal('cantidad',14,3); $table->decimal('stock_anterior',14,3); $table->decimal('stock_nuevo',14,3); $table->dateTime('fecha'); $table->foreignId('user_id')->constrained('users'); $table->timestamps(); $table->index(['producto_id','bodega_id','fecha']);
        });
        Schema::table('comprobantes_contables', function (Blueprint $table) { $table->foreignId('documento_id')->nullable()->after('id')->constrained('documentos')->nullOnDelete(); $table->index('documento_id'); });
    }
    public function down(): void { Schema::table('comprobantes_contables', fn(Blueprint $t) => $t->dropConstrainedForeignId('documento_id')); Schema::dropIfExists('movimientos_inventario'); Schema::dropIfExists('documento_detalles'); Schema::dropIfExists('documentos'); Schema::dropIfExists('documento_consecutivos'); Schema::dropIfExists('tipos_documento'); }
};
