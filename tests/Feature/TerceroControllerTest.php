<?php

namespace Tests\Feature;

use App\Models\Roles;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Hallazgo #10 de la revisión de seguridad: el NIT se guardaba tal cual lo
 * escribiera el usuario (con puntos, guion, DV pegado). Factus exige solo
 * dígitos sin DV, y antes eso solo se limpiaba al enviar la factura, no al
 * guardar el tercero.
 */
class TerceroControllerTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $rol = Roles::firstOrCreate(['nombre' => 'Administrador'], ['descripcion' => 'Test']);

        return User::create([
            'username' => 'admin-tercero-test',
            'name' => 'Admin Test',
            'password' => bcrypt('secret'),
            'activo' => true,
            'rol_id' => $rol->id,
        ]);
    }

    public function test_el_nit_con_puntos_guion_y_dv_se_normaliza_a_solo_digitos()
    {
        $respuesta = $this->actingAs($this->admin())->postJson('/terceros', [
            'tipo' => 'empresa',
            'razon_social' => 'Cliente Real S.A.S',
            'nit' => '900.123.456-7',
            'celular' => '3000000000',
        ]);

        $respuesta->assertOk();
        $this->assertDatabaseHas('terceros', ['nit' => '900123456']);
        $this->assertDatabaseMissing('terceros', ['nit' => '900.123.456-7']);
    }

    public function test_un_nit_con_letras_es_rechazado()
    {
        $respuesta = $this->actingAs($this->admin())->postJson('/terceros', [
            'tipo' => 'empresa',
            'razon_social' => 'Cliente Inválido',
            'nit' => 'ABC123',
            'celular' => '3000000000',
        ]);

        $respuesta->assertStatus(422);
    }

    public function test_dos_nits_que_normalizan_igual_no_pueden_duplicarse()
    {
        $admin = $this->admin();

        $this->actingAs($admin)->postJson('/terceros', [
            'tipo' => 'empresa',
            'razon_social' => 'Cliente Uno',
            'nit' => '900123456-7',
            'celular' => '3000000000',
        ])->assertOk();

        $respuesta = $this->actingAs($admin)->postJson('/terceros', [
            'tipo' => 'empresa',
            'razon_social' => 'Cliente Dos',
            'nit' => '900.123.456',
            'celular' => '3000000001',
        ]);

        $respuesta->assertStatus(422);
    }
}
