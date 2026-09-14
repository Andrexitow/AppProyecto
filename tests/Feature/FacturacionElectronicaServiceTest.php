<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Contracts\FacturaElectronicaProvider;
use App\DataTransferObjects\ResultadoFacturaElectronica;
use App\Models\{Bodega, Caja, Factura, FacturaDetalle, Mesa, Producto, Roles, User, Zona};
use App\Models\{ConfiguracionEmisor, NotaFactura};
use App\Services\FacturaElectronica\NullFacturaElectronicaProvider;
use App\Services\FacturacionElectronicaService;

/**
 * Hallazgo #3 de la auditoría DIAN: integración real con la DIAN. En vez de
 * construirla ahora (requiere certificado, XML UBL, habilitación — ver
 * auditoría), se deja el "enchufe": mientras no haya proveedor configurado,
 * el sistema no cambia en nada; el día que se contrate uno, esto se activa
 * solo (ver App\Contracts\FacturaElectronicaProvider).
 */
class FacturacionElectronicaServiceTest extends TestCase
{
    use RefreshDatabase;

    /** Requerido desde que FacturacionElectronicaService falla rápido si el emisor está incompleto. */
    private function configurarEmisorCompleto(): void
    {
        ConfiguracionEmisor::actual()->update([
            'razon_social' => 'Negocio de Prueba S.A.S', 'nit' => '900123456', 'dv' => '1',
            'direccion' => 'Calle 1 # 2-3', 'ciudad' => 'Bogotá',
        ]);
    }

    private function user(): User
    {
        $r = Roles::firstOrCreate(['nombre' => 'Administrador'], ['descripcion' => 'Test']);
        return User::firstOrCreate(['username' => 'fe-test'], ['name' => 'FE Test', 'password' => bcrypt('secret'), 'activo' => true, 'rol_id' => $r->id]);
    }

    private function factura(?string $numero = null): Factura
    {
        static $contador = 0;
        $contador++;
        $numero = $numero ?? ('FR-' . str_pad((string) $contador, 5, '0', STR_PAD_LEFT));

        $bodega = Bodega::create(['descripcion' => 'Bodega FE']);
        $caja = Caja::create(['nombre' => 'Caja FE ' . $contador, 'prefijo' => 'FR', 'proximo_numero' => 1, 'bodega_id' => $bodega->id, 'activa' => true]);
        $zona = Zona::create(['nombre' => 'Zona FE ' . $contador, 'bodega_id' => $bodega->id]);
        $mesa = Mesa::create(['zona_id' => $zona->id, 'numero' => 'M-FE-' . $contador, 'capacidad' => 4]);
        $usuario = $this->user();
        $producto = Producto::create(['codigo' => 'FE' . $contador, 'descripcion' => 'Producto FE', 'und_detal' => 'UND']);

        $factura = Factura::create([
            'numero_factura' => $numero, 'mesa_id' => $mesa->id, 'user_id' => $usuario->id, 'caja_id' => $caja->id,
            'subtotal' => 10000, 'impuestos' => 0, 'total' => 10000, 'metodo_pago' => 'efectivo', 'estado' => 'pagada',
        ]);
        FacturaDetalle::create(['factura_id' => $factura->id, 'producto_id' => $producto->id, 'cantidad' => 1, 'precio_unitario' => 10000, 'subtotal' => 10000]);

        return $factura;
    }

    public function test_no_encola_nada_mientras_no_hay_proveedor_configurado()
    {
        config(['services.factura_electronica.habilitada' => false]);
        $factura = $this->factura();

        app(FacturacionElectronicaService::class)->encolarFactura($factura);

        $this->assertEquals('no_aplica', $factura->fresh()->estado_dian, 'Sin proveedor configurado, no debe tocarse el estado.');
    }

    public function test_encola_pendiente_y_el_proveedor_nulo_lo_resuelve_a_no_aplica()
    {
        config(['services.factura_electronica.habilitada' => true]);
        $this->configurarEmisorCompleto();
        // Fija explícitamente el Null provider: AppServiceProvider hoy
        // apunta a Factus (se está probando ese proveedor), pero ESTE test
        // verifica el mecanismo de cola en sí, no a Factus en particular.
        $this->app->bind(FacturaElectronicaProvider::class, NullFacturaElectronicaProvider::class);
        $factura = $this->factura();

        app(FacturacionElectronicaService::class)->encolarFactura($factura);
        $this->assertEquals('pendiente', $factura->fresh()->estado_dian);

        // El NullFacturaElectronicaProvider (el que corre hoy, sin proveedor
        // real contratado) responde 'no_aplica' — confirma que el mecanismo
        // de cola/proceso funciona de punta a punta aunque no haya proveedor.
        app(FacturacionElectronicaService::class)->procesarPendientes();
        $this->assertEquals('no_aplica', $factura->fresh()->estado_dian);
    }

    public function test_con_un_proveedor_real_guarda_cufe_y_marca_aceptada()
    {
        config(['services.factura_electronica.habilitada' => true]);
        $this->configurarEmisorCompleto();
        $this->app->bind(FacturaElectronicaProvider::class, fn () => new class implements FacturaElectronicaProvider {
            public function emitirFactura($factura): ResultadoFacturaElectronica
            {
                return new ResultadoFacturaElectronica(estado: 'aceptada', cufe: 'CUFE-DE-PRUEBA-123', pdfUrl: 'https://proveedor.test/f.pdf');
            }
            public function emitirNotaCredito($nota): ResultadoFacturaElectronica { return new ResultadoFacturaElectronica(estado: 'aceptada'); }
            public function emitirNotaDebito($nota): ResultadoFacturaElectronica { return new ResultadoFacturaElectronica(estado: 'aceptada'); }
            public function consultarEstado($documento): ResultadoFacturaElectronica { return new ResultadoFacturaElectronica(estado: 'aceptada'); }
        });

        $factura = $this->factura();
        app(FacturacionElectronicaService::class)->encolarFactura($factura);
        app(FacturacionElectronicaService::class)->procesarPendientes();

        $factura->refresh();
        $this->assertEquals('aceptada', $factura->estado_dian);
        $this->assertEquals('CUFE-DE-PRUEBA-123', $factura->cufe);
        $this->assertEquals('https://proveedor.test/f.pdf', $factura->pdf_url);
        $this->assertNotNull($factura->fecha_transmision_dian);
    }

    /** Un proveedor caído no debe romper nada — solo queda para reintentar. */
    public function test_un_error_del_proveedor_deja_el_documento_para_reintentar()
    {
        config(['services.factura_electronica.habilitada' => true]);
        $this->configurarEmisorCompleto();
        $this->app->bind(FacturaElectronicaProvider::class, fn () => new class implements FacturaElectronicaProvider {
            public function emitirFactura($factura): ResultadoFacturaElectronica { throw new \RuntimeException('Timeout del proveedor'); }
            public function emitirNotaCredito($nota): ResultadoFacturaElectronica { return new ResultadoFacturaElectronica(estado: 'error'); }
            public function emitirNotaDebito($nota): ResultadoFacturaElectronica { return new ResultadoFacturaElectronica(estado: 'error'); }
            public function consultarEstado($documento): ResultadoFacturaElectronica { return new ResultadoFacturaElectronica(estado: 'error'); }
        });

        $factura = $this->factura();
        app(FacturacionElectronicaService::class)->encolarFactura($factura);
        app(FacturacionElectronicaService::class)->procesarPendientes();

        $factura->refresh();
        $this->assertEquals('error', $factura->estado_dian);
        $this->assertStringContainsString('Timeout', $factura->mensaje_dian);
        $this->assertEquals(1, $factura->intentos_dian);

        // El siguiente procesarPendientes() (p. ej. el comando programado 5
        // minutos después) debe recogerla de nuevo, no dejarla huérfana.
        $this->assertEquals(1, \App\Models\Factura::whereIn('estado_dian', ['pendiente', 'error'])->count());
    }

    /** Hallazgo de auditoría: sin esto, un documento con error se reintentaba para siempre. */
    public function test_deja_de_reintentar_automaticamente_tras_agotar_los_intentos()
    {
        config(['services.factura_electronica.habilitada' => true]);
        $this->configurarEmisorCompleto();
        $this->app->bind(FacturaElectronicaProvider::class, fn () => new class implements FacturaElectronicaProvider {
            public function emitirFactura($factura): ResultadoFacturaElectronica { return new ResultadoFacturaElectronica(estado: 'error', mensaje: 'Proveedor caído'); }
            public function emitirNotaCredito($nota): ResultadoFacturaElectronica { return new ResultadoFacturaElectronica(estado: 'error'); }
            public function emitirNotaDebito($nota): ResultadoFacturaElectronica { return new ResultadoFacturaElectronica(estado: 'error'); }
            public function consultarEstado($documento): ResultadoFacturaElectronica { return new ResultadoFacturaElectronica(estado: 'error'); }
        });

        $factura = $this->factura();
        app(FacturacionElectronicaService::class)->encolarFactura($factura);

        for ($i = 0; $i < 5; $i++) {
            app(FacturacionElectronicaService::class)->procesarPendientes();
        }

        $factura->refresh();
        $this->assertEquals('fallida', $factura->estado_dian, 'Tras agotar los intentos debe dejar de reintentarse automáticamente.');
        $this->assertEquals(5, $factura->intentos_dian);

        // Una vez 'fallida', procesarPendientes() ya no debe tocarla más
        // (queda fuera del filtro pendiente/error).
        app(FacturacionElectronicaService::class)->procesarPendientes();
        $this->assertEquals(5, $factura->fresh()->intentos_dian, 'No debe seguir intentando tras marcarse fallida.');
    }

    /** Hallazgo de auditoría: con Factus activo, un emisor incompleto no debe ni intentar transmitir. */
    public function test_no_transmite_si_el_emisor_esta_incompleto()
    {
        config(['services.factura_electronica.habilitada' => true]);
        // OJO: sin configurarEmisorCompleto() a propósito.
        $intentosAlProveedor = 0;
        $this->app->bind(FacturaElectronicaProvider::class, function () use (&$intentosAlProveedor) {
            return new class($intentosAlProveedor) implements FacturaElectronicaProvider {
                public function __construct(private &$contador) {}
                public function emitirFactura($factura): ResultadoFacturaElectronica { $this->contador++; return new ResultadoFacturaElectronica(estado: 'aceptada'); }
                public function emitirNotaCredito($nota): ResultadoFacturaElectronica { return new ResultadoFacturaElectronica(estado: 'aceptada'); }
                public function emitirNotaDebito($nota): ResultadoFacturaElectronica { return new ResultadoFacturaElectronica(estado: 'aceptada'); }
                public function consultarEstado($documento): ResultadoFacturaElectronica { return new ResultadoFacturaElectronica(estado: 'aceptada'); }
            };
        });

        $factura = $this->factura();
        app(FacturacionElectronicaService::class)->encolarFactura($factura);
        app(FacturacionElectronicaService::class)->procesarPendientes();

        $factura->refresh();
        $this->assertEquals('error', $factura->estado_dian);
        $this->assertStringContainsString('Completa los datos del emisor', $factura->mensaje_dian);
        $this->assertEquals(0, $intentosAlProveedor, 'No debe siquiera llamar al proveedor si el emisor está incompleto.');
        $this->assertEquals(0, $factura->intentos_dian, 'No cuenta como intento fallido: la causa ya se conocía de antemano.');
    }

    /** Hallazgo de auditoría: un documento 'enviada' (recibido, DIAN aún sin validar) nunca se volvía a mirar. */
    public function test_reconsulta_y_actualiza_un_documento_que_quedo_enviada()
    {
        config(['services.factura_electronica.habilitada' => true]);
        $this->configurarEmisorCompleto();
        $this->app->bind(FacturaElectronicaProvider::class, fn () => new class implements FacturaElectronicaProvider {
            public function emitirFactura($factura): ResultadoFacturaElectronica { return new ResultadoFacturaElectronica(estado: 'error'); }
            public function emitirNotaCredito($nota): ResultadoFacturaElectronica { return new ResultadoFacturaElectronica(estado: 'error'); }
            public function emitirNotaDebito($nota): ResultadoFacturaElectronica { return new ResultadoFacturaElectronica(estado: 'error'); }
            public function consultarEstado($documento): ResultadoFacturaElectronica { return new ResultadoFacturaElectronica(estado: 'aceptada', cufe: 'CUFE-YA-VALIDADO'); }
        });

        // Simula una factura que ya quedó 'enviada' en una corrida anterior
        // (Factus la recibió, la DIAN aún no la validaba en esa respuesta).
        $factura = $this->factura();
        $factura->update(['estado_dian' => 'enviada', 'numero_proveedor' => 'SETP001']);

        app(FacturacionElectronicaService::class)->procesarPendientes();

        $factura->refresh();
        $this->assertEquals('aceptada', $factura->estado_dian);
        $this->assertEquals('CUFE-YA-VALIDADO', $factura->cufe);
        $this->assertEquals(0, $factura->intentos_dian, 'Reconsultar no cuenta como intento fallido.');
    }

    /** Hallazgo de auditoría: no había forma de recuperar manualmente un documento 'fallida'. */
    public function test_reintentar_manualmente_reactiva_un_documento_fallida()
    {
        $factura = $this->factura();
        $factura->update(['estado_dian' => 'fallida', 'intentos_dian' => 5, 'mensaje_dian' => 'Proveedor caído']);

        app(FacturacionElectronicaService::class)->reintentarManualmente($factura->fresh());

        $factura->refresh();
        $this->assertEquals('pendiente', $factura->estado_dian);
        $this->assertEquals(0, $factura->intentos_dian);
        $this->assertNull($factura->mensaje_dian);
    }

    public function test_anular_no_se_bloquea_por_no_aplica_pero_si_por_aceptada()
    {
        $facturaNormal = $this->factura();
        $this->assertEquals('no_aplica', $facturaNormal->fresh()->estado_dian);
        $this->actingAs($this->user())->postJson("/facturas/{$facturaNormal->id}/anular")->assertOk();

        $facturaTransmitida = $this->factura();
        $facturaTransmitida->update(['estado_dian' => 'aceptada']);

        $respuesta = $this->actingAs($this->user())->postJson("/facturas/{$facturaTransmitida->id}/anular");
        $respuesta->assertStatus(422);
        $respuesta->assertJsonFragment(['message' => 'No se puede anular: el documento electrónico ya fue transmitido a la DIAN. Debes hacer una nota crédito.']);
    }
}
