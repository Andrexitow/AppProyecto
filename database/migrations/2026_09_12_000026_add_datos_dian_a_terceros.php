<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Datos que la DIAN exige para el reporte de información exógena (Formato
 * 1001) y que hasta ahora no existían en el directorio de terceros: sin
 * ciudad no se puede diligenciar el código de municipio DANE de cada
 * cliente/proveedor, y sin régimen tributario no se sabe si a un proveedor
 * hay que practicarle retención (un Gran Contribuyente o autorretenedor de
 * renta no se retiene de la misma forma que un responsable de IVA común).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('terceros', function (Blueprint $table) {
            $table->string('ciudad')->nullable()->after('direccion');
            $table->string('regimen_tributario')->nullable()->after('ciudad');
            $table->string('codigo_ciiu', 10)->nullable()->after('regimen_tributario');
        });
    }

    public function down(): void
    {
        Schema::table('terceros', function (Blueprint $table) {
            $table->dropColumn(['ciudad', 'regimen_tributario', 'codigo_ciiu']);
        });
    }
};
