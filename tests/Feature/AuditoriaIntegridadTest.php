<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\{Bodega, Compra, Documento, Inventario, MovimientoInventario, Producto, Roles, Tercero, User};

class AuditoriaIntegridadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        foreach (['TipoDocumentoSeeder','TipoDocumentoContableSeeder','PucSeeder','ConfiguracionContableSeeder','ParametrizacionInicialContableSeeder','ProcesoContableSeeder','MetodoPagoContableSeeder','CuentaTesoreriaSeeder'] as $s) {
            $this->seed('Database\\Seeders\\'.$s);
        }
        $r=Roles::create(['nombre'=>'Administrador','descripcion'=>'Auditoría']);
        $this->actingAs(User::create(['username'=>'audit','name'=>'Audit','password'=>bcrypt('secret'),'activo'=>true,'rol_id'=>$r->id]));
    }

    private function purchase(bool $duplicate=false): Compra
    {
        $b=Bodega::create(['descripcion'=>'Audit']);
        $p=Producto::create(['codigo'=>'AUD','descripcion'=>'Audit','und_detal'=>'UND']);
        $t=Tercero::create(['tipo'=>'empresa','razon_social'=>'Audit']);
        $line=['producto_id'=>$p->id,'bodega_id'=>$b->id,'cantidad'=>10,'costo_unitario'=>1000];
        $this->postJson('/compras',['prefijo'=>'AU','consecutivo'=>1,'numero_factura'=>'AU1','proveedor_id'=>$t->id,'fecha'=>now()->toDateString(),'confirmar'=>true,'items'=>$duplicate?[$line,$line]:[$line],'pagos'=>[['metodo_pago'=>'credito','valor'=>$duplicate?20000:10000]]])->assertCreated();
        return Compra::latest('id')->firstOrFail();
    }

    public function test_compra_anulada_concilia_kardex_y_documento(): void
    {
        $c=$this->purchase();
        $this->postJson("/compras/$c->id/anular")->assertOk();
        $this->assertEquals(0, Inventario::sum('stock'));
        $net=MovimientoInventario::selectRaw("SUM(CASE WHEN tipo='ENTRADA' THEN cantidad ELSE -cantidad END) AS net")->value('net');
        $this->assertEquals(0,$net,'La anulación debe revertir también el Kardex');
        $this->assertEquals('anulado',Documento::find($c->documento_id)->estado);
        $this->postJson("/compras/$c->id/revertir")->assertOk();
        $this->assertEquals(10,Inventario::sum('stock'));
        $this->assertEquals(10,MovimientoInventario::selectRaw("SUM(CASE WHEN tipo='ENTRADA' THEN cantidad ELSE -cantidad END) AS net")->value('net'));
    }

    public function test_compra_lineas_repetidas_encadena_saldos_kardex(): void
    {
        $this->purchase(true);
        $m=MovimientoInventario::orderBy('id')->get();
        $this->assertEquals(0,$m[0]->stock_anterior);
        $this->assertEquals(10,$m[0]->stock_nuevo);
        $this->assertEquals(10,$m[1]->stock_anterior);
        $this->assertEquals(20,$m[1]->stock_nuevo);
    }
}
