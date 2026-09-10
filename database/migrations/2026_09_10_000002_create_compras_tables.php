<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('compras', function(Blueprint $t){$t->id();$t->string('prefijo',10);$t->string('numero_factura',50);$t->foreignId('proveedor_id')->constrained('terceros');$t->date('fecha');$t->text('observaciones')->nullable();$t->enum('estado',['borrador','confirmada','anulada'])->default('borrador');$t->decimal('subtotal',14,2)->default(0);$t->decimal('descuentos',14,2)->default(0);$t->decimal('iva',14,2)->default(0);$t->decimal('ico',14,2)->default(0);$t->decimal('retenciones',14,2)->default(0);$t->decimal('otros_cargos',14,2)->default(0);$t->decimal('total',14,2)->default(0);$t->timestamps();$t->unique(['prefijo','numero_factura']);});
  Schema::create('compra_detalles', function(Blueprint $t){$t->id();$t->foreignId('compra_id')->constrained('compras')->cascadeOnDelete();$t->foreignId('producto_id')->constrained('productos');$t->foreignId('bodega_id')->constrained('bodegas');$t->decimal('cantidad',14,3);$t->decimal('costo_unitario',14,2);$t->decimal('descuento_porcentaje',8,2)->default(0);$t->decimal('iva_porcentaje',8,2)->default(0);$t->decimal('ico_porcentaje',8,2)->default(0);$t->decimal('retefuente',14,2)->default(0);$t->decimal('reteiva',14,2)->default(0);$t->decimal('reteica',14,2)->default(0);$t->decimal('otros_cargos',14,2)->default(0);$t->decimal('subtotal',14,2);$t->decimal('total',14,2);$t->timestamps();});
  Schema::create('compra_pagos', function(Blueprint $t){$t->id();$t->foreignId('compra_id')->constrained('compras')->cascadeOnDelete();$t->string('metodo_pago',30);$t->decimal('valor',14,2);$t->string('referencia')->nullable();$t->timestamps();});
 }
 public function down(): void {Schema::dropIfExists('compra_pagos');Schema::dropIfExists('compra_detalles');Schema::dropIfExists('compras');}
};
