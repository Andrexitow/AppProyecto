<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\{CuentaContable, TipoDocumentoContable, User, Roles, ConfiguracionContable, Tercero};
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
        return $this->registrarComprobante([
            ['cuenta_id' => $caja->id, 'debito' => $valor, 'credito' => 0],
            ['cuenta_id' => $ingreso->id, 'debito' => 0, 'credito' => $valor],
        ], $fecha);
    }

    /** Registra un comprobante con líneas arbitrarias (soporta tercero_id por línea). */
    private function registrarComprobante(array $lineas, string $fecha)
    {
        $tipo = TipoDocumentoContable::create(['codigo' => 'CG-' . uniqid(), 'nombre' => 'General', 'prefijo' => 'CG', 'consecutivo' => 1, 'longitud' => 4, 'estado' => true]);
        $servicio = app(AccountingService::class);
        $comprobante = $servicio->crearBorrador(['tipo_documento_contable_id' => $tipo->id, 'fecha' => $fecha, 'descripcion' => 'Movimiento'], $this->user()->id);
        $servicio->registrar($comprobante, $lineas, $this->user()->id);

        return $comprobante;
    }

    /** Mapea una clave de ConfiguracionContable a una cuenta, como lo hace ParametrizacionInicialContableSeeder. */
    private function configurar(string $clave, CuentaContable $cuenta): void
    {
        ConfiguracionContable::updateOrCreate(['clave' => $clave], ['nombre' => $clave, 'cuenta_contable_id' => $cuenta->id]);
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

    /**
     * Antes, el Balance General mezclaba TODO el histórico (desde 0001-01-01)
     * en una sola cifra de "resultado del ejercicio" — una contadora necesita
     * ver la utilidad del año en curso separada de lo acumulado en años
     * anteriores, tal como exige la presentación real de un balance.
     */
    public function test_balance_general_separa_utilidad_del_ejercicio_actual_de_acumuladas()
    {
        $caja = $this->cuenta('110505', 'ACTIVO', 'DEBITO');
        $ventas = $this->cuenta('413505', 'INGRESO', 'CREDITO');
        $this->registrarVenta($caja, $ventas, 1000, '2025-06-10'); // año anterior
        $this->registrarVenta($caja, $ventas, 300, '2026-02-20');  // año en curso

        $balance = app(ReporteContableService::class)->balanceGeneral('2026-12-31');

        $this->assertEquals(300, $balance['utilidadEjercicioActual']);
        $this->assertEquals(1000, $balance['utilidadesAcumuladas']);
        $this->assertEquals(1300, $balance['resultadoEjercicio']);
        $this->assertTrue($balance['cuadrado']);
    }

    public function test_iva_periodo_calcula_neto_a_pagar()
    {
        $caja = $this->cuenta('110505', 'ACTIVO', 'DEBITO');
        $ivaGenerado = $this->cuenta('240805', 'PASIVO', 'CREDITO');
        $ivaDescontable = $this->cuenta('240810', 'PASIVO', 'DEBITO');
        $this->configurar('CUENTA_IVA_GENERADO', $ivaGenerado);
        $this->configurar('CUENTA_IVA_DESCONTABLE', $ivaDescontable);

        // Venta: $1.000.000 de IVA generado (crédito).
        $this->registrarComprobante([
            ['cuenta_id' => $caja->id, 'debito' => 1000000, 'credito' => 0],
            ['cuenta_id' => $ivaGenerado->id, 'debito' => 0, 'credito' => 1000000],
        ], '2026-01-10');

        // Compra: $400.000 de IVA descontable (débito).
        $this->registrarComprobante([
            ['cuenta_id' => $ivaDescontable->id, 'debito' => 400000, 'credito' => 0],
            ['cuenta_id' => $caja->id, 'debito' => 0, 'credito' => 400000],
        ], '2026-01-15');

        $iva = app(ReporteContableService::class)->ivaPeriodo('2026-01-01', '2026-01-31');

        $this->assertEquals(1000000, $iva['ivaGenerado']);
        $this->assertEquals(400000, $iva['ivaDescontable']);
        $this->assertEquals(600000, $iva['neto']);
        $this->assertTrue($iva['aPagar']);
        $this->assertFalse($iva['saldoAFavor']);
        $this->assertEquals(600000, $iva['valorAbsoluto']);
    }

    public function test_iva_periodo_detecta_saldo_a_favor()
    {
        $caja = $this->cuenta('110505', 'ACTIVO', 'DEBITO');
        $ivaGenerado = $this->cuenta('240805', 'PASIVO', 'CREDITO');
        $ivaDescontable = $this->cuenta('240810', 'PASIVO', 'DEBITO');
        $this->configurar('CUENTA_IVA_GENERADO', $ivaGenerado);
        $this->configurar('CUENTA_IVA_DESCONTABLE', $ivaDescontable);

        $this->registrarComprobante([
            ['cuenta_id' => $caja->id, 'debito' => 190000, 'credito' => 0],
            ['cuenta_id' => $ivaGenerado->id, 'debito' => 0, 'credito' => 190000],
        ], '2026-01-10');

        $this->registrarComprobante([
            ['cuenta_id' => $ivaDescontable->id, 'debito' => 500000, 'credito' => 0],
            ['cuenta_id' => $caja->id, 'debito' => 0, 'credito' => 500000],
        ], '2026-01-15');

        $iva = app(ReporteContableService::class)->ivaPeriodo('2026-01-01', '2026-01-31');

        $this->assertEquals(-310000, $iva['neto']);
        $this->assertFalse($iva['aPagar']);
        $this->assertTrue($iva['saldoAFavor']);
        $this->assertEquals(310000, $iva['valorAbsoluto']);
    }

    /**
     * Antes las 3 retenciones (fuente/IVA/ICA) caían todas en una sola cuenta
     * combinada, así que era imposible saber cuánto declarar en cada renglón
     * del Formulario 350. Este informe debe reportarlas separadas y por
     * tercero (a quién se le practicó cada retención).
     */
    public function test_retenciones_practicadas_separa_por_tipo_y_tercero()
    {
        $caja = $this->cuenta('110505', 'ACTIVO', 'DEBITO');
        $gasto = $this->cuenta('519505', 'GASTO', 'DEBITO');
        $retefuente = $this->cuenta('236540', 'PASIVO', 'CREDITO');
        $reteiva = $this->cuenta('236705', 'PASIVO', 'CREDITO');
        $reteica = $this->cuenta('236805', 'PASIVO', 'CREDITO');
        $this->configurar('CUENTA_RETEFUENTE', $retefuente);
        $this->configurar('CUENTA_RETEIVA', $reteiva);
        $this->configurar('CUENTA_RETEICA', $reteica);

        // La cédula/NIT del tercero viaja en el reporte porque el certificado de
        // retención que se le entrega debe identificarlo formalmente.
        $proveedor = Tercero::create(['tipo' => 'persona', 'nombre' => 'Juan', 'apellido' => 'Pérez', 'cedula' => '111', 'estado' => true]);
        $otroProveedor = Tercero::create(['tipo' => 'persona', 'nombre' => 'Ana', 'apellido' => 'Gómez', 'cedula' => '222', 'estado' => true]);

        // Compra a Juan por $1.000.000: retefuente $35.000, reteiva $19.000.
        $this->registrarComprobante([
            ['cuenta_id' => $gasto->id, 'debito' => 1000000, 'credito' => 0],
            ['cuenta_id' => $caja->id, 'debito' => 0, 'credito' => 946000],
            ['cuenta_id' => $retefuente->id, 'debito' => 0, 'credito' => 35000, 'tercero_id' => $proveedor->id],
            ['cuenta_id' => $reteiva->id, 'debito' => 0, 'credito' => 19000, 'tercero_id' => $proveedor->id],
        ], '2026-01-10');

        // Compra a Ana por $1.000.000: reteica $7.000.
        $this->registrarComprobante([
            ['cuenta_id' => $gasto->id, 'debito' => 1000000, 'credito' => 0],
            ['cuenta_id' => $caja->id, 'debito' => 0, 'credito' => 993000],
            ['cuenta_id' => $reteica->id, 'debito' => 0, 'credito' => 7000, 'tercero_id' => $otroProveedor->id],
        ], '2026-01-12');

        $reporte = app(ReporteContableService::class)->retencionesPracticadas('2026-01-01', '2026-01-31');

        $this->assertEquals(35000, $reporte['tipos']['retefuente']['total']);
        $this->assertEquals(19000, $reporte['tipos']['reteiva']['total']);
        $this->assertEquals(7000, $reporte['tipos']['reteica']['total']);
        $this->assertEquals(61000, $reporte['totalGeneral']);

        $porTerceroRetefuente = $reporte['tipos']['retefuente']['porTercero'];
        $this->assertCount(1, $porTerceroRetefuente);
        $this->assertStringContainsString('Juan', $porTerceroRetefuente->first()->tercero);
        $this->assertEquals('111', $porTerceroRetefuente->first()->cedula);

        $porTerceroReteica = $reporte['tipos']['reteica']['porTercero'];
        $this->assertCount(1, $porTerceroReteica);
        $this->assertStringContainsString('Ana', $porTerceroReteica->first()->tercero);
    }

    public function test_indicadores_financieros_calcula_margenes_y_rentabilidad()
    {
        $caja = $this->cuenta('110505', 'ACTIVO', 'DEBITO');
        $ventas = $this->cuenta('413505', 'INGRESO', 'CREDITO');
        $costo = $this->cuenta('613505', 'COSTO', 'DEBITO');
        $capital = $this->cuenta('310505', 'PATRIMONIO', 'CREDITO');

        // Capital inicial de 1.000.000, luego una venta de 1.000 con costo de 400.
        $this->registrarComprobante([
            ['cuenta_id' => $caja->id, 'debito' => 1000000, 'credito' => 0],
            ['cuenta_id' => $capital->id, 'debito' => 0, 'credito' => 1000000],
        ], '2026-01-01');
        $this->registrarVenta($caja, $ventas, 1000, '2026-01-10');
        $this->registrarComprobante([
            ['cuenta_id' => $costo->id, 'debito' => 400, 'credito' => 0],
            ['cuenta_id' => $caja->id, 'debito' => 0, 'credito' => 400],
        ], '2026-01-10');

        $ind = app(ReporteContableService::class)->indicadoresFinancieros('2026-01-01', '2026-01-31');

        // Utilidad bruta = 1000 - 400 = 600; margen bruto = 600/1000 = 60%.
        $this->assertEquals(60.0, $ind['margenBruto']);
        $this->assertEquals(60.0, $ind['margenNeto']); // sin gastos operacionales, neta = bruta
        $this->assertEquals(0.0, $ind['endeudamiento']); // sin pasivos
        $this->assertEqualsWithDelta(1000600, $ind['activoTotal'], 0.01);
    }
}
