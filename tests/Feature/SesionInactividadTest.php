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

    /**
     * La duración máxima es distinta a la inactividad: cierra la sesión
     * aunque el mesero siga activo (última_actividad reciente), porque
     * lleva demasiadas horas conectado — pensado para el fin de un turno,
     * ya que la pantalla de facturación se autorefresca sola y nunca
     * "parece" inactiva por sí misma.
     */
    public function test_una_sesion_de_mesero_se_cierra_al_superar_la_duracion_maxima_aunque_siga_activo(): void
    {
        config(['nexora.sesion_maxima_operativos_horas' => 8]);

        $rol = Roles::firstOrCreate(['nombre' => 'Mesero'], ['descripcion' => 'Test']);
        $mesero = User::create([
            'name' => 'Mesero Test',
            'username' => 'mesero-turno-largo-test',
            'password' => bcrypt('secret'),
            'rol_id' => $rol->id,
            'activo' => true,
        ]);

        $respuesta = $this->actingAs($mesero)
            ->withSession([
                'ultima_actividad' => now(),
                'inicio_sesion' => now()->subHours(9),
            ])
            ->get('/');

        $respuesta->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_una_sesion_dentro_de_la_duracion_maxima_no_se_cierra(): void
    {
        config(['nexora.sesion_maxima_operativos_horas' => 8]);

        $rol = Roles::firstOrCreate(['nombre' => 'Mesero'], ['descripcion' => 'Test']);
        $mesero = User::create([
            'name' => 'Mesero Test',
            'username' => 'mesero-turno-corto-test',
            'password' => bcrypt('secret'),
            'rol_id' => $rol->id,
            'activo' => true,
        ]);

        $respuesta = $this->actingAs($mesero)
            ->withSession([
                'ultima_actividad' => now(),
                'inicio_sesion' => now()->subHours(2),
            ])
            ->get('/');

        // Un Mesero en "/" redirige normal a facturacion.index — lo
        // relevante aquí es que NO lo mandó al login por sesión vencida.
        $respuesta->assertRedirect(route('facturacion.index'));
        $this->assertAuthenticated();
    }

    /**
     * 0 = sin límite — el valor por defecto para Administrador/Contabilidad,
     * que no trabajan por turnos fijos.
     */
    public function test_duracion_maxima_en_cero_significa_sin_limite(): void
    {
        config(['nexora.sesion_maxima_admin_horas' => 0]);

        $rol = Roles::firstOrCreate(['nombre' => 'Administrador'], ['descripcion' => 'Test']);
        $admin = User::create([
            'name' => 'Admin Test',
            'username' => 'admin-sin-limite-test',
            'password' => bcrypt('secret'),
            'rol_id' => $rol->id,
            'activo' => true,
        ]);

        $respuesta = $this->actingAs($admin)
            ->withSession([
                'ultima_actividad' => now(),
                'inicio_sesion' => now()->subHours(100),
            ])
            ->get('/');

        $respuesta->assertOk();
        $this->assertAuthenticated();
    }

    /**
     * El "reloj" de duración máxima arranca solo — no hace falta que
     * AuthController lo prepare al hacer login, el propio middleware lo
     * guarda en la primera petición de la sesión.
     */
    public function test_se_registra_el_inicio_de_sesion_en_la_primera_peticion(): void
    {
        $rol = Roles::firstOrCreate(['nombre' => 'Administrador'], ['descripcion' => 'Test']);
        $admin = User::create([
            'name' => 'Admin Test',
            'username' => 'admin-primera-peticion-test',
            'password' => bcrypt('secret'),
            'rol_id' => $rol->id,
            'activo' => true,
        ]);

        $this->actingAs($admin)->get('/')->assertOk();

        $this->assertNotNull(session('inicio_sesion'));
    }
}
