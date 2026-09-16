<?php
namespace Tests\Feature;
use Tests\TestCase;
class AssetVHostingerTest extends TestCase
{
    public function test_login_renderiza_si_el_logo_no_esta_en_public_path(): void
    {
        $this->app->usePublicPath(sys_get_temp_dir().'/nexora-logo-ausente-'.uniqid());
        $this->get('/login')->assertOk()->assertSee('imgs/nexora-logo.png', false);
    }
}
