<?php

namespace App\Services\FacturaElectronica;

use App\Contracts\FacturaElectronicaProvider;
use App\DataTransferObjects\ResultadoFacturaElectronica;
use App\Models\ConfiguracionEmisor;
use App\Models\Factura;
use App\Models\NotaFactura;
use Illuminate\Support\Facades\Http;

/**
 * PLANTILLA — no está conectada a nada (no se usa en AppServiceProvider).
 * Es un ejemplo de cómo se ve normalmente una implementación real, porque
 * casi todos los proveedores tecnológicos en Colombia comparten esta misma
 * forma: API REST, token en el header, un endpoint para facturas y otro
 * para notas, respuesta con cufe/xml/pdf.
 *
 * CUANDO TENGAS LA DOCUMENTACIÓN DE TU PROVEEDOR:
 * 1. Copia este archivo a, por ejemplo, FactusFacturaElectronicaProvider.php
 *    (o el nombre del proveedor que contrates).
 * 2. Ajusta la URL, los headers y la forma del payload/respuesta al formato
 *    exacto de SU documentación (cada proveedor difiere en detalle, no en
 *    la idea general).
 * 3. Agrega FACTURA_ELECTRONICA_API_URL y FACTURA_ELECTRONICA_API_KEY a tu
 *    .env con los valores reales que te den.
 * 4. En AppServiceProvider::register(), cambia el bind() de
 *    NullFacturaElectronicaProvider a tu nueva clase. Nada más cambia.
 */
class EjemploHttpFacturaElectronicaProvider implements FacturaElectronicaProvider
{
    private string $baseUrl;
    private string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.factura_electronica.url', env('FACTURA_ELECTRONICA_API_URL', ''));
        $this->apiKey = config('services.factura_electronica.key', env('FACTURA_ELECTRONICA_API_KEY', ''));
    }

    public function emitirFactura(Factura $factura): ResultadoFacturaElectronica
    {
        $factura->loadMissing('detalles.producto', 'cliente');
        $emisor = ConfiguracionEmisor::actual();

        // La forma exacta de este payload la define la documentación de TU
        // proveedor — esto es solo la idea general (emisor + cliente + items).
        $payload = [
            'numero_documento' => $factura->numero_factura,
            'fecha' => $factura->created_at->toIso8601String(),
            'emisor' => [
                'nit' => $emisor->nit,
                'dv' => $emisor->dv,
                'razon_social' => $emisor->razon_social,
            ],
            'cliente' => [
                'nombre' => $factura->cliente->nombre_completo ?? 'Consumidor Final',
                'identificacion' => $factura->cliente->nit ?? $factura->cliente->cedula ?? null,
            ],
            'items' => $factura->detalles->map(fn ($d) => [
                'descripcion' => $d->producto->descripcion,
                'cantidad' => (float) $d->cantidad,
                'precio_unitario' => (float) $d->precio_unitario,
                'iva_porcentaje' => (float) ($d->producto->iva_ventas ?? 0),
            ])->all(),
            'total' => (float) $factura->total,
        ];

        return $this->enviar('/facturas', $payload);
    }

    public function emitirNotaCredito(NotaFactura $nota): ResultadoFacturaElectronica
    {
        return $this->emitirNota($nota, '/notas-credito');
    }

    public function emitirNotaDebito(NotaFactura $nota): ResultadoFacturaElectronica
    {
        return $this->emitirNota($nota, '/notas-debito');
    }

    private function emitirNota(NotaFactura $nota, string $endpoint): ResultadoFacturaElectronica
    {
        $nota->loadMissing('detalles.producto', 'factura');

        $payload = [
            // La mayoría de proveedores piden el CUFE de la factura que se
            // está corrigiendo — por eso el flujo debe emitir la factura
            // ANTES de poder emitir una nota sobre ella electrónicamente.
            'factura_cufe' => $nota->factura->cufe,
            'motivo' => $nota->motivo,
            'items' => $nota->detalles->map(fn ($d) => [
                'descripcion' => $d->producto->descripcion,
                'cantidad' => (float) $d->cantidad,
                'precio_unitario' => (float) $d->precio_unitario,
            ])->all(),
            'total' => (float) $nota->total,
        ];

        return $this->enviar($endpoint, $payload);
    }

    public function consultarEstado(Factura|NotaFactura $documento): ResultadoFacturaElectronica
    {
        $respuesta = Http::withToken($this->apiKey)->get("{$this->baseUrl}/documentos/{$documento->cufe}");

        if (!$respuesta->successful()) {
            return new ResultadoFacturaElectronica(estado: 'error', mensaje: $respuesta->body());
        }

        $datos = $respuesta->json();

        return new ResultadoFacturaElectronica(
            estado: $datos['estado'] ?? 'enviada',
            cufe: $datos['cufe'] ?? $documento->cufe,
            xmlUrl: $datos['xml_url'] ?? null,
            pdfUrl: $datos['pdf_url'] ?? null,
            qrTexto: $datos['qr'] ?? null,
        );
    }

    private function enviar(string $endpoint, array $payload): ResultadoFacturaElectronica
    {
        try {
            $respuesta = Http::withToken($this->apiKey)
                ->timeout(30)
                ->post("{$this->baseUrl}{$endpoint}", $payload);

            if (!$respuesta->successful()) {
                return new ResultadoFacturaElectronica(
                    estado: 'error',
                    mensaje: "El proveedor respondió {$respuesta->status()}: {$respuesta->body()}"
                );
            }

            $datos = $respuesta->json();

            return new ResultadoFacturaElectronica(
                estado: $datos['estado'] ?? 'enviada',
                cufe: $datos['cufe'] ?? null,
                xmlUrl: $datos['xml_url'] ?? null,
                pdfUrl: $datos['pdf_url'] ?? null,
                qrTexto: $datos['qr'] ?? null,
            );
        } catch (\Throwable $e) {
            // Nunca dejar que un error de red aquí tumbe la venta: quien
            // llama a este método ya corre dentro del flujo de cola/reintento
            // de FacturacionElectronicaService, no del cierre de la mesa.
            return new ResultadoFacturaElectronica(estado: 'error', mensaje: $e->getMessage());
        }
    }
}
