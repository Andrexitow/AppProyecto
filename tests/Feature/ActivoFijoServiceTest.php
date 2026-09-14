<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\{TipoDocumentoContableSeeder, ProcesoContableSeeder, ConfiguracionContableSeeder, PucSeeder, ParametrizacionInicialContableSeeder};
use App\Models\{ActivoFijo, CuentaContable, Roles, User};
use App\Services\ActivoFijoService;
use Illuminate\Validation\ValidationException;

class ActivoFijoServiceTest extends TestCase
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
        return User::firstOrCreate(['username' => 'activos-test'], ['name' => 'Activos Test', 'password' => bcrypt('secret'), 'activo' => true, 'rol_id' => $r->id]);
    }

    private function cajaId(): int
    {
        return CuentaContable::where('codigo', '110505')->value('id');
    }

    public function test_registrar_activo_contabiliza_el_alta_y_calcula_cuota_mensual()
    {
        $activo = app(ActivoFijoService::class)->registrar([
            'nombre' => 'Computador Dell',
            'categoria' => 'equipo_computo',
            'fecha_adquisicion' => '2026-01-10',
            'valor_adquisicion' => 2400000,
            'valor_residual' => 0,
            'vida_util_meses' => 24,
            'cuenta_contrapartida_id' => $this->cajaId(),
        ], $this->user()->id);

        $this->assertEquals('BIEN-0001', $activo->codigo);
        $this->assertEquals(100000, $activo->depreciacion_mensual); // 2.400.000 / 24
        $this->assertNotNull($activo->comprobante_alta_id);

        $comprobante = $activo->comprobanteAlta;
        $this->assertEquals('CONTABILIZADO', $comprobante->estado);
        $this->assertEquals(2400000, $comprobante->movimientos->sum('debito'));
        $this->assertEquals(2400000, $comprobante->movimientos->sum('credito'));
    }

    public function test_depreciar_periodo_genera_un_comprobante_balanceado_y_acumula()
    {
        $servicio = app(ActivoFijoService::class);
        $activo = $servicio->registrar([
            'nombre' => 'Nevera Industrial',
            'categoria' => 'equipo_cocina',
            'fecha_adquisicion' => '2026-01-01',
            'valor_adquisicion' => 1200000,
            'valor_residual' => 0,
            'vida_util_meses' => 12,
            'cuenta_contrapartida_id' => $this->cajaId(),
        ], $this->user()->id);

        $resultado = $servicio->depreciarPeriodo('2026-01', $this->user()->id);

        $this->assertEquals(1, $resultado['procesados']);
        $this->assertEquals(100000, $resultado['total_depreciado']);

        $activo->refresh();
        $this->assertEquals(100000, $activo->depreciacion_acumulada);
        $this->assertEquals(1100000, $activo->valor_libros);

        // Correr el mismo período otra vez no debe duplicar el gasto.
        $segundaVez = $servicio->depreciarPeriodo('2026-01', $this->user()->id);
        $this->assertEquals(0, $segundaVez['procesados']);
        $activo->refresh();
        $this->assertEquals(100000, $activo->depreciacion_acumulada);
    }

    public function test_depreciacion_no_pasa_del_valor_de_salvamento()
    {
        $servicio = app(ActivoFijoService::class);
        $activo = $servicio->registrar([
            'nombre' => 'Silla Gerencial',
            'categoria' => 'mueble_enseres',
            'fecha_adquisicion' => '2025-01-01',
            'valor_adquisicion' => 300000,
            'valor_residual' => 50000,
            'vida_util_meses' => 5, // cuota mensual = 50.000
            'cuenta_contrapartida_id' => $this->cajaId(),
        ], $this->user()->id);

        foreach (['2025-01', '2025-02', '2025-03', '2025-04', '2025-05', '2025-06'] as $periodo) {
            $servicio->depreciarPeriodo($periodo, $this->user()->id);
        }

        $activo->refresh();
        // Solo se deprecian los 5 meses de vida útil (250.000), nunca por debajo del residual.
        $this->assertEquals(250000, $activo->depreciacion_acumulada);
        $this->assertEquals(50000, $activo->valor_libros);

        // Un sexto período ya no genera nada porque no queda valor depreciable.
        $sexto = $servicio->depreciarPeriodo('2025-07', $this->user()->id);
        $this->assertEquals(0, $sexto['procesados']);
    }

    public function test_dar_de_baja_reversa_depreciacion_y_reconoce_perdida()
    {
        $servicio = app(ActivoFijoService::class);
        $activo = $servicio->registrar([
            'nombre' => 'Impresora de Cocina',
            'categoria' => 'equipo_computo',
            'fecha_adquisicion' => '2026-01-01',
            'valor_adquisicion' => 500000,
            'valor_residual' => 0,
            'vida_util_meses' => 10, // cuota = 50.000
            'cuenta_contrapartida_id' => $this->cajaId(),
        ], $this->user()->id);

        $servicio->depreciarPeriodo('2026-01', $this->user()->id);
        $servicio->depreciarPeriodo('2026-02', $this->user()->id);
        $activo->refresh();
        $this->assertEquals(100000, $activo->depreciacion_acumulada);

        $activo = $servicio->darDeBaja($activo, '2026-03-01', $this->user()->id, 'Dañada');

        $this->assertEquals('de_baja', $activo->estado);
        $comprobante = $activo->comprobanteBaja;
        $this->assertEquals('CONTABILIZADO', $comprobante->estado);
        // Débito: 100.000 (reverso depreciación) + 400.000 (pérdida) = 500.000. Crédito: 500.000 (activo).
        $this->assertEquals(500000, $comprobante->movimientos->sum('debito'));
        $this->assertEquals(500000, $comprobante->movimientos->sum('credito'));
    }

    public function test_no_permite_valor_residual_mayor_o_igual_al_de_adquisicion()
    {
        $this->expectException(ValidationException::class);

        app(ActivoFijoService::class)->registrar([
            'nombre' => 'Activo Inválido',
            'categoria' => 'mueble_enseres',
            'fecha_adquisicion' => '2026-01-01',
            'valor_adquisicion' => 100000,
            'valor_residual' => 100000,
            'vida_util_meses' => 12,
            'cuenta_contrapartida_id' => $this->cajaId(),
        ], $this->user()->id);
    }
}
