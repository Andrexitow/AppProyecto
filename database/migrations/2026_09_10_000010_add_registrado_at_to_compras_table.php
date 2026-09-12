<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('compras', function (Blueprint $table) {
            $table->timestamp('registrado_at')->nullable()->after('estado');
        });

        DB::table('compras')
            ->where('estado', 'confirmada')
            ->update(['registrado_at' => DB::raw('created_at')]);
    }

    public function down(): void
    {
        Schema::table('compras', function (Blueprint $table) {
            $table->dropColumn('registrado_at');
        });
    }
};
