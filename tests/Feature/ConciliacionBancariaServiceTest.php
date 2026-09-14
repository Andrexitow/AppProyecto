<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\{TipoDocumentoContableSeeder, ProcesoContableSeeder, ConfiguracionContableSeeder, PucSeeder, ParametrizacionInicialContableSeeder, CuentaTesoreriaSeeder};
use App\Models\{CuentaContable, CuentaTesoreria, Roles, User};
use App\Services\{ConciliacionBancariaService, TesoreriaService};
use Illuminate\Validation\ValidationException;

class ConciliacionBancariaServiceTest extends TestCase
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
        return User::firstOrCreate(['username' => 'conciliacion-test'], ['name' => 'Conciliacion Test', 'password' => bcrypt('secret'), 'activo' => true, 'rol_id' => $r->id]);
    }

    private function banco(): CuentaTesoreria
    {
        return CuentaTesoreria::where('nombre', 'Banco Principal')->firstOrFail();
    }

    public function test_sugiere_coincidencia_por_mismo_valor_y_fecha_cercana()
    {
        $banco = $this->banco();
        $contrapartida = CuentaContable::where('codigo', '413505')->firstOrFail();

        app(TesoreriaService::class)->registrarIngreso([
            'cuenta_tesoreria_id' => $banco->id, 'cuenta_contrapartida_id' => $contrapartida->id,
            'fecha' => '2026-01-10', 'valor' => 500000, 'descripcion' => 'Venta transferencia',
        ], $this->user()->id);

        $conciliacion = app(ConciliacionBancariaService::class);
        $conciliacion->agregarLinea([
            'cuenta_tesoreria_id' => $banco->id, 'fecha' => '2026-01-12', 'descripcion' => 'Consignación', 'valor' => 500000,
        ], $this->user()->id);

        $sugerencias = $conciliacion->sugerirCoincidencias($banco, '2026-01-01', '2026-01-31');

        $this->assertCount(1, $sugerencias);
        $this->assertEquals(500000, $sugerencias[0]['valor']);
    }

    public function test_conciliar_marca_ambos_lados_y_desconciliar_revierte()
    {
        $banco = $this->banco();
        $contrapartida = CuentaContable::where('codigo', '413505')->firstOrFail();

        app(TesoreriaService::class)->registrarIngreso([
            'cuenta_tesoreria_id' => $banco->id, 'cuenta_contrapartida_id' => $contrapartida->id,
            'fecha' => '2026-01-10', 'valor' => 300000, 'descripcion' => 'Venta',
        ], $this->user()->id);

        $conciliacion = app(ConciliacionBancariaService::class);
        $linea = $conciliacion->agregarLinea([
            'cuenta_tesoreria_id' => $banco->id, 'fecha' => '2026-01-10', 'descripcion' => 'Consignación', 'valor' => 300000,
        ], $this->user()->id);

        $movimientoId = $conciliacion->movimientosSistema($banco, '2026-01-01', '2026-01-31')[0]->id;
        $conciliacion->conciliar($linea, $movimientoId);

        $resumen = $conciliacion->resumen($banco, '2026-01-01', '2026-01-31');
        $this->assertCount(0, $resumen['pendientes_sistema']);
        $this->assertCount(0, $resumen['pendientes_extracto']);

        $conciliacion->desconciliar($linea->fresh());
        $resumen2 = $conciliacion->resumen($banco, '2026-01-01', '2026-01-31');
        $this->assertCount(1, $resumen2['pendientes_sistema']);
        $this->assertCount(1, $resumen2['pendientes_extracto']);
    }

    public function test_no_permite_conciliar_el_mismo_movimiento_dos_veces()
    {
        $banco = $this->banco();
        $contrapartida = CuentaContable::where('codigo', '413505')->firstOrFail();

        app(TesoreriaService::class)->registrarIngreso([
            'cuenta_tesoreria_id' => $banco->id, 'cuenta_contrapartida_id' => $contrapartida->id,
            'fecha' => '2026-01-10', 'valor' => 100000, 'descripcion' => 'Venta',
        ], $this->user()->id);

        $conciliacion = app(ConciliacionBancariaService::class);
        $movimientoId = $conciliacion->movimientosSistema($banco, '2026-01-01', '2026-01-31')[0]->id;

        $linea1 = $conciliacion->agregarLinea(['cuenta_tesoreria_id' => $banco->id, 'fecha' => '2026-01-10', 'descripcion' => 'A', 'valor' => 100000], $this->user()->id);
        $linea2 = $conciliacion->agregarLinea(['cuenta_tesoreria_id' => $banco->id, 'fecha' => '2026-01-10', 'descripcion' => 'B', 'valor' => 100000], $this->user()->id);

        $conciliacion->conciliar($linea1, $movimientoId);

        $this->expectException(ValidationException::class);
        $conciliacion->conciliar($linea2, $movimientoId);
    }

    public function test_resumen_calcula_totales_y_pendientes()
    {
        $banco = $this->banco();
        $contrapartida = CuentaContable::where('codigo', '413505')->firstOrFail();

        app(TesoreriaService::class)->registrarIngreso([
            'cuenta_tesoreria_id' => $banco->id, 'cuenta_contrapartida_id' => $contrapartida->id,
            'fecha' => '2026-01-05', 'valor' => 200000, 'descripcion' => 'Venta 1',
        ], $this->user()->id);

        $conciliacion = app(ConciliacionBancariaService::class);
        // Comisión bancaria que el banco cobró pero el sistema aún no registró.
        $conciliacion->agregarLinea(['cuenta_tesoreria_id' => $banco->id, 'fecha' => '2026-01-06', 'descripcion' => 'Comisión', 'valor' => -5000], $this->user()->id);

        $resumen = $conciliacion->resumen($banco, '2026-01-01', '2026-01-31');

        $this->assertEquals(200000, $resumen['total_movimientos_libros']);
        $this->assertEquals(-5000, $resumen['total_lineas_extracto']);
        $this->assertEquals(200000, $resumen['total_pendientes_sistema']);
        $this->assertEquals(-5000, $resumen['total_pendientes_extracto']);
    }
}
