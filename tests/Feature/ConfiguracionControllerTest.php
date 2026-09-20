<?php

namespace Tests\Feature;

use App\Models\ConfiguracionSistema;
use App\Models\Roles;
use App\Models\User;
use App\Providers\AppServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConfiguracionControllerTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $rol = Roles::firstOrCreate(['nombre' => 'Administrador'], ['descripcion' => 'Test']);

        return User::create([
            'name' => 'Admin Test',
            'username' => 'admin-config-test',
            'password' => bcrypt('secret'),
            'rol_id' => $rol->id,
            'activo' => true,
        ]);
    }

    public function test_sin_nada_guardado_muestra_los_valores_por_defecto_del_env(): void
    {
        // El .env local de este equipo SÍ trae credenciales reales de Factus
        // (sandbox) y un token de agente — se limpian aquí para probar el
        // caso "nada configurado todavía" sin depender de qué haya en el
        // .env de quien corra los tests.
        config([
            'services.factura_electronica.habilitada' => false,
            'services.factus.client_secret' => null,
            'app.agente_impresion_token' => null,
        ]);

        $respuesta = $this->actingAs($this->admin())
            ->getJson('/configuracion/datos')
            ->assertOk();

        $respuesta->assertJsonPath('data.hora_corte_operativo', '00:00');
        $respuesta->assertJsonPath('data.sesion_maxima_operativos_horas', 8);
        $respuesta->assertJsonPath('data.sesion_maxima_admin_horas', 0);
        $respuesta->assertJsonPath('data.factura_electronica_habilitada', false);
        $respuesta->assertJsonPath('data.factus_client_secret_configurado', false);
        $respuesta->assertJsonPath('data.agente_impresion_token_configurado', false);
    }

    /**
     * config('app.agente_impresion_token') no existe en config/app.php, así
     * que sin un valor de reserva SIEMPRE da null — hay que leerlo con el
     * mismo fallback a env() que usa AutenticarAgenteImpresion, si no la
     * pantalla dice "sin generar" aunque el .env sí tenga uno puesto.
     */
    public function test_ve_configurado_el_token_del_agente_aunque_solo_este_en_el_env(): void
    {
        config(['app.agente_impresion_token' => 'valor-que-vino-del-env']);

        $this->actingAs($this->admin())
            ->getJson('/configuracion/datos')
            ->assertJsonPath('data.agente_impresion_token_configurado', true);
    }

    public function test_administrador_puede_guardar_la_configuracion_general(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->putJson('/configuracion', [
                'hora_corte_operativo' => '05:30',
                'inactividad_operativos_minutos' => 20,
                'inactividad_admin_minutos' => 90,
                'sesion_maxima_operativos_horas' => 8,
                'sesion_maxima_admin_horas' => 0,
                'factura_electronica_habilitada' => '1',
                'factus_url' => 'https://api.factus.com.co',
                'factus_client_id' => 'mi-client-id',
                'factus_username' => 'facturacion@negocio.com',
            ])
            ->assertOk();

        $this->assertSame('05:30', ConfiguracionSistema::where('clave', 'hora_corte_operativo')->value('valor'));
        $this->assertSame('20', ConfiguracionSistema::where('clave', 'inactividad_operativos_minutos')->value('valor'));
        $this->assertSame('1', ConfiguracionSistema::where('clave', 'factura_electronica_habilitada')->value('valor'));
        $this->assertSame('mi-client-id', ConfiguracionSistema::where('clave', 'factus_client_id')->value('valor'));

        $this->actingAs($admin)
            ->getJson('/configuracion/datos')
            ->assertJsonPath('data.hora_corte_operativo', '05:30')
            ->assertJsonPath('data.inactividad_operativos_minutos', 20)
            ->assertJsonPath('data.factura_electronica_habilitada', true);
    }

    /**
     * Dejar un campo de contraseña/secreto vacío en el formulario debe
     * CONSERVAR lo que ya había — igual que la clave de un usuario en
     * Cuentas y roles. Si no fuera así, guardar cualquier otro cambio de
     * esta pantalla borraría sin querer las credenciales de Factus.
     */
    public function test_dejar_vacio_un_campo_secreto_conserva_el_valor_guardado(): void
    {
        $admin = $this->admin();
        ConfiguracionSistema::create(['clave' => 'factus_client_secret', 'valor' => 'secreto-original']);

        $this->actingAs($admin)
            ->putJson('/configuracion', [
                'hora_corte_operativo' => '04:00',
                'inactividad_operativos_minutos' => 15,
                'inactividad_admin_minutos' => 60,
                'sesion_maxima_operativos_horas' => 8,
                'sesion_maxima_admin_horas' => 0,
                'factura_electronica_habilitada' => '0',
                'factus_client_secret' => '',
            ])
            ->assertOk();

        $this->assertSame('secreto-original', ConfiguracionSistema::where('clave', 'factus_client_secret')->value('valor'));
    }

    public function test_enviar_un_valor_nuevo_en_un_campo_secreto_si_lo_actualiza(): void
    {
        $admin = $this->admin();
        ConfiguracionSistema::create(['clave' => 'factus_client_secret', 'valor' => 'secreto-viejo']);

        $this->actingAs($admin)
            ->putJson('/configuracion', [
                'hora_corte_operativo' => '04:00',
                'inactividad_operativos_minutos' => 15,
                'inactividad_admin_minutos' => 60,
                'sesion_maxima_operativos_horas' => 8,
                'sesion_maxima_admin_horas' => 0,
                'factura_electronica_habilitada' => '0',
                'factus_client_secret' => 'secreto-nuevo',
            ])
            ->assertOk();

        $this->assertSame('secreto-nuevo', ConfiguracionSistema::where('clave', 'factus_client_secret')->value('valor'));
    }

    /**
     * El endpoint de "ver configuración" nunca debe filtrar el secreto en
     * texto plano — solo si está configurado o no.
     */
    public function test_los_campos_secretos_nunca_se_devuelven_en_texto_plano(): void
    {
        ConfiguracionSistema::create(['clave' => 'factus_client_secret', 'valor' => 'no-deberia-verse']);
        ConfiguracionSistema::create(['clave' => 'agente_impresion_token', 'valor' => 'tampoco-este']);

        $respuesta = $this->actingAs($this->admin())
            ->getJson('/configuracion/datos')
            ->assertOk();

        $this->assertStringNotContainsString('no-deberia-verse', $respuesta->getContent());
        $this->assertStringNotContainsString('tampoco-este', $respuesta->getContent());
        $respuesta->assertJsonPath('data.factus_client_secret_configurado', true);
        $respuesta->assertJsonPath('data.agente_impresion_token_configurado', true);
    }

    public function test_regenerar_token_del_agente_crea_uno_nuevo_y_lo_devuelve_una_sola_vez(): void
    {
        $admin = $this->admin();
        ConfiguracionSistema::create(['clave' => 'agente_impresion_token', 'valor' => 'token-antiguo']);

        $respuesta = $this->actingAs($admin)
            ->postJson('/configuracion/regenerar-token-agente')
            ->assertOk();

        $tokenNuevo = $respuesta->json('token');
        $this->assertNotEmpty($tokenNuevo);
        $this->assertNotSame('token-antiguo', $tokenNuevo);
        $this->assertSame($tokenNuevo, ConfiguracionSistema::where('clave', 'agente_impresion_token')->value('valor'));
    }

    public function test_administrador_puede_descargar_el_agente_de_impresion(): void
    {
        $respuesta = $this->actingAs($this->admin())
            ->get('/configuracion/descargar-agente');

        $respuesta->assertOk();
        $respuesta->assertHeader('content-type', 'application/zip');
        $this->assertStringContainsString('agente-impresion.zip', $respuesta->headers->get('content-disposition'));
    }

    /**
     * El .zip nunca debe traer node_modules (pesado, se regenera con
     * npm install) ni un .env real con el token de producción — cada
     * negocio pone el suyo siguiendo el LEEME.txt.
     */
    public function test_el_paquete_del_agente_no_trae_node_modules_ni_env_real(): void
    {
        $this->actingAs($this->admin())->get('/configuracion/descargar-agente');

        $zip = new \ZipArchive();
        $zip->open(storage_path('app/agente-impresion.zip'));

        $nombres = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $nombres[] = $zip->getNameIndex($i);
        }
        $zip->close();

        $this->assertContains('agente-impresion/index.js', $nombres);
        $this->assertContains('agente-impresion/.env.example', $nombres);
        $this->assertContains('agente-impresion/LEEME.txt', $nombres);
        $this->assertNotContains('agente-impresion/.env', $nombres);

        foreach ($nombres as $nombre) {
            $this->assertStringNotContainsString('node_modules', $nombre);
        }
    }

    public function test_un_mesero_no_puede_entrar_a_configuracion(): void
    {
        $rolMesero = Roles::firstOrCreate(['nombre' => 'Mesero'], ['descripcion' => 'Test']);
        $mesero = User::create([
            'name' => 'Mesero Test',
            'username' => 'mesero-config-test',
            'password' => bcrypt('secret'),
            'rol_id' => $rolMesero->id,
            'activo' => true,
        ]);

        $this->actingAs($mesero)
            ->getJson('/configuracion/datos')
            ->assertForbidden();
    }

    /**
     * Esto es lo que de verdad hace útil la pantalla: lo que se guarda en
     * BD debe sobrescribir config() en el arranque de la app — ver
     * AppServiceProvider::aplicarConfiguracionGuardada(). Sin esto, la
     * pantalla solo estaría "decorando" valores que el resto del código
     * (el middleware de inactividad, el proveedor de Factus, el agente de
     * impresión) nunca llegaría a leer.
     */
    public function test_los_valores_guardados_sobrescriben_el_config_de_arranque(): void
    {
        ConfiguracionSistema::create(['clave' => 'inactividad_operativos_minutos', 'valor' => '7']);
        ConfiguracionSistema::create(['clave' => 'factura_electronica_habilitada', 'valor' => '1']);
        ConfiguracionSistema::create(['clave' => 'agente_impresion_token', 'valor' => 'token-de-prueba-123']);

        (new AppServiceProvider($this->app))->boot();

        $this->assertSame(7, config('nexora.inactividad_operativos_minutos'));
        $this->assertTrue(config('services.factura_electronica.habilitada'));
        $this->assertSame('token-de-prueba-123', config('app.agente_impresion_token'));
    }

    public function test_administrador_puede_guardar_la_duracion_maxima_de_sesion(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->putJson('/configuracion', [
                'hora_corte_operativo' => '04:00',
                'inactividad_operativos_minutos' => 15,
                'inactividad_admin_minutos' => 60,
                'sesion_maxima_operativos_horas' => 8,
                'sesion_maxima_admin_horas' => 12,
                'factura_electronica_habilitada' => '0',
            ])
            ->assertOk();

        $this->assertSame('8', ConfiguracionSistema::where('clave', 'sesion_maxima_operativos_horas')->value('valor'));
        $this->assertSame('12', ConfiguracionSistema::where('clave', 'sesion_maxima_admin_horas')->value('valor'));

        $this->actingAs($admin)
            ->getJson('/configuracion/datos')
            ->assertJsonPath('data.sesion_maxima_operativos_horas', 8)
            ->assertJsonPath('data.sesion_maxima_admin_horas', 12);
    }

    /**
     * Igual que con inactividad: lo guardado aquí debe llegar hasta el
     * middleware real que cierra la sesión — ver
     * CerrarSesionPorInactividad y SesionInactividadTest.
     */
    public function test_la_duracion_maxima_guardada_sobrescribe_el_config_de_arranque(): void
    {
        ConfiguracionSistema::create(['clave' => 'sesion_maxima_operativos_horas', 'valor' => '6']);

        (new AppServiceProvider($this->app))->boot();

        $this->assertSame(6.0, config('nexora.sesion_maxima_operativos_horas'));
    }
}
