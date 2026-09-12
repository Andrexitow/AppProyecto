<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\{TipoDocumentoSeeder, TipoDocumentoContableSeeder, ProcesoContableSeeder, ConfiguracionContableSeeder, PucSeeder, ParametrizacionInicialContableSeeder, MetodoPagoContableSeeder};
use App\Models\{Bodega, Compra, ComprobanteContable, PagoProveedorAplicacion, Producto, Roles, Tercero, User};

class CompraContableServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TipoDocumentoSeeder::class);
        $this->seed(TipoDocumentoContableSeeder::class);
        $this->seed(PucSeeder::class);
        $this->seed(ConfiguracionContableSeeder::class);
        $this->seed(ParametrizacionInicialContableSeeder::class);
        $this->seed(ProcesoContableSeeder::class);
        $this->seed(MetodoPagoContableSeeder::class);
    }

    private function user(): User
    {
        $r = Roles::firstOrCreate(['nombre' => 'Administrador'], ['descripcion' => 'Test']);
        return User::firstOrCreate(['username' => 'compra-test'], ['name' => 'Compra Test', 'password' => bcrypt('secret'), 'activo' => true, 'rol_id' => $r->id]);
    }

    /** Crea y confirma una compra de contado por HTTP (el flujo real), devuelve el modelo fresco. */
    private function compraConfirmada(): Compra
    {
        $proveedor = Tercero::create(['tipo' => 'empresa', 'razon_social' => 'Proveedor Test']);
        $bodega = Bodega::create(['descripcion' => 'Bodega Test']);
        $producto = Producto::create(['codigo' => 'PC1', 'descripcion' => 'Producto Compra', 'und_detal' => 'UND']);

        $respuesta = $this->actingAs($this->user())->postJson('/compras', [
            'prefijo' => 'FC', 'consecutivo' => 1, 'numero_factura' => 'F-0001',
            'proveedor_id' => $proveedor->id, 'fecha' => now()->toDateString(), 'confirmar' => true,
            'items' => [[
                'producto_id' => $producto->id, 'bodega_id' => $bodega->id,
                'cantidad' => 10, 'costo_unitario' => 1000,
            ]],
            'pagos' => [['metodo_pago' => 'efectivo', 'valor' => 10000]],
        ]);

        $respuesta->assertStatus(201);

        return Compra::latest('id')->firstOrFail();
    }

    public function test_compra_de_contado_contabiliza_balanceado()
    {
        $compra = $this->compraConfirmada();

        $comprobante = ComprobanteContable::where('documento_origen', 'COMPRA')->where('documento_origen_id', $compra->id)->where('estado', 'CONTABILIZADO')->firstOrFail();
        $movs = $comprobante->movimientos;
        $this->assertEquals((float) $movs->sum('debito'), (float) $movs->sum('credito'));
        $this->assertEquals(10000, (float) $movs->sum('debito'));
    }

    /** El bug real: anular() descontaba inventario pero dejaba comprobante y pagos activos. */
    public function test_anular_compra_revierte_comprobante_y_pagos_iniciales()
    {
        $compra = $this->compraConfirmada();

        $respuesta = $this->actingAs($this->user())->postJson("/compras/{$compra->id}/anular");
        $respuesta->assertOk();

        $compra->refresh();
        $this->assertEquals('anulada', $compra->estado);

        $comprobante = ComprobanteContable::where('documento_origen', 'COMPRA')->where('documento_origen_id', $compra->id)->firstOrFail();
        $this->assertEquals('ANULADO', $comprobante->estado);

        $pagosActivos = PagoProveedorAplicacion::whereHas('pago', fn ($q) => $q->where('estado', 'registrado'))
            ->where('compra_id', $compra->id)->count();
        $this->assertEquals(0, $pagosActivos, 'Los pagos iniciales deberían quedar anulados junto con la compra.');

        $this->assertEquals(0, (float) $compra->saldo_pendiente);
        $this->assertEquals('pendiente', $compra->estado_pago);
    }

    /**
     * Encontrado probando el sistema de punta a punta: el selector de "medio de
     * pago" del abono a proveedor traía "credito" como opción (y hasta
     * preseleccionada) — igual que el bug ya corregido del lado de Cuentas por
     * Cobrar. Sin este chequeo, un abono "a crédito" terminaba acreditando
     * CUENTA_CLIENTES (la cartera de clientes, sin relación con el proveedor)
     * en vez de Caja/Banco.
     */
    public function test_rechaza_abono_a_proveedor_pagado_con_metodo_credito()
    {
        $proveedor = Tercero::create(['tipo' => 'empresa', 'razon_social' => 'Proveedor Credito']);
        $bodega = Bodega::create(['descripcion' => 'Bodega Credito']);
        $producto = Producto::create(['codigo' => 'PCX', 'descripcion' => 'Producto Credito', 'und_detal' => 'UND']);

        $this->actingAs($this->user())->postJson('/compras', [
            'prefijo' => 'FC', 'consecutivo' => 88, 'numero_factura' => 'F-0088',
            'proveedor_id' => $proveedor->id, 'fecha' => now()->toDateString(), 'confirmar' => true,
            'items' => [['producto_id' => $producto->id, 'bodega_id' => $bodega->id, 'cantidad' => 1, 'costo_unitario' => 100000]],
            'pagos' => [['metodo_pago' => 'credito', 'valor' => 100000]],
        ])->assertStatus(201);

        $compra = Compra::latest('id')->firstOrFail();
        $this->assertEquals(100000, (float) $compra->saldo_pendiente);

        $metodoCredito = \App\Models\MetodoPagoContable::where('metodo_pago', 'credito')->firstOrFail();

        $respuesta = $this->actingAs($this->user())->postJson("/compras/{$compra->id}/pagos", [
            'fecha' => now()->toDateString(), 'valor' => 50000, 'metodo_pago_contable_id' => $metodoCredito->id,
        ]);
        $respuesta->assertStatus(422);
        $this->assertEquals(100000, (float) $compra->fresh()->saldo_pendiente, 'El saldo no debe moverse si el abono se rechaza.');
    }

    public function test_rechaza_una_segunda_compra_con_el_mismo_prefijo_y_consecutivo()
    {
        $proveedor = Tercero::create(['tipo' => 'empresa', 'razon_social' => 'Proveedor Test 2']);
        $bodega = Bodega::create(['descripcion' => 'Bodega Test 2']);
        $producto = Producto::create(['codigo' => 'PC2', 'descripcion' => 'Producto Compra 2', 'und_detal' => 'UND']);
        $usuario = $this->user();

        $payload = [
            'prefijo' => 'FC', 'consecutivo' => 77, 'numero_factura' => 'F-0077',
            'proveedor_id' => $proveedor->id, 'fecha' => now()->toDateString(), 'confirmar' => false,
            'items' => [['producto_id' => $producto->id, 'bodega_id' => $bodega->id, 'cantidad' => 1, 'costo_unitario' => 100]],
        ];

        $this->actingAs($usuario)->postJson('/compras', $payload)->assertStatus(201);

        // Misma combinación prefijo+consecutivo: el lockForUpdate()->exists() (o,
        // como respaldo, el unique de la tabla) debe rechazarla con un 422 claro,
        // no con un 500 ni con una segunda compra duplicada.
        $respuesta = $this->actingAs($usuario)->postJson('/compras', array_merge($payload, ['numero_factura' => 'F-0078']));
        $respuesta->assertStatus(422);
        $this->assertEquals(1, Compra::where('prefijo', 'FC')->where('consecutivo', 77)->count());
    }

    /**
     * Verificación empírica de que las retenciones NO inflan el inventario ni
     * sobrecargan a Proveedores (una revisión externa lo señaló como bug crítico
     * dos veces; se confirmó con álgebra y con esta misma ejecución en tinker
     * contra código real antes de escribir el test). compra->total ya sale NETO
     * de retención desde detalles(), así que Db Inventario = base gravable pura
     * y Cr Proveedores = lo que de verdad se le debe al proveedor tras retener.
     */
    public function test_retencion_no_infla_inventario_y_proveedores_queda_neto_de_la_retencion()
    {
        $proveedor = Tercero::create(['tipo' => 'empresa', 'razon_social' => 'Proveedor Retencion']);
        $bodega = Bodega::create(['descripcion' => 'Bodega Retencion']);
        $producto = Producto::create(['codigo' => 'PCR1', 'descripcion' => 'Producto Retencion', 'und_detal' => 'UND']);

        $respuesta = $this->actingAs($this->user())->postJson('/compras', [
            'prefijo' => 'FC', 'consecutivo' => 55, 'numero_factura' => 'F-0055',
            'proveedor_id' => $proveedor->id, 'fecha' => now()->toDateString(), 'confirmar' => true,
            'retefuente_porcentaje' => 2.5,
            'items' => [[
                'producto_id' => $producto->id, 'bodega_id' => $bodega->id,
                'cantidad' => 1, 'costo_unitario' => 1000000, 'iva_porcentaje' => 19,
            ]],
            'pagos' => [
                ['metodo_pago' => 'efectivo', 'valor' => 500000],
                ['metodo_pago' => 'credito', 'valor' => 665000],
            ],
        ]);
        $respuesta->assertStatus(201);

        $compra = Compra::latest('id')->firstOrFail();
        $this->assertEquals(1165000, (float) $compra->total);
        $this->assertEquals(25000, (float) $compra->retenciones);
        $this->assertEquals(665000, (float) $compra->saldo_pendiente, 'Proveedores debe quedar por el neto (bruto 690.000 - retención 25.000), no por el bruto.');

        $comprobante = ComprobanteContable::where('documento_origen', 'COMPRA')->where('documento_origen_id', $compra->id)->firstOrFail();
        $inventario = $comprobante->movimientos->firstWhere('cuenta_contable_id', \App\Models\CuentaContable::where('codigo', '143505')->value('id'));
        $this->assertEquals(1000000, (float) $inventario->debito, 'Inventario debe recibir solo la base gravable (1.000.000), no la base más la retención.');

        $this->assertEquals((float) $comprobante->movimientos->sum('debito'), (float) $comprobante->movimientos->sum('credito'));
    }

    public function test_revertir_una_compra_anulada_la_recontabiliza_limpia()
    {
        $compra = $this->compraConfirmada();
        $this->actingAs($this->user())->postJson("/compras/{$compra->id}/anular")->assertOk();

        $respuesta = $this->actingAs($this->user())->postJson("/compras/{$compra->id}/revertir");
        $respuesta->assertOk();

        $compra->refresh();
        $this->assertEquals('confirmada', $compra->estado);

        $comprobante = ComprobanteContable::where('documento_origen', 'COMPRA')->where('documento_origen_id', $compra->id)->where('estado', 'CONTABILIZADO')->first();
        $this->assertNotNull($comprobante, 'Debe existir un comprobante CONTABILIZADO fresco tras revertir la anulación.');
    }
}
