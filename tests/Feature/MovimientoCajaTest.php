<?php

namespace Tests\Feature;

use App\Models\Bodega;
use App\Models\Caja;
use App\Models\ConceptoCaja;
use App\Models\MovimientoCaja;
use App\Models\Roles;
use App\Models\Tercero;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Antes, "salida de caja" solo guardaba un texto libre y ni siquiera tenía
 * endpoint (el JS llamaba a /pedidos/guardar-movimiento, que no existía).
 * Ahora exige un concepto del catálogo y, para las salidas, el tercero que
 * recibe el dinero — para poder auditar e imprimir el comprobante que esa
 * persona firma.
 */
class MovimientoCajaTest extends TestCase
{
    use RefreshDatabase;

    private function cajero(): User
    {
        $rol = Roles::firstOrCreate(['nombre' => 'Cajero'], ['descripcion' => 'Test']);

        return User::create([
            'username' => 'cajero-mov-test',
            'name' => 'Cajero Test',
            'password' => bcrypt('secret'),
            'activo' => true,
            'rol_id' => $rol->id,
        ]);
    }

    private function tercero(): Tercero
    {
        return Tercero::create([
            'tipo' => 'persona',
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'cedula' => '123456789',
            'celular' => '3000000000',
        ]);
    }

    public function test_una_salida_requiere_concepto_y_tercero()
    {
        $user = $this->cajero();

        $respuesta = $this->actingAs($user)->postJson('/pedidos/guardar-movimiento', [
            'tipo' => 'salida',
            'monto' => 20000,
        ]);

        $respuesta->assertStatus(422);
        $respuesta->assertJsonValidationErrors(['concepto_caja_id', 'tercero_id']);
    }

    public function test_un_ingreso_no_requiere_tercero()
    {
        $user = $this->cajero();
        $concepto = ConceptoCaja::create(['nombre' => 'Base de caja', 'tipo' => 'ingreso', 'activo' => true]);

        $respuesta = $this->actingAs($user)->postJson('/pedidos/guardar-movimiento', [
            'tipo' => 'ingreso',
            'monto' => 50000,
            'concepto_caja_id' => $concepto->id,
        ]);

        $respuesta->assertOk();
        $respuesta->assertJson(['success' => true]);

        $movimiento = MovimientoCaja::first();
        $this->assertNotNull($movimiento);
        $this->assertSame('entrada', $movimiento->tipo);
        $this->assertNull($movimiento->tercero_id);
        $this->assertEquals(50000, (float) $movimiento->valor);
    }

    public function test_una_salida_completa_guarda_concepto_y_tercero_y_encola_comprobante_si_hay_impresora()
    {
        $user = $this->cajero();
        $concepto = ConceptoCaja::create(['nombre' => 'Pago turno meseros', 'tipo' => 'salida', 'activo' => true]);
        $tercero = $this->tercero();

        $bodega = Bodega::create(['descripcion' => 'Bodega Mov']);
        $impresora = \App\Models\Impresora::create(['nombre' => 'Impresora Caja', 'ip' => '192.168.1.50', 'puerto' => 9100, 'activa' => true]);
        $caja = Caja::create(['nombre' => 'Caja Mov', 'prefijo' => 'MV', 'proximo_numero' => 1, 'bodega_id' => $bodega->id, 'impresora_id' => $impresora->id, 'activa' => true]);
        $user->update(['caja_id' => $caja->id]);

        $respuesta = $this->actingAs($user)->postJson('/pedidos/guardar-movimiento', [
            'tipo' => 'salida',
            'monto' => 15000,
            'concepto_caja_id' => $concepto->id,
            'tercero_id' => $tercero->id,
            'nota' => 'Turno del sábado',
        ]);

        $respuesta->assertOk();
        $respuesta->assertJson(['success' => true, 'comprobante_impreso' => true]);

        $movimiento = MovimientoCaja::first();
        $this->assertSame('salida', $movimiento->tipo);
        $this->assertSame($concepto->id, $movimiento->concepto_caja_id);
        $this->assertSame($tercero->id, $movimiento->tercero_id);
        $this->assertStringContainsString('Turno del sábado', $movimiento->concepto);

        $this->assertDatabaseHas('comandas_pendientes', [
            'tipo' => 'movimiento_caja',
            'impresora_id' => $impresora->id,
        ]);
    }

    public function test_una_salida_sin_impresora_configurada_igual_se_registra()
    {
        $user = $this->cajero();
        $concepto = ConceptoCaja::create(['nombre' => 'Compras generales', 'tipo' => 'ambos', 'activo' => true]);
        $tercero = $this->tercero();

        $respuesta = $this->actingAs($user)->postJson('/pedidos/guardar-movimiento', [
            'tipo' => 'salida',
            'monto' => 8000,
            'concepto_caja_id' => $concepto->id,
            'tercero_id' => $tercero->id,
        ]);

        $respuesta->assertOk();
        $respuesta->assertJson(['success' => true, 'comprobante_impreso' => false]);
    }

    public function test_no_deja_usar_un_concepto_que_no_aplica_al_tipo()
    {
        $user = $this->cajero();
        $concepto = ConceptoCaja::create(['nombre' => 'Solo ingresos', 'tipo' => 'ingreso', 'activo' => true]);
        $tercero = $this->tercero();

        $respuesta = $this->actingAs($user)->postJson('/pedidos/guardar-movimiento', [
            'tipo' => 'salida',
            'monto' => 5000,
            'concepto_caja_id' => $concepto->id,
            'tercero_id' => $tercero->id,
        ]);

        $respuesta->assertStatus(422);
    }

    public function test_las_opciones_de_concepto_filtran_por_tipo_e_incluyen_ambos()
    {
        ConceptoCaja::create(['nombre' => 'Solo salida', 'tipo' => 'salida', 'activo' => true]);
        ConceptoCaja::create(['nombre' => 'Solo ingreso', 'tipo' => 'ingreso', 'activo' => true]);
        ConceptoCaja::create(['nombre' => 'Para ambos', 'tipo' => 'ambos', 'activo' => true]);
        ConceptoCaja::create(['nombre' => 'Inactivo de salida', 'tipo' => 'salida', 'activo' => false]);

        $respuesta = $this->actingAs($this->cajero())->getJson('/conceptos-caja/opciones?tipo=salida');

        $respuesta->assertOk();
        $nombres = collect($respuesta->json('data'))->pluck('nombre')->all();

        $this->assertContains('Solo salida', $nombres);
        $this->assertContains('Para ambos', $nombres);
        $this->assertNotContains('Solo ingreso', $nombres);
        $this->assertNotContains('Inactivo de salida', $nombres);
    }

    public function test_un_cajero_sin_rol_administrador_puede_buscar_y_crear_terceros_desde_el_pos()
    {
        $user = $this->cajero();
        $this->tercero();

        $busqueda = $this->actingAs($user)->getJson('/pedidos/terceros/buscar?query=Juan');
        $busqueda->assertOk();
        $this->assertNotEmpty($busqueda->json());

        $creacion = $this->actingAs($user)->postJson('/pedidos/terceros', [
            'tipo' => 'persona',
            'nombre' => 'Nuevo',
            'apellido' => 'Proveedor',
            'cedula' => '999888777',
            'celular' => '3001112233',
        ]);

        $creacion->assertOk();
        $creacion->assertJson(['success' => true]);
        $this->assertDatabaseHas('terceros', ['cedula' => '999888777']);
    }
}
