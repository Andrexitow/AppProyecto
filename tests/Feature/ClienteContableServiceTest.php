<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\{TipoDocumentoContableSeeder, ProcesoContableSeeder, ConfiguracionContableSeeder, PucSeeder, ParametrizacionInicialContableSeeder, MetodoPagoContableSeeder};
use App\Models\{Factura, Mesa, Tercero, User, Roles, Caja, Zona, Bodega};
use App\Services\ClienteContableService;
use Illuminate\Validation\ValidationException;

class ClienteContableServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TipoDocumentoContableSeeder::class);
        $this->seed(PucSeeder::class);
        $this->seed(ConfiguracionContableSeeder::class);
        $this->seed(ParametrizacionInicialContableSeeder::class);
        $this->seed(ProcesoContableSeeder::class);
        $this->seed(MetodoPagoContableSeeder::class);
    }

    private function user(): User
    {
        $r = Roles::firstOrCreate(['nombre' => 'Administrador'], ['descripcion' => 'Test']);
        return User::firstOrCreate(['username' => 'cxc-test'], ['name' => 'CxC Test', 'password' => bcrypt('secret'), 'activo' => true, 'rol_id' => $r->id]);
    }

    private function cliente(): Tercero
    {
        return Tercero::create(['tipo' => 'persona', 'nombre' => 'Cliente', 'apellido' => 'De Prueba']);
    }

    private function facturaACredito(float $total): Factura
    {
        $bodega = Bodega::create(['descripcion' => 'Bodega Test']);
        $zona = Zona::create(['nombre' => 'Zona Test', 'bodega_id' => $bodega->id]);
        $mesa = Mesa::create(['zona_id' => $zona->id, 'numero' => '1', 'estado' => 'disponible']);
        $caja = Caja::create(['nombre' => 'Caja Test', 'prefijo' => 'CX', 'bodega_id' => $bodega->id, 'activa' => true]);

        return Factura::create([
            'numero_factura' => 'CX-' . uniqid(),
            'mesa_id' => $mesa->id,
            'user_id' => $this->user()->id,
            'cliente_id' => $this->cliente()->id,
            'caja_id' => $caja->id,
            'subtotal' => $total,
            'total' => $total,
            'metodo_pago' => 'credito',
            'estado' => 'pagada',
            'estado_pago' => 'pendiente',
            'total_pagado' => 0,
            'saldo_pendiente' => $total,
        ]);
    }

    private function metodoEfectivoId(): int
    {
        return \App\Models\MetodoPagoContable::where('metodo_pago', 'efectivo')->firstOrFail()->id;
    }

    public function test_abono_parcial_deja_la_factura_parcialmente_pagada_y_contabilizada()
    {
        $factura = $this->facturaACredito(1000);
        $servicio = app(ClienteContableService::class);

        $pago = $servicio->registrarAbono($factura, ['fecha' => now()->toDateString(), 'valor' => 400, 'metodo_pago_contable_id' => $this->metodoEfectivoId()], $this->user()->id);

        $factura->refresh();
        $this->assertEquals(400, (float) $factura->total_pagado);
        $this->assertEquals(600, (float) $factura->saldo_pendiente);
        $this->assertEquals('parcialmente_pagada', $factura->estado_pago);
        $this->assertNotNull($pago->comprobante_contable_id);

        $comprobante = $pago->comprobante()->first();
        $this->assertEquals('CONTABILIZADO', $comprobante->estado);
        $movimientos = $comprobante->movimientos()->get();
        $this->assertEquals((float) $movimientos->sum('debito'), (float) $movimientos->sum('credito'));
    }

    public function test_dos_abonos_completan_el_saldo_y_marcan_pagada()
    {
        $factura = $this->facturaACredito(1000);
        $servicio = app(ClienteContableService::class);

        $servicio->registrarAbono($factura, ['fecha' => now()->toDateString(), 'valor' => 600, 'metodo_pago_contable_id' => $this->metodoEfectivoId()], $this->user()->id);
        $servicio->registrarAbono($factura->fresh(), ['fecha' => now()->toDateString(), 'valor' => 400, 'metodo_pago_contable_id' => $this->metodoEfectivoId()], $this->user()->id);

        $factura->refresh();
        $this->assertEquals(0, (float) $factura->saldo_pendiente);
        $this->assertEquals('pagada', $factura->estado_pago);
    }

    public function test_rechaza_abono_que_supera_el_saldo_pendiente()
    {
        $factura = $this->facturaACredito(1000);
        $servicio = app(ClienteContableService::class);

        try {
            $servicio->registrarAbono($factura, ['fecha' => now()->toDateString(), 'valor' => 1500, 'metodo_pago_contable_id' => $this->metodoEfectivoId()], $this->user()->id);
            $this->fail('Debía rechazar un abono mayor al saldo pendiente.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('valor', $e->errors());
        }
    }

    public function test_rechaza_abono_sobre_factura_ya_pagada()
    {
        $factura = $this->facturaACredito(1000);
        $factura->update(['estado_pago' => 'pagada', 'total_pagado' => 1000, 'saldo_pendiente' => 0]);
        $servicio = app(ClienteContableService::class);

        try {
            $servicio->registrarAbono($factura, ['fecha' => now()->toDateString(), 'valor' => 100, 'metodo_pago_contable_id' => $this->metodoEfectivoId()], $this->user()->id);
            $this->fail('Debía rechazar un abono sobre una factura ya pagada.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('factura', $e->errors());
        }
    }

    public function test_endpoint_de_venta_a_credito_exige_cliente_real_no_consumidor_final()
    {
        Tercero::updateOrCreate(['id' => 1], ['tipo' => 'empresa', 'razon_social' => 'Consumidor Final']);
        $bodega = Bodega::create(['descripcion' => 'Bodega Endpoint']);
        $zona = Zona::create(['nombre' => 'Zona Endpoint', 'bodega_id' => $bodega->id]);
        $mesa = Mesa::create(['zona_id' => $zona->id, 'numero' => '2', 'estado' => 'disponible']);

        $usuario = $this->user();
        $caja = Caja::create(['nombre' => 'Caja Endpoint', 'prefijo' => 'EP', 'bodega_id' => $bodega->id, 'activa' => true]);
        $usuario->update(['caja_id' => $caja->id]);

        // El pedido queda a nombre del "Consumidor Final" (id 1): es lo que
        // realmente termina en la factura, aunque el request mande otro cliente_id.
        \App\Models\Pedido::create(['mesa_id' => $mesa->id, 'user_id' => $usuario->id, 'cliente_id' => 1, 'total' => 500, 'estado' => 'pendiente']);

        $respuesta = $this->actingAs($usuario)->postJson('/pedidos/cerrar-mesa', [
            'mesa_id' => $mesa->id,
            'metodo_pago' => 'credito',
            'total' => 500,
            'cliente_id' => 1,
        ]);

        $respuesta->assertStatus(422);
    }
}
