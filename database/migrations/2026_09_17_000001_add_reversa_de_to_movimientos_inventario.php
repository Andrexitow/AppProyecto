<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void { Schema::table('movimientos_inventario', function(Blueprint $t) { $t->foreignId('reversa_de_id')->nullable()->unique()->constrained('movimientos_inventario')->restrictOnDelete(); }); }
    public function down(): void { Schema::table('movimientos_inventario', function(Blueprint $t) { $t->dropForeign(['reversa_de_id']); $t->dropColumn('reversa_de_id'); }); }
};
