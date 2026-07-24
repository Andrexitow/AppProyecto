<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comprobantes_contables', function (Blueprint $table) {

            $table->id();

            $table->foreignId('tipo_documento_contable_id')
                ->constrained('tipos_documento_contable');

            $table->string('numero',30);

            $table->date('fecha');

            $table->text('observacion')->nullable();

            $table->foreignId('usuario_id')
                ->constrained('users');

            // Documento que originó el comprobante
            $table->string('documento_origen')->nullable();

            // Id del documento origen (factura, compra, etc.)
            $table->unsignedBigInteger('documento_origen_id')->nullable();

            $table->enum('estado',[
                'BORRADOR',
                'CONTABILIZADO',
                'ANULADO'
            ])->default('BORRADOR');

            $table->timestamps();

            $table->unique([
                'tipo_documento_contable_id',
                'numero'
            ]);

            $table->index('fecha');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comprobantes_contables');
    }
};