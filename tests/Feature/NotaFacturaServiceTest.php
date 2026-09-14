<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\{TipoDocumentoSeeder, TipoDocumentoContableSeeder, ProcesoContableSeeder, ConfiguracionContableSeeder, PucSeeder, ParametrizacionInicialContableSeeder, MetodoPagoContableSeeder, PlantillaContableSeeder, IntegracionContableSeeder};
use App\Models\{Bodega, Caja, ComprobanteContable, CuentaContable, Factura, FacturaDetalle, IntegracionContable, Inventario, Mesa, Producto, Roles, User, Zona};
use App\Services\{FacturacionContableService, LegacyDocumentSyncService, NotaFacturaService};
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Hallazgo #4 de la auditoría DIAN: NOTA_CREDITO/NOTA_DEBITO existían
 * parametrizadas contablemente pero sin ningún flujo real para emitirlas.
 */
class NotaFacturaServiceTest extends TestCase
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
        return User::firstOrCreate(['username' => 'nota-test'], ['name' => 'Nota Test', 'password' => bcrypt('secret'), 'activo' => true, 'rol_id' => $r->id]);
    }

    /** Factura de 1 línea (cantidad=3), ya contabilizada, lista para notar. */
    private function facturaVendida(float $cantidad = 3, float $costoPromedio = 5000, float $precioUnitario = 20000, string $metodoPago = 'efectivo', array $pagosMixtos = []): Factura
    {
        $bodega = Bodega::create(['descripcion' => 'Bodega Test']);
        $caja = Caja::create(['nombre' => 'Caja Test', 'prefijo' => 'FR', 'proximo_numero' => 1, 'bodega_id' => $bodega->id, 'activa' => true]);
        $zona = Zona::create(['nombre' => 'Zona Test', 'bodega_id' => $bodega->id]);
        $mesa = Mesa::create(['zona_id' => $zona->id, 'numero' => 'M-1', 'capacidad' => 4]);
        $usuario = $this->user();

        $integracion = IntegracionContable::where('codigo', 'CERVEZAS')->firstOrFail();
        $producto = Producto::create([
            'codigo' => 'PT1', 'descripcion' => 'Producto Test', 'und_detal' => 'UND',
            // iva_ventas=0 a propósito: estas pruebas verifican montos/reparto,
            // no la extracción de IVA (ya cubierta en FacturacionContableServiceTest).
            'afecta_inventario' => true, 'iva_ventas' => 0,
            'integracion_contable_id' => $integracion->id,
        ]);
        Inventario::create(['producto_id' => $producto->id, 'bodega_id' => $bodega->id, 'stock' => 100, 'costo_promedio' => $costoPromedio]);

        $subtotal = round($cantidad * $precioUnitario, 2);
        $factura = Factura::create([
            'numero_factura' => 'FR-00001', 'mesa_id' => $mesa->id, 'user_id' => $usuario->id, 'caja_id' => $caja->id,
            'subtotal' => $subtotal, 'impuestos' => 0, 'total' => $subtotal, 'metodo_pago' => $metodoPago, 'estado' => 'pagada',
        ]);
        FacturaDetalle::create(['factura_id' => $factura->id, 'producto_id' => $producto->id, 'cantidad' => $cantidad, 'precio_unitario' => $precioUnitario, 'subtotal' => $subtotal]);

        // Debe existir ANTES de contabilizar: resolverPagos() solo usa el
        // desglose real (factura_pagos) si ya está guardado en ese momento.
        foreach ($pagosMixtos as $pago) {
            $factura->pagos()->create($pago);
        }

        app(LegacyDocumentSyncService::class)->factura($factura);
        app(FacturacionContableService::class)->contabilizar($factura->fresh());

        return $factura->fresh();
    }

    public function test_emite_nota_credito_parcial_reversa_venta_y_restaura_inventario()
    {
        $factura = $this->facturaVendida(cantidad: 3, costoPromedio: 5000, precioUnitario: 20000);
        $detalle = $factura->detalles->first();
        $stockAntes = (float) DB::table('inventarios')->where('producto_id', $detalle->producto_id)->value('stock');

        $nota = app(NotaFacturaService::class)->emitir(
            $factura,
            'credito',
            [['factura_detalle_id' => $detalle->id, 'cantidad' => 1]],
            'Cliente devolvió 1 unidad por error de pedido',
            true,
            $this->user()->id
        );

        $this->assertEquals(20000, (float) $nota->subtotal); // 1 unidad, sin IVA en este producto
        $this->assertNotNull($nota->numero);

        $stockDespues = (float) DB::table('inventarios')->where('producto_id', $detalle->producto_id)->value('stock');
        $this->assertEquals($stockAntes + 1, $stockDespues, 'La unidad devuelta debe reingresar a bodega.');

        $comprobante = ComprobanteContable::where('documento_origen', 'NOTA-' . $factura->numero_factura)->where('documento_origen_id', $nota->id)->where('estado', 'CONTABILIZADO')->firstOrFail();
        $movs = $comprobante->movimientos;

        $caja = $movs->firstWhere('cuenta_contable_id', CuentaContable::where('codigo', '110505')->value('id'));
        $ventas = $movs->firstWhere('cuenta_contable_id', CuentaContable::where('codigo', '413505')->value('id'));
        $inventario = $movs->firstWhere('cuenta_contable_id', CuentaContable::where('codigo', '143505')->value('id'));
        $costoVentas = $movs->firstWhere('cuenta_contable_id', CuentaContable::where('codigo', '613505')->value('id'));

        $this->assertEquals(20000, (float) $caja->credito, 'Se devuelve en efectivo, igual a como se cobró.');
        $this->assertEquals(20000, (float) $ventas->debito, 'Reversa de venta.');
        $this->assertEquals(5000, (float) $inventario->debito, 'Reingreso a inventario al costo promedio vigente.');
        $this->assertEquals(5000, (float) $costoVentas->credito, 'Reversa de costo de ventas.');
        $this->assertEquals((float) $movs->sum('debito'), (float) $movs->sum('credito'));
    }

    public function test_rechaza_notar_mas_cantidad_de_la_disponible()
    {
        $factura = $this->facturaVendida(cantidad: 2);
        $detalle = $factura->detalles->first();

        $this->expectException(ValidationException::class);

        app(NotaFacturaService::class)->emitir(
            $factura, 'credito',
            [['factura_detalle_id' => $detalle->id, 'cantidad' => 5]],
            'Cantidad inválida a propósito', false, $this->user()->id
        );
    }

    public function test_dos_notas_credito_parciales_no_pueden_superar_lo_vendido()
    {
        $factura = $this->facturaVendida(cantidad: 2);
        $detalle = $factura->detalles->first();

        app(NotaFacturaService::class)->emitir(
            $factura, 'credito', [['factura_detalle_id' => $detalle->id, 'cantidad' => 2]],
            'Primera nota: se acredita todo lo vendido', false, $this->user()->id
        );

        $this->expectException(ValidationException::class);

        // Ya no queda nada disponible para una segunda nota crédito.
        app(NotaFacturaService::class)->emitir(
            $factura, 'credito', [['factura_detalle_id' => $detalle->id, 'cantidad' => 1]],
            'Segunda nota: no debería poder', false, $this->user()->id
        );
    }

    public function test_nota_debito_cobra_valor_adicional_sin_tocar_inventario()
    {
        $factura = $this->facturaVendida(cantidad: 1, precioUnitario: 20000);
        $detalle = $factura->detalles->first();
        $stockAntes = (float) DB::table('inventarios')->where('producto_id', $detalle->producto_id)->value('stock');

        $nota = app(NotaFacturaService::class)->emitir(
            $factura, 'debito', [['factura_detalle_id' => $detalle->id, 'cantidad' => 1]],
            'Corrección de precio: se facturó por debajo del valor real', false, $this->user()->id
        );

        $this->assertEquals($stockAntes, (float) DB::table('inventarios')->where('producto_id', $detalle->producto_id)->value('stock'), 'Una nota débito no mueve inventario.');

        $comprobante = ComprobanteContable::where('documento_origen', 'NOTA-' . $factura->numero_factura)->where('documento_origen_id', $nota->id)->where('estado', 'CONTABILIZADO')->firstOrFail();
        $movs = $comprobante->movimientos;
        $caja = $movs->firstWhere('cuenta_contable_id', CuentaContable::where('codigo', '110505')->value('id'));
        $ventas = $movs->firstWhere('cuenta_contable_id', CuentaContable::where('codigo', '413505')->value('id'));

        $this->assertEquals(20000, (float) $caja->debito, 'Cobro adicional en efectivo.');
        $this->assertEquals(20000, (float) $ventas->credito, 'Mayor valor de venta.');
        $this->assertEquals((float) $movs->sum('debito'), (float) $movs->sum('credito'));
    }

    /** El reembolso respeta la MISMA proporción de pago que tuvo la venta original. */
    public function test_nota_credito_sobre_venta_mixta_devuelve_en_la_misma_proporcion()
    {
        $factura = $this->facturaVendida(cantidad: 2, precioUnitario: 10000, metodoPago: 'mixto', pagosMixtos: [
            ['metodo_pago' => 'efectivo', 'valor' => 12000],
            ['metodo_pago' => 'tarjeta', 'valor' => 8000],
        ]);

        $detalle = $factura->detalles->first();

        $nota = app(NotaFacturaService::class)->emitir(
            $factura, 'credito', [['factura_detalle_id' => $detalle->id, 'cantidad' => 1]],
            'Devolución de 1 unidad', false, $this->user()->id
        );

        $comprobante = ComprobanteContable::where('documento_origen', 'NOTA-' . $factura->numero_factura)->where('documento_origen_id', $nota->id)->where('estado', 'CONTABILIZADO')->firstOrFail();
        $movs = $comprobante->movimientos;
        $caja = $movs->firstWhere('cuenta_contable_id', CuentaContable::where('codigo', '110505')->value('id'));

        // El primer pago registrado (efectivo, 12.000) se consume primero en la
        // cascada: la nota de 10.000 (1 unidad) sale completa de esa línea.
        $this->assertEquals(10000, (float) $caja->credito);
        $this->assertEquals((float) $movs->sum('debito'), (float) $movs->sum('credito'));
    }
}
