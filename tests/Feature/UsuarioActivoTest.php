<?php

namespace Tests\Feature;

use App\Models\Roles;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UsuarioActivoTest extends TestCase
{
    use RefreshDatabase;

    private function admin(string $username = 'admin-test'): User
    {
        $rol = Roles::firstOrCreate(['nombre' => 'Administrador'], ['descripcion' => 'Test']);

        return User::create([
            'name' => 'Admin Test',
            'username' => $username,
            'password' => bcrypt('secret'),
            'rol_id' => $rol->id,
            'activo' => true,
        ]);
    }

    /**
     * El administrador puede "apagar" el acceso de un usuario (mesero que
     * hoy no trabaja, por ejemplo) sin borrar su cuenta ni su historial.
     */
    public function test_administrador_puede_desactivar_y_reactivar_a_otro_usuario(): void
    {
        $admin = $this->admin();
        $rolMesero = Roles::firstOrCreate(['nombre' => 'Mesero'], ['descripcion' => 'Test']);
        $mesero = User::create([
            'name' => 'Mesero Test',
            'username' => 'mesero-toggle-test',
            'password' => bcrypt('secret'),
            'rol_id' => $rolMesero->id,
            'activo' => true,
        ]);

        $this->actingAs($admin)
            ->patchJson("/usuarios/{$mesero->id}/toggle-activo")
            ->assertOk()
            ->assertJsonPath('activo', false);

        $this->assertFalse($mesero->fresh()->activo);

        $this->actingAs($admin)
            ->patchJson("/usuarios/{$mesero->id}/toggle-activo")
            ->assertOk()
            ->assertJsonPath('activo', true);

        $this->assertTrue($mesero->fresh()->activo);
    }

    public function test_administrador_no_puede_desactivar_su_propia_cuenta(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->patchJson("/usuarios/{$admin->id}/toggle-activo")
            ->assertStatus(422);

        $this->assertTrue($admin->fresh()->activo);
    }

    /**
     * Desactivar no solo debe bloquear FUTUROS logins: si el usuario ya
     * tenía una sesión abierta en otro dispositivo (el mesero que se fue a
     * la casa dejando la sesión abierta), esa sesión debe morir de una vez
     * — no seguir viva hasta que expire sola por inactividad.
     */
    public function test_desactivar_a_un_usuario_borra_sus_sesiones_activas(): void
    {
        config(['session.driver' => 'database']);

        $admin = $this->admin();
        $rolMesero = Roles::firstOrCreate(['nombre' => 'Mesero'], ['descripcion' => 'Test']);
        $mesero = User::create([
            'name' => 'Mesero Test',
            'username' => 'mesero-sesion-viva-test',
            'password' => bcrypt('secret'),
            'rol_id' => $rolMesero->id,
            'activo' => true,
        ]);

        DB::table('sessions')->insert([
            'id' => 'sesion-fake-del-mesero',
            'user_id' => $mesero->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
            'payload' => base64_encode(serialize([])),
            'last_activity' => now()->timestamp,
        ]);

        $this->actingAs($admin)
            ->patchJson("/usuarios/{$mesero->id}/toggle-activo")
            ->assertOk();

        $this->assertDatabaseMissing('sessions', ['user_id' => $mesero->id]);
    }

    /**
     * Reactivar a alguien no debe borrar sesiones de nadie más — solo se
     * purgan sesiones al DESACTIVAR.
     */
    public function test_reactivar_a_un_usuario_no_toca_sesiones(): void
    {
        config(['session.driver' => 'database']);

        $admin = $this->admin();
        $rolMesero = Roles::firstOrCreate(['nombre' => 'Mesero'], ['descripcion' => 'Test']);
        $mesero = User::create([
            'name' => 'Mesero Test',
            'username' => 'mesero-reactivar-test',
            'password' => bcrypt('secret'),
            'rol_id' => $rolMesero->id,
            'activo' => false,
        ]);

        DB::table('sessions')->insert([
            'id' => 'sesion-de-otro-usuario',
            'user_id' => $admin->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
            'payload' => base64_encode(serialize([])),
            'last_activity' => now()->timestamp,
        ]);

        $this->actingAs($admin)
            ->patchJson("/usuarios/{$mesero->id}/toggle-activo")
            ->assertOk();

        $this->assertDatabaseHas('sessions', ['user_id' => $admin->id]);
    }

    /**
     * Un usuario desactivado no puede iniciar sesión aunque la contraseña
     * sea correcta — es el mecanismo para revocar acceso de inmediato.
     */
    public function test_un_usuario_desactivado_no_puede_iniciar_sesion(): void
    {
        $rolMesero = Roles::firstOrCreate(['nombre' => 'Mesero'], ['descripcion' => 'Test']);
        $mesero = User::create([
            'name' => 'Mesero Test',
            'username' => 'mesero-desactivado-test',
            'password' => bcrypt('secret'),
            'rol_id' => $rolMesero->id,
            'activo' => false,
        ]);

        $respuesta = $this->post('/login', [
            'username' => 'mesero-desactivado-test',
            'password' => 'secret',
        ]);

        $respuesta->assertSessionHasErrors('username');
        $this->assertGuest();
    }
}
