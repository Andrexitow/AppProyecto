<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\{TipoDocumentoContableSeeder, ProcesoContableSeeder, PucSeeder};
use App\Models\{Empleado, ParametroNomina, Roles, User};
use App\Services\NominaService;
use Illuminate\Validation\ValidationException;

class NominaServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TipoDocumentoContableSeeder::class);
        $this->seed(PucSeeder::class);
        $this->seed(ProcesoContableSeeder::class);
    }

    /** Parámetros fijos y redondos para que las matemáticas del test sean fáciles de verificar a mano. */
    private function sembrarParametros(): void
    {
        ParametroNomina::create([
            'smmlv' => 1300000,
            'auxilio_transporte' => 200000,
            'salud_empleado_pct' => 4,
            'pension_empleado_pct' => 4,
            'salud_patronal_pct' => 8.5,
            'pension_patronal_pct' => 12,
            'cesantias_pct' => 8.33,
            'intereses_cesantias_pct' => 1,
            'prima_pct' => 8.33,
            'vacaciones_pct' => 4.17,
            'sena_pct' => 2,
            'icbf_pct' => 3,
            'caja_compensacion_pct' => 4,
        ]);
    }

    private function user(): User
    {
        $r = Roles::firstOrCreate(['nombre' => 'Administrador'], ['descripcion' => 'Test']);
        return User::firstOrCreate(['username' => 'nomina-test'], ['name' => 'Nomina Test', 'password' => bcrypt('secret'), 'activo' => true, 'rol_id' => $r->id]);
    }

    private function empleado(float $salario, string $codigo = 'EMP-0001'): Empleado
    {
        return Empleado::create([
            'codigo' => $codigo,
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'cedula' => (string) random_int(100000000, 999999999),
            'fecha_ingreso' => '2025-01-01',
            'salario_base' => $salario,
            'arl_tarifa' => 0.522,
            'estado' => 'activo',
        ]);
    }

    /**
     * ParametroNomina::vigente() usa firstOrCreate(), que devuelve el objeto
     * recién construido en MEMORIA (no una fila releída de la BD). Si algún
     * porcentaje se omitiera de los valores por defecto de vigente() y se
     * dejara solo en el ->default(...) de la migración, esa primera llamada
     * traería null (0 al calcular) aunque la fila en la BD sí tenga el valor
     * correcto — exactamente el bug que este test evita que vuelva a pasar.
     */
    public function test_parametro_nomina_vigente_no_llega_en_cero_en_la_primera_llamada()
    {
        $this->assertDatabaseCount('parametros_nomina', 0);

        $p = ParametroNomina::vigente();

        $this->assertGreaterThan(0, (float) $p->salud_empleado_pct);
        $this->assertGreaterThan(0, (float) $p->pension_empleado_pct);
        $this->assertGreaterThan(0, (float) $p->cesantias_pct);
        $this->assertGreaterThan(0, (float) $p->prima_pct);
        $this->assertGreaterThan(0, (float) $p->vacaciones_pct);
        $this->assertGreaterThan(0, (float) $p->sena_pct);
        $this->assertGreaterThan(0, (float) $p->icbf_pct);
        $this->assertGreaterThan(0, (float) $p->caja_compensacion_pct);
    }

    public function test_calcula_auxilio_de_transporte_solo_para_salarios_de_hasta_2_smmlv()
    {
        $this->sembrarParametros();
        $p = ParametroNomina::vigente();
        $servicio = app(NominaService::class);

        $bajo = $this->empleado(1300000, 'EMP-0001'); // 1 SMMLV → sí aplica
        $alto = $this->empleado(3000000, 'EMP-0002'); // > 2 SMMLV → no aplica

        $this->assertEquals(200000, $servicio->calcular($bajo, $p)['auxTransporte']);
        $this->assertEquals(0, $servicio->calcular($alto, $p)['auxTransporte']);
    }

    public function test_liquidar_periodo_contabiliza_balanceado_y_calcula_neto()
    {
        $this->sembrarParametros();
        $empleado = $this->empleado(1300000);
        $servicio = app(NominaService::class);

        $liquidacion = $servicio->liquidarPeriodo('2026-01', '2026-01-31', $this->user()->id);

        $this->assertEquals('REGISTRADA', $liquidacion->estado);
        $this->assertCount(1, $liquidacion->detalles);

        $detalle = $liquidacion->detalles->first();
        // Neto = 1.300.000 + 200.000 (aux) - 4% - 4% de 1.300.000 = 1.500.000 - 104.000 = 1.396.000
        $this->assertEquals(1396000, (float) $detalle->neto_pagado);
        $this->assertEquals(1396000, (float) $liquidacion->total_neto);

        $comprobante = $liquidacion->comprobante;
        $this->assertEquals('CONTABILIZADO', $comprobante->estado);
        $this->assertEquals(
            round((float) $comprobante->movimientos->sum('debito'), 2),
            round((float) $comprobante->movimientos->sum('credito'), 2)
        );
        $this->assertGreaterThan(0, $comprobante->movimientos->sum('debito'));
    }

    public function test_no_permite_liquidar_dos_veces_el_mismo_periodo()
    {
        $this->sembrarParametros();
        $this->empleado(1300000);
        $servicio = app(NominaService::class);
        $servicio->liquidarPeriodo('2026-01', '2026-01-31', $this->user()->id);

        $this->expectException(ValidationException::class);
        $servicio->liquidarPeriodo('2026-01', '2026-01-31', $this->user()->id);
    }

    public function test_rechaza_liquidar_sin_empleados_activos()
    {
        $servicio = app(NominaService::class);

        $this->expectException(ValidationException::class);
        $servicio->liquidarPeriodo('2026-01', '2026-01-31', $this->user()->id);
    }

    public function test_anular_deja_sin_efecto_el_comprobante()
    {
        $this->sembrarParametros();
        $this->empleado(1300000);
        $servicio = app(NominaService::class);
        $liquidacion = $servicio->liquidarPeriodo('2026-01', '2026-01-31', $this->user()->id);

        $servicio->anular($liquidacion, 'Error en el cálculo', $this->user()->id);

        $liquidacion->refresh();
        $this->assertEquals('ANULADA', $liquidacion->estado);
        $this->assertEquals('ANULADO', $liquidacion->comprobante->fresh()->estado);

        // Debe poder volver a liquidarse el mismo período tras anular.
        $nueva = $servicio->liquidarPeriodo('2026-01', '2026-01-31', $this->user()->id);
        $this->assertEquals('REGISTRADA', $nueva->estado);
    }
}
