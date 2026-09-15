<?php

namespace Tests\Feature;

use App\Models\Prefijo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrefijoTest extends TestCase
{
    use RefreshDatabase;

    public function test_siguiente_numero_es_secuencial_y_avanza_el_contador()
    {
        Prefijo::create(['codigo' => 'PX', 'nombre' => 'Prueba', 'proximo_numero' => 1]);

        $this->assertSame(1, Prefijo::siguienteNumero('PX'));
        $this->assertSame(2, Prefijo::siguienteNumero('PX'));
        $this->assertSame(3, Prefijo::siguienteNumero('PX'));
    }

    public function test_siguiente_numero_arranca_donde_quedo_el_backfill()
    {
        Prefijo::create(['codigo' => 'PY', 'nombre' => 'Prueba', 'proximo_numero' => 31]);

        $this->assertSame(31, Prefijo::siguienteNumero('PY'));
        $this->assertSame(32, Prefijo::siguienteNumero('PY'));
    }

    public function test_falla_si_el_prefijo_no_existe_en_el_catalogo()
    {
        $this->expectException(\RuntimeException::class);

        Prefijo::siguienteNumero('NOEXISTE');
    }

    public function test_falla_si_el_rango_autorizado_por_la_dian_ya_se_agoto()
    {
        Prefijo::create(['codigo' => 'PZ', 'nombre' => 'Prueba', 'proximo_numero' => 100, 'rango_desde' => 1, 'rango_hasta' => 99]);

        $this->expectException(\RuntimeException::class);

        Prefijo::siguienteNumero('PZ');
    }

    public function test_sin_rango_configurado_no_bloquea_nada()
    {
        Prefijo::create(['codigo' => 'PW', 'nombre' => 'Prueba', 'proximo_numero' => 999999]);

        $this->assertSame(999999, Prefijo::siguienteNumero('PW'));
    }
}
