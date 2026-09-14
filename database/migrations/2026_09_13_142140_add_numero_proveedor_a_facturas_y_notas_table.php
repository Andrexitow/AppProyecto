<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * El número que el PROVEEDOR (Factus, etc.) le asigna al documento (p. ej.
 * "SETP990001131") es distinto tanto de nuestro propio numero_factura como
 * del CUFE — y es el dato que hay que enviar como `bill_number` al emitir
 * una nota crédito/débito sobre esa factura. Sin guardarlo no hay forma de
 * referenciar la factura ante el proveedor después de crearla.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facturas', function (Blueprint $table) {
            $table->string('numero_proveedor')->nullable()->after('cufe');
        });

        Schema::table('notas_factura', function (Blueprint $table) {
            $table->string('numero_proveedor')->nullable()->after('cufe');
        });
    }

    public function down(): void
    {
        Schema::table('facturas', function (Blueprint $table) {
            $table->dropColumn('numero_proveedor');
        });

        Schema::table('notas_factura', function (Blueprint $table) {
            $table->dropColumn('numero_proveedor');
        });
    }
};
