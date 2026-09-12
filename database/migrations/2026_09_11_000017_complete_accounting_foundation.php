<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('cuentas_contables', function (Blueprint $table) {
            $table->boolean('requiere_tercero')->default(false)->after('permite_movimientos');
            $table->boolean('requiere_centro_costo')->default(false)->after('requiere_tercero');
        });

        Schema::create('centros_costo', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30)->unique();
            $table->string('nombre', 120);
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });

        Schema::table('movimientos_contables', function (Blueprint $table) {
            $table->foreign('centro_costo_id')->references('id')->on('centros_costo')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('movimientos_contables', function (Blueprint $table) {
            $table->dropForeign(['centro_costo_id']);
        });
        Schema::dropIfExists('centros_costo');
        Schema::table('cuentas_contables', function (Blueprint $table) {
            $table->dropColumn(['requiere_tercero', 'requiere_centro_costo']);
        });
    }
};
