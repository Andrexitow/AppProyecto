<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\{TipoDocumentoContableSeeder, ProcesoContableSeeder, ConfiguracionContableSeeder, PucSeeder, ParametrizacionInicialContableSeeder, CuentaTesoreriaSeeder};
use App\Models\{Ajuste, AjusteDetalle, Bodega, CuentaContable, CuentaTesoreria, PeriodoContable, Roles, User};
use App\Services\{AccountingService, AjusteContableService, PeriodoContableService, TesoreriaService};
use Illuminate\Validation\ValidationException;

class PeriodoContableServiceTest extends TestCase
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
        return User::firstOrCreate(['username' => 'periodo-test'], ['name' => 'Periodo Test', 'password' => bcrypt('secret'), 'activo' => true, 'rol_id' => $r->id]);
    }

    public function test_estaCerrado_solo_es_true_dentro_del_rango_cerrado()
    {
        $servicio = app(PeriodoContableService::class);
        $servicio->cerrarPeriodo('2026-01-01', '2026-01-31', 'Enero 2026', $this->user()->id);

        $this->assertTrue($servicio->estaCerrado('2026-01-15'));
        $this->assertTrue($servicio->estaCerrado('2026-01-01'));
        $this->assertTrue($servicio->estaCerrado('2026-01-31'));
        $this->assertFalse($servicio->estaCerrado('2026-02-01'));
        $this->assertFalse($servicio->estaCerrado('2025-12-31'));
    }

    public function test_cerrarPeriodo_rechaza_rangos_solapados()
    {
        $servicio = app(PeriodoContableService::class);
        $servicio->cerrarPeriodo('2026-01-01', '2026-01-31', 'Enero 2026', $this->user()->id);

        try {
            $servicio->cerrarPeriodo('2026-01-15', '2026-02-15', 'Solapado', $this->user()->id);
            $this->fail('Debía rechazar un período que se solapa con uno existente.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('periodo', $e->errors());
        }
    }

    public function test_reabrirPeriodo_permite_volver_a_operar_y_rechaza_si_ya_esta_abierto()
    {
        $servicio = app(PeriodoContableService::class);
        $periodo = $servicio->cerrarPeriodo('2026-01-01', '2026-01-31', 'Enero 2026', $this->user()->id);

        $this->assertTrue($servicio->estaCerrado('2026-01-15'));
        $servicio->reabrirPeriodo($periodo, $this->user()->id);
        $this->assertFalse($servicio->estaCerrado('2026-01-15'));

        try {
            $servicio->reabrirPeriodo($periodo->fresh(), $this->user()->id);
            $this->fail('Debía rechazar reabrir un período que ya está abierto.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('periodo', $e->errors());
        }
    }

    public function test_periodo_cerrado_bloquea_un_comprobante_manual_nuevo()
    {
        app(PeriodoContableService::class)->cerrarPeriodo('2026-01-01', '2026-01-31', 'Enero 2026', $this->user()->id);

        try {
            app(AccountingService::class)->crearBorrador(['tipo_documento_contable_id' => \App\Models\TipoDocumentoContable::where('codigo', 'CD')->firstOrFail()->id, 'fecha' => '2026-01-15', 'descripcion' => 'Prueba'], $this->user()->id);
            $this->fail('Debía rechazar un comprobante fechado en un período cerrado.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('periodo', $e->errors());
        }
    }

    public function test_periodo_cerrado_bloquea_un_ingreso_de_tesoreria()
    {
        app(PeriodoContableService::class)->cerrarPeriodo('2026-01-01', '2026-01-31', 'Enero 2026', $this->user()->id);

        $caja = CuentaTesoreria::where('nombre', 'Caja General')->firstOrFail();
        $contrapartida = CuentaContable::where('permite_movimientos', true)->where('estado', true)->where('codigo', '!=', '110505')->firstOrFail();

        try {
            app(TesoreriaService::class)->registrarIngreso([
                'cuenta_tesoreria_id' => $caja->id,
                'cuenta_contrapartida_id' => $contrapartida->id,
                'fecha' => '2026-01-15',
                'valor' => 1000,
                'descripcion' => 'No debería pasar',
            ], $this->user()->id);
            $this->fail('Debía rechazar un ingreso de tesorería fechado en un período cerrado.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('periodo', $e->errors());
        }
    }

    /** Cubre el punto que hace update() masivo (no dispara eventos de Eloquent) y por eso lleva su propio chequeo explícito. */
    public function test_periodo_cerrado_bloquea_anular_un_ajuste_via_update_masivo()
    {
        $usuario = $this->user();
        $bodega = Bodega::create(['descripcion' => 'Bodega Periodo']);
        $ajuste = Ajuste::create(['prefijo' => 'AJ', 'numero' => 1, 'fecha' => '2026-01-10', 'bodega_id' => $bodega->id, 'total' => 0, 'registrado' => true, 'user_id' => $usuario->id]);
        AjusteDetalle::create(['ajuste_id' => $ajuste->id, 'producto_id' => \App\Models\Producto::create(['codigo' => 'PX', 'descripcion' => 'PX', 'und_detal' => 'UND'])->id, 'cantidad' => 1, 'tipo' => 'entrada', 'precio' => 0]);

        app(PeriodoContableService::class)->cerrarPeriodo('2026-01-01', '2026-01-31', 'Enero 2026', $usuario->id);

        try {
            app(AjusteContableService::class)->anular($ajuste);
            $this->fail('Debía rechazar anular un ajuste fechado en un período cerrado.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('periodo', $e->errors());
        }
    }
}
