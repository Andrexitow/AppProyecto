<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\{Bodega, Caja, Factura, FacturaDetalle, GrupoMenu, Mesa, Producto, Roles, Tercero, User, Zona};
use App\Services\VentaProductoReporteService;

class VentaProductoReporteServiceTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $username = 'venta-test'): User
    {
        $r = Roles::firstOrCreate(['nombre' => 'Administrador'], ['descripcion' => 'Test']);
        return User::firstOrCreate(['username' => $username], ['name' => ucfirst($username), 'password' => bcrypt('secret'), 'activo' => true, 'rol_id' => $r->id]);
    }

    private function caja(Bodega $bodega): Caja
    {
        return Caja::create(['nombre' => 'Caja Test', 'prefijo' => 'FR', 'proximo_numero' => 1, 'bodega_id' => $bodega->id, 'activa' => true]);
    }

    private function producto(string $codigo, string $categoria, ?int $grupoMenuId, float $ivaVentas = 0): Producto
    {
        return Producto::create(['codigo' => $codigo, 'descripcion' => $codigo, 'und_detal' => 'UND', 'categoria' => $categoria, 'grupo_menu_id' => $grupoMenuId, 'iva_ventas' => $ivaVentas]);
    }

    private function factura(Bodega $bodega, Caja $caja, User $user, ?Tercero $cliente, string $numero, string $fecha): Factura
    {
        $zona = Zona::create(['nombre' => 'Zona ' . $bodega->id, 'bodega_id' => $bodega->id]);
        $mesa = Mesa::create(['zona_id' => $zona->id, 'numero' => 'M-' . $numero, 'capacidad' => 4]);

        $factura = Factura::create([
            'numero_factura' => $numero,
            'mesa_id' => $mesa->id,
            'user_id' => $user->id,
            'cliente_id' => $cliente?->id,
            'caja_id' => $caja->id,
            'subtotal' => 0,
            'total' => 0,
            'metodo_pago' => 'efectivo',
            'estado' => 'pagada',
        ]);
        $factura->created_at = $fecha;
        $factura->save();

        return $factura;
    }

    public function test_agrupa_ventas_por_producto_y_calcula_con_y_sin_iva()
    {
        $bodega = Bodega::create(['descripcion' => 'Restaurante']);
        $caja = $this->caja($bodega);
        $user = $this->user();
        $producto = $this->producto('P1', 'Bebidas', null, 19); // 19% IVA incluido en el precio

        $f1 = $this->factura($bodega, $caja, $user, null, 'FR-00001', '2026-01-10 12:00:00');
        FacturaDetalle::create(['factura_id' => $f1->id, 'producto_id' => $producto->id, 'cantidad' => 2, 'precio_unitario' => 11900, 'subtotal' => 23800]);

        $f2 = $this->factura($bodega, $caja, $user, null, 'FR-00002', '2026-01-11 12:00:00');
        FacturaDetalle::create(['factura_id' => $f2->id, 'producto_id' => $producto->id, 'cantidad' => 1, 'precio_unitario' => 11900, 'subtotal' => 11900]);

        $servicio = app(VentaProductoReporteService::class);

        $conIva = $servicio->ventaPorProducto(['desde' => '2026-01-01 00:00:00', 'hasta' => '2026-01-31 23:59:59', 'con_iva' => true]);
        $this->assertEquals(1, $conIva['totales']['productos']);
        $this->assertEquals(3, $conIva['filas'][0]['cantidad_vendida']);

        // Trazabilidad: debe poder verse exactamente de qué facturas sale el total.
        $detalle = $conIva['filas'][0]['facturas_detalle'];
        $this->assertCount(2, $detalle);
        $this->assertEquals(['FR-00001', 'FR-00002'], $detalle->pluck('numero_factura')->all());
        $this->assertEquals(23800, $detalle->firstWhere('numero_factura', 'FR-00001')['valor']);
        $this->assertEquals(35700, $conIva['filas'][0]['valor_total']); // 23800 + 11900
        $this->assertEquals(2, $conIva['filas'][0]['facturas']);

        // Sin IVA: 35700 / 1.19 = 30000 (precio ya incluye el 19%)
        $sinIva = $servicio->ventaPorProducto(['desde' => '2026-01-01 00:00:00', 'hasta' => '2026-01-31 23:59:59', 'con_iva' => false]);
        $this->assertEqualsWithDelta(30000, $sinIva['filas'][0]['valor_total'], 0.5);
    }

    public function test_filtra_por_bodega_categoria_grupo_menu_y_producto()
    {
        $restaurante = Bodega::create(['descripcion' => 'Restaurante']);
        $discoteca = Bodega::create(['descripcion' => 'Discoteca']);
        $cajaR = $this->caja($restaurante);
        $cajaD = $this->caja($discoteca);
        $user = $this->user();
        $grupo = GrupoMenu::create(['nombre' => 'Bebidas Frías']);

        $comida = $this->producto('COM1', 'Comidas', null);
        $bebida = $this->producto('BEB1', 'Bebidas', $grupo->id);

        $f1 = $this->factura($restaurante, $cajaR, $user, null, 'FR-00001', '2026-01-10 12:00:00');
        FacturaDetalle::create(['factura_id' => $f1->id, 'producto_id' => $comida->id, 'cantidad' => 1, 'precio_unitario' => 20000, 'subtotal' => 20000]);

        $f2 = $this->factura($discoteca, $cajaD, $user, null, 'FD-00001', '2026-01-10 20:00:00');
        FacturaDetalle::create(['factura_id' => $f2->id, 'producto_id' => $bebida->id, 'cantidad' => 5, 'precio_unitario' => 10000, 'subtotal' => 50000]);

        $servicio = app(VentaProductoReporteService::class);
        $rango = ['desde' => '2026-01-01 00:00:00', 'hasta' => '2026-01-31 23:59:59'];

        $porBodega = $servicio->ventaPorProducto($rango + ['bodega_id' => $restaurante->id]);
        $this->assertCount(1, $porBodega['filas']);
        $this->assertEquals('COM1', $porBodega['filas'][0]['codigo']);

        $porPrefijo = $servicio->ventaPorProducto($rango + ['prefijo' => 'FD']);
        $this->assertCount(1, $porPrefijo['filas']);
        $this->assertEquals('BEB1', $porPrefijo['filas'][0]['codigo']);

        $porGrupoMenu = $servicio->ventaPorProducto($rango + ['grupo_menu_id' => $grupo->id]);
        $this->assertCount(1, $porGrupoMenu['filas']);
        $this->assertEquals('BEB1', $porGrupoMenu['filas'][0]['codigo']);

        $porCategoria = $servicio->ventaPorProducto($rango + ['categoria' => 'Comidas']);
        $this->assertCount(1, $porCategoria['filas']);
        $this->assertEquals('COM1', $porCategoria['filas'][0]['codigo']);

        $sinFiltro = $servicio->ventaPorProducto($rango);
        $this->assertCount(2, $sinFiltro['filas']);
    }

    public function test_excluye_facturas_anuladas()
    {
        $bodega = Bodega::create(['descripcion' => 'Restaurante']);
        $caja = $this->caja($bodega);
        $user = $this->user();
        $producto = $this->producto('P1', 'Bebidas', null);

        $f1 = $this->factura($bodega, $caja, $user, null, 'FR-00001', '2026-01-10 12:00:00');
        $f1->update(['estado' => 'anulada']);
        FacturaDetalle::create(['factura_id' => $f1->id, 'producto_id' => $producto->id, 'cantidad' => 1, 'precio_unitario' => 10000, 'subtotal' => 10000]);

        $servicio = app(VentaProductoReporteService::class);
        $reporte = $servicio->ventaPorProducto(['desde' => '2026-01-01 00:00:00', 'hasta' => '2026-01-31 23:59:59']);

        $this->assertCount(0, $reporte['filas']);
    }
}
