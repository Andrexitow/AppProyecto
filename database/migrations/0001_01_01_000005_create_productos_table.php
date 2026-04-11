<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();

            // OBLIGATORIOS
            $table->string('codigo')->unique();
            $table->string('descripcion');
            $table->string('und_detal');

            // OPCIONALES
            $table->string('codigo_barras')->nullable();
            $table->string('referencia')->nullable();
            $table->text('caracteristicas')->nullable();

            $table->string('und_mayor')->nullable();
            $table->string('und_adicional')->nullable();

            $table->decimal('factor_mayor', 10, 2)->nullable();
            $table->decimal('factor_adicional', 10, 2)->nullable();

            // 🔴 SIN RELACIONES (por ahora)
            $table->string('categoria')->nullable();
            $table->string('categoria2')->nullable();
            $table->string('linea')->nullable();
            $table->string('grupo_menu')->nullable();

            // INVENTARIO
            $table->boolean('afecta_inventario')->default(true);

            // IMPUESTOS
            $table->decimal('iva_ventas', 5, 2)->nullable();
            $table->decimal('ico_ventas', 5, 2)->nullable();
            $table->decimal('valor_ico_ventas', 10, 2)->nullable();

            $table->decimal('imp_saludable', 5, 2)->nullable();
            $table->decimal('valor_imp_saludable', 10, 2)->nullable();

            // CONTABLE
            $table->string('integracion_contable')->nullable();

            // COMPRAS
            $table->decimal('iva_compras', 5, 2)->nullable();
            $table->decimal('ico_compras', 5, 2)->nullable();

            // PRECIO
            $table->decimal('precio', 12, 2)->nullable();

            // ESTADO
            $table->boolean('inactivo')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
