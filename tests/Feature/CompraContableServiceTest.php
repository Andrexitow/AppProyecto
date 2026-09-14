<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\{TipoDocumentoSeeder, TipoDocumentoContableSeeder, ProcesoContableSeeder, ConfiguracionContableSeeder, PucSeeder, ParametrizacionInicialContableSeeder, MetodoPagoContableSeeder, CuentaTesoreriaSeeder};
use App\Models\{Bodega, Compra, ComprobanteContable, CuentaContable, CuentaTesoreria, PagoProveedorAplicacion, Producto, Roles, Tercero, User};
use App\Services\TesoreriaService;
use Illuminate\Validation\ValidationException;

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
        $this->seed(CuentaTesoreriaSeeder::class);

        // Con la validación de saldo de tesorería, pagar en efectivo/tarjeta ya
        // exige que la cuenta tenga fondos reales — se fondea Caja General una
        // vez aquí para que el resto de pruebas de este archivo (que no prueban
        // esa validación en sí) sigan representando compras de contado normales.
        $caja = CuentaTesoreria::where('nombre', 'Caja General')->firstOrFail();
        $contrapartida = CuentaContable::where('permite_movimientos', true)->where('estado', true)->where('codigo', '!=', '110505')->firstOrFail();
        app(TesoreriaService::class)->registrarIngreso([
            'cuenta_tesoreria_id' => $caja->id, 'cuenta_contrapartida_id' => $contrapartida->id,
            'fecha' => now()->toDateString(), 'valor' => 10000000, 'descripcion' => 'Fondeo para pruebas',
        ], $this->user()->id);
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

    /**
     * Encontrado end-to-end probando el sistema con datos reales: un abono o un
     * pago inicial a proveedor podía dejar Caja/Banco en negativo sin ningún
     * aviso, a diferencia de un egreso hecho desde Tesorería (que sí valida el
     * saldo disponible). Ambos caminos afectan la misma cuenta contable.
     */
    public function test_rechaza_compra_de_contado_que_supera_el_saldo_de_caja()
    {
        $proveedor = Tercero::create(['tipo' => 'empresa', 'razon_social' => 'Proveedor Sin Fondos']);
        $bodega = Bodega::create(['descripcion' => 'Bodega Sin Fondos']);
        $producto = Producto::create(['codigo' => 'PSF1', 'descripcion' => 'Producto Sin Fondos', 'und_detal' => 'UND']);

        // Caja General ya trae $10.000.000 del fondeo de setUp(): se pide pagar
        // de contado más de lo que hay disponible.
        $respuesta = $this->actingAs($this->user())->postJson('/compras', [
            'prefijo' => 'FC', 'consecutivo' => 99, 'numero_factura' => 'F-0099',
            'proveedor_id' => $proveedor->id, 'fecha' => now()->toDateString(), 'confirmar' => true,
            'items' => [['producto_id' => $producto->id, 'bodega_id' => $bodega->id, 'cantidad' => 1, 'costo_unitario' => 20000000]],
            'pagos' => [['metodo_pago' => 'efectivo', 'valor' => 20000000]],
        ]);

        $respuesta->assertStatus(422);
        $respuesta->assertJsonValidationErrors('valor');
        $this->assertDatabaseMissing('compras', ['numero_factura' => 'F-0099']);
    }

    public function test_rechaza_abono_que_supera_el_saldo_de_la_cuenta_de_tesoreria()
    {
        $compra = $this->compraConfirmada(); // pagada de contado, sin saldo pendiente

        // Fuerza un saldo pendiente artificial para poder intentar un abono,
        // sin necesitar otra compra completa a crédito.
        $compra->update(['saldo_pendiente' => 20000000, 'estado_pago' => 'parcialmente_pagada']);
        $metodoEfectivo = \App\Models\MetodoPagoContable::where('metodo_pago', 'efectivo')->firstOrFail();

        try {
            app(\App\Services\CompraContableService::class)->registrarAbono($compra, [
                'fecha' => now()->toDateString(), 'valor' => 20000000, 'metodo_pago_contable_id' => $metodoEfectivo->id,
            ], $this->user()->id);
            $this->fail('Debía rechazar un abono mayor al saldo disponible en Caja General.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('valor', $e->errors());
        }
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

    /**
     * Antes, retefuente + reteiva + reteica se sumaban y se acreditaban TODAS a
     * la cuenta de Retefuente — contablemente incorrecto (son pasivos y
     * declaraciones distintas) e imposible de desglosar en un informe de
     * retenciones. Ahora cada una va a su propia cuenta.
     */
    public function test_retenciones_de_fuente_iva_e_ica_van_a_cuentas_separadas()
    {
        $proveedor = Tercero::create(['tipo' => 'empresa', 'razon_social' => 'Proveedor Retenciones Varias']);
        $bodega = Bodega::create(['descripcion' => 'Bodega Retenciones Varias']);
        $producto = Producto::create(['codigo' => 'PCR2', 'descripcion' => 'Producto Retenciones Varias', 'und_detal' => 'UND']);

        $this->actingAs($this->user())->postJson('/compras', [
            'prefijo' => 'FC', 'consecutivo' => 56, 'numero_factura' => 'F-0056',
            'proveedor_id' => $proveedor->id, 'fecha' => now()->toDateString(), 'confirmar' => true,
            'retefuente_porcentaje' => 2.5, 'reteiva_porcentaje' => 15, 'reteica_porcentaje' => 0.7,
            'items' => [['producto_id' => $producto->id, 'bodega_id' => $bodega->id, 'cantidad' => 1, 'costo_unitario' => 1000000, 'iva_porcentaje' => 19]],
            // reteiva y reteica también se calculan sobre la base gravable (no
            // sobre el IVA) en este sistema: 15%*1.000.000=150.000, 0.7%*1.000.000=7.000.
            'pagos' => [['metodo_pago' => 'credito', 'valor' => 1000000 + 190000 - 25000 - 150000 - 7000]],
        ])->assertStatus(201);

        $compra = Compra::latest('id')->firstOrFail();
        $comprobante = ComprobanteContable::where('documento_origen', 'COMPRA')->where('documento_origen_id', $compra->id)->firstOrFail();

        $creditoDe = fn (string $codigo) => (float) $comprobante->movimientos
            ->firstWhere('cuenta_contable_id', CuentaContable::where('codigo', $codigo)->value('id'))
            ?->credito;

        $this->assertEquals(25000, $creditoDe('236540'), 'Retefuente debe quedar en su propia cuenta.');
        $this->assertEquals(150000, $creditoDe('236705'), 'Reteiva debe quedar en su propia cuenta, no mezclada con retefuente.');
        $this->assertEquals(7000, $creditoDe('236805'), 'Reteica debe quedar en su propia cuenta, no mezclada con retefuente.');
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
