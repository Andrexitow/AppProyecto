<?php

namespace Tests\Feature;

use App\Models\Roles;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SesionInactividadTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Un mesero que deja la sesión abierta y no hace nada por más del
     * límite configurado debe quedar deslogueado en la siguiente petición
     * — la mitigación contra "el mesero se fue a la casa y sigue
     * comandando" cuando la app ya es accesible desde internet.
     */
    public function test_una_sesion_de_mesero_se_cierra_tras_superar_el_limite_de_inactividad(): void
    {
        config(['nexora.inactividad_operativos_minutos' => 15]);

        $rol = Roles::firstOrCreate(['nombre' => 'Mesero'], ['descripcion' => 'Test']);
        $mesero = User::create([
            'name' => 'Mesero Test',
            'username' => 'mesero-inactivo-test',
            'password' => bcrypt('secret'),
            'rol_id' => $rol->id,
            'activo' => true,
        ]);

        $respuesta = $this->actingAs($mesero)
            ->withSession(['ultima_actividad' => now()->subMinutes(20)])
            ->get('/');

        $respuesta->assertRedirect(route('login'));
        $this->assertGuest();
    }

    /**
     * Dentro del límite, la sesión sigue viva con normalidad.
     */
    public function test_una_sesion_dentro_del_limite_de_inactividad_no_se_cierra(): void
    {
        config(['nexora.inactividad_admin_minutos' => 60]);

        $rol = Roles::firstOrCreate(['nombre' => 'Administrador'], ['descripcion' => 'Test']);
        $admin = User::create([
            'name' => 'Admin Test',
            'username' => 'admin-activo-test',
            'password' => bcrypt('secret'),
            'rol_id' => $rol->id,
            'activo' => true,
        ]);

        $respuesta = $this->actingAs($admin)
            ->withSession(['ultima_actividad' => now()->subMinutes(5)])
            ->get('/');

        $respuesta->assertOk();
        $this->assertAuthenticated();
    }

    /**
     * Administrador/Contabilidad tienen un límite más largo que los roles
     * operativos: a los 20 minutos un mesero ya se desloguea, pero un
     * administrador en el mismo escenario sigue con la sesión activa.
     */
    public function test_administrador_tiene_un_limite_de_inactividad_mas_largo_que_mesero(): void
    {
        config(['nexora.inactividad_operativos_minutos' => 15]);
        config(['nexora.inactividad_admin_minutos' => 60]);

        $rol = Roles::firstOrCreate(['nombre' => 'Administrador'], ['descripcion' => 'Test']);
        $admin = User::create([
            'name' => 'Admin Test',
            'username' => 'admin-largo-test',
            'password' => bcrypt('secret'),
            'rol_id' => $rol->id,
            'activo' => true,
        ]);

        $respuesta = $this->actingAs($admin)
            ->withSession(['ultima_actividad' => now()->subMinutes(20)])
            ->get('/');

        $respuesta->assertOk();
        $this->assertAuthenticated();
    }
}
