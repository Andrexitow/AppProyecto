<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\TipoDocumentoSeeder;
use App\Models\{Bodega, Documento, Producto, Roles, TipoDocumento, User};
use App\Services\{KardexReporteService, KardexService};

class KardexReporteServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TipoDocumentoSeeder::class);
    }

    private function user(): User
    {
        $r = Roles::firstOrCreate(['nombre' => 'Administrador'], ['descripcion' => 'Test']);
        return User::firstOrCreate(['username' => 'kardex-rep-test'], ['name' => 'Kardex Rep Test', 'password' => bcrypt('secret'), 'activo' => true, 'rol_id' => $r->id]);
    }

    private function producto(string $codigo = 'P1'): Producto
    {
        return Producto::create(['codigo' => $codigo, 'descripcion' => $codigo, 'und_detal' => 'UND']);
    }

    private function bodega(string $descripcion): Bodega
    {
        return Bodega::create(['descripcion' => $descripcion]);
    }

    private function documentoDummy(string $numero): int
    {
        $tipo = TipoDocumento::where('codigo', 'AJUSTE')->firstOrFail();
        return Documento::create([
            'tipo_documento_id' => $tipo->id,
            'prefijo' => 'FR',
            'numero' => $numero,
            'fecha' => now()->toDateString(),
            'user_id' => $this->user()->id,
            'estado' => 'registrado',
        ])->id;
    }

    /**
     * documentos.numero ya trae el prefijo incluido (ej. "FR-00003"); el
     * servicio no debe exponer 'prefijo' por separado para que la vista no
     * vuelva a concatenarlo y produzca un número duplicado ("FRFR-00003").
     */
    public function test_no_expone_prefijo_por_separado_para_evitar_duplicarlo_en_pantalla()
    {
        $producto = $this->producto();
        $bodega = $this->bodega('Principal');
        $documentoId = $this->documentoDummy('FR-00003');

        app(KardexService::class)->registrar($documentoId, null, $producto->id, $bodega->id, 'ENTRADA', 10, 0, 10, 100, $this->user()->id);

        $reporte = app(KardexReporteService::class)->movimientos($producto->id, $bodega->id, now()->subDay()->toDateString(), now()->addDay()->toDateString());

        $fila = $reporte['movimientos']->first();
        $this->assertEquals('FR-00003', $fila->numero);
        $this->assertFalse(property_exists($fila, 'prefijo'), 'La fila no debe traer prefijo por separado (ya viene incluido en numero).');
    }

    public function test_bodega_id_null_consolida_todas_las_bodegas_con_promedio_ponderado()
    {
        $producto = $this->producto();
        $principal = $this->bodega('Principal');
        $sucursal = $this->bodega('Sucursal');
        $kardex = app(KardexService::class);
        $doc = $this->documentoDummy('AJ-0001');

        // Principal: 10 unidades a $100 = $1.000
        $kardex->registrar($doc, null, $producto->id, $principal->id, 'ENTRADA', 10, 0, 10, 100, $this->user()->id);
        // Sucursal: 20 unidades a $50 = $1.000
        $kardex->registrar($doc, null, $producto->id, $sucursal->id, 'ENTRADA', 20, 0, 20, 50, $this->user()->id);

        $reporte = app(KardexReporteService::class)->movimientos($producto->id, null, now()->subDay()->toDateString(), now()->addDay()->toDateString());

        $this->assertNull($reporte['bodega']);
        // 30 unidades totales, valor total $2.000 -> promedio ponderado real = 2000/30 = 66.6667
        $this->assertEquals(30, $reporte['saldo_final']['cantidad']);
        $this->assertEquals(2000, $reporte['saldo_final']['valor']);
        $this->assertEqualsWithDelta(66.6667, $reporte['saldo_final']['costo_promedio'], 0.001);

        // Cada línea del detalle conserva su propia bodega, sin mezclarlas.
        $this->assertCount(2, $reporte['movimientos']);
        $bodegasEnDetalle = $reporte['movimientos']->pluck('bodega_nombre')->sort()->values()->all();
        $this->assertEquals(['Principal', 'Sucursal'], $bodegasEnDetalle);
    }

    public function test_bodega_especifica_sigue_funcionando_igual_que_antes()
    {
        $producto = $this->producto();
        $bodega = $this->bodega('Principal');
        $otra = $this->bodega('Otra');
        $kardex = app(KardexService::class);
        $doc = $this->documentoDummy('AJ-0002');

        $kardex->registrar($doc, null, $producto->id, $bodega->id, 'ENTRADA', 10, 0, 10, 100, $this->user()->id);
        // Movimiento en otra bodega: no debe aparecer al consultar una bodega específica.
        $kardex->registrar($doc, null, $producto->id, $otra->id, 'ENTRADA', 999, 0, 999, 1, $this->user()->id);

        $reporte = app(KardexReporteService::class)->movimientos($producto->id, $bodega->id, now()->subDay()->toDateString(), now()->addDay()->toDateString());

        $this->assertEquals($bodega->id, $reporte['bodega']->id);
        $this->assertCount(1, $reporte['movimientos']);
        $this->assertEquals(10, $reporte['saldo_final']['cantidad']);
    }
}
