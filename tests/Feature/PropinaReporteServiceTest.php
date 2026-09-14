<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\{Bodega, Caja, Factura, Mesa, Roles, User, Zona};
use App\Services\PropinaReporteService;

class PropinaReporteServiceTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $username, string $name): User
    {
        $r = Roles::firstOrCreate(['nombre' => 'Mesero'], ['descripcion' => 'Test']);
        return User::firstOrCreate(['username' => $username], ['name' => $name, 'password' => bcrypt('secret'), 'activo' => true, 'rol_id' => $r->id]);
    }

    private function factura(Caja $caja, User $user, string $numero, float $subtotal, float $propina, string $fecha, string $estado = 'pagada'): Factura
    {
        $zona = Zona::create(['nombre' => 'Zona']);
        $mesa = Mesa::create(['zona_id' => $zona->id, 'numero' => 'M-' . $numero, 'capacidad' => 4]);

        $factura = Factura::create([
            'numero_factura' => $numero,
            'mesa_id' => $mesa->id,
            'user_id' => $user->id,
            'caja_id' => $caja->id,
            'subtotal' => $subtotal,
            'total' => $subtotal + $propina,
            'propina' => $propina,
            'metodo_pago' => 'efectivo',
            'estado' => $estado,
        ]);
        $factura->created_at = $fecha;
        $factura->save();

        return $factura;
    }

    public function test_agrupa_propinas_por_vendedor_y_calcula_promedio()
    {
        $bodega = Bodega::create(['descripcion' => 'Restaurante']);
        $caja = Caja::create(['nombre' => 'Caja', 'prefijo' => 'FR', 'proximo_numero' => 1, 'bodega_id' => $bodega->id, 'activa' => true]);
        $ana = $this->user('ana.test', 'Ana');
        $luis = $this->user('luis.test', 'Luis');

        $this->factura($caja, $ana, 'FR-00001', 100000, 10000, '2026-08-05');
        $this->factura($caja, $ana, 'FR-00002', 50000, 0, '2026-08-06'); // sin propina
        $this->factura($caja, $luis, 'FR-00003', 200000, 30000, '2026-08-07');

        $reporte = app(PropinaReporteService::class)->porVendedor(['desde' => '2026-08-01', 'hasta' => '2026-08-31']);

        $this->assertEquals(2, $reporte['totales']['vendedores']);
        $this->assertEquals(40000, $reporte['totales']['total_propinas']);

        // Ordenado desc por total_propinas: Luis (30.000) primero.
        $this->assertEquals('Luis', $reporte['filas'][0]['vendedor']);
        $this->assertEquals(30000, $reporte['filas'][0]['total_propinas']);
        $this->assertEquals(1, $reporte['filas'][0]['facturas_con_propina']);

        // Trazabilidad: solo debe listar la(s) factura(s) que sí trajeron propina.
        $this->assertCount(1, $reporte['filas'][0]['facturas_detalle']);
        $this->assertEquals('FR-00003', $reporte['filas'][0]['facturas_detalle'][0]['numero_factura']);

        $ana_fila = $reporte['filas']->firstWhere('vendedor', 'Ana');
        $this->assertEquals(2, $ana_fila['facturas']);
        $this->assertEquals(1, $ana_fila['facturas_con_propina']);
        $this->assertEquals(10000, $ana_fila['total_propinas']);
        $this->assertEquals(10000, $ana_fila['propina_promedio']); // promedio solo entre las que sí tienen propina
    }

    public function test_excluye_facturas_anuladas_del_calculo_de_propinas()
    {
        $bodega = Bodega::create(['descripcion' => 'Restaurante']);
        $caja = Caja::create(['nombre' => 'Caja', 'prefijo' => 'FR', 'proximo_numero' => 1, 'bodega_id' => $bodega->id, 'activa' => true]);
        $ana = $this->user('ana.test', 'Ana');

        $this->factura($caja, $ana, 'FR-00001', 100000, 10000, '2026-08-05', 'anulada');

        $reporte = app(PropinaReporteService::class)->porVendedor(['desde' => '2026-08-01', 'hasta' => '2026-08-31']);

        $this->assertEquals(0, $reporte['totales']['facturas']);
    }
}
