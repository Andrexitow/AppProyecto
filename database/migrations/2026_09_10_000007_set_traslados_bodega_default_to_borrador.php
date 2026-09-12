<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('traslados_bodega', function (Blueprint $table) {
            $table->string('estado', 20)->default('borrador')->change();
        });
    }

    public function down(): void
    {
        Schema::table('traslados_bodega', function (Blueprint $table) {
            $table->string('estado', 20)->default('confirmado')->change();
        });
    }
};
