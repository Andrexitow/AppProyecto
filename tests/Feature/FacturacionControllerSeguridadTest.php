<?php

namespace Tests\Feature;

use App\Models\Bodega;
use App\Models\DetallePedido;
use App\Models\Mesa;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Roles;
use App\Models\Tercero;
use App\Models\User;
use App\Models\Zona;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Cubre los hallazgos #1, #2 y #4 de la revisión de seguridad del POS:
 * - guardarPedido() confiaba en el precio/producto que mandara el navegador.
 * - liberarMesa() borraba el pedido de cualquiera sin validar rol, dueño
 *   ni si ya se había enviado a cocina.
 * - varias acciones del POS solo exigían "auth", sin rol.
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

    private function producto(float $precio = 35000, bool $activo = true): Producto
    {
        static $contador = 0;
        $contador++;

        return Producto::create([
            'codigo' => 'SEG' . $contador,
            'descripcion' => 'Producto Seguridad ' . $contador,
            'und_detal' => 'UND',
            'precio' => $precio,
            'inactivo' => !$activo,
        ]);
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
}
