<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\{TipoDocumentoContableSeeder, ProcesoContableSeeder, ConfiguracionContableSeeder, PucSeeder, ParametrizacionInicialContableSeeder, CuentaTesoreriaSeeder};
use App\Models\{CuentaTesoreria, User, Roles, CuentaContable};
use App\Services\TesoreriaService;
use Illuminate\Validation\ValidationException;

class TesoreriaServiceTest extends TestCase
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
        $this->seed(CuentaTesoreriaSeeder::class);
    }

    private function user(): User
    {
        $r = Roles::firstOrCreate(['nombre' => 'Administrador'], ['descripcion' => 'Test']);
        return User::firstOrCreate(['username' => 'tesoreria-test'], ['name' => 'Tesoreria Test', 'password' => bcrypt('secret'), 'activo' => true, 'rol_id' => $r->id]);
    }

    private function cajaGeneral(): CuentaTesoreria
    {
        return CuentaTesoreria::where('nombre', 'Caja General')->firstOrFail();
    }

    private function bancoPrincipal(): CuentaTesoreria
    {
        return CuentaTesoreria::where('nombre', 'Banco Principal')->firstOrFail();
    }

    /** Cuenta cualquiera que sirva de "contrapartida" para un ingreso/egreso (ej. Ventas o un gasto). */
    private function cuentaContrapartida(): CuentaContable
    {
        return CuentaContable::where('permite_movimientos', true)->where('estado', true)->where('codigo', '!=', '110505')->where('codigo', '!=', '111005')->firstOrFail();
    }

    public function test_ingreso_aumenta_el_saldo_de_la_cuenta_y_contabiliza_balanceado()
    {
        $servicio = app(TesoreriaService::class);
        $caja = $this->cajaGeneral();
        $contrapartida = $this->cuentaContrapartida();

        $mov = $servicio->registrarIngreso([
            'cuenta_tesoreria_id' => $caja->id,
            'cuenta_contrapartida_id' => $contrapartida->id,
            'fecha' => now()->toDateString(),
            'valor' => 1000,
            'descripcion' => 'Ingreso de prueba',
        ], $this->user()->id);

        $this->assertEquals(1000, $servicio->saldo($caja));
        $this->assertEquals('CONTABILIZADO', $mov->comprobante->estado);
        $movs = $mov->comprobante->movimientos;
        $this->assertEquals((float) $movs->sum('debito'), (float) $movs->sum('credito'));
    }

    public function test_egreso_disminuye_el_saldo_y_rechaza_si_supera_el_disponible()
    {
        $servicio = app(TesoreriaService::class);
        $caja = $this->cajaGeneral();
        $contrapartida = $this->cuentaContrapartida();

        $servicio->registrarIngreso(['cuenta_tesoreria_id' => $caja->id, 'cuenta_contrapartida_id' => $contrapartida->id, 'fecha' => now()->toDateString(), 'valor' => 1000, 'descripcion' => 'Fondeo'], $this->user()->id);
        $servicio->registrarEgreso(['cuenta_tesoreria_id' => $caja->id, 'cuenta_contrapartida_id' => $contrapartida->id, 'fecha' => now()->toDateString(), 'valor' => 400, 'descripcion' => 'Gasto'], $this->user()->id);

        $this->assertEquals(600, $servicio->saldo($caja));

        try {
            $servicio->registrarEgreso(['cuenta_tesoreria_id' => $caja->id, 'cuenta_contrapartida_id' => $contrapartida->id, 'fecha' => now()->toDateString(), 'valor' => 99999, 'descripcion' => 'Gasto excesivo'], $this->user()->id);
            $this->fail('Debía rechazar un egreso mayor al saldo disponible.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('valor', $e->errors());
        }
    }

    public function test_transferencia_mueve_el_saldo_entre_cuentas_y_queda_balanceada()
    {
        $servicio = app(TesoreriaService::class);
        $caja = $this->cajaGeneral();
        $banco = $this->bancoPrincipal();
        $contrapartida = $this->cuentaContrapartida();

        $servicio->registrarIngreso(['cuenta_tesoreria_id' => $caja->id, 'cuenta_contrapartida_id' => $contrapartida->id, 'fecha' => now()->toDateString(), 'valor' => 5000, 'descripcion' => 'Fondeo caja'], $this->user()->id);

        [$salida, $entrada] = $servicio->registrarTransferencia(['cuenta_origen_id' => $caja->id, 'cuenta_destino_id' => $banco->id, 'fecha' => now()->toDateString(), 'valor' => 2000], $this->user()->id);

        $this->assertEquals(3000, $servicio->saldo($caja));
        $this->assertEquals(2000, $servicio->saldo($banco));
        $this->assertEquals($salida->comprobante_contable_id, $entrada->comprobante_contable_id);

        $movs = $salida->comprobante->movimientos;
        $this->assertEquals((float) $movs->sum('debito'), (float) $movs->sum('credito'));
    }

    public function test_transferencia_rechaza_si_supera_el_saldo_de_origen()
    {
        $servicio = app(TesoreriaService::class);
        $caja = $this->cajaGeneral();
        $banco = $this->bancoPrincipal();

        try {
            $servicio->registrarTransferencia(['cuenta_origen_id' => $caja->id, 'cuenta_destino_id' => $banco->id, 'fecha' => now()->toDateString(), 'valor' => 500], $this->user()->id);
            $this->fail('Debía rechazar una transferencia mayor al saldo de origen.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('valor', $e->errors());
        }
    }

    public function test_transferencia_rechaza_misma_cuenta_como_origen_y_destino()
    {
        $servicio = app(TesoreriaService::class);
        $caja = $this->cajaGeneral();

        try {
            $servicio->registrarTransferencia(['cuenta_origen_id' => $caja->id, 'cuenta_destino_id' => $caja->id, 'fecha' => now()->toDateString(), 'valor' => 100], $this->user()->id);
            $this->fail('Debía rechazar una transferencia con la misma cuenta de origen y destino.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('cuenta_destino_id', $e->errors());
        }
    }
}
