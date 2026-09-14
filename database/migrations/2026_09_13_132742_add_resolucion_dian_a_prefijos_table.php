<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Datos de la resolución de facturación que la DIAN autoriza POR PREFIJO
 * (rango de numeración, vigencia, clave técnica). La auditoría de
 * facturación electrónica señaló esto como una carencia concreta: el
 * catálogo de Prefijos solo tenía codigo/nombre/descripcion, sin ningún dato
 * de la resolución en sí. Se agrega aquí (no en una tabla nueva) porque la
 * resolución es, precisamente, un atributo del prefijo autorizado.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prefijos', function (Blueprint $table) {
            $table->string('resolucion_numero', 30)->nullable()->after('descripcion');
            $table->date('resolucion_fecha')->nullable()->after('resolucion_numero');
            $table->unsignedBigInteger('rango_desde')->nullable()->after('resolucion_fecha');
            $table->unsignedBigInteger('rango_hasta')->nullable()->after('rango_desde');
            $table->date('vigencia_desde')->nullable()->after('rango_hasta');
            $table->date('vigencia_hasta')->nullable()->after('vigencia_desde');
            $table->string('clave_tecnica', 100)->nullable()->after('vigencia_hasta');
        });
    }

    public function down(): void
    {
        Schema::table('prefijos', function (Blueprint $table) {
            $table->dropColumn(['resolucion_numero', 'resolucion_fecha', 'rango_desde', 'rango_hasta', 'vigencia_desde', 'vigencia_hasta', 'clave_tecnica']);
        });
    }
};
