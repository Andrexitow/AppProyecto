<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\{TipoDocumentoContableSeeder, ProcesoContableSeeder, ConfiguracionContableSeeder, PucSeeder, ParametrizacionInicialContableSeeder};
use App\Models\{Bodega, Caja, Factura, Mesa, Roles, Tercero, User, Zona};
use App\Services\ProvisionCarteraService;

class ProvisionCarteraServiceTest extends TestCase
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
    }

    private function user(): User
    {
        $r = Roles::firstOrCreate(['nombre' => 'Administrador'], ['descripcion' => 'Test']);
        return User::firstOrCreate(['username' => 'provision-test'], ['name' => 'Provision Test', 'password' => bcrypt('secret'), 'activo' => true, 'rol_id' => $r->id]);
    }

    /** Crea una factura a crédito pendiente, "creada" hace $diasAtras días (para simular mora). */
    private function facturaVencida(float $saldo, int $diasAtras, string $numero): Factura
    {
        $zona = Zona::create(['nombre' => 'Zona Test']);
        $mesa = Mesa::create(['zona_id' => $zona->id, 'numero' => 'M-' . $numero, 'capacidad' => 4]);
        $cliente = Tercero::create(['tipo' => 'persona', 'nombre' => 'Cliente', 'apellido' => $numero, 'cedula' => uniqid(), 'estado' => true]);
        $bodega = Bodega::firstOrCreate(['descripcion' => 'Bodega Test']);
        $caja = Caja::firstOrCreate(['nombre' => 'Caja Test'], ['prefijo' => 'CT', 'bodega_id' => $bodega->id, 'activa' => true]);

        $factura = Factura::create([
            'numero_factura' => $numero,
            'mesa_id' => $mesa->id,
            'user_id' => $this->user()->id,
            'cliente_id' => $cliente->id,
            'caja_id' => $caja->id,
            'subtotal' => $saldo,
            'total' => $saldo,
            'metodo_pago' => 'credito',
            'estado_pago' => 'pendiente',
            'total_pagado' => 0,
            'saldo_pendiente' => $saldo,
        ]);
        // created_at se controla aparte porque Factura no lo expone en $fillable.
        $factura->created_at = now()->subDays($diasAtras);
        $factura->save();

        return $factura;
    }

    public function test_calcula_provision_segun_tramos_de_mora()
    {
        $hoy = now()->toDateString();
        $this->facturaVencida(1000000, 10, 'F-AL-DIA');   // 0-30 días → 0%
        $this->facturaVencida(1000000, 45, 'F-31-60');    // → 10%
        $this->facturaVencida(1000000, 200, 'F-MAS-180'); // → 100%

        $servicio = app(ProvisionCarteraService::class);
        $calculo = $servicio->calcular($hoy);

        $this->assertEquals(3000000, $calculo['total_cartera']);
        // 0 + 100.000 (10% de 1.000.000) + 1.000.000 (100%) = 1.100.000
        $this->assertEquals(1100000, $calculo['total_provision_requerida']);
        $this->assertCount(2, $calculo['detalle']); // la "al día" no aparece (provisión 0)
    }

    public function test_contabiliza_solo_el_ajuste_no_el_total_cada_vez()
    {
        $hoy = now()->toDateString();
        $this->facturaVencida(1000000, 200, 'F-VIEJA'); // 100% de provisión = 1.000.000
        $servicio = app(ProvisionCarteraService::class);

        $primero = $servicio->contabilizarAjuste($hoy, $this->user()->id);
        $this->assertEquals(1000000, $primero['ajuste']);
        $this->assertNotNull($primero['comprobante']);

        // Sin cambios en la cartera, un segundo cálculo el mismo día no debe
        // generar un nuevo ajuste (la provisión ya está al día).
        $segundo = $servicio->contabilizarAjuste($hoy, $this->user()->id);
        $this->assertEquals(0.0, $segundo['ajuste']);
        $this->assertNull($segundo['comprobante']);

        $comprobante = $primero['comprobante'];
        $this->assertEquals(1000000, $comprobante->movimientos->sum('debito'));
        $this->assertEquals(1000000, $comprobante->movimientos->sum('credito'));
    }

    public function test_reduce_la_provision_cuando_la_cartera_mejora()
    {
        $servicio = app(ProvisionCarteraService::class);
        $hoy = now()->toDateString();
        $manana = now()->addDay()->toDateString();

        $factura = $this->facturaVencida(1000000, 200, 'F-MEJORA'); // 100% = 1.000.000
        $servicio->contabilizarAjuste($hoy, $this->user()->id);

        // La factura se abona y el saldo baja a 200.000 (sigue vencida, pero ahora provisiona menos).
        $factura->update(['saldo_pendiente' => 200000]);

        $resultado = $servicio->contabilizarAjuste($manana, $this->user()->id);

        $this->assertEquals(-800000, $resultado['ajuste']);
        $this->assertEquals(200000, $servicio->saldoProvisionActual($manana));
    }
}
