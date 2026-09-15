<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use App\Models\{Bodega, Caja, Factura, FacturaDetalle, IntegracionContable, Mesa, NotaFactura, NotaFacturaDetalle, Prefijo, Producto, Roles, Tercero, User, Zona};
use App\Services\FacturaElectronica\FactusFacturaElectronicaProvider;

/**
 * Construida a partir de la colección oficial de Postman de Factus API v2 +
 * su documentación pública — sin sandbox propio para probar contra el real,
 * así que se simulan las respuestas con Http::fake() en vez de pegarle a
 * api-sandbox.factus.com.co. Antes de producción, correr esto una vez
 * contra el sandbox real para confirmar que el formato no cambió.
 */
class FactusFacturaElectronicaProviderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush(); // el token de Factus se cachea; no debe filtrarse entre tests
        config([
            'services.factus.url' => 'https://api-sandbox.factus.test',
            'services.factus.client_id' => 'client-123',
            'services.factus.client_secret' => 'secret-456',
            'services.factus.username' => 'test@negocio.com',
            'services.factus.password' => 'clave',
        ]);
    }

    private function fakeAuth(): void
    {
        Http::fake([
            'api-sandbox.factus.test/oauth/token' => Http::response([
                'token_type' => 'Bearer', 'expires_in' => 3600, 'access_token' => 'token-de-prueba', 'refresh_token' => 'refresh-xyz',
            ], 200),
        ]);
    }

    private function factura(float $ivaPorcentaje = 19, string $metodoPago = 'efectivo', float $propina = 0, ?int $clienteId = null, array $pagosMixtos = []): Factura
    {
        static $contador = 0;
        $contador++;

        $bodega = Bodega::create(['descripcion' => 'Bodega Factus']);
        $caja = Caja::create(['nombre' => 'Caja Factus ' . $contador, 'prefijo' => 'FR', 'proximo_numero' => 1, 'bodega_id' => $bodega->id, 'activa' => true]);
        $zona = Zona::create(['nombre' => 'Zona Factus ' . $contador, 'bodega_id' => $bodega->id]);
        $mesa = Mesa::create(['zona_id' => $zona->id, 'numero' => 'M-FX-' . $contador, 'capacidad' => 4]);
        $usuario = User::firstOrCreate(['username' => 'factus-test'], ['name' => 'Factus Test', 'password' => bcrypt('x'), 'activo' => true, 'rol_id' => Roles::firstOrCreate(['nombre' => 'Administrador'], ['descripcion' => 'Test'])->id]);
        // No se necesita el motor contable completo para estos tests (no se
        // llama a contabilizar()) — solo lo mínimo para que integracion_contable_id
        // exista como FK válida.
        $tipoDoc = \App\Models\TipoDocumentoContable::firstOrCreate(['codigo' => 'FV'], ['nombre' => 'Factura de Venta', 'prefijo' => 'FV']);
        $proceso = \App\Models\ProcesoContable::firstOrCreate(['codigo' => 'VENTA_CONTADO'], ['nombre' => 'Venta de contado', 'tipo_documento_contable_id' => $tipoDoc->id, 'estado' => true]);
        $integracion = IntegracionContable::firstOrCreate(['codigo' => 'GEN'], ['nombre' => 'General', 'porcentaje_iva' => 19, 'porcentaje_inc' => 0, 'proceso_contable_id' => $proceso->id, 'estado' => true]);
        $producto = Producto::create(['codigo' => 'FX' . $contador, 'descripcion' => 'Producto Factus', 'und_detal' => 'UND', 'iva_ventas' => $ivaPorcentaje, 'integracion_contable_id' => $integracion->id]);

        $factura = Factura::create([
            'numero_factura' => 'FR-' . str_pad((string) $contador, 5, '0', STR_PAD_LEFT), 'mesa_id' => $mesa->id, 'user_id' => $usuario->id, 'caja_id' => $caja->id,
            'cliente_id' => $clienteId,
            'subtotal' => 10000, 'impuestos' => 1900, 'propina' => $propina, 'total' => 11900 + $propina, 'metodo_pago' => $metodoPago, 'estado' => 'pagada',
        ]);
        FacturaDetalle::create(['factura_id' => $factura->id, 'producto_id' => $producto->id, 'cantidad' => 1, 'precio_unitario' => 11900, 'subtotal' => 11900]);

        foreach ($pagosMixtos as $pago) {
            $factura->pagos()->create($pago);
        }

        return $factura->fresh();
    }

    public function test_autentica_y_emite_factura_guardando_cufe_y_numero()
    {
        $this->fakeAuth();
        Http::fake([
            'api-sandbox.factus.test/oauth/token' => Http::response(['access_token' => 'token-de-prueba', 'expires_in' => 3600], 200),
            'api-sandbox.factus.test/v2/bills/validate' => Http::response([
                'status' => 'Created',
                'message' => 'Documento registrado y validado con éxito.',
                'data' => [
                    'number' => 'SETP990001131',
                    'cufe' => 'CUFE-DE-PRUEBA-ABC123',
                    'is_validated' => true,
                    'links' => ['qr' => 'https://catalogo-vpfe.dian.gov.co/document/qr?123', 'public_url' => 'https://factus.com.co/bills/SETP990001131'],
                ],
            ], 201),
        ]);

        $factura = $this->factura();
        $resultado = app(FactusFacturaElectronicaProvider::class)->emitirFactura($factura);

        $this->assertEquals('aceptada', $resultado->estado);
        $this->assertEquals('CUFE-DE-PRUEBA-ABC123', $resultado->cufe);
        $this->assertEquals('SETP990001131', $resultado->numeroProveedor);
        $this->assertEquals('https://factus.com.co/bills/SETP990001131', $resultado->pdfUrl);
    }

    public function test_extrae_el_iva_del_precio_antes_de_enviarlo_a_factus()
    {
        $this->fakeAuth();
        Http::fake([
            'api-sandbox.factus.test/oauth/token' => Http::response(['access_token' => 't', 'expires_in' => 3600], 200),
            'api-sandbox.factus.test/v2/bills/validate' => Http::response(['data' => ['number' => 'X', 'cufe' => 'Y', 'is_validated' => true, 'links' => []]], 201),
        ]);

        // precio_unitario = 11900 CON IVA (19%) -> Factus espera el precio SIN IVA: 10000.00
        $factura = $this->factura(ivaPorcentaje: 19);
        app(FactusFacturaElectronicaProvider::class)->emitirFactura($factura);

        Http::assertSent(function ($request) {
            if (!str_contains($request->url(), '/v2/bills/validate')) return false;
            $item = $request->data()['items'][0];
            return $item['price'] === '10000.00' && $item['taxes'][0]['code'] === '01' && $item['taxes'][0]['rate'] === '19.00';
        });
    }

    public function test_factura_sin_cliente_usa_consumidor_final()
    {
        $this->fakeAuth();
        Http::fake([
            'api-sandbox.factus.test/oauth/token' => Http::response(['access_token' => 't', 'expires_in' => 3600], 200),
            'api-sandbox.factus.test/v2/bills/validate' => Http::response(['data' => ['number' => 'X', 'cufe' => 'Y', 'is_validated' => true, 'links' => []]], 201),
        ]);

        $factura = $this->factura();
        app(FactusFacturaElectronicaProvider::class)->emitirFactura($factura);

        Http::assertSent(function ($request) {
            if (!str_contains($request->url(), '/v2/bills/validate')) return false;
            $cliente = $request->data()['customer'];
            return $cliente['identification_document_code'] === '13' && $cliente['names'] === 'Consumidor Final';
        });
    }

    public function test_token_se_cachea_y_no_se_vuelve_a_pedir_en_la_segunda_llamada()
    {
        $this->fakeAuth();
        Http::fake([
            'api-sandbox.factus.test/oauth/token' => Http::response(['access_token' => 'token-unico', 'expires_in' => 3600], 200),
            'api-sandbox.factus.test/v2/bills/validate' => Http::response(['data' => ['number' => 'X', 'cufe' => 'Y', 'is_validated' => true, 'links' => []]], 201),
        ]);

        $provider = app(FactusFacturaElectronicaProvider::class);
        $provider->emitirFactura($this->factura());
        $provider->emitirFactura($this->factura());

        Http::assertSentCount(3); // 1 auth + 2 facturas (no 2 auth + 2 facturas)
    }

    /**
     * Factus documenta este caso: un timeout de nuestro lado no significa
     * que Factus no haya recibido la factura. Un reintento con el mismo
     * reference_code puede toparse con un borrador sin validar pendiente
     * (409) — hay que eliminarlo, no quedarse reintentando el mismo 409.
     */
    public function test_limpia_el_borrador_atascado_cuando_factus_responde_409()
    {
        $this->fakeAuth();
        $llamadaDelete = null;
        Http::fake([
            'api-sandbox.factus.test/oauth/token' => Http::response(['access_token' => 't', 'expires_in' => 3600], 200),
            'api-sandbox.factus.test/v2/bills/validate' => Http::response([
                'message' => 'Se encontró una factura pendiente por enviar a la DIAN.',
            ], 409),
            'api-sandbox.factus.test/v2/bills/destroy/reference/*' => Http::response(['message' => 'Eliminada'], 200),
        ]);

        $factura = $this->factura();
        $resultado = app(FactusFacturaElectronicaProvider::class)->emitirFactura($factura);

        $this->assertEquals('error', $resultado->estado);
        $this->assertStringContainsString('borrador sin validar pendiente', $resultado->mensaje);

        Http::assertSent(function ($request) use ($factura) {
            return $request->method() === 'DELETE'
                && str_contains($request->url(), "/v2/bills/destroy/reference/{$factura->numero_factura}");
        });
    }

    /** Factus exige due_date cuando payment_form=2 (pago a crédito) — antes no se enviaba. */
    public function test_venta_a_credito_incluye_due_date()
    {
        $this->fakeAuth();
        Http::fake([
            'api-sandbox.factus.test/oauth/token' => Http::response(['access_token' => 't', 'expires_in' => 3600], 200),
            'api-sandbox.factus.test/v2/bills/validate' => Http::response(['data' => ['number' => 'X', 'cufe' => 'Y', 'is_validated' => false, 'links' => []]], 201),
        ]);

        $factura = $this->factura(metodoPago: 'credito');
        app(FactusFacturaElectronicaProvider::class)->emitirFactura($factura);

        Http::assertSent(function ($request) {
            if (!str_contains($request->url(), '/v2/bills/validate')) return false;
            $pago = $request->data()['payment_details'][0];
            return $pago['payment_form'] === '2' && !empty($pago['due_date']);
        });
    }

    /**
     * Antes: una venta 'mixto' mandaba un solo payment_detail por el total,
     * tratado como efectivo (codigoMedioPago('mixto') caía al default '10').
     * Ahora debe mandar una línea por cada forma de pago real registrada en
     * factura_pagos.
     */
    public function test_venta_mixta_envia_una_linea_por_cada_forma_de_pago()
    {
        $this->fakeAuth();
        Http::fake([
            'api-sandbox.factus.test/oauth/token' => Http::response(['access_token' => 't', 'expires_in' => 3600], 200),
            'api-sandbox.factus.test/v2/bills/validate' => Http::response(['data' => ['number' => 'X', 'cufe' => 'Y', 'is_validated' => true, 'links' => []]], 201),
        ]);

        // Total de items+IVA = 11900. Repartido 60/40 entre efectivo/tarjeta.
        $factura = $this->factura(metodoPago: 'mixto', pagosMixtos: [
            ['metodo_pago' => 'efectivo', 'valor' => 7140],
            ['metodo_pago' => 'tarjeta', 'valor' => 4760],
        ]);
        app(FactusFacturaElectronicaProvider::class)->emitirFactura($factura);

        Http::assertSent(function ($request) {
            if (!str_contains($request->url(), '/v2/bills/validate')) return false;
            $pagos = $request->data()['payment_details'];
            if (count($pagos) !== 2) return false;
            $suma = array_sum(array_map('floatval', array_column($pagos, 'amount')));
            $codigos = array_column($pagos, 'payment_method_code');
            return abs($suma - 11900) < 0.01 && in_array('10', $codigos) && in_array('48', $codigos);
        });
    }

    /**
     * La propina está incluida en Factura::total pero NO en items (no es
     * base gravable de la venta) — antes payment_details.amount usaba
     * factura->total completo, desajustando la suma contra los items.
     */
    public function test_excluye_la_propina_del_monto_pagado_a_factus()
    {
        $this->fakeAuth();
        Http::fake([
            'api-sandbox.factus.test/oauth/token' => Http::response(['access_token' => 't', 'expires_in' => 3600], 200),
            'api-sandbox.factus.test/v2/bills/validate' => Http::response(['data' => ['number' => 'X', 'cufe' => 'Y', 'is_validated' => true, 'links' => []]], 201),
        ]);

        // Total items+IVA = 11900; +1000 de propina = 12900 en factura->total.
        $factura = $this->factura(propina: 1000);
        app(FactusFacturaElectronicaProvider::class)->emitirFactura($factura);

        Http::assertSent(function ($request) {
            if (!str_contains($request->url(), '/v2/bills/validate')) return false;
            $pago = $request->data()['payment_details'][0];
            return $pago['amount'] === '11900.00'; // NO 12900.00
        });
    }

    public function test_consumidor_final_incluye_legal_organization_code()
    {
        $this->fakeAuth();
        Http::fake([
            'api-sandbox.factus.test/oauth/token' => Http::response(['access_token' => 't', 'expires_in' => 3600], 200),
            'api-sandbox.factus.test/v2/bills/validate' => Http::response(['data' => ['number' => 'X', 'cufe' => 'Y', 'is_validated' => true, 'links' => []]], 201),
        ]);

        $factura = $this->factura();
        app(FactusFacturaElectronicaProvider::class)->emitirFactura($factura);

        Http::assertSent(function ($request) {
            if (!str_contains($request->url(), '/v2/bills/validate')) return false;
            return ($request->data()['customer']['legal_organization_code'] ?? null) === '2';
        });
    }

    /** Factus exige el NIT sin guion ni DV — Tercero.nit es texto libre y puede traerlo pegado. */
    public function test_limpia_el_nit_con_guion_y_dv_antes_de_enviarlo()
    {
        $this->fakeAuth();
        Http::fake([
            'api-sandbox.factus.test/oauth/token' => Http::response(['access_token' => 't', 'expires_in' => 3600], 200),
            'api-sandbox.factus.test/v2/bills/validate' => Http::response(['data' => ['number' => 'X', 'cufe' => 'Y', 'is_validated' => true, 'links' => []]], 201),
        ]);

        // id=1 está reservado a "Consumidor Final" en producción — se ocupa
        // aquí también para no disparar por accidente la rama de consumidor
        // final de cliente() (que compara por ese id).
        Tercero::create(['tipo' => 'persona', 'nombre' => 'Consumidor', 'apellido' => 'Final']);
        $cliente = Tercero::create(['tipo' => 'empresa', 'razon_social' => 'Cliente Real S.A.S', 'nit' => '900.123.456-7', 'codigo_municipio' => '11001']);
        $factura = $this->factura(clienteId: $cliente->id);
        app(FactusFacturaElectronicaProvider::class)->emitirFactura($factura);

        Http::assertSent(function ($request) {
            if (!str_contains($request->url(), '/v2/bills/validate')) return false;
            $cliente = $request->data()['customer'];
            return $cliente['identification'] === '900123456' && $cliente['legal_organization_code'] === '1';
        });
    }

    /**
     * Confirmado contra el sandbox real de Factus (2026-09-14): una factura
     * responde con 'cufe', pero una nota crédito/débito responde con 'cude'
     * en su lugar — sin este fallback, toda nota se guardaba sin CUFE aunque
     * Factus la hubiera validado bien.
     */
    public function test_una_nota_credito_valida_usa_cude_como_cufe()
    {
        $factura = $this->factura();
        $factura->update(['numero_proveedor' => 'SETP001']);

        $nota = NotaFactura::create([
            'factura_id' => $factura->id, 'tipo' => 'credito', 'fecha' => now()->toDateString(),
            'motivo' => 'Prueba', 'subtotal' => 10000, 'iva' => 1900, 'total' => 11900,
            'restaura_inventario' => false, 'user_id' => $factura->user_id,
        ]);
        NotaFacturaDetalle::create([
            'nota_factura_id' => $nota->id, 'factura_detalle_id' => $factura->detalles->first()->id,
            'producto_id' => $factura->detalles->first()->producto_id, 'cantidad' => 1, 'precio_unitario' => 11900, 'subtotal' => 11900,
        ]);

        Http::fake([
            'api-sandbox.factus.test/oauth/token' => Http::response(['access_token' => 't', 'expires_in' => 3600], 200),
            'api-sandbox.factus.test/v2/credit-notes/validate' => Http::response([
                'data' => ['number' => 'NC001', 'is_validated' => true, 'cude' => 'CUDE-DE-PRUEBA', 'links' => []],
            ], 201),
        ]);

        $resultado = app(FactusFacturaElectronicaProvider::class)->emitirNotaCredito($nota->fresh());

        $this->assertEquals('aceptada', $resultado->estado);
        $this->assertEquals('CUDE-DE-PRUEBA', $resultado->cufe);
    }

    public function test_no_emite_nota_credito_si_la_factura_no_tiene_numero_de_proveedor()
    {
        $factura = $this->factura();
        $this->assertNull($factura->numero_proveedor);

        $nota = NotaFactura::create([
            'factura_id' => $factura->id, 'tipo' => 'credito', 'fecha' => now()->toDateString(),
            'motivo' => 'Prueba', 'subtotal' => 10000, 'iva' => 1900, 'total' => 11900,
            'restaura_inventario' => false, 'user_id' => $factura->user_id,
        ]);
        NotaFacturaDetalle::create([
            'nota_factura_id' => $nota->id, 'factura_detalle_id' => $factura->detalles->first()->id,
            'producto_id' => $factura->detalles->first()->producto_id, 'cantidad' => 1, 'precio_unitario' => 11900, 'subtotal' => 11900,
        ]);

        Http::fake(); // ninguna llamada debería salir

        $resultado = app(FactusFacturaElectronicaProvider::class)->emitirNotaCredito($nota->fresh());

        $this->assertEquals('error', $resultado->estado);
        Http::assertNothingSent();
    }

    public function test_rechaza_transmitir_si_un_producto_tiene_ico_configurado()
    {
        $factura = $this->factura();
        $factura->detalles->first()->producto->update(['ico_ventas' => 8]);

        Http::fake(); // ninguna llamada debería salir

        $resultado = app(FactusFacturaElectronicaProvider::class)->emitirFactura($factura->fresh());

        $this->assertEquals('error', $resultado->estado);
        $this->assertStringContainsString('ICO', $resultado->mensaje);
        Http::assertNothingSent();
    }

    public function test_rechaza_transmitir_si_un_producto_tiene_impuesto_saludable_configurado()
    {
        $factura = $this->factura();
        $factura->detalles->first()->producto->update(['imp_saludable' => 300]);

        Http::fake();

        $resultado = app(FactusFacturaElectronicaProvider::class)->emitirFactura($factura->fresh());

        $this->assertEquals('error', $resultado->estado);
        Http::assertNothingSent();
    }

    /**
     * Confirmado contra el sandbox real de Factus (2026-09-14): un cliente
     * real sin código de municipio hace que la API rechace la factura con
     * 422. Se valida antes de llamar, con un mensaje claro.
     */
    public function test_rechaza_transmitir_si_el_cliente_real_no_tiene_codigo_de_municipio()
    {
        Tercero::create(['tipo' => 'persona', 'nombre' => 'Consumidor', 'apellido' => 'Final']);
        $cliente = Tercero::create(['tipo' => 'empresa', 'razon_social' => 'Cliente Sin Municipio S.A.S', 'nit' => '900999888']);
        $factura = $this->factura(clienteId: $cliente->id);

        Http::fake();

        $resultado = app(FactusFacturaElectronicaProvider::class)->emitirFactura($factura->fresh());

        $this->assertEquals('error', $resultado->estado);
        $this->assertStringContainsString('código de municipio', $resultado->mensaje);
        Http::assertNothingSent();
    }

    public function test_consumidor_final_no_necesita_codigo_de_municipio()
    {
        Http::fake([
            'api-sandbox.factus.test/oauth/token' => Http::response(['access_token' => 't', 'expires_in' => 3600], 200),
            'api-sandbox.factus.test/v2/bills/validate' => Http::response(['data' => ['number' => 'X', 'cufe' => 'Y', 'is_validated' => true, 'links' => []]], 201),
        ]);

        $factura = $this->factura(); // sin cliente_id -> Consumidor Final

        $resultado = app(FactusFacturaElectronicaProvider::class)->emitirFactura($factura);

        $this->assertEquals('aceptada', $resultado->estado);
    }
}
