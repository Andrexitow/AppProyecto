<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\TipoDocumentoSeeder;
use App\Models\{Bodega, Inventario, MovimientoInventario, Producto, Roles, Tercero, User};
use App\Models\{Ajuste, TrasladoBodega};
use App\Services\KardexService;

class KardexServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TipoDocumentoSeeder::class);
    }

    private function user(): User
    {
        $r = Roles::firstOrCreate(['nombre' => 'Administrador'], ['descripcion' => 'Test']);
        return User::firstOrCreate(['username' => 'kardex-test'], ['name' => 'Kardex Test', 'password' => bcrypt('secret'), 'activo' => true, 'rol_id' => $r->id]);
    }

    private function producto(string $codigo = 'P1'): Producto
    {
        return Producto::create(['codigo' => $codigo, 'descripcion' => $codigo, 'und_detal' => 'UND']);
    }

    private function bodega(string $descripcion): Bodega
    {
        return Bodega::create(['descripcion' => $descripcion]);
    }

    /** El costo promedio ponderado se recalcula en cada entrada y se conserva en las salidas. */
    public function test_costo_promedio_ponderado_se_calcula_en_entradas_y_se_conserva_en_salidas()
    {
        $producto = $this->producto();
        $bodega = $this->bodega('Principal');
        $kardex = app(KardexService::class);

        // Entrada: 10 unidades a $100
        $documentoId = $this->documentoDummy();

        $kardex->registrar($documentoId, null, $producto->id, $bodega->id, 'ENTRADA', 10, 0, 10, 100, $this->user()->id);
        $inv = Inventario::where(['producto_id' => $producto->id, 'bodega_id' => $bodega->id])->first();
        $this->assertEquals(100, (float) $inv->costo_promedio);

        // Entrada: 10 unidades a $120 -> promedio (10*100 + 10*120)/20 = 110
        $kardex->registrar($documentoId, null, $producto->id, $bodega->id, 'ENTRADA', 10, 10, 20, 120, $this->user()->id);
        $inv->refresh();
        $this->assertEquals(110, (float) $inv->costo_promedio);

        // Salida de 5 unidades: sale al costo promedio vigente (110) y el promedio no cambia
        $movSalida = $kardex->registrar($documentoId, null, $producto->id, $bodega->id, 'SALIDA', 5, 20, 15, null, $this->user()->id);
        $inv->refresh();
        $this->assertEquals(110, (float) $movSalida->costo_unitario);
        $this->assertEquals(550, (float) $movSalida->valor_movimiento);
        $this->assertEquals(110, (float) $inv->costo_promedio);
    }

    public function test_el_costo_promedio_es_independiente_por_bodega()
    {
        $producto = $this->producto();
        $bodegaA = $this->bodega('A');
        $bodegaB = $this->bodega('B');
        $kardex = app(KardexService::class);
        $documentoId = $this->documentoDummy();

        $kardex->registrar($documentoId, null, $producto->id, $bodegaA->id, 'ENTRADA', 10, 0, 10, 50, $this->user()->id);
        $kardex->registrar($documentoId, null, $producto->id, $bodegaB->id, 'ENTRADA', 10, 0, 10, 200, $this->user()->id);

        $this->assertEquals(50, (float) Inventario::where(['producto_id' => $producto->id, 'bodega_id' => $bodegaA->id])->value('costo_promedio'));
        $this->assertEquals(200, (float) Inventario::where(['producto_id' => $producto->id, 'bodega_id' => $bodegaB->id])->value('costo_promedio'));
    }

    /**
     * Encontrado probando el sistema de punta a punta: un plato de cocina con
     * afecta_inventario=false generaba de todos modos un movimiento de SALIDA
     * y una fila de inventarios en 0 de la nada, porque
     * LegacyDocumentSyncService::factura() nunca revisaba esa bandera (a
     * diferencia de FacturacionController::cerrarMesa(), que sí la respeta al
     * descontar el stock real).
     */
    public function test_venta_de_producto_sin_afecta_inventario_no_deja_movimiento_de_kardex()
    {
        $bodega = $this->bodega('Principal');
        $usuario = $this->user();
        $platoCocina = Producto::create(['codigo' => 'PLATO', 'descripcion' => 'Plato de cocina', 'und_detal' => 'UND', 'afecta_inventario' => false]);
        $cerveza = $this->producto('CERVEZA'); // afecta_inventario=true por defecto

        $caja = \App\Models\Caja::create(['nombre' => 'Caja Test', 'prefijo' => 'FT', 'bodega_id' => $bodega->id, 'activa' => true]);
        $cliente = Tercero::create(['tipo' => 'empresa', 'razon_social' => 'Consumidor Final']);
        $mesa = \App\Models\Mesa::create(['zona_id' => \App\Models\Zona::create(['nombre' => 'Zona Test'])->id, 'numero' => '1', 'estado' => 'disponible']);

        $factura = \App\Models\Factura::create([
            'numero_factura' => 'FT-00001', 'mesa_id' => $mesa->id, 'user_id' => $usuario->id, 'cliente_id' => $cliente->id,
            'caja_id' => $caja->id, 'subtotal' => 15000, 'total' => 15000, 'metodo_pago' => 'efectivo', 'estado' => 'pagada',
        ]);
        $factura->detalles()->create(['producto_id' => $platoCocina->id, 'cantidad' => 1, 'precio_unitario' => 10000, 'subtotal' => 10000]);
        $factura->detalles()->create(['producto_id' => $cerveza->id, 'cantidad' => 1, 'precio_unitario' => 5000, 'subtotal' => 5000]);

        app(\App\Services\LegacyDocumentSyncService::class)->factura($factura->fresh());

        $this->assertEquals(0, MovimientoInventario::where('producto_id', $platoCocina->id)->count(), 'Un producto con afecta_inventario=false no debe dejar historial de kardex.');
        // No debe crearse una fila de inventario fantasma para un producto no rastreado.
        $this->assertDatabaseMissing('inventarios', ['producto_id' => $platoCocina->id]);

        $this->assertEquals(1, MovimientoInventario::where('producto_id', $cerveza->id)->where('tipo', 'SALIDA')->count(), 'Un producto con afecta_inventario=true sí debe dejar su salida en el kardex.');
    }

    public function test_ajuste_de_entrada_y_salida_deja_historial_de_kardex()
    {
        $producto = $this->producto();
        $bodega = $this->bodega('Principal');
        $usuario = $this->user();

        $ajuste = Ajuste::create(['prefijo' => 'AJ', 'numero' => 1, 'fecha' => now()->toDateString(), 'bodega_id' => $bodega->id, 'total' => 0, 'registrado' => false, 'user_id' => $usuario->id]);

        $this->actingAs($usuario)->postJson("/ajustes/{$ajuste->id}/detalles", [
            'detalles' => [
                ['producto_id' => $producto->id, 'cantidad' => 8, 'tipo' => 'entrada', 'precio' => 0],
            ],
        ])->assertOk();

        $this->assertEquals(8, (float) Inventario::where(['producto_id' => $producto->id, 'bodega_id' => $bodega->id])->value('stock'));
        $this->assertDatabaseHas('movimientos_inventario', ['producto_id' => $producto->id, 'bodega_id' => $bodega->id, 'tipo' => 'ENTRADA', 'cantidad' => 8, 'stock_anterior' => 0, 'stock_nuevo' => 8]);

        $ajuste2 = Ajuste::create(['prefijo' => 'AJ', 'numero' => 2, 'fecha' => now()->toDateString(), 'bodega_id' => $bodega->id, 'total' => 0, 'registrado' => false, 'user_id' => $usuario->id]);
        $this->actingAs($usuario)->postJson("/ajustes/{$ajuste2->id}/detalles", [
            'detalles' => [
                ['producto_id' => $producto->id, 'cantidad' => 3, 'tipo' => 'salida', 'precio' => 0],
            ],
        ])->assertOk();

        $this->assertEquals(5, (float) Inventario::where(['producto_id' => $producto->id, 'bodega_id' => $bodega->id])->value('stock'));
        $this->assertDatabaseHas('movimientos_inventario', ['producto_id' => $producto->id, 'bodega_id' => $bodega->id, 'tipo' => 'SALIDA', 'cantidad' => 3, 'stock_anterior' => 8, 'stock_nuevo' => 5]);
    }

    public function test_traslado_arrastra_el_costo_promedio_de_la_bodega_origen_a_la_destino()
    {
        $producto = $this->producto();
        $origen = $this->bodega('Origen');
        $destino = $this->bodega('Destino');
        $usuario = $this->user();

        Inventario::create(['producto_id' => $producto->id, 'bodega_id' => $origen->id, 'stock' => 10, 'costo_promedio' => 80]);
        Inventario::create(['producto_id' => $producto->id, 'bodega_id' => $destino->id, 'stock' => 6, 'costo_promedio' => 100]);

        $traslado = TrasladoBodega::create(['prefijo' => 'TR', 'consecutivo' => 1, 'fecha' => now()->toDateString(), 'bodega_origen_id' => $origen->id, 'bodega_destino_id' => $destino->id, 'estado' => 'borrador', 'user_id' => $usuario->id]);
        $traslado->detalles()->create(['producto_id' => $producto->id, 'cantidad' => 4]);

        $this->actingAs($usuario)->postJson("/traslados-bodega/{$traslado->id}/registrar")->assertOk();

        $this->assertEquals(6, (float) Inventario::where(['producto_id' => $producto->id, 'bodega_id' => $origen->id])->value('stock'));
        $this->assertEquals(10, (float) Inventario::where(['producto_id' => $producto->id, 'bodega_id' => $destino->id])->value('stock'));

        // La salida en origen se valora a su propio costo promedio (80).
        $this->assertDatabaseHas('movimientos_inventario', ['producto_id' => $producto->id, 'bodega_id' => $origen->id, 'tipo' => 'SALIDA', 'costo_unitario' => 80]);

        // La entrada en destino hereda ese mismo costo (80), no el propio de destino.
        $this->assertDatabaseHas('movimientos_inventario', ['producto_id' => $producto->id, 'bodega_id' => $destino->id, 'tipo' => 'ENTRADA', 'costo_unitario' => 80]);

        // Nuevo promedio en destino: (6*100 + 4*80) / 10 = 92
        $this->assertEquals(92, (float) Inventario::where(['producto_id' => $producto->id, 'bodega_id' => $destino->id])->value('costo_promedio'));

        // El promedio de origen no cambia por una salida.
        $this->assertEquals(80, (float) Inventario::where(['producto_id' => $producto->id, 'bodega_id' => $origen->id])->value('costo_promedio'));
    }

    /** Crea un Documento mínimo válido para poder registrar movimientos de kardex sueltos en las pruebas. */
    private function documentoDummy(): int
    {
        $tipo = \App\Models\TipoDocumento::where('codigo', 'AJUSTE')->firstOrFail();
        return \App\Models\Documento::create([
            'tipo_documento_id' => $tipo->id,
            'prefijo' => 'TST',
            'numero' => (string) random_int(1, 999999),
            'fecha' => now()->toDateString(),
            'user_id' => $this->user()->id,
            'estado' => 'registrado',
        ])->id;
    }
}
