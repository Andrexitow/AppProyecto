<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\{TipoDocumentoContableSeeder, PucSeeder};
use App\Models\{CuentaContable, Roles, User};
use App\Services\SaldoInicialService;
use Illuminate\Validation\ValidationException;

class SaldoInicialServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TipoDocumentoContableSeeder::class);
        $this->seed(PucSeeder::class);
    }

    private function user(): User
    {
        $r = Roles::firstOrCreate(['nombre' => 'Administrador'], ['descripcion' => 'Test']);
        return User::firstOrCreate(['username' => 'saldos-test'], ['name' => 'Saldos Test', 'password' => bcrypt('secret'), 'activo' => true, 'rol_id' => $r->id]);
    }

    public function test_registra_saldos_iniciales_balanceados()
    {
        $caja = CuentaContable::where('codigo', '110505')->firstOrFail();
        $capital = CuentaContable::where('codigo', '310505')->firstOrFail();

        $comprobante = app(SaldoInicialService::class)->registrar([
            'fecha' => '2026-01-01',
            'lineas' => [
                ['cuenta_id' => $caja->id, 'debito' => 5000000, 'credito' => 0],
                ['cuenta_id' => $capital->id, 'debito' => 0, 'credito' => 5000000],
            ],
        ], $this->user()->id);

        $this->assertEquals('REGISTRADO', $comprobante->estado);
        $this->assertEquals('SI', $comprobante->prefijo);
        $this->assertEquals(5000000, $comprobante->total_debito);
        $this->assertEquals(5000000, $comprobante->total_credito);
    }

    public function test_rechaza_saldos_iniciales_descuadrados()
    {
        $caja = CuentaContable::where('codigo', '110505')->firstOrFail();
        $capital = CuentaContable::where('codigo', '310505')->firstOrFail();

        $this->expectException(ValidationException::class);

        app(SaldoInicialService::class)->registrar([
            'fecha' => '2026-01-01',
            'lineas' => [
                ['cuenta_id' => $caja->id, 'debito' => 5000000, 'credito' => 0],
                ['cuenta_id' => $capital->id, 'debito' => 0, 'credito' => 4000000],
            ],
        ], $this->user()->id);
    }

    public function test_requiere_al_menos_dos_lineas()
    {
        $caja = CuentaContable::where('codigo', '110505')->firstOrFail();

        $this->expectException(ValidationException::class);

        app(SaldoInicialService::class)->registrar([
            'fecha' => '2026-01-01',
            'lineas' => [
                ['cuenta_id' => $caja->id, 'debito' => 5000000, 'credito' => 0],
            ],
        ], $this->user()->id);
    }
}
