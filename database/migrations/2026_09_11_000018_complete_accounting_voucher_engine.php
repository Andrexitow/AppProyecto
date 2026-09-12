<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration {
 public function up(): void {
  Schema::table('comprobantes_contables', function(Blueprint $t){
   $t->string('tipo',100)->nullable()->after('tipo_documento_contable_id');
   $t->string('prefijo',20)->nullable()->after('tipo');
   $t->foreignId('tercero_id')->nullable()->constrained('terceros')->nullOnDelete();
   $t->string('descripcion',2000)->nullable()->after('observacion');
   $t->decimal('total_debito',18,2)->default(0);
   $t->decimal('total_credito',18,2)->default(0);
   $t->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
   $t->timestamp('registrado_at')->nullable();
   $t->foreignId('anulado_por')->nullable()->constrained('users')->nullOnDelete();
   $t->timestamp('anulado_at')->nullable();
   $t->text('motivo_anulacion')->nullable();
  });
  Schema::table('movimientos_contables', function(Blueprint $t){ $t->string('documento_referencia',100)->nullable()->after('referencia'); });
  if (DB::getDriverName() !== 'sqlite') DB::statement("ALTER TABLE comprobantes_contables MODIFY estado ENUM('BORRADOR','REGISTRADO','CONTABILIZADO','ANULADO') NOT NULL DEFAULT 'BORRADOR'");
 }
 public function down(): void { Schema::table('movimientos_contables',fn(Blueprint $t)=>$t->dropColumn('documento_referencia')); }
};
