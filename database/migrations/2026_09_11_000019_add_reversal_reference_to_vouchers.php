<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void { Schema::table('comprobantes_contables',function(Blueprint $t){$t->foreignId('comprobante_reversion_id')->nullable()->unique()->constrained('comprobantes_contables')->nullOnDelete();});}
 public function down(): void {Schema::table('comprobantes_contables',function(Blueprint $t){$t->dropForeign(['comprobante_reversion_id']);$t->dropUnique(['comprobante_reversion_id']);$t->dropColumn('comprobante_reversion_id');});}
};
