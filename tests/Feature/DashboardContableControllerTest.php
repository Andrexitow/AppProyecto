<?php

namespace Tests\Feature;

use App\Models\Roles;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardContableControllerTest extends TestCase
{
    use RefreshDatabase;

    private function usuario(string $rolNombre, string $username): User
    {
        $rol = Roles::firstOrCreate(['nombre' => $rolNombre], ['descripcion' => 'Test']);

        return User::create([
            'name' => ucfirst(strtolower($rolNombre)) . ' Test',
            'username' => $username,
            'password' => bcrypt('secret'),
            'rol_id' => $rol->id,
            'activo' => true,
        ]);
    }

    /**
     * Antes de este cambio, Contabilidad caía en el mismo panel de
     * ventas/cocina de Administrador — que no le sirve de nada a quien
     * lleva la parte contable del negocio.
     */
    public function test_un_usuario_de_contabilidad_que_entra_a_home_ve_el_panel_contable(): void
    {
        $contadora = $this->usuario('Contabilidad', 'contadora-home-test');

        $this->actingAs($contadora)
            ->get('/pos')
            ->assertOk()
            ->assertSee('Centro de control contable')
            ->assertSee('Cartera por cobrar')
            ->assertSee('Cuentas por pagar')
            ->assertSee('IVA del período');
    }

    public function test_un_administrador_sigue_viendo_el_panel_de_ventas_no_el_contable(): void
    {
        $admin = $this->usuario('Administrador', 'admin-home-test');

        $this->actingAs($admin)
            ->get('/pos')
            ->assertOk()
            ->assertSee('Todo el negocio en una sola mirada')
            ->assertDontSee('Centro de control contable');
    }

    public function test_el_resumen_contable_trae_todas_las_secciones_esperadas(): void
    {
        $contadora = $this->usuario('Contabilidad', 'contadora-resumen-test');

        $this->actingAs($contadora)
            ->getJson('/dashboard-contable/resumen')
            ->assertOk()
            ->assertJsonStructure([
                'cartera' => ['total', 'facturas_pendientes', 'vencida_30_dias'],
                'por_pagar' => ['total', 'compras_pendientes', 'vencida_90_dias'],
                'tesoreria' => ['saldo_total'],
                'periodo' => ['nombre', 'cerrado'],
                'comprobantes' => ['registrados_hoy', 'borrador', 'ultimos'],
                'iva' => ['ivaGenerado', 'ivaDescontable', 'neto', 'aPagar', 'saldoAFavor', 'valorAbsoluto'],
            ]);
    }

    /**
     * Con la base de datos vacía (negocio recién instalado, sin facturas de
     * crédito ni compras ni comprobantes) el panel no debe tronar — todo
     * en cero, nada de errores por división/null.
     */
    public function test_el_resumen_contable_no_falla_con_la_base_de_datos_vacia(): void
    {
        $contadora = $this->usuario('Contabilidad', 'contadora-vacio-test');

        $respuesta = $this->actingAs($contadora)
            ->getJson('/dashboard-contable/resumen')
            ->assertOk();

        $respuesta->assertJsonPath('cartera.total', 0);
        $respuesta->assertJsonPath('por_pagar.total', 0);
        $respuesta->assertJsonPath('tesoreria.saldo_total', 0);
        $respuesta->assertJsonPath('comprobantes.ultimos', []);
    }

    public function test_un_mesero_no_puede_ver_el_resumen_contable(): void
    {
        $mesero = $this->usuario('Mesero', 'mesero-resumen-contable-test');

        $this->actingAs($mesero)
            ->getJson('/dashboard-contable/resumen')
            ->assertForbidden();
    }
}
