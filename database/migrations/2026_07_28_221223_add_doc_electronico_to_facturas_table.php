<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facturas', function (Blueprint $table) {
            $table->boolean('doc_electronico')->default(false)->after('estado');
            $table->timestamp('doc_electronico_fecha')->nullable()->after('doc_electronico');
        });
    }

    public function down(): void
    {
        Schema::table('facturas', function (Blueprint $table) {
            $table->dropColumn(['doc_electronico', 'doc_electronico_fecha']);
        });
    }
};