<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\{TipoDocumentoSeeder, TipoDocumentoContableSeeder, ProcesoContableSeeder, ConfiguracionContableSeeder, PucSeeder, ParametrizacionInicialContableSeeder, MetodoPagoContableSeeder, PlantillaContableSeeder, IntegracionContableSeeder};
use App\Models\{Bodega, Caja, ComprobanteContable, CuentaContable, Factura, FacturaDetalle, IntegracionContable, Inventario, Mesa, Producto, Roles, User, Zona};
use App\Services\{FacturacionContableService, LegacyDocumentSyncService};

/**
 * Antes de este fix, `contabilizar()` solo registraba Caja/Ventas/IVA: el
 * Kardex bajaba el stock físico pero la contabilidad nunca sacaba ese
 * inventario del activo ni reconocía el costo de ventas, inflando utilidad
 * e inventario contable en cada venta (hallazgo #1 de la auditoría DIAN).
 */
class FacturacionContableServiceTest extends TestCase
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
        $this->seed(PlantillaContableSeeder::class);
        $this->seed(IntegracionContableSeeder::class);
        $this->seed(MetodoPagoContableSeeder::class);
    }

    private function user(): User
    {
        $r = Roles::firstOrCreate(['nombre' => 'Administrador'], ['descripcion' => 'Test']);
        return User::firstOrCreate(['username' => 'factcontable-test'], ['name' => 'Factura Contable Test', 'password' => bcrypt('secret'), 'activo' => true, 'rol_id' => $r->id]);
    }

    /** Factura de 1 sola línea, con su producto, bodega, caja e inventario ya montados. */
    private function facturaConProducto(bool $afectaInventario, float $cantidad, float $costoPromedio, float $precioUnitario): Factura
    {
        $bodega = Bodega::create(['descripcion' => 'Bodega Test']);
        $caja = Caja::create(['nombre' => 'Caja Test', 'prefijo' => 'FR', 'proximo_numero' => 1, 'bodega_id' => $bodega->id, 'activa' => true]);
        $zona = Zona::create(['nombre' => 'Zona Test', 'bodega_id' => $bodega->id]);
        $mesa = Mesa::create(['zona_id' => $zona->id, 'numero' => 'M-1', 'capacidad' => 4]);
        $usuario = $this->user();

        $integracion = IntegracionContable::where('codigo', 'CERVEZAS')->firstOrFail();
        $producto = Producto::create([
            'codigo' => 'PT1', 'descripcion' => 'Producto Test', 'und_detal' => 'UND',
            'afecta_inventario' => $afectaInventario, 'iva_ventas' => 19,
            'integracion_contable_id' => $integracion->id,
        ]);

        if ($afectaInventario) {
            Inventario::create(['producto_id' => $producto->id, 'bodega_id' => $bodega->id, 'stock' => 100, 'costo_promedio' => $costoPromedio]);
        }

        $subtotal = round($cantidad * $precioUnitario, 2);
        $factura = Factura::create([
            'numero_factura' => 'FR-00001', 'mesa_id' => $mesa->id, 'user_id' => $usuario->id, 'caja_id' => $caja->id,
            'subtotal' => $subtotal, 'impuestos' => 0, 'total' => $subtotal, 'metodo_pago' => 'efectivo', 'estado' => 'pagada',
        ]);
        FacturaDetalle::create(['factura_id' => $factura->id, 'producto_id' => $producto->id, 'cantidad' => $cantidad, 'precio_unitario' => $precioUnitario, 'subtotal' => $subtotal]);

        return $factura;
    }

    public function test_contabiliza_costo_de_ventas_contra_inventario_al_vender()
    {
        // 2 unidades a costo promedio 5.000 = 10.000 de costo de ventas.
        $factura = $this->facturaConProducto(afectaInventario: true, cantidad: 2, costoPromedio: 5000, precioUnitario: 11900);

        app(LegacyDocumentSyncService::class)->factura($factura);
        app(FacturacionContableService::class)->contabilizar($factura);

        $comprobante = ComprobanteContable::where('documento_origen_id', $factura->id)->where('estado', 'CONTABILIZADO')->firstOrFail();
        $movs = $comprobante->movimientos;

        $costoVentas = $movs->firstWhere('cuenta_contable_id', CuentaContable::where('codigo', '613505')->value('id'));
        $inventario = $movs->firstWhere('cuenta_contable_id', CuentaContable::where('codigo', '143505')->value('id'));

        $this->assertNotNull($costoVentas, 'Debe existir una línea de Costo de Ventas (613505).');
        $this->assertNotNull($inventario, 'Debe existir una línea de salida de Inventario (143505).');
        $this->assertEquals(10000, (float) $costoVentas->debito);
        $this->assertEquals(10000, (float) $inventario->credito);

        $this->assertEquals((float) $movs->sum('debito'), (float) $movs->sum('credito'), 'El comprobante debe seguir cuadrado con las 2 líneas nuevas.');
    }

    /**
     * Un producto que no afecta inventario (p. ej. un plato cuyo insumo se
     * controla aparte) no generó movimiento de kardex: su costo debe quedar
     * en 0 y, gracias a `omitir_si_cero`, no debe crear las líneas de
     * Costo de Ventas/Inventario en absoluto (nada que reconocer).
     */
    public function test_no_genera_movimiento_de_costo_para_productos_que_no_afectan_inventario()
    {
        $factura = $this->facturaConProducto(afectaInventario: false, cantidad: 1, costoPromedio: 0, precioUnitario: 20000);

        app(LegacyDocumentSyncService::class)->factura($factura);
        app(FacturacionContableService::class)->contabilizar($factura);

        $comprobante = ComprobanteContable::where('documento_origen_id', $factura->id)->where('estado', 'CONTABILIZADO')->firstOrFail();
        $movs = $comprobante->movimientos;

        $costoVentas = $movs->firstWhere('cuenta_contable_id', CuentaContable::where('codigo', '613505')->value('id'));
        $inventario = $movs->firstWhere('cuenta_contable_id', CuentaContable::where('codigo', '143505')->value('id'));

        $this->assertNull($costoVentas, 'Sin salida real de inventario no debe reconocerse costo de ventas.');
        $this->assertNull($inventario, 'Sin salida real de inventario no debe acreditarse la cuenta de inventario.');
        $this->assertEquals((float) $movs->sum('debito'), (float) $movs->sum('credito'));
    }

    /**
     * Hallazgo #2 de la auditoría DIAN: 'mixto' se aceptaba sin desglose y se
     * contabilizaba todo como Caja. Ahora el ingreso se reparte exactamente
     * entre Caja y Banco según factura_pagos (60.000 efectivo + 30.000
     * tarjeta, sobre una venta de 90.000 con IVA incluido).
     */
    public function test_reparte_una_venta_mixta_entre_caja_y_banco()
    {
        $factura = $this->facturaConProducto(afectaInventario: true, cantidad: 1, costoPromedio: 20000, precioUnitario: 90000);
        $factura->update(['metodo_pago' => 'mixto']);
        $factura->pagos()->create(['metodo_pago' => 'efectivo', 'valor' => 60000]);
        $factura->pagos()->create(['metodo_pago' => 'tarjeta', 'valor' => 30000]);

        app(LegacyDocumentSyncService::class)->factura($factura);
        app(FacturacionContableService::class)->contabilizar($factura->fresh());

        $comprobante = ComprobanteContable::where('documento_origen_id', $factura->id)->where('estado', 'CONTABILIZADO')->firstOrFail();
        $movs = $comprobante->movimientos;

        $caja = $movs->firstWhere('cuenta_contable_id', CuentaContable::where('codigo', '110505')->value('id'));
        $banco = $movs->firstWhere('cuenta_contable_id', CuentaContable::where('codigo', '111005')->value('id'));

        $this->assertEquals(60000, (float) $caja->debito, 'La porción en efectivo debe ir a Caja.');
        $this->assertEquals(30000, (float) $banco->debito, 'La porción en tarjeta debe ir a Banco, no mezclada con Caja.');
        $this->assertEquals((float) $movs->sum('debito'), (float) $movs->sum('credito'));
    }

    /**
     * Antes, un método de pago sin parametrizar contablemente (cualquier
     * cosa distinta de efectivo/tarjeta/transferencia/nequi/daviplata/
     * credito) caía en Caja por defecto con solo un warning en el log. Ahora
     * debe rechazar la venta en vez de contabilizarla mal.
     */
    public function test_rechaza_un_metodo_de_pago_no_parametrizado_contablemente()
    {
        $factura = $this->facturaConProducto(afectaInventario: true, cantidad: 1, costoPromedio: 5000, precioUnitario: 20000);
        $factura->update(['metodo_pago' => 'criptomoneda']);

        app(LegacyDocumentSyncService::class)->factura($factura);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("El método de pago 'criptomoneda' no está parametrizado contablemente");

        app(FacturacionContableService::class)->contabilizar($factura->fresh());
    }

    /**
     * Antes, anular() reponía el stock con SQL crudo pero dejaba el
     * comprobante contable en CONTABILIZADO para siempre — la factura
     * quedaba "anulada" con sus asientos de ingreso/IVA/costo aún activos.
     * Mismo bug que ya se había corregido en Compras.
     */
    public function test_anular_una_factura_reversa_su_comprobante_contable()
    {
        $factura = $this->facturaConProducto(afectaInventario: true, cantidad: 1, costoPromedio: 5000, precioUnitario: 20000);
        app(LegacyDocumentSyncService::class)->factura($factura);
        app(FacturacionContableService::class)->contabilizar($factura->fresh());

        $comprobante = ComprobanteContable::where('documento_origen_id', $factura->id)->where('estado', 'CONTABILIZADO')->firstOrFail();

        $respuesta = $this->actingAs($this->user())->postJson("/facturas/{$factura->id}/anular");
        $respuesta->assertOk();

        $this->assertEquals('anulada', $factura->fresh()->estado);
        $this->assertEquals('ANULADO', $comprobante->fresh()->estado);
    }

    /** Debe poder recontabilizarse limpio, sin chocar con el comprobante ya anulado. */
    public function test_revertir_anulacion_recontabiliza_la_factura_con_un_comprobante_nuevo()
    {
        $factura = $this->facturaConProducto(afectaInventario: true, cantidad: 1, costoPromedio: 5000, precioUnitario: 20000);
        app(LegacyDocumentSyncService::class)->factura($factura);
        app(FacturacionContableService::class)->contabilizar($factura->fresh());

        $numeroOriginal = ComprobanteContable::where('documento_origen_id', $factura->id)->where('documento_origen', $factura->numero_factura)->value('numero');

        $this->actingAs($this->user())->postJson("/facturas/{$factura->id}/anular")->assertOk();
        $this->actingAs($this->user())->postJson("/facturas/{$factura->id}/revertir")->assertOk();

        $this->assertEquals('pagada', $factura->fresh()->estado);

        $comprobanteNuevo = ComprobanteContable::where('documento_origen_id', $factura->id)
            ->where('documento_origen', $factura->numero_factura)
            ->where('estado', 'CONTABILIZADO')
            ->firstOrFail();

        $this->assertNotEquals($numeroOriginal, $comprobanteNuevo->numero, 'Debe ser un comprobante nuevo, no el mismo reactivado.');
        $this->assertEquals((float) $comprobanteNuevo->movimientos->sum('debito'), (float) $comprobanteNuevo->movimientos->sum('credito'));
    }
}
