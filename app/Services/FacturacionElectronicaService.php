<?php

namespace App\Services;

use App\Contracts\FacturaElectronicaProvider;
use App\DataTransferObjects\ResultadoFacturaElectronica;
use App\Models\ConfiguracionEmisor;
use App\Models\Factura;
use App\Models\NotaFactura;
use Illuminate\Support\Facades\Log;

/**
 * Único punto de entrada del sistema hacia la facturación electrónica.
 *
 * Mientras `config('services.factura_electronica.habilitada')` sea false
 * (el valor por defecto), encolar() no hace nada: ninguna factura/nota
 * cambia de estado, cero llamadas de red, cero impacto en el sistema
 * actual. El día que actives un proveedor real (ver App\Contracts\
 * FacturaElectronicaProvider), esto empieza a funcionar solo — nada más en
 * FacturacionController ni NotaFacturaService necesita cambiar.
 *
 * Deliberadamente NO transmite en el momento de facturar/notar (síncrono):
 * un proveedor lento o caído nunca debe demorar ni tumbar el cierre de una
 * mesa. Se marca 'pendiente' y un comando programado (ver
 * app/Console/Commands/ProcesarFacturacionElectronicaPendiente.php) la
 * transmite en segundo plano — el mismo principio que ya usa PrintService
 * con comandas_pendientes para no bloquear el POS por un servicio externo.
 */
class FacturacionElectronicaService
{
    /** Después de este número de intentos fallidos, se deja de reintentar automáticamente. */
    private const MAX_INTENTOS = 5;

    public function __construct(private readonly FacturaElectronicaProvider $provider)
    {
    }

    public function encolarFactura(Factura $factura): void
    {
        if (!$this->habilitada() || $factura->estado_dian === 'aceptada') {
            return;
        }

        $factura->update(['estado_dian' => 'pendiente']);
    }

    public function encolarNota(NotaFactura $nota): void
    {
        if (!$this->habilitada() || $nota->estado_dian === 'aceptada') {
            return;
        }

        $nota->update(['estado_dian' => 'pendiente']);
    }

    /** Intenta transmitir todo lo pendiente/con error, y reconsulta lo 'enviada'. Devuelve cuántos documentos procesó. */
    public function procesarPendientes(): int
    {
        if (!$this->habilitada()) {
            return 0;
        }

        $procesados = 0;

        Factura::whereIn('estado_dian', ['pendiente', 'error'])
            ->where('estado', '!=', 'anulada')
            ->chunkById(50, function ($facturas) use (&$procesados) {
                foreach ($facturas as $factura) {
                    $this->transmitirFactura($factura);
                    $procesados++;
                }
            });

        NotaFactura::whereIn('estado_dian', ['pendiente', 'error'])
            ->chunkById(50, function ($notas) use (&$procesados) {
                foreach ($notas as $nota) {
                    $this->transmitirNota($nota);
                    $procesados++;
                }
            });

        // 'enviada' = el proveedor lo recibió pero la DIAN todavía no lo
        // validaba en la respuesta original — sin esto, un documento así se
        // quedaba huérfano para siempre (nunca se volvía a mirar).
        Factura::where('estado_dian', 'enviada')
            ->chunkById(50, function ($facturas) use (&$procesados) {
                foreach ($facturas as $factura) {
                    $this->reconsultarFactura($factura);
                    $procesados++;
                }
            });

        NotaFactura::where('estado_dian', 'enviada')
            ->chunkById(50, function ($notas) use (&$procesados) {
                foreach ($notas as $nota) {
                    $this->reconsultarNota($nota);
                    $procesados++;
                }
            });

        return $procesados;
    }

    /**
     * Recupera manualmente un documento 'fallida' (agotó los reintentos
     * automáticos) para que vuelva a intentarse en el próximo
     * procesarPendientes() — p. ej. después de corregir lo que lo bloqueaba
     * (completar el emisor, revisar el cliente, etc.).
     */
    public function reintentarManualmente(Factura|NotaFactura $documento): void
    {
        $documento->update(['estado_dian' => 'pendiente', 'intentos_dian' => 0, 'mensaje_dian' => null]);
    }

    private function transmitirFactura(Factura $factura): void
    {
        if ($error = $this->errorPreVuelo()) {
            $this->guardarResultado($factura, $error, incrementarIntentos: false);
            return;
        }

        try {
            $resultado = $this->provider->emitirFactura($factura);
        } catch (\Throwable $e) {
            Log::error("Facturación electrónica: fallo transmitiendo {$factura->numero_factura}: {$e->getMessage()}");
            $resultado = new ResultadoFacturaElectronica(estado: 'error', mensaje: $e->getMessage());
        }

        $this->guardarResultado($factura, $resultado);
    }

    private function transmitirNota(NotaFactura $nota): void
    {
        if ($error = $this->errorPreVuelo()) {
            $this->guardarResultado($nota, $error, incrementarIntentos: false);
            return;
        }

        try {
            $resultado = $nota->tipo === 'credito'
                ? $this->provider->emitirNotaCredito($nota)
                : $this->provider->emitirNotaDebito($nota);
        } catch (\Throwable $e) {
            Log::error("Facturación electrónica: fallo transmitiendo nota {$nota->numero}: {$e->getMessage()}");
            $resultado = new ResultadoFacturaElectronica(estado: 'error', mensaje: $e->getMessage());
        }

        $this->guardarResultado($nota, $resultado);
    }

    private function reconsultarFactura(Factura $factura): void
    {
        try {
            $resultado = $this->provider->consultarEstado($factura);
        } catch (\Throwable $e) {
            Log::error("Facturación electrónica: fallo reconsultando {$factura->numero_factura}: {$e->getMessage()}");
            return; // no cuenta como intento; se vuelve a mirar en la próxima corrida
        }

        // No incrementa intentos_dian: reconsultar no es "intentar
        // transmitir" — es solo mirar si la DIAN ya resolvió.
        $this->guardarResultado($factura, $resultado, incrementarIntentos: false);
    }

    private function reconsultarNota(NotaFactura $nota): void
    {
        try {
            $resultado = $this->provider->consultarEstado($nota);
        } catch (\Throwable $e) {
            Log::error("Facturación electrónica: fallo reconsultando nota {$nota->numero}: {$e->getMessage()}");
            return;
        }

        $this->guardarResultado($nota, $resultado, incrementarIntentos: false);
    }

    /**
     * Antes de gastar una llamada HTTP: si al emisor le faltan datos
     * mínimos (NIT, DV, dirección, ciudad), cualquier proveedor real va a
     * rechazar el documento de todas formas — mejor fallar rápido con un
     * mensaje accionable que dejar que el proveedor devuelva un error
     * críptico y consumir intentos por una causa que ya sabíamos de antemano.
     * No cuenta como intento (no es culpa del documento ni del proveedor).
     */
    private function errorPreVuelo(): ?ResultadoFacturaElectronica
    {
        if (ConfiguracionEmisor::actual()->estaCompleta()) {
            return null;
        }

        return new ResultadoFacturaElectronica(
            estado: 'error',
            mensaje: 'Completa los datos del emisor (razón social, NIT, DV, dirección, ciudad) en "Datos del Emisor" antes de facturar electrónicamente.'
        );
    }

    private function guardarResultado(Factura|NotaFactura $documento, ResultadoFacturaElectronica $resultado, bool $incrementarIntentos = true): void
    {
        $intentos = $documento->intentos_dian;
        $estado = $resultado->estado;

        if ($incrementarIntentos && $estado === 'error') {
            $intentos++;
            // Se agotaron los reintentos automáticos: pasa a 'fallida' (fuera
            // del filtro pendiente/error de procesarPendientes) para no
            // seguir golpeando al proveedor con lo mismo indefinidamente.
            if ($intentos >= self::MAX_INTENTOS) {
                $estado = 'fallida';
            }
        }

        $documento->update([
            'cufe' => $resultado->cufe ?? $documento->cufe,
            'numero_proveedor' => $resultado->numeroProveedor ?? $documento->numero_proveedor,
            'xml_url' => $resultado->xmlUrl ?? $documento->xml_url,
            'pdf_url' => $resultado->pdfUrl ?? $documento->pdf_url,
            'qr_texto' => $resultado->qrTexto ?? $documento->qr_texto,
            'estado_dian' => $estado,
            'mensaje_dian' => $resultado->mensaje,
            'intentos_dian' => $intentos,
            'fecha_transmision_dian' => $resultado->exitosa() ? now() : $documento->fecha_transmision_dian,
        ]);
    }

    private function habilitada(): bool
    {
        return (bool) config('services.factura_electronica.habilitada');
    }
}
