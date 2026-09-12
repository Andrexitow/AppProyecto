<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\{CuentaContable, TipoDocumentoContable, User, Roles};
use App\Services\AccountingService;
use App\Services\ReporteContableService;

class ReporteContableServiceTest extends TestCase
{
    use RefreshDatabase;

    private function user()
    {
        $r = Roles::firstOrCreate(['nombre' => 'Administrador'], ['descripcion' => 'Test']);
        return User::firstOrCreate(['username' => 'reportes-test'], ['name' => 'Reportes Test', 'password' => bcrypt('secret'), 'activo' => true, 'rol_id' => $r->id]);
    }

    private function cuenta(string $codigo, string $clasificacion, string $naturaleza): CuentaContable
    {
        return CuentaContable::create([
            'codigo' => $codigo,
            'nombre' => $codigo,
            'nivel' => 1,
            'clasificacion' => $clasificacion,
            'naturaleza' => $naturaleza,
            'tipo' => 'DETALLE',
            'permite_movimientos' => true,
            'estado' => true,
        ]);
    }

    /** Registra un comprobante simple: débito a $caja por $valor, crédito a $contrapartida. */
    private function registrarVenta(CuentaContable $caja, CuentaContable $ingreso, float $valor, string $fecha)
    {
        $tipo = TipoDocumentoContable::create(['codigo' => 'CG-' . uniqid(), 'nombre' => 'General', 'prefijo' => 'CG', 'consecutivo' => 1, 'longitud' => 4, 'estado' => true]);
        $servicio = app(AccountingService::class);
        $comprobante = $servicio->crearBorrador(['tipo_documento_contable_id' => $tipo->id, 'fecha' => $fecha, 'descripcion' => 'Venta'], $this->user()->id);
        $servicio->registrar($comprobante, [
            ['cuenta_id' => $caja->id, 'debito' => $valor, 'credito' => 0],
            ['cuenta_id' => $ingreso->id, 'debito' => 0, 'credito' => $valor],
        ], $this->user()->id);

        return $comprobante;
    }

    public function test_balance_de_prueba_cuadra_y_calcula_saldos()
    {
        $caja = $this->cuenta('110505', 'ACTIVO', 'DEBITO');
        $ventas = $this->cuenta('413505', 'INGRESO', 'CREDITO');
        $this->registrarVenta($caja, $ventas, 500, '2026-01-15');

        $reporte = app(ReporteContableService::class)->balancePrueba('2026-01-01', '2026-01-31');

        $filaCaja = $reporte['filas']->firstWhere('codigo', '110505');
        $filaVentas = $reporte['filas']->firstWhere('codigo', '413505');

        $this->assertEquals(500, $filaCaja['saldo_final_debito']);
        $this->assertEquals(0, $filaCaja['saldo_final_credito']);
        $this->assertEquals(500, $filaVentas['saldo_final_credito']);
        $this->assertEquals($reporte['totales']['saldo_final_debito'], $reporte['totales']['saldo_final_credito']);
    }

    public function test_balance_de_prueba_excluye_comprobantes_fuera_del_rango()
    {
        $caja = $this->cuenta('110505', 'ACTIVO', 'DEBITO');
        $ventas = $this->cuenta('413505', 'INGRESO', 'CREDITO');
        $this->registrarVenta($caja, $ventas, 500, '2026-03-01');

        $reporte = app(ReporteContableService::class)->balancePrueba('2026-01-01', '2026-01-31');

        $this->assertCount(0, $reporte['filas']);
    }

    public function test_libro_diario_lista_movimientos_balanceados()
    {
        $caja = $this->cuenta('110505', 'ACTIVO', 'DEBITO');
        $ventas = $this->cuenta('413505', 'INGRESO', 'CREDITO');
        $this->registrarVenta($caja, $ventas, 500, '2026-01-15');

        $movimientos = app(ReporteContableService::class)->libroDiario('2026-01-01', '2026-01-31');

        $this->assertCount(2, $movimientos);
        $this->assertEquals(500, $movimientos->sum('debito'));
        $this->assertEquals(500, $movimientos->sum('credito'));
    }

    public function test_libro_mayor_calcula_saldo_corriente()
    {
        $caja = $this->cuenta('110505', 'ACTIVO', 'DEBITO');
        $ventas = $this->cuenta('413505', 'INGRESO', 'CREDITO');
        $this->registrarVenta($caja, $ventas, 300, '2026-01-10');
        $this->registrarVenta($caja, $ventas, 200, '2026-01-20');

        $mayor = app(ReporteContableService::class)->libroMayor('2026-01-01', '2026-01-31', $caja->id);

        $this->assertEquals(0, $mayor['saldo_inicial']);
        $this->assertEquals(500, $mayor['saldo_final']);
        $this->assertEquals(300, $mayor['movimientos'][0]->saldo);
        $this->assertEquals(500, $mayor['movimientos'][1]->saldo);
    }

    public function test_estado_de_resultados_calcula_utilidad_neta()
    {
        $caja = $this->cuenta('110505', 'ACTIVO', 'DEBITO');
        $ventas = $this->cuenta('413505', 'INGRESO', 'CREDITO');
        $costo = $this->cuenta('613505', 'COSTO', 'DEBITO');
        $gasto = $this->cuenta('519505', 'GASTO', 'DEBITO');

        $this->registrarVenta($caja, $ventas, 1000, '2026-01-10');
        $this->registrarVenta($costo, $caja, 400, '2026-01-10');
        $this->registrarVenta($gasto, $caja, 100, '2026-01-10');

        $estado = app(ReporteContableService::class)->estadoResultados('2026-01-01', '2026-01-31');

        $this->assertEquals(1000, $estado['ingresos']['total']);
        $this->assertEquals(400, $estado['costos']['total']);
        $this->assertEquals(100, $estado['gastos']['total']);
        $this->assertEquals(600, $estado['utilidadBruta']);
        $this->assertEquals(500, $estado['utilidadNeta']);
    }

    public function test_balance_general_cuadra_activo_contra_pasivo_y_patrimonio()
    {
        $caja = $this->cuenta('110505', 'ACTIVO', 'DEBITO');
        $ventas = $this->cuenta('413505', 'INGRESO', 'CREDITO');
        $this->registrarVenta($caja, $ventas, 500, '2026-01-15');

        $balance = app(ReporteContableService::class)->balanceGeneral('2026-01-31');

        $this->assertEquals(500, $balance['activo']['total']);
        $this->assertEquals(0, $balance['pasivo']['total']);
        $this->assertEquals(500, $balance['resultadoEjercicio']);
        $this->assertTrue($balance['cuadrado']);
        $this->assertEquals(0.0, $balance['diferencia']);
    }
}
