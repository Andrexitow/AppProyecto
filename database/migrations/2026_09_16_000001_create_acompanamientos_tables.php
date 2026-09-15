<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Acompañamientos: un grupo de productos elegibles con un máximo de
 * unidades total (ej. "Servicio de Cubetazo" = hasta 10 unidades a repartir
 * libremente entre Poker/Águila/Costeña). Un producto de venta (ej.
 * "Cubetazo Mix") se vende a SU PROPIO precio fijo y se liga a uno de estos
 * grupos — al comandarlo, el mesero reparte el máximo entre las opciones
 * del grupo, y eso es lo que se descuenta del inventario (ver
 * FacturacionController::cerrarMesa() y el módulo de Consumos).
 *
 * A diferencia de "producto ensamblado" (un solo insumo, factor fijo), aquí
 * hay VARIAS opciones y el reparto lo decide el mesero en el momento.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('acompanamiento_grupos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->string('descripcion');
            $table->unsignedInteger('cantidad_maxima');
            $table->timestamps();
        });

        Schema::create('acompanamiento_opciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('acompanamiento_grupo_id')->constrained('acompanamiento_grupos')->cascadeOnDelete();
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['acompanamiento_grupo_id', 'producto_id'], 'acomp_opciones_grupo_producto_unique');
        });

        Schema::table('productos', function (Blueprint $table) {
            $table->foreignId('acompanamiento_grupo_id')
                ->nullable()
                ->after('factor_consumo')
                ->constrained('acompanamiento_grupos')
                ->nullOnDelete();
        });

        // Reparto real elegido por el mesero para una línea de pedido
        // concreta (ej. esta comanda de "Cubetazo Mix" llevó 4 Poker + 3
        // Águila + 3 Costeña). Vive del lado del pedido para sobrevivir
        // mientras la mesa sigue abierta, antes de facturar.
        Schema::create('detalle_pedido_acompanamientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('detalle_pedido_id')->constrained('detalle_pedidos')->cascadeOnDelete();
            $table->foreignId('producto_id')->constrained('productos');
            $table->unsignedInteger('cantidad');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_pedido_acompanamientos');
        Schema::table('productos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('acompanamiento_grupo_id');
        });
        Schema::dropIfExists('acompanamiento_opciones');
        Schema::dropIfExists('acompanamiento_grupos');
    }
};
