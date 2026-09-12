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
            $table->unsignedInteger('consecutivo')->default(0)->after('prefijo');
            $table->foreignId('user_id')->nullable()->after('proveedor_id')->constrained('users')->nullOnDelete();
        });

        $consecutivos = [];
        DB::table('compras')
            ->select(['id', 'prefijo'])
            ->orderBy('prefijo')
            ->orderBy('created_at')
            ->orderBy('id')
            ->each(function ($compra) use (&$consecutivos) {
                $prefijo = $compra->prefijo ?: 'FC';
                $consecutivos[$prefijo] = ($consecutivos[$prefijo] ?? 0) + 1;

                DB::table('compras')
                    ->where('id', $compra->id)
                    ->update(['consecutivo' => $consecutivos[$prefijo]]);
            });

        Schema::table('compras', function (Blueprint $table) {
            $table->unique(['prefijo', 'consecutivo']);
        });
    }

    public function down(): void
    {
        Schema::table('compras', function (Blueprint $table) {
            $table->dropUnique(['prefijo', 'consecutivo']);
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn('consecutivo');
        });
    }
};
