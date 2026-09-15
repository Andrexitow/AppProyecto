<?php

namespace Tests\Feature;

use App\Models\Bodega;
use App\Models\Caja;
use App\Models\DetallePedido;
use App\Models\Factura;
use App\Models\Inventario;
use App\Models\Mesa;
use App\Models\MovimientoInventario;
use App\Models\Pedido;
use App\Models\Prefijo;
use App\Models\Producto;
use App\Models\Roles;
use App\Models\Tercero;
use App\Models\User;
use App\Models\Zona;
use Database\Seeders\ConfiguracionContableSeeder;
use Database\Seeders\IntegracionContableSeeder;
use Database\Seeders\MetodoPagoContableSeeder;
use Database\Seeders\ParametrizacionInicialContableSeeder;
use Database\Seeders\PlantillaContableSeeder;
use Database\Seeders\ProcesoContableSeeder;
use Database\Seeders\PucSeeder;
use Database\Seeders\TipoDocumentoContableSeeder;
use Database\Seeders\TipoDocumentoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Cubre los hallazgos #1, #2 y #4 de la revisión de seguridad del POS:
 * - guardarPedido() confiaba en el precio/producto que mandara el navegador.
 * - liberarMesa() borraba el pedido de cualquiera sin validar rol, dueño
 *   ni si ya se había enviado a cocina.
 * - varias acciones del POS solo exigían "auth", sin rol.
 *
 * Y, de la segunda ronda, los hallazgos #1 y #2 de riesgos de concurrencia:
 * - el consecutivo de factura se calculaba por caja, no por prefijo, así
 *   que dos cajas con el mismo prefijo podían generar el mismo número.
 * - el stock se validaba línea por línea, no agrupado por producto, así
 *   que dos líneas del mismo producto en un mismo pedido podían pasar la
 *   validación aunque su suma superara el stock disponible.
 */
class FacturacionControllerSeguridadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // pedidos.cliente_id tiene default(1) + FK a terceros — sin esta
        // fila, cualquier Pedido::create() sin cliente_id explícito falla.
        Tercero::create([
            'id' => 1,
            'tipo' => 'persona',
            'nombre' => 'Consumidor',
            'apellido' => 'Final',
            'celular' => '0000000000',
        ]);

        // LegacyDocumentSyncService::factura() (llamado por cerrarMesa)
        // exige un TipoDocumento con codigo 'VENTA'.
        $this->seed(TipoDocumentoSeeder::class);

        // Cadena contable real (mismo orden que DatabaseSeeder): cerrarMesa()
        // ahora EXIGE que cada producto tenga integración + proceso válidos
        // (hallazgo #4), así que contabilizar() debe poder completarse de
        // verdad — no basta con una IntegracionContable/ProcesoContable
        // fabricados a mano sin su plantilla real detrás.
        $this->seed(TipoDocumentoContableSeeder::class);
        $this->seed(ConfiguracionContableSeeder::class);
        $this->seed(ProcesoContableSeeder::class);
        $this->seed(PlantillaContableSeeder::class);
        $this->seed(PucSeeder::class);
        $this->seed(ParametrizacionInicialContableSeeder::class);
        $this->seed(IntegracionContableSeeder::class);
        $this->seed(MetodoPagoContableSeeder::class);
    }

    private function usuario(string $rol): User
    {
        static $contador = 0;
        $contador++;
        $rolModelo = Roles::firstOrCreate(['nombre' => $rol], ['descripcion' => 'Test']);

        return User::create([
            'username' => 'test-' . strtolower($rol) . '-' . $contador,
            'name' => $rol . ' Test',
            'password' => bcrypt('secret'),
            'activo' => true,
            'rol_id' => $rolModelo->id,
        ]);
    }

    private function mesa(): Mesa
    {
        static $contador = 0;
        $contador++;
        $bodega = Bodega::create(['descripcion' => 'Bodega Test ' . $contador]);
        $zona = Zona::create(['nombre' => 'Zona Test ' . $contador, 'bodega_id' => $bodega->id]);

        return Mesa::create(['zona_id' => $zona->id, 'numero' => 'M-' . $contador, 'capacidad' => 4, 'estado' => 'disponible']);
    }

    private function producto(float $precio = 35000, bool $activo = true, bool $conIntegracionContable = true): Producto
    {
        static $contador = 0;
        $contador++;

        // cerrarMesa() exige que cada producto tenga una integración
        // contable con proceso y plantilla reales detrás — se reutiliza la
        // primera integración de la cadena contable sembrada en setUp().
        $integracionId = $conIntegracionContable
            ? \App\Models\IntegracionContable::query()->value('id')
            : null;

        return Producto::create([
            'codigo' => 'SEG' . $contador,
            'descripcion' => 'Producto Seguridad ' . $contador,
            'und_detal' => 'UND',
            'precio' => $precio,
            'inactivo' => !$activo,
            'integracion_contable_id' => $integracionId,
        ]);
    }

    /** Crea (o reutiliza) el Prefijo del catálogo y una Caja que lo usa. */
    private function caja(string $codigoPrefijo, Bodega $bodega): Caja
    {
        static $contador = 0;
        $contador++;

        Prefijo::firstOrCreate(['codigo' => $codigoPrefijo], ['nombre' => 'Prefijo ' . $codigoPrefijo]);

        return Caja::create([
            'nombre' => 'Caja ' . $codigoPrefijo . ' ' . $contador,
            'prefijo' => $codigoPrefijo,
            'bodega_id' => $bodega->id,
            'activa' => true,
        ]);
    }

    private function inventario(Producto $producto, Bodega $bodega, float $stock): Inventario
    {
        return Inventario::create(['producto_id' => $producto->id, 'bodega_id' => $bodega->id, 'stock' => $stock]);
    }

    private function pedidoConDetalles(Mesa $mesa, User $user, array $lineas): Pedido
    {
        $total = collect($lineas)->sum(fn ($l) => $l['cantidad'] * $l['producto']->precio);
        $pedido = Pedido::create(['mesa_id' => $mesa->id, 'user_id' => $user->id, 'total' => $total, 'estado' => 'pendiente']);

        foreach ($lineas as $linea) {
            DetallePedido::create([
                'pedido_id' => $pedido->id,
                'producto_id' => $linea['producto']->id,
                'cantidad' => $linea['cantidad'],
                'precio_unitario' => $linea['producto']->precio,
                'subtotal' => $linea['cantidad'] * $linea['producto']->precio,
            ]);
        }

        return $pedido;
    }

    public function test_un_mesero_puede_eliminar_un_item_enviado_con_la_clave_del_administrador(): void
    {
        $mesero = $this->usuario('Mesero');
        $administrador = $this->usuario('Administrador');
        $administrador->update(['clave_anulacion' => Hash::make('4567')]);
        $mesa = $this->mesa();
        $mesa->update(['estado' => 'ocupada']);
        $producto = $this->producto();
        $pedido = $this->pedidoConDetalles($mesa, $mesero, [['producto' => $producto, 'cantidad' => 1]]);

        $this->actingAs($mesero)
            ->postJson('/pedidos/eliminar-item', [
                'mesa_id' => $mesa->id,
                'producto_id' => $producto->id,
                'clave' => '4567',
            ])
            ->assertOk()
            ->assertJsonPath('pedido_eliminado', true);

        $this->assertDatabaseMissing('pedidos', ['id' => $pedido->id]);
        $this->assertDatabaseMissing('detalle_pedidos', ['pedido_id' => $pedido->id]);
        $this->assertSame('disponible', $mesa->fresh()->estado);
    }

    /**
     * Antes el POS traía TODAS las mesas de TODAS las zonas sin importar
     * la caja/bodega del usuario logueado: un mesero de Discoteca veía (y
     * podía tomar) las mesas del Restaurante. Ahora index() y bloquearMesa()
     * deben filtrar por la bodega de la caja del usuario.
     */
    public function test_un_mesero_solo_ve_y_puede_tomar_mesas_de_su_propia_sede(): void
    {
        $bodegaRestaurante = Bodega::create(['descripcion' => 'BarRestaurante']);
        $bodegaDiscoteca = Bodega::create(['descripcion' => 'BarDiscoteca']);

        $zonaRestaurante = Zona::create(['nombre' => 'Restaurante', 'bodega_id' => $bodegaRestaurante->id]);
        $zonaDiscoteca = Zona::create(['nombre' => 'Discoteca', 'bodega_id' => $bodegaDiscoteca->id]);

        $mesaRestaurante = Mesa::create(['zona_id' => $zonaRestaurante->id, 'numero' => 'R-1', 'capacidad' => 4, 'estado' => 'disponible']);
        $mesaDiscoteca = Mesa::create(['zona_id' => $zonaDiscoteca->id, 'numero' => 'D-1', 'capacidad' => 4, 'estado' => 'disponible']);

        $cajaDiscoteca = $this->caja('FD', $bodegaDiscoteca);
        $meseroDisco = $this->usuario('Mesero');
        $meseroDisco->update(['caja_id' => $cajaDiscoteca->id]);

        $respuesta = $this->actingAs($meseroDisco)->get('/facturacion');
        $respuesta->assertOk();
        $mesasVistas = $respuesta->viewData('mesas')->pluck('numero');
        $this->assertTrue($mesasVistas->contains('D-1'), 'Debe ver su propia mesa de Discoteca.');
        $this->assertFalse($mesasVistas->contains('R-1'), 'No debe ver la mesa del Restaurante.');

        // Tampoco puede tomarla directamente por id, aunque adivine la URL.
        $this->actingAs($meseroDisco)
            ->postJson('/mesas/' . $mesaRestaurante->id . '/bloquear')
            ->assertStatus(403);

        $this->actingAs($meseroDisco)
            ->postJson('/mesas/' . $mesaDiscoteca->id . '/bloquear')
            ->assertOk();

        // Un administrador sigue viendo todas las sedes.
        $administrador = $this->usuario('Administrador');
        $respuestaAdmin = $this->actingAs($administrador)->get('/facturacion');
        $mesasAdmin = $respuestaAdmin->viewData('mesas')->pluck('numero');
        $this->assertTrue($mesasAdmin->contains('R-1'));
        $this->assertTrue($mesasAdmin->contains('D-1'));
    }

    /**
     * Antes, cancelar UNA línea de un pedido con varias (ej. 3 hamburguesas
     * distintas) la borraba de detalle_pedidos sin dejar rastro para
     * Cocina. Ahora debe quedar marcada (cancelado_at/cancelado_por) y
     * las otras 2 líneas del pedido deben seguir intactas y facturables.
     */
    public function test_eliminar_un_item_de_varios_lo_marca_cancelado_en_vez_de_borrarlo(): void
    {
        $mesero = $this->usuario('Mesero');
        $administrador = $this->usuario('Administrador');
        $administrador->update(['clave_anulacion' => Hash::make('4567')]);
        $mesa = $this->mesa();
        $mesa->update(['estado' => 'ocupada']);
        $clasica = $this->producto();
        $master = $this->producto();
        $mexicana = $this->producto();
        $pedido = $this->pedidoConDetalles($mesa, $mesero, [
            ['producto' => $clasica, 'cantidad' => 1],
            ['producto' => $master, 'cantidad' => 1],
            ['producto' => $mexicana, 'cantidad' => 1],
        ]);
        $itemMexicana = $pedido->detalles()->where('producto_id', $mexicana->id)->first();

        $this->actingAs($mesero)
            ->postJson('/pedidos/eliminar-item', [
                'mesa_id' => $mesa->id,
                'producto_id' => $mexicana->id,
                'clave' => '4567',
            ])
            ->assertOk()
            ->assertJsonMissingPath('pedido_eliminado');

        // El pedido y las otras 2 líneas siguen vivos.
        $this->assertDatabaseHas('pedidos', ['id' => $pedido->id]);
        $this->assertDatabaseHas('detalle_pedidos', ['id' => $itemMexicana->id]);
        $this->assertNotNull($itemMexicana->fresh()->cancelado_at);
        $this->assertSame($administrador->id, $itemMexicana->fresh()->cancelado_por);
        $this->assertSame(2, $pedido->detalles()->whereNull('cancelado_at')->count());
        $this->assertSame('ocupada', $mesa->fresh()->estado);

        // El total del pedido ya no incluye la línea cancelada.
        $totalEsperado = $clasica->precio + $master->precio;
        $this->assertEquals($totalEsperado, (float) $pedido->fresh()->total);
    }

    public function test_eliminar_item_enviado_rechaza_una_clave_no_configurada(): void
    {
        $mesero = $this->usuario('Mesero');
        $administrador = $this->usuario('Administrador');
        $administrador->update(['clave_anulacion' => Hash::make('4567')]);
        $mesa = $this->mesa();
        $producto = $this->producto();
        $pedido = $this->pedidoConDetalles($mesa, $mesero, [['producto' => $producto, 'cantidad' => 1]]);

        $this->actingAs($mesero)
            ->postJson('/pedidos/eliminar-item', [
                'mesa_id' => $mesa->id,
                'producto_id' => $producto->id,
                'clave' => '9999',
            ])
            ->assertStatus(403)
            ->assertJsonPath('message', 'Clave incorrecta');

        $this->assertDatabaseHas('pedidos', ['id' => $pedido->id]);
        $this->assertDatabaseHas('detalle_pedidos', ['pedido_id' => $pedido->id]);
    }

    // ------------------------------------------------------------------
    // Hallazgo #1 (segunda ronda): consecutivo de factura por PREFIJO,
    // no por caja — dos cajas pueden compartir un mismo prefijo.
    // ------------------------------------------------------------------

    public function test_dos_cajas_con_el_mismo_prefijo_no_generan_el_mismo_numero_de_factura()
    {
        $cajero = $this->usuario('Cajero');
        $bodega = Bodega::create(['descripcion' => 'Bodega Compartida']);
        $producto = $this->producto(precio: 10000);
        $this->inventario($producto, $bodega, 100);

        $cajaA = $this->caja('CP', $bodega);
        $cajaB = $this->caja('CP', $bodega); // mismo prefijo que cajaA, a propósito

        $mesaA = $this->mesa();
        $mesaB = $this->mesa();
        $this->pedidoConDetalles($mesaA, $cajero, [['producto' => $producto, 'cantidad' => 1]]);
        $this->pedidoConDetalles($mesaB, $cajero, [['producto' => $producto, 'cantidad' => 1]]);

        $cajero->update(['caja_id' => $cajaA->id]);
        $respuestaA = $this->actingAs($cajero)->postJson('/pedidos/cerrar-mesa', [
            'mesa_id' => $mesaA->id, 'metodo_pago' => 'efectivo', 'total' => 10000,
        ]);
        $respuestaA->assertOk();

        $cajero->update(['caja_id' => $cajaB->id]);
        $respuestaB = $this->actingAs($cajero)->postJson('/pedidos/cerrar-mesa', [
            'mesa_id' => $mesaB->id, 'metodo_pago' => 'efectivo', 'total' => 10000,
        ]);
        $respuestaB->assertOk();

        $numeros = Factura::orderBy('id')->pluck('numero_factura')->all();
        $this->assertSame(['CP-00001', 'CP-00002'], $numeros, 'Dos cajas con el mismo prefijo no pueden repetir número de factura.');
    }

    // ------------------------------------------------------------------
    // Hallazgo #2 (segunda ronda): el stock se agrupa por producto antes
    // de validar, no línea por línea.
    // ------------------------------------------------------------------

    public function test_cerrar_mesa_rechaza_si_dos_lineas_del_mismo_producto_superan_el_stock_disponible()
    {
        $cajero = $this->usuario('Cajero');
        $bodega = Bodega::create(['descripcion' => 'Bodega Stock']);
        $producto = $this->producto(precio: 10000);
        $producto->update(['afecta_inventario' => true]);
        $this->inventario($producto, $bodega, 1); // solo hay 1 unidad

        $caja = $this->caja('ST', $bodega);
        $cajero->update(['caja_id' => $caja->id]);

        $mesa = $this->mesa();
        // Dos líneas de 1 unidad cada una del MISMO producto = piden 2 en total.
        $this->pedidoConDetalles($mesa, $cajero, [
            ['producto' => $producto, 'cantidad' => 1],
            ['producto' => $producto, 'cantidad' => 1],
        ]);

        $respuesta = $this->actingAs($cajero)->postJson('/pedidos/cerrar-mesa', [
            'mesa_id' => $mesa->id, 'metodo_pago' => 'efectivo', 'total' => 20000,
        ]);

        $respuesta->assertStatus(500); // cerrarMesa reporta errores de negocio así (ver Stock insuficiente)
        $respuesta->assertJsonFragment(['message' => "Stock insuficiente para: {$producto->descripcion}"]);
        $this->assertSame(0, Factura::count(), 'No debe quedar ninguna factura si el stock combinado no alcanza.');
        $this->assertEquals(1, Inventario::first()->stock, 'El stock no debe tocarse si la venta se rechaza.');
    }

    public function test_cerrar_mesa_permite_dos_lineas_del_mismo_producto_si_el_stock_combinado_alcanza()
    {
        $cajero = $this->usuario('Cajero');
        $bodega = Bodega::create(['descripcion' => 'Bodega Stock OK']);
        $producto = $this->producto(precio: 10000);
        $producto->update(['afecta_inventario' => true]);
        $this->inventario($producto, $bodega, 2); // exactamente lo que se pide

        $caja = $this->caja('ST2', $bodega);
        $cajero->update(['caja_id' => $caja->id]);

        $mesa = $this->mesa();
        $this->pedidoConDetalles($mesa, $cajero, [
            ['producto' => $producto, 'cantidad' => 1],
            ['producto' => $producto, 'cantidad' => 1],
        ]);

        $respuesta = $this->actingAs($cajero)->postJson('/pedidos/cerrar-mesa', [
            'mesa_id' => $mesa->id, 'metodo_pago' => 'efectivo', 'total' => 20000,
        ]);

        $respuesta->assertOk();
        $this->assertEquals(0, Inventario::first()->stock);
    }

    // ------------------------------------------------------------------
    // Hallazgo #1: precio/producto se resuelven en servidor
    // ------------------------------------------------------------------

    public function test_guardar_pedido_ignora_el_precio_manipulado_por_el_cliente_y_usa_el_del_producto()
    {
        $mesero = $this->usuario('Mesero');
        $mesa = $this->mesa();
        $producto = $this->producto(precio: 35000);

        $respuesta = $this->actingAs($mesero)->postJson('/pedidos/guardar', [
            'mesa_id' => $mesa->id,
            'items' => [
                ['id' => $producto->id, 'cantidad' => 2, 'precio' => 1], // precio manipulado
            ],
        ]);

        $respuesta->assertOk();

        $detalle = DetallePedido::first();
        $this->assertNotNull($detalle);
        $this->assertEquals(35000, (float) $detalle->precio_unitario, 'Debe usar el precio oficial del producto, no el 1 enviado.');
        $this->assertEquals(70000, (float) $detalle->subtotal);
    }

    public function test_guardar_pedido_rechaza_un_producto_inexistente()
    {
        $mesero = $this->usuario('Mesero');
        $mesa = $this->mesa();

        $respuesta = $this->actingAs($mesero)->postJson('/pedidos/guardar', [
            'mesa_id' => $mesa->id,
            'items' => [
                ['id' => 999999, 'cantidad' => 1, 'precio' => 10000],
            ],
        ]);

        $respuesta->assertStatus(422);
        $this->assertSame(0, DetallePedido::count());
    }

    public function test_guardar_pedido_rechaza_un_producto_inactivo()
    {
        $mesero = $this->usuario('Mesero');
        $mesa = $this->mesa();
        $producto = $this->producto(activo: false);

        $respuesta = $this->actingAs($mesero)->postJson('/pedidos/guardar', [
            'mesa_id' => $mesa->id,
            'items' => [
                ['id' => $producto->id, 'cantidad' => 1, 'precio' => 10000],
            ],
        ]);

        $respuesta->assertStatus(422);
        $this->assertSame(0, DetallePedido::count());
    }

    // ------------------------------------------------------------------
    // Hallazgo #4: las acciones del POS exigen rol, no solo autenticación
    // ------------------------------------------------------------------

    public function test_un_usuario_de_contabilidad_no_puede_invocar_guardar_pedido_por_url_directa()
    {
        $contable = $this->usuario('Contabilidad');
        $mesa = $this->mesa();
        $producto = $this->producto();

        $respuesta = $this->actingAs($contable)->postJson('/pedidos/guardar', [
            'mesa_id' => $mesa->id,
            'items' => [['id' => $producto->id, 'cantidad' => 1, 'precio' => 10000]],
        ]);

        $respuesta->assertStatus(403);
    }

    public function test_un_usuario_de_contabilidad_no_puede_liberar_una_mesa()
    {
        $contable = $this->usuario('Contabilidad');
        $mesa = $this->mesa();

        $respuesta = $this->actingAs($contable)->postJson("/mesas/{$mesa->id}/liberar");

        $respuesta->assertStatus(403);
    }

    // ------------------------------------------------------------------
    // Hallazgo #2: liberarMesa valida dueño y estado antes de borrar
    // ------------------------------------------------------------------

    public function test_liberar_mesa_rechaza_a_quien_no_es_dueno_del_pedido()
    {
        $mesero1 = $this->usuario('Mesero');
        $mesero2 = $this->usuario('Mesero');
        $mesa = $this->mesa();

        Pedido::create(['mesa_id' => $mesa->id, 'user_id' => $mesero1->id, 'total' => 0, 'estado' => 'pendiente']);

        $respuesta = $this->actingAs($mesero2)->postJson("/mesas/{$mesa->id}/liberar");

        $respuesta->assertStatus(403);
        $this->assertSame(1, Pedido::count(), 'El pedido de otro mesero no debe borrarse.');
    }

    public function test_liberar_mesa_permite_al_dueno_cancelar_su_propio_pedido_sin_enviar()
    {
        $mesero = $this->usuario('Mesero');
        $mesa = $this->mesa();
        $mesa->update(['estado' => 'seleccionada']);

        $pedido = Pedido::create(['mesa_id' => $mesa->id, 'user_id' => $mesero->id, 'total' => 0, 'estado' => 'pendiente']);

        $respuesta = $this->actingAs($mesero)->postJson("/mesas/{$mesa->id}/liberar");

        $respuesta->assertOk();
        $this->assertSame(0, Pedido::count());
        $this->assertSame('disponible', $mesa->fresh()->estado);
    }

    public function test_liberar_mesa_no_borra_un_pedido_ya_enviado_a_cocina_si_no_es_administrador()
    {
        $cajero = $this->usuario('Cajero');
        $mesa = $this->mesa();
        $mesa->update(['estado' => 'ocupada']); // ya hay comandas enviadas

        $pedido = Pedido::create(['mesa_id' => $mesa->id, 'user_id' => $cajero->id, 'total' => 35000, 'estado' => 'pendiente']);

        $respuesta = $this->actingAs($cajero)->postJson("/mesas/{$mesa->id}/liberar");

        $respuesta->assertStatus(422);
        $this->assertSame(1, Pedido::count(), 'Un pedido ya en cocina no debe perderse por un "cancelar".');
        $this->assertSame('ocupada', $mesa->fresh()->estado);
    }

    public function test_un_administrador_si_puede_forzar_la_liberacion_de_una_mesa_ocupada()
    {
        $admin = $this->usuario('Administrador');
        $mesero = $this->usuario('Mesero');
        $mesa = $this->mesa();
        $mesa->update(['estado' => 'ocupada']);

        Pedido::create(['mesa_id' => $mesa->id, 'user_id' => $mesero->id, 'total' => 35000, 'estado' => 'pendiente']);

        $respuesta = $this->actingAs($admin)->postJson("/mesas/{$mesa->id}/liberar");

        $respuesta->assertOk();
        $this->assertSame(0, Pedido::count());
        $this->assertSame('disponible', $mesa->fresh()->estado);
    }

    // ------------------------------------------------------------------
    // Hallazgo #3 (segunda ronda): bloquearMesa registra quién la tomó y
    // corre bajo lockForUpdate(); liberarMesa respeta ese dueño incluso
    // cuando todavía no existe un pedido detrás.
    // ------------------------------------------------------------------

    public function test_bloquear_mesa_registra_quien_la_tomo_y_la_hora()
    {
        $mesero = $this->usuario('Mesero');
        $mesa = $this->mesa();

        $respuesta = $this->actingAs($mesero)->postJson("/mesas/{$mesa->id}/bloquear");

        $respuesta->assertOk();
        $mesa->refresh();
        $this->assertSame('seleccionada', $mesa->estado);
        $this->assertSame($mesero->id, $mesa->bloqueada_por);
        $this->assertNotNull($mesa->bloqueada_at);
    }

    public function test_bloquear_una_mesa_ya_tomada_rechaza_al_segundo()
    {
        $mesero1 = $this->usuario('Mesero');
        $mesero2 = $this->usuario('Mesero');
        $mesa = $this->mesa();

        $this->actingAs($mesero1)->postJson("/mesas/{$mesa->id}/bloquear")->assertOk();
        $respuesta = $this->actingAs($mesero2)->postJson("/mesas/{$mesa->id}/bloquear");

        $respuesta->assertStatus(403);
        $this->assertSame($mesero1->id, $mesa->fresh()->bloqueada_por, 'El segundo intento no debe pisar al dueño real.');
    }

    public function test_liberar_una_mesa_sin_pedido_rechaza_a_quien_no_la_tomo()
    {
        $mesero1 = $this->usuario('Mesero');
        $mesero2 = $this->usuario('Mesero');
        $mesa = $this->mesa();
        $this->actingAs($mesero1)->postJson("/mesas/{$mesa->id}/bloquear")->assertOk();

        $respuesta = $this->actingAs($mesero2)->postJson("/mesas/{$mesa->id}/liberar");

        $respuesta->assertStatus(403);
        $this->assertSame('seleccionada', $mesa->fresh()->estado);
    }

    public function test_liberar_una_mesa_sin_pedido_permite_a_quien_la_tomo()
    {
        $mesero = $this->usuario('Mesero');
        $mesa = $this->mesa();
        $this->actingAs($mesero)->postJson("/mesas/{$mesa->id}/bloquear")->assertOk();

        $respuesta = $this->actingAs($mesero)->postJson("/mesas/{$mesa->id}/liberar");

        $respuesta->assertOk();
        $mesa->refresh();
        $this->assertSame('disponible', $mesa->estado);
        $this->assertNull($mesa->bloqueada_por);
        $this->assertNull($mesa->bloqueada_at);
    }

    public function test_liberar_una_mesa_sin_pedido_permite_a_administrador_aunque_no_la_haya_tomado()
    {
        $mesero = $this->usuario('Mesero');
        $admin = $this->usuario('Administrador');
        $mesa = $this->mesa();
        $this->actingAs($mesero)->postJson("/mesas/{$mesa->id}/bloquear")->assertOk();

        $respuesta = $this->actingAs($admin)->postJson("/mesas/{$mesa->id}/liberar");

        $respuesta->assertOk();
        $this->assertSame('disponible', $mesa->fresh()->estado);
    }

    public function test_el_auto_revertido_libera_una_mesa_seleccionada_vencida_sin_pedido()
    {
        $mesero = $this->usuario('Mesero');
        $mesa = $this->mesa();
        $mesa->update(['estado' => 'seleccionada', 'bloqueada_por' => $mesero->id, 'bloqueada_at' => now()->subMinutes(5)]);
        Mesa::where('id', $mesa->id)->update(['updated_at' => now()->subMinutes(5)]);

        $this->actingAs($mesero)->getJson('/mesas/actualizar')->assertOk();

        $mesa->refresh();
        $this->assertSame('disponible', $mesa->estado);
        $this->assertNull($mesa->bloqueada_por);
    }

    public function test_el_auto_revertido_no_toca_una_mesa_seleccionada_que_si_tiene_pedido_pendiente()
    {
        $mesero = $this->usuario('Mesero');
        $mesa = $this->mesa();
        $mesa->update(['estado' => 'seleccionada', 'bloqueada_por' => $mesero->id]);
        Mesa::where('id', $mesa->id)->update(['updated_at' => now()->subMinutes(5)]);
        // Caso borde: no debería pasar por construcción, pero si un pedido
        // pendiente quedara enganchado a una mesa "seleccionada", el
        // auto-revertido no debe pisarlo.
        Pedido::create(['mesa_id' => $mesa->id, 'user_id' => $mesero->id, 'total' => 10000, 'estado' => 'pendiente']);

        $this->actingAs($mesero)->getJson('/mesas/actualizar')->assertOk();

        $this->assertSame('seleccionada', $mesa->fresh()->estado, 'No debe revertir una mesa con pedido pendiente real.');
    }

    // ------------------------------------------------------------------
    // Hallazgo #5 (segunda ronda): fecha_vencimiento fotografiada en la
    // factura, a partir de Tercero::dias_credito o la política general.
    // ------------------------------------------------------------------

    public function test_credito_con_cliente_sin_dias_credito_usa_la_politica_general()
    {
        $cajero = $this->usuario('Cajero');
        $bodega = Bodega::create(['descripcion' => 'Bodega Credito']);
        $producto = $this->producto(precio: 10000);
        $this->inventario($producto, $bodega, 10);
        $caja = $this->caja('CR', $bodega);
        $cajero->update(['caja_id' => $caja->id]);

        $cliente = Tercero::create(['tipo' => 'persona', 'nombre' => 'Cliente', 'apellido' => 'Real', 'celular' => '3000000000']);
        $mesa = $this->mesa();
        $pedido = $this->pedidoConDetalles($mesa, $cajero, [['producto' => $producto, 'cantidad' => 1]]);
        $pedido->update(['cliente_id' => $cliente->id]);

        $respuesta = $this->actingAs($cajero)->postJson('/pedidos/cerrar-mesa', [
            'mesa_id' => $mesa->id, 'metodo_pago' => 'credito', 'total' => 10000, 'cliente_id' => $cliente->id,
        ]);

        $respuesta->assertOk();
        $factura = Factura::first();
        $this->assertNotNull($factura->fecha_vencimiento);
        $this->assertSame(
            now()->addDays(\App\Http\Controllers\FacturacionController::DIAS_CREDITO_POR_DEFECTO)->toDateString(),
            $factura->fecha_vencimiento->toDateString()
        );
    }

    public function test_credito_con_cliente_que_tiene_dias_credito_propios_los_respeta()
    {
        $cajero = $this->usuario('Cajero');
        $bodega = Bodega::create(['descripcion' => 'Bodega Credito 2']);
        $producto = $this->producto(precio: 10000);
        $this->inventario($producto, $bodega, 10);
        $caja = $this->caja('CR2', $bodega);
        $cajero->update(['caja_id' => $caja->id]);

        $cliente = Tercero::create(['tipo' => 'persona', 'nombre' => 'Cliente', 'apellido' => 'VIP', 'celular' => '3000000001', 'dias_credito' => 60]);
        $mesa = $this->mesa();
        $pedido = $this->pedidoConDetalles($mesa, $cajero, [['producto' => $producto, 'cantidad' => 1]]);
        $pedido->update(['cliente_id' => $cliente->id]);

        $respuesta = $this->actingAs($cajero)->postJson('/pedidos/cerrar-mesa', [
            'mesa_id' => $mesa->id, 'metodo_pago' => 'credito', 'total' => 10000, 'cliente_id' => $cliente->id,
        ]);

        $respuesta->assertOk();
        $factura = Factura::first();
        $this->assertSame(now()->addDays(60)->toDateString(), $factura->fecha_vencimiento->toDateString());
    }

    public function test_una_venta_no_credito_no_guarda_fecha_de_vencimiento()
    {
        $cajero = $this->usuario('Cajero');
        $bodega = Bodega::create(['descripcion' => 'Bodega Contado']);
        $producto = $this->producto(precio: 10000);
        $this->inventario($producto, $bodega, 10);
        $caja = $this->caja('CO', $bodega);
        $cajero->update(['caja_id' => $caja->id]);

        $mesa = $this->mesa();
        $this->pedidoConDetalles($mesa, $cajero, [['producto' => $producto, 'cantidad' => 1]]);

        $respuesta = $this->actingAs($cajero)->postJson('/pedidos/cerrar-mesa', [
            'mesa_id' => $mesa->id, 'metodo_pago' => 'efectivo', 'total' => 10000,
        ]);

        $respuesta->assertOk();
        $this->assertNull(Factura::first()->fecha_vencimiento);
    }

    // ------------------------------------------------------------------
    // Hallazgo #4 (segunda ronda): un producto sin integración contable
    // bloquea la venta en vez de contabilizarse a medias en silencio.
    // ------------------------------------------------------------------

    public function test_cerrar_mesa_rechaza_un_producto_sin_integracion_contable()
    {
        $cajero = $this->usuario('Cajero');
        $bodega = Bodega::create(['descripcion' => 'Bodega Sin Integracion']);
        $producto = $this->producto(precio: 10000, conIntegracionContable: false);
        $caja = $this->caja('SI', $bodega);
        $cajero->update(['caja_id' => $caja->id]);

        $mesa = $this->mesa();
        $this->pedidoConDetalles($mesa, $cajero, [['producto' => $producto, 'cantidad' => 1]]);

        $respuesta = $this->actingAs($cajero)->postJson('/pedidos/cerrar-mesa', [
            'mesa_id' => $mesa->id, 'metodo_pago' => 'efectivo', 'total' => 10000,
        ]);

        $respuesta->assertStatus(422);
        $respuesta->assertJsonFragment(['message' => "El producto '{$producto->descripcion}' no tiene una integración contable configurada correctamente. Pide a un administrador que la asigne en Productos antes de facturarlo."]);
        $this->assertSame(0, Factura::count(), 'No debe quedar ninguna factura si un producto no se puede contabilizar.');
    }

    // ------------------------------------------------------------------
    // Producto ensamblado (ej. "Cubetazo Poker"): al venderse debe
    // descontar el inventario del producto BASE (ej. "Poker"), multiplicado
    // por el factor de consumo, y dejar registrado un Consumo trazable a
    // la misma factura.
    // ------------------------------------------------------------------

    public function test_vender_un_producto_ensamblado_descuenta_el_inventario_del_producto_base()
    {
        $cajero = $this->usuario('Cajero');
        $bodega = Bodega::create(['descripcion' => 'Bodega Ensamble']);

        $base = $this->producto(precio: 5000);
        $base->update(['afecta_inventario' => true]);
        $this->inventario($base, $bodega, 100); // 100 Poker en stock

        $ensamblado = $this->producto(precio: 25000);
        $ensamblado->update([
            'afecta_inventario' => false, // el cubetazo no lleva su propio stock
            'es_ensamblado' => true,
            'producto_base_id' => $base->id,
            'factor_consumo' => 6, // 1 cubetazo = 6 Poker
        ]);

        $caja = $this->caja('EN', $bodega);
        $cajero->update(['caja_id' => $caja->id]);

        $mesa = $this->mesa();
        $this->pedidoConDetalles($mesa, $cajero, [['producto' => $ensamblado, 'cantidad' => 2]]); // 2 cubetazos

        $respuesta = $this->actingAs($cajero)->postJson('/pedidos/cerrar-mesa', [
            'mesa_id' => $mesa->id, 'metodo_pago' => 'efectivo', 'total' => 50000,
        ]);

        $respuesta->assertOk();

        // 2 cubetazos x 6 Poker = 12 Poker descontados del stock BASE.
        $this->assertEquals(88, Inventario::where('producto_id', $base->id)->where('bodega_id', $bodega->id)->value('stock'));

        $factura = Factura::first();
        $consumo = \App\Models\Consumo::first();
        $this->assertNotNull($consumo, 'Debe generarse un Consumo al vender un producto ensamblado.');
        $this->assertSame($factura->numero_factura, $consumo->numero_factura, 'El consumo debe llevar el MISMO número de la factura que lo originó.');
        $this->assertSame($factura->id, $consumo->factura_id);
        $this->assertStringContainsString('Consumo de materia prima de venta', $consumo->observacion);
        $this->assertNotNull($consumo->user_id);

        $detalle = $consumo->detalles()->first();
        $this->assertSame($base->id, $detalle->producto_base_id);
        $this->assertSame($ensamblado->id, $detalle->producto_ensamblado_id);
        $this->assertEquals(12, $detalle->cantidad);

        // El Consumo por sí solo no bastaba: sin esto, el stock del insumo
        // (Poker) bajaba de verdad pero ningún movimiento de kardex lo
        // explicaba — "se generó el consumo pero no el movimiento en kardex".
        $movimiento = MovimientoInventario::where('producto_id', $base->id)->where('bodega_id', $bodega->id)->first();
        $this->assertNotNull($movimiento, 'Debe quedar un movimiento de kardex por el insumo real descontado (ensamblado).');
        $this->assertSame('SALIDA', $movimiento->tipo);
        $this->assertEquals(12, $movimiento->cantidad);
        $this->assertEquals(100, $movimiento->stock_anterior);
        $this->assertEquals(88, $movimiento->stock_nuevo);
    }

    /**
     * Antes esto rechazaba TODA la factura (500) si el insumo de un
     * ensamblado no alcanzaba. Pero el dinero ya entró — no se puede dejar
     * la cuenta sin cobrar. Ahora la factura pasa igual, el stock del
     * insumo NO se toca, y el Consumo queda 'no_registrado' esperando a
     * que un administrador ajuste el inventario o descarte la línea (ver
     * ConsumoController::registrar()).
     */
    public function test_cerrar_mesa_deja_consumo_no_registrado_si_el_producto_base_no_tiene_stock_suficiente()
    {
        $cajero = $this->usuario('Cajero');
        $bodega = Bodega::create(['descripcion' => 'Bodega Ensamble Sin Stock']);

        $base = $this->producto(precio: 5000);
        $base->update(['afecta_inventario' => true]);
        $this->inventario($base, $bodega, 5); // solo 5 Poker, no alcanzan para 6

        $ensamblado = $this->producto(precio: 25000);
        $ensamblado->update([
            'afecta_inventario' => false,
            'es_ensamblado' => true,
            'producto_base_id' => $base->id,
            'factor_consumo' => 6,
        ]);

        $caja = $this->caja('ES', $bodega);
        $cajero->update(['caja_id' => $caja->id]);

        $mesa = $this->mesa();
        $this->pedidoConDetalles($mesa, $cajero, [['producto' => $ensamblado, 'cantidad' => 1]]);

        $respuesta = $this->actingAs($cajero)->postJson('/pedidos/cerrar-mesa', [
            'mesa_id' => $mesa->id, 'metodo_pago' => 'efectivo', 'total' => 25000,
        ]);

        $respuesta->assertOk();
        $this->assertSame(1, Factura::count(), 'La factura debe pasar: el dinero ya entró.');
        $this->assertSame(5, Inventario::where('producto_id', $base->id)->where('bodega_id', $bodega->id)->value('stock'), 'El stock del insumo NO debe tocarse mientras el consumo esté sin registrar.');

        $consumo = \App\Models\Consumo::first();
        $this->assertNotNull($consumo);
        $this->assertSame('no_registrado', $consumo->estado);
        $this->assertNull($consumo->registrado_por);
        $this->assertNull($consumo->registrado_at);
        $this->assertSame(0, MovimientoInventario::where('producto_id', $base->id)->count(), 'No debe quedar kardex de algo que todavía no se descontó.');
    }

    /**
     * Cuando un mismo pedido mezcla un insumo con stock suficiente (las
     * cervezas de un Cubetazo) y otro sin stock (la carne de una
     * hamburguesa), NINGUNO de los dos se descuenta todavía — el Consumo
     * completo de la factura queda 'no_registrado', no solo la línea
     * problemática (así lo pidió el negocio: todo o nada por factura).
     */
    public function test_faltante_de_un_insumo_deja_pendiente_tambien_el_insumo_que_si_alcanzaba()
    {
        $cajero = $this->usuario('Cajero');
        $bodega = Bodega::create(['descripcion' => 'Bodega Mixta']);

        $cerveza = $this->producto(precio: 5000);
        $cerveza->update(['afecta_inventario' => true]);
        $this->inventario($cerveza, $bodega, 50); // sí alcanza

        $carne = $this->producto(precio: 8000);
        $carne->update(['afecta_inventario' => true]);
        $this->inventario($carne, $bodega, 0.05); // no alcanza para 0.16kg

        $cubetazo = $this->producto(precio: 60000);
        $cubetazo->update([
            'afecta_inventario' => false, 'es_ensamblado' => true,
            'producto_base_id' => $cerveza->id, 'factor_consumo' => 10,
        ]);

        $hamburguesa = $this->producto(precio: 22000);
        $hamburguesa->update([
            'afecta_inventario' => false, 'es_ensamblado' => true,
            'producto_base_id' => $carne->id, 'factor_consumo' => 0.16,
        ]);

        $caja = $this->caja('MX2', $bodega);
        $cajero->update(['caja_id' => $caja->id]);

        $mesa = $this->mesa();
        $this->pedidoConDetalles($mesa, $cajero, [
            ['producto' => $cubetazo, 'cantidad' => 1],
            ['producto' => $hamburguesa, 'cantidad' => 1],
        ]);

        $respuesta = $this->actingAs($cajero)->postJson('/pedidos/cerrar-mesa', [
            'mesa_id' => $mesa->id, 'metodo_pago' => 'efectivo', 'total' => 82000,
        ]);

        $respuesta->assertOk();
        $this->assertEquals(50, Inventario::where('producto_id', $cerveza->id)->where('bodega_id', $bodega->id)->value('stock'), 'La cerveza NO debe descontarse aunque sí alcanzaba: todo el consumo de la factura queda pendiente.');
        $this->assertEquals(0.05, Inventario::where('producto_id', $carne->id)->where('bodega_id', $bodega->id)->value('stock'));

        $consumo = \App\Models\Consumo::first();
        $this->assertSame('no_registrado', $consumo->estado);
        $this->assertSame(2, $consumo->detalles()->count());
    }

    /**
     * Un producto vendido DIRECTAMENTE (no derivado de una receta) sin
     * stock sigue bloqueando la venta como siempre — solo se difiere un
     * faltante que viene de un ensamblado/acompañamiento.
     */
    public function test_producto_directo_sin_stock_sigue_bloqueando_la_venta(): void
    {
        $cajero = $this->usuario('Cajero');
        $bodega = Bodega::create(['descripcion' => 'Bodega Directo Sin Stock']);

        $producto = $this->producto(precio: 8000);
        $producto->update(['afecta_inventario' => true]);
        $this->inventario($producto, $bodega, 1); // solo 1, se piden 2

        $caja = $this->caja('DS', $bodega);
        $cajero->update(['caja_id' => $caja->id]);

        $mesa = $this->mesa();
        $this->pedidoConDetalles($mesa, $cajero, [['producto' => $producto, 'cantidad' => 2]]);

        $respuesta = $this->actingAs($cajero)->postJson('/pedidos/cerrar-mesa', [
            'mesa_id' => $mesa->id, 'metodo_pago' => 'efectivo', 'total' => 16000,
        ]);

        $respuesta->assertStatus(500);
        $this->assertSame(0, Factura::count());
        $this->assertSame(1, Inventario::where('producto_id', $producto->id)->where('bodega_id', $bodega->id)->value('stock'));
    }

    // ------------------------------------------------------------------
    // Acompañamiento (ej. "Cubetazo Mix"): el mesero reparte libremente el
    // máximo del grupo entre varias opciones al comandar — a diferencia
    // del ensamblado (un solo insumo, factor fijo), aquí puede haber
    // varios insumos distintos con cantidades distintas.
    // ------------------------------------------------------------------

    private function grupoAcompanamiento(int $cantidadMaxima, array $productosElegibles): \App\Models\AcompanamientoGrupo
    {
        static $contador = 0;
        $contador++;

        $grupo = \App\Models\AcompanamientoGrupo::create([
            'codigo' => 'ACG' . $contador,
            'descripcion' => 'Grupo Acompañamiento Test ' . $contador,
            'cantidad_maxima' => $cantidadMaxima,
        ]);

        foreach ($productosElegibles as $producto) {
            \App\Models\AcompanamientoOpcion::create([
                'acompanamiento_grupo_id' => $grupo->id,
                'producto_id' => $producto->id,
            ]);
        }

        return $grupo;
    }

    public function test_guardar_pedido_rechaza_reparto_de_acompanamiento_mayor_al_maximo()
    {
        $mesero = $this->usuario('Mesero');
        $bodega = Bodega::create(['descripcion' => 'Bodega Acomp 1']);
        $poker = $this->producto(precio: 5000);
        $aguila = $this->producto(precio: 5500);
        $grupo = $this->grupoAcompanamiento(10, [$poker, $aguila]);

        $mix = $this->producto(precio: 60000);
        $mix->update(['acompanamiento_grupo_id' => $grupo->id, 'afecta_inventario' => false]);

        $mesa = $this->mesa();

        $respuesta = $this->actingAs($mesero)->postJson('/pedidos/guardar', [
            'mesa_id' => $mesa->id,
            'items' => [[
                'id' => $mix->id,
                'cantidad' => 1,
                'acompanamiento' => [
                    ['producto_id' => $poker->id, 'cantidad' => 8],
                    ['producto_id' => $aguila->id, 'cantidad' => 5], // 8+5=13 > máximo 10
                ],
            ]],
        ]);

        $respuesta->assertStatus(422);
        $this->assertSame(0, DetallePedido::count());
    }

    public function test_guardar_pedido_rechaza_reparto_con_producto_fuera_del_grupo()
    {
        $mesero = $this->usuario('Mesero');
        $bodega = Bodega::create(['descripcion' => 'Bodega Acomp 2']);
        $poker = $this->producto(precio: 5000);
        $intruso = $this->producto(precio: 9000); // no pertenece al grupo
        $grupo = $this->grupoAcompanamiento(10, [$poker]);

        $mix = $this->producto(precio: 60000);
        $mix->update(['acompanamiento_grupo_id' => $grupo->id, 'afecta_inventario' => false]);

        $mesa = $this->mesa();

        $respuesta = $this->actingAs($mesero)->postJson('/pedidos/guardar', [
            'mesa_id' => $mesa->id,
            'items' => [[
                'id' => $mix->id,
                'cantidad' => 1,
                'acompanamiento' => [['producto_id' => $intruso->id, 'cantidad' => 5]],
            ]],
        ]);

        $respuesta->assertStatus(422);
        $this->assertSame(0, DetallePedido::count());
    }

    public function test_vender_un_producto_con_acompanamiento_descuenta_cada_insumo_elegido()
    {
        $mesero = $this->usuario('Mesero');
        $cajero = $this->usuario('Cajero');
        $bodega = Bodega::create(['descripcion' => 'Bodega Acomp 3']);

        $poker = $this->producto(precio: 5000);
        $poker->update(['afecta_inventario' => true]);
        $this->inventario($poker, $bodega, 50);

        $aguila = $this->producto(precio: 5500);
        $aguila->update(['afecta_inventario' => true]);
        $this->inventario($aguila, $bodega, 50);

        $costena = $this->producto(precio: 4800);
        $costena->update(['afecta_inventario' => true]);
        $this->inventario($costena, $bodega, 50);

        $grupo = $this->grupoAcompanamiento(10, [$poker, $aguila, $costena]);

        $mix = $this->producto(precio: 60000);
        $mix->update(['acompanamiento_grupo_id' => $grupo->id, 'afecta_inventario' => false]);

        $caja = $this->caja('MX', $bodega);
        $mesero->update(['caja_id' => $caja->id]);
        $cajero->update(['caja_id' => $caja->id]);

        $mesa = $this->mesa();

        $guardar = $this->actingAs($mesero)->postJson('/pedidos/guardar', [
            'mesa_id' => $mesa->id,
            'items' => [[
                'id' => $mix->id,
                'cantidad' => 1,
                'acompanamiento' => [
                    ['producto_id' => $poker->id, 'cantidad' => 4],
                    ['producto_id' => $aguila->id, 'cantidad' => 3],
                    ['producto_id' => $costena->id, 'cantidad' => 3],
                ],
            ]],
        ]);
        $guardar->assertOk();

        $respuesta = $this->actingAs($cajero)->postJson('/pedidos/cerrar-mesa', [
            'mesa_id' => $mesa->id, 'metodo_pago' => 'efectivo', 'total' => 60000,
        ]);
        $respuesta->assertOk();

        $this->assertEquals(46, Inventario::where('producto_id', $poker->id)->where('bodega_id', $bodega->id)->value('stock'));
        $this->assertEquals(47, Inventario::where('producto_id', $aguila->id)->where('bodega_id', $bodega->id)->value('stock'));
        $this->assertEquals(47, Inventario::where('producto_id', $costena->id)->where('bodega_id', $bodega->id)->value('stock'));

        $factura = Factura::first();
        $consumo = \App\Models\Consumo::first();
        $this->assertNotNull($consumo);
        $this->assertSame($factura->numero_factura, $consumo->numero_factura);
        $this->assertSame(3, $consumo->detalles()->count(), 'Debe quedar una línea de consumo por cada insumo elegido.');

        $cantidadesPorInsumo = $consumo->detalles()->pluck('cantidad', 'producto_base_id');
        $this->assertEquals(4, (float) $cantidadesPorInsumo[$poker->id]);
        $this->assertEquals(3, (float) $cantidadesPorInsumo[$aguila->id]);
        $this->assertEquals(3, (float) $cantidadesPorInsumo[$costena->id]);

        // Cada insumo repartido (ej. "Cubetazo Águila"/Mix) debe dejar su
        // propio movimiento de kardex — antes ninguno de los 3 lo dejaba.
        foreach ([$poker->id => 4, $aguila->id => 3, $costena->id => 3] as $productoId => $cantidadEsperada) {
            $movimiento = MovimientoInventario::where('producto_id', $productoId)->where('bodega_id', $bodega->id)->first();
            $this->assertNotNull($movimiento, "Debe quedar un movimiento de kardex para el insumo id {$productoId}.");
            $this->assertSame('SALIDA', $movimiento->tipo);
            $this->assertEquals($cantidadEsperada, $movimiento->cantidad);
        }
    }

    /**
     * Dos líneas del MISMO ensamblado en una sola factura (ej. el mesero
     * agregó "Cubetazo Águila" dos veces) generan 2 filas de ConsumoDetalle
     * para el mismo insumo. Al construir el kardex para esa factura, leer
     * el stock "actual" para cada una por separado las dejaba a las dos con
     * el mismo stock_anterior/stock_nuevo (el stock ya había bajado de las
     * DOS antes de llegar aquí) — hay que reconstruir la secuencia real.
     */
    public function test_dos_lineas_del_mismo_ensamblado_en_una_factura_dejan_kardex_encadenado_correctamente(): void
    {
        $cajero = $this->usuario('Cajero');
        $bodega = Bodega::create(['descripcion' => 'Bodega Ensamble Doble']);

        $base = $this->producto(precio: 5000);
        $base->update(['afecta_inventario' => true]);
        $this->inventario($base, $bodega, 100);

        $ensamblado = $this->producto(precio: 25000);
        $ensamblado->update([
            'afecta_inventario' => false,
            'es_ensamblado' => true,
            'producto_base_id' => $base->id,
            'factor_consumo' => 6,
        ]);

        $caja = $this->caja('DB', $bodega);
        $cajero->update(['caja_id' => $caja->id]);

        $mesa = $this->mesa();
        // 2 líneas separadas del mismo ensamblado (no una sola con cantidad 2).
        $this->pedidoConDetalles($mesa, $cajero, [
            ['producto' => $ensamblado, 'cantidad' => 1],
        ]);
        DetallePedido::create([
            'pedido_id' => Pedido::where('mesa_id', $mesa->id)->where('estado', 'pendiente')->value('id'),
            'producto_id' => $ensamblado->id,
            'cantidad' => 1,
            'precio_unitario' => $ensamblado->precio,
            'subtotal' => $ensamblado->precio,
        ]);
        Pedido::where('mesa_id', $mesa->id)->update(['total' => $ensamblado->precio * 2]);

        $respuesta = $this->actingAs($cajero)->postJson('/pedidos/cerrar-mesa', [
            'mesa_id' => $mesa->id, 'metodo_pago' => 'efectivo', 'total' => 50000,
        ]);
        $respuesta->assertOk();

        // 2 líneas x 6 = 12 descontados del stock base (100 -> 88).
        $this->assertEquals(88, Inventario::where('producto_id', $base->id)->where('bodega_id', $bodega->id)->value('stock'));

        $movimientos = MovimientoInventario::where('producto_id', $base->id)->where('bodega_id', $bodega->id)->orderBy('id')->get();
        $this->assertCount(2, $movimientos, 'Debe quedar un movimiento de kardex por cada línea, no uno solo ni fusionados.');
        $this->assertEquals(100, $movimientos[0]->stock_anterior);
        $this->assertEquals(94, $movimientos[0]->stock_nuevo);
        $this->assertEquals(94, $movimientos[1]->stock_anterior);
        $this->assertEquals(88, $movimientos[1]->stock_nuevo);
    }

    /**
     * Un producto real (CUBETAZO AGUILA) quedó con es_ensamblado=1 Y
     * afecta_inventario=1 a la vez — el sistema le creó su propia fila de
     * inventario y un movimiento de kardex "fantasma" cada vez que se
     * vendía, además de ocultar que el insumo real (Águila) nunca dejaba
     * su propio movimiento. ProductoController debe forzar
     * afecta_inventario=false para ensamblado/acompañamiento sin importar
     * lo que llegue del formulario.
     */
    public function test_producto_ensamblado_o_con_acompanamiento_no_puede_quedar_con_afecta_inventario(): void
    {
        $admin = $this->usuario('Administrador');
        $grupoMenu = \App\Models\GrupoMenu::create(['nombre' => 'Grupo Test']);
        $integracion = \App\Models\IntegracionContable::query()->first();
        $base = $this->producto();
        $base->update(['afecta_inventario' => true]);

        $respuesta = $this->actingAs($admin)->postJson('/productos', [
            'codigo' => 'CUB-TEST',
            'descripcion' => 'Cubetazo Test',
            'und_detal' => 'UND',
            'precio' => 60000,
            'afecta_inventario' => 1, // intento de colar afecta_inventario=1
            'grupo_menu_id' => $grupoMenu->id,
            'integracion_contable_id' => $integracion->id,
            'es_ensamblado' => 1,
            'producto_base_id' => $base->id,
            'factor_consumo' => 10,
        ]);

        $respuesta->assertOk();
        $creado = Producto::where('codigo', 'CUB-TEST')->firstOrFail();
        $this->assertFalse((bool) $creado->afecta_inventario, 'Un producto ensamblado no debe quedar con afecta_inventario=true.');

        // Lo mismo al editarlo, si alguien intenta reactivarlo después.
        $respuestaUpdate = $this->actingAs($admin)->putJson('/productos/' . $creado->id, [
            'codigo' => 'CUB-TEST',
            'descripcion' => 'Cubetazo Test',
            'und_detal' => 'UND',
            'precio' => 60000,
            'afecta_inventario' => 1,
            'grupo_menu_id' => $grupoMenu->id,
            'integracion_contable_id' => $integracion->id,
            'es_ensamblado' => 1,
            'producto_base_id' => $base->id,
            'factor_consumo' => 10,
        ]);

        $respuestaUpdate->assertOk();
        $this->assertFalse((bool) $creado->fresh()->afecta_inventario);
    }

    /**
     * La hamburguesa se vende desde la caja de "Discoteca", pero la carne
     * (su insumo) vive en la bodega de "Cocina" (Producto::bodega_origen_id).
     * El stock/kardex debe tocar Cocina, no la bodega de la caja.
     */
    public function test_insumo_con_bodega_origen_se_descuenta_de_esa_bodega_no_de_la_caja(): void
    {
        $cajero = $this->usuario('Cajero');
        $bodegaDiscoteca = Bodega::create(['descripcion' => 'Discoteca']);
        $bodegaCocina = Bodega::create(['descripcion' => 'Cocina']);

        $carne = $this->producto(precio: 8000);
        $carne->update(['afecta_inventario' => true]);
        $this->inventario($carne, $bodegaCocina, 1); // 1000gr en Cocina
        // A propósito NO se crea inventario de carne en Discoteca — si el
        // código revisara la bodega equivocada, esto siempre fallaría.

        $hamburguesa = $this->producto(precio: 22000);
        $hamburguesa->update([
            'afecta_inventario' => false,
            'es_ensamblado' => true,
            'producto_base_id' => $carne->id,
            'factor_consumo' => 0.16, // 160gr
            'bodega_origen_id' => $bodegaCocina->id,
        ]);

        $caja = $this->caja('DI', $bodegaDiscoteca);
        $cajero->update(['caja_id' => $caja->id]);

        $mesa = $this->mesa();
        $this->pedidoConDetalles($mesa, $cajero, [['producto' => $hamburguesa, 'cantidad' => 1]]);

        $respuesta = $this->actingAs($cajero)->postJson('/pedidos/cerrar-mesa', [
            'mesa_id' => $mesa->id, 'metodo_pago' => 'efectivo', 'total' => 22000,
        ]);

        $respuesta->assertOk();
        $this->assertEquals(0.84, Inventario::where('producto_id', $carne->id)->where('bodega_id', $bodegaCocina->id)->value('stock'));

        $consumo = \App\Models\Consumo::first();
        $this->assertSame('registrado', $consumo->estado, 'Sí había stock en Cocina, así que debe registrarse de una vez.');
        $this->assertSame($bodegaCocina->id, $consumo->detalles()->first()->bodega_id);

        $movimiento = MovimientoInventario::where('producto_id', $carne->id)->first();
        $this->assertNotNull($movimiento);
        $this->assertSame($bodegaCocina->id, $movimiento->bodega_id);
    }

    /**
     * Flujo completo de revisión: el administrador ve el consumo pendiente,
     * corrige la cantidad (la receta real usa menos carne de la que se
     * registró) para que quepa en lo disponible, y lo registra — recién
     * ahí se descuenta el inventario y queda el kardex.
     */
    public function test_administrador_edita_cantidad_y_registra_un_consumo_pendiente(): void
    {
        $cajero = $this->usuario('Cajero');
        $admin = $this->usuario('Administrador');
        $bodega = Bodega::create(['descripcion' => 'Bodega Revision']);

        $carne = $this->producto(precio: 8000);
        $carne->update(['afecta_inventario' => true]);
        $this->inventario($carne, $bodega, 0.10); // solo 100gr

        $hamburguesa = $this->producto(precio: 22000);
        $hamburguesa->update([
            'afecta_inventario' => false, 'es_ensamblado' => true,
            'producto_base_id' => $carne->id, 'factor_consumo' => 0.16,
        ]);

        $caja = $this->caja('RV', $bodega);
        $cajero->update(['caja_id' => $caja->id]);

        $mesa = $this->mesa();
        $this->pedidoConDetalles($mesa, $cajero, [['producto' => $hamburguesa, 'cantidad' => 1]]);

        $this->actingAs($cajero)->postJson('/pedidos/cerrar-mesa', [
            'mesa_id' => $mesa->id, 'metodo_pago' => 'efectivo', 'total' => 22000,
        ])->assertOk();

        $consumo = \App\Models\Consumo::first();
        $this->assertSame('no_registrado', $consumo->estado);
        $detalle = $consumo->detalles()->first();

        // El admin corrige: la receta real solo usa 100gr, no 160.
        $this->actingAs($admin)->putJson('/consumos/detalles/' . $detalle->id, ['cantidad' => 0.10])
            ->assertOk();

        $this->actingAs($admin)->postJson('/consumos/' . $consumo->id . '/registrar')
            ->assertOk();

        $consumo->refresh();
        $this->assertSame('registrado', $consumo->estado);
        $this->assertSame($admin->id, $consumo->registrado_por);
        $this->assertNotNull($consumo->registrado_at);
        $this->assertEquals(0, Inventario::where('producto_id', $carne->id)->where('bodega_id', $bodega->id)->value('stock'));
        $this->assertSame(1, MovimientoInventario::where('producto_id', $carne->id)->count());
    }

    /**
     * En vez de ajustar el inventario, el administrador decide que esa
     * línea no debía contarse y la elimina — el consumo se puede registrar
     * sin ella (nada que descontar, porque ya no queda ninguna línea).
     */
    public function test_administrador_elimina_la_linea_problematica_de_un_consumo_pendiente(): void
    {
        $cajero = $this->usuario('Cajero');
        $admin = $this->usuario('Administrador');
        $bodega = Bodega::create(['descripcion' => 'Bodega Eliminar Linea']);

        $carne = $this->producto(precio: 8000);
        $carne->update(['afecta_inventario' => true]);
        $this->inventario($carne, $bodega, 0); // nada de carne

        $hamburguesa = $this->producto(precio: 22000);
        $hamburguesa->update([
            'afecta_inventario' => false, 'es_ensamblado' => true,
            'producto_base_id' => $carne->id, 'factor_consumo' => 0.16,
        ]);

        $caja = $this->caja('EL', $bodega);
        $cajero->update(['caja_id' => $caja->id]);

        $mesa = $this->mesa();
        $this->pedidoConDetalles($mesa, $cajero, [['producto' => $hamburguesa, 'cantidad' => 1]]);

        $this->actingAs($cajero)->postJson('/pedidos/cerrar-mesa', [
            'mesa_id' => $mesa->id, 'metodo_pago' => 'efectivo', 'total' => 22000,
        ])->assertOk();

        $consumo = \App\Models\Consumo::first();
        $detalle = $consumo->detalles()->first();

        $this->actingAs($admin)->deleteJson('/consumos/detalles/' . $detalle->id)->assertOk();

        // Sin líneas, el consumo se cierra solo (no queda nada por registrar).
        $this->assertSame('registrado', $consumo->fresh()->estado);
        $this->assertSame(0, $consumo->fresh()->detalles()->count());
        $this->assertSame(0, MovimientoInventario::where('producto_id', $carne->id)->count());
    }
}
