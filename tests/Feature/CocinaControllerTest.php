<?php

namespace Tests\Feature;

use App\Models\ComandaPendiente;
use App\Models\DetallePedido;
use App\Models\Impresora;
use App\Models\Mesa;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Roles;
use App\Models\Tercero;
use App\Models\User;
use App\Models\Zona;
use App\Models\Bodega;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CocinaControllerTest extends TestCase
{
    use RefreshDatabase;

    private function chef(): User
    {
        $rol = Roles::firstOrCreate(['nombre' => 'Cocina'], ['descripcion' => 'Chef']);

        return User::create([
            'name' => 'Chef Test',
            'username' => 'chef-test',
            'password' => bcrypt('secret'),
            'rol_id' => $rol->id,
            'role' => 'cocina',
            'activo' => true,
        ]);
    }

    private function impresoraCocina(): Impresora
    {
        return Impresora::create([
            'nombre' => 'Cocina principal',
            'ip' => '127.0.0.1',
            'puerto' => 9100,
            'tipo' => 'RED',
            'activa' => true,
        ]);
    }

    /**
     * Un pedido de 3 hamburguesas donde una (la mexicana) ya fue cancelada
     * después de enviada a cocina: Cocina debe seguir viendo las 3 líneas
     * en la MISMA comanda, con la cancelada marcada y con quién la
     * canceló — en vez de una ficha "anulación" aparte (lo que hacía que
     * un solo item cancelado se viera repetido/multiplicado en pantalla).
     */
    public function test_cocina_muestra_el_item_cancelado_tachado_dentro_de_su_misma_comanda(): void
    {
        $bodega = Bodega::create(['descripcion' => 'Bodega Test']);
        $zona = Zona::create(['nombre' => 'Zona Test', 'bodega_id' => $bodega->id]);
        $mesa = Mesa::create(['zona_id' => $zona->id, 'numero' => 'M-4', 'capacidad' => 4, 'estado' => 'ocupada']);

        $rolMesero = Roles::firstOrCreate(['nombre' => 'Mesero'], ['descripcion' => 'Test']);
        $mesero = User::create(['name' => 'Mesero Test', 'username' => 'mesero-test', 'password' => bcrypt('secret'), 'rol_id' => $rolMesero->id, 'activo' => true]);

        $rolAdmin = Roles::firstOrCreate(['nombre' => 'Administrador'], ['descripcion' => 'Test']);
        $admin = User::create(['name' => 'Admin Test', 'username' => 'admin-test', 'password' => bcrypt('secret'), 'rol_id' => $rolAdmin->id, 'activo' => true, 'clave_anulacion' => Hash::make('4567')]);

        Tercero::firstOrCreate(['id' => 1], ['tipo' => 'persona', 'nombre' => 'Consumidor', 'apellido' => 'Final', 'celular' => '0000000000']);
        $pedido = Pedido::create(['mesa_id' => $mesa->id, 'user_id' => $mesero->id, 'total' => 0, 'cliente_id' => 1, 'estado' => 'pendiente']);

        $clasica = Producto::create(['codigo' => 'BC1', 'descripcion' => 'Hamburguesa clasica', 'und_detal' => 'UND', 'precio' => 20000, 'inactivo' => 0]);
        $mexicana = Producto::create(['codigo' => 'BM1', 'descripcion' => 'Hamburguesa mexicana', 'und_detal' => 'UND', 'precio' => 22000, 'inactivo' => 0]);

        $itemClasica = DetallePedido::create(['pedido_id' => $pedido->id, 'producto_id' => $clasica->id, 'cantidad' => 1, 'precio_unitario' => 20000, 'subtotal' => 20000]);
        $itemMexicana = DetallePedido::create([
            'pedido_id' => $pedido->id,
            'producto_id' => $mexicana->id,
            'cantidad' => 1,
            'precio_unitario' => 22000,
            'subtotal' => 22000,
            'observacion' => 'Sin cebolla',
            'cancelado_at' => now(),
            'cancelado_por' => $admin->id,
        ]);

        $impresora = $this->impresoraCocina();
        $comanda = ComandaPendiente::create([
            'pedido_id' => $pedido->id,
            'tipo' => 'comanda',
            'impresora_id' => $impresora->id,
            'contenido' => 'ORDEN DE PISO',
            'detalle_ids' => [$itemClasica->id, $itemMexicana->id],
            'estado' => 'pendiente',
        ]);

        // Un ticket 'anulacion' (el que dispara el papel físico) NO debe
        // aparecer como su propia ficha en pantalla.
        ComandaPendiente::create([
            'pedido_id' => $pedido->id,
            'tipo' => 'anulacion',
            'impresora_id' => $impresora->id,
            'contenido' => 'ANULACION: Hamburguesa mexicana',
            'detalle_ids' => ['mesa' => 'M-4', 'producto' => 'Hamburguesa mexicana', 'cantidad' => 1, 'observacion' => 'Sin cebolla'],
            'estado' => 'pendiente',
        ]);

        $respuesta = $this->actingAs($this->chef())
            ->getJson('/cocina/comandas')
            ->assertOk()
            ->assertJsonPath('data.0.id', $comanda->id)
            ->assertJsonPath('data.0.mesa', 'M-4')
            ->assertJsonCount(1, 'data') // la anulación no forma una ficha aparte
            ->assertJsonCount(2, 'data.0.items');

        $items = collect($respuesta->json('data.0.items'));
        $lineaClasica = $items->firstWhere('producto', 'Hamburguesa clasica');
        $lineaMexicana = $items->firstWhere('producto', 'Hamburguesa mexicana');

        $this->assertFalse($lineaClasica['cancelado']);
        $this->assertTrue($lineaMexicana['cancelado']);
        $this->assertSame('Admin Test', $lineaMexicana['cancelado_por']);
    }
}
