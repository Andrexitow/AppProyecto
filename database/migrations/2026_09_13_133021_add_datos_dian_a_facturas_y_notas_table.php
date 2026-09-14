<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Respuesta del proveedor de facturación electrónica (CUFE, XML firmado,
 * PDF, estado ante la DIAN), tanto para facturas como para notas
 * crédito/débito. `estado_dian` reemplaza al viejo `doc_electronico`
 * booleano como fuente de verdad — ese campo nunca se llegó a poblar desde
 * ningún flujo real (ver auditoría DIAN, hallazgo #3) y se deja intacto por
 * compatibilidad, pero de aquí en adelante el estado real vive en
 * `estado_dian`.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facturas', function (Blueprint $table) {
            $table->string('cufe')->nullable()->after('doc_electronico_fecha');
            $table->string('xml_url')->nullable()->after('cufe');
            $table->string('pdf_url')->nullable()->after('xml_url');
            $table->text('qr_texto')->nullable()->after('pdf_url');
            $table->string('estado_dian', 20)->default('no_aplica')->after('qr_texto');
            $table->text('mensaje_dian')->nullable()->after('estado_dian');
            $table->timestamp('fecha_transmision_dian')->nullable()->after('mensaje_dian');
        });

        Schema::table('notas_factura', function (Blueprint $table) {
            $table->string('cufe')->nullable();
            $table->string('xml_url')->nullable();
            $table->string('pdf_url')->nullable();
            $table->text('qr_texto')->nullable();
            $table->string('estado_dian', 20)->default('no_aplica');
            $table->text('mensaje_dian')->nullable();
            $table->timestamp('fecha_transmision_dian')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('facturas', function (Blueprint $table) {
            $table->dropColumn(['cufe', 'xml_url', 'pdf_url', 'qr_texto', 'estado_dian', 'mensaje_dian', 'fecha_transmision_dian']);
        });

        Schema::table('notas_factura', function (Blueprint $table) {
            $table->dropColumn(['cufe', 'xml_url', 'pdf_url', 'qr_texto', 'estado_dian', 'mensaje_dian', 'fecha_transmision_dian']);
        });
    }
};
