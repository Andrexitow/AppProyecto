<?php

namespace App\Services\FacturaElectronica;

use App\Contracts\FacturaElectronicaProvider;
use App\DataTransferObjects\ResultadoFacturaElectronica;
use App\Models\Factura;
use App\Models\NotaFactura;
use App\Models\Prefijo;
use App\Models\Tercero;
use App\Services\FacturacionContableService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Integración real con Factus API v2 (https://developers.factus.com.co).
 * Construida a partir de la colección oficial de Postman + la documentación
 * pública (no hay entorno propio para probar en este momento — revisar
 * contra el sandbox real antes de producción).
 *
 * Simplificaciones deliberadas de este V1 (documentadas aquí para no
 * perderlas de vista):
 * - unit_measure_code fijo en "94" (Unidad) y standard_code fijo en "999"
 *   (sin clasificar) — Nexora no tiene hoy un catálogo de unidades/UNSPSC
 *   por producto. Ajustar si un producto puntual lo necesita.
 * - Solo se mapea IVA (tax code "01"). ico_ventas/imp_saludable de Producto
 *   no se envían todavía.
 * - Un cliente real (no "Consumidor Final") necesita municipality_code
 *   (código DANE) — Tercero no lo tiene todavía. Es opcional según Factus
 *   (no debería bloquear la transmisión), pero deja el dato geográfico
 *   incompleto en la factura de un cliente real.
 * - codigoMedioPago() es un mapeo simplificado del catálogo DIAN de medios
 *   de pago; revísalo si tu operación necesita distinguir tarjeta débito de
 *   crédito, o un medio no cubierto aquí.
 * - due_date de una venta a crédito se fija en 30 días desde la venta
 *   (Nexora no captura hoy un plazo de crédito por factura).
 */
class FactusFacturaElectronicaProvider implements FacturaElectronicaProvider
{
    private string $baseUrl;

    public function __construct(private readonly FacturacionContableService $facturacionContableService)
    {
        $this->baseUrl = rtrim((string) config('services.factus.url'), '/');
    }

    public function emitirFactura(Factura $factura): ResultadoFacturaElectronica
    {
        $factura->loadMissing('detalles.producto', 'cliente', 'caja', 'pagos');

        $payload = [
            'reference_code' => $factura->numero_factura,
            'document' => '01',
            'operation_type' => '10',
            'send_email' => false,
            'observation' => "Venta POS {$factura->numero_factura}",
            'payment_details' => $this->pagosDetalle($factura),
            'cash_rounding_amount' => '0.00',
            'customer' => $this->cliente($factura->cliente),
            'items' => $this->items($factura->detalles),
        ];

        if ($rangoId = $this->numberingRangeId($factura->caja?->prefijo)) {
            $payload['numbering_range_id'] = $rangoId;
        }

        return $this->enviar('/v2/bills/validate', $payload, '/v2/bills/destroy/reference/{reference_code}');
    }

    public function emitirNotaCredito(NotaFactura $nota): ResultadoFacturaElectronica
    {
        // "2" = Anulación de factura electrónica (catálogo DIAN de conceptos
        // de corrección) — el concepto genérico más razonable para
        // devoluciones/correcciones de valor desde el POS.
        return $this->emitirNota($nota, '/v2/credit-notes/validate', '/v2/credit-notes/reference/{reference_code}', '2');
    }

    public function emitirNotaDebito(NotaFactura $nota): ResultadoFacturaElectronica
    {
        // "4" = Otros (catálogo DIAN de conceptos de corrección para notas
        // débito) — cobros adicionales no cubiertos por los conceptos 1-3.
        return $this->emitirNota($nota, '/v2/debit-notes/validate', '/v2/debit-notes/reference/{reference_code}', '4');
    }

    private function emitirNota(NotaFactura $nota, string $endpoint, string $deleteEndpoint, string $conceptoCorreccion): ResultadoFacturaElectronica
    {
        $nota->loadMissing('detalles.producto', 'factura.cliente', 'factura.caja', 'factura.pagos');
        $factura = $nota->factura;

        if (!$factura->numero_proveedor) {
            return new ResultadoFacturaElectronica(
                estado: 'error',
                mensaje: 'La factura original todavía no tiene número asignado por Factus (no se ha transmitido, o sigue pendiente/con error). No se puede emitir la nota hasta que eso se resuelva.'
            );
        }

        $payload = [
            'reference_code' => 'NOTA-' . $nota->id . '-' . $factura->numero_factura,
            'correction_concept_code' => $conceptoCorreccion,
            'bill_number' => $factura->numero_proveedor,
            'observation' => $nota->motivo,
            'payment_details' => $this->pagosDetalle($factura, (float) $nota->total),
            'cash_rounding_amount' => '0.00',
            'customer' => $this->cliente($factura->cliente),
            'items' => $this->items($nota->detalles),
        ];

        if ($rangoId = $this->numberingRangeId($factura->caja?->prefijo)) {
            $payload['numbering_range_id'] = $rangoId;
        }

        return $this->enviar($endpoint, $payload, $deleteEndpoint);
    }

    public function consultarEstado(Factura|NotaFactura $documento): ResultadoFacturaElectronica
    {
        if (!$documento->numero_proveedor) {
            return new ResultadoFacturaElectronica(estado: 'error', mensaje: 'Este documento todavía no tiene número asignado por Factus — no se puede reconsultar.');
        }

        $endpoint = match (true) {
            $documento instanceof Factura => '/v2/bills/',
            $documento->tipo === 'credito' => '/v2/credit-notes/',
            default => '/v2/debit-notes/',
        };

        try {
            $respuesta = Http::withToken($this->token())
                ->timeout(30)
                ->acceptJson()
                ->get("{$this->baseUrl}{$endpoint}{$documento->numero_proveedor}");
        } catch (\Throwable $e) {
            return new ResultadoFacturaElectronica(estado: 'error', mensaje: $e->getMessage());
        }

        if (!$respuesta->successful()) {
            return new ResultadoFacturaElectronica(estado: 'error', mensaje: "Factus respondió {$respuesta->status()}: {$respuesta->body()}");
        }

        $datos = $respuesta->json('data') ?? [];

        return new ResultadoFacturaElectronica(
            estado: ($datos['is_validated'] ?? false) ? 'aceptada' : 'enviada',
            cufe: $datos['cufe'] ?? $documento->cufe,
            numeroProveedor: $datos['number'] ?? $documento->numero_proveedor,
            pdfUrl: $datos['links']['public_url'] ?? $documento->pdf_url,
            qrTexto: $datos['links']['qr'] ?? $documento->qr_texto,
            mensaje: $respuesta->json('message'),
        );
    }

    // ── Construcción del payload ────────────────────────────────────────

    /**
     * Un objeto payment_details por cada forma de pago realmente usada
     * (antes: siempre uno solo, tratado como efectivo incluso si la venta
     * fue mixta). Reutiliza FacturacionContableService::resolverPagos(), la
     * misma fuente de verdad que ya usa la contabilidad interna.
     *
     * Esa suma incluye la propina (ver resolverPagos()), pero Factus espera
     * que payment_details sume exactamente el total de items — la propina
     * en Colombia no es parte de la base gravable de la venta. Se escala
     * proporcionalmente cada línea para excluirla, en vez de reportarla
     * como allowance_charges (que la incluiría en el documento fiscal como
     * si fuera parte de la venta, que no lo es).
     *
     * $valorObjetivo permite forzar el total a repartir (usado por las
     * notas, cuyo total ya excluye la propina de por sí).
     */
    private function pagosDetalle(Factura $factura, ?float $valorObjetivo = null): array
    {
        $pagos = $this->facturacionContableService->resolverPagos($factura)->values();
        $sumaOriginal = (float) $pagos->sum('valor');

        if ($sumaOriginal <= 0.0001 || $pagos->isEmpty()) {
            return [];
        }

        $objetivo = $valorObjetivo ?? round((float) $factura->subtotal + (float) $factura->impuestos, 2);
        $factor = $objetivo / $sumaOriginal;

        $lineas = [];
        $acumulado = 0.0;
        $ultimoIndice = $pagos->count() - 1;

        foreach ($pagos as $indice => $pago) {
            $esUltimo = $indice === $ultimoIndice;
            $monto = $esUltimo ? round($objetivo - $acumulado, 2) : round($pago['valor'] * $factor, 2);
            $acumulado += $monto;

            if ($monto <= 0) {
                continue;
            }

            $esCredito = $pago['metodo_pago'] === 'credito';
            $linea = [
                'payment_form' => $esCredito ? '2' : '1',
                'payment_method_code' => $this->codigoMedioPago($pago['metodo_pago']),
                'reference_code' => $factura->numero_factura,
                'amount' => number_format($monto, 2, '.', ''),
            ];

            if ($esCredito) {
                // Factus exige due_date cuando payment_form=2. Nexora no
                // captura hoy un plazo de crédito por factura — se usa 30
                // días desde la venta como valor por defecto razonable;
                // ajustar si el negocio maneja plazos distintos.
                $linea['due_date'] = $factura->created_at->copy()->addDays(30)->toDateString();
            }

            $lineas[] = $linea;
        }

        return $lineas;
    }

    /** Catálogo DIAN de medios de pago — ver nota de simplificación arriba. */
    private function codigoMedioPago(string $metodoPago): string
    {
        return match ($metodoPago) {
            'tarjeta' => '48',                              // Tarjeta crédito
            'transferencia', 'nequi', 'daviplata' => '42',  // Consignación/transferencia
            'credito' => '1',                                // Instrumento no definido (a crédito)
            default => '10',                                 // Efectivo
        };
    }

    private function cliente(?Tercero $cliente): array
    {
        // Sin cliente registrado, o "Consumidor Final" (id=1 en este
        // sistema): Factus documenta este identificador genérico.
        // legal_organization_code es obligatorio según la documentación de
        // campos de Factus (a diferencia de tribute_code/responsibilities,
        // que si tienen ⁠"opcional" marcado) — se envía "2" (persona natural)
        // por consistencia con identification_document_code=13 (cédula).
        if (!$cliente || $cliente->id === 1 || !($cliente->nit || $cliente->cedula)) {
            return [
                'identification_document_code' => '13',
                'identification' => '222222222222',
                'names' => 'Consumidor Final',
                'legal_organization_code' => '2',
            ];
        }

        $esNit = filled($cliente->nit);

        return array_filter([
            'identification_document_code' => $esNit ? '31' : '13',
            // Factus exige el NIT SIN el dígito de verificación ni el guion
            // que lo separa (documentación de campos de factura) — si no se
            // envía customer.dv, la API lo calcula sola. Tercero.nit es
            // texto libre y puede traer "900123456-7"; se limpia aquí.
            'identification' => $esNit ? $this->soloDigitos($cliente->nit) : $cliente->cedula,
            'company' => $esNit ? $cliente->razon_social : null,
            'names' => $esNit ? null : trim("{$cliente->nombre} {$cliente->apellido}"),
            'address' => $cliente->direccion,
            'email' => $cliente->email,
            'phone' => $cliente->celular,
            'legal_organization_code' => $esNit ? '1' : '2',
            'tribute_code' => 'ZZ',
            'country_code' => 'CO',
            // Faltan municipality_code (código DANE) y responsibilities —
            // Tercero no los tiene todavía. municipality_code es opcional
            // según Factus, y responsibilities cae al default R-99-PN si se
            // omite, así que esto no debería bloquear la transmisión, pero
            // deja la factura de un cliente real incompleta geográficamente.
        ], fn ($valor) => $valor !== null);
    }

    /**
     * TerceroController ya normaliza el NIT al guardarlo (ver
     * Tercero::soloDigitos()); esto queda como red de seguridad para
     * terceros guardados antes de ese fix, que aún puedan tener el guion,
     * el DV pegado o puntos en el campo.
     */
    private function soloDigitos(string $identificacion): string
    {
        return \App\Models\Tercero::soloDigitos($identificacion);
    }

    private function items(Collection $detalles): array
    {
        return $detalles->map(function ($detalle) {
            $producto = $detalle->producto;
            $ivaPorcentaje = (float) ($producto->iva_ventas ?? 0);

            // precio_unitario ya incluye IVA en este sistema (ver
            // FacturacionContableService::calcularDesglose) — Factus espera
            // el precio SIN IVA, hay que extraerlo primero.
            $precioConIva = (float) $detalle->precio_unitario;
            $precioSinIva = $ivaPorcentaje > 0
                ? round($precioConIva / (1 + $ivaPorcentaje / 100), 2)
                : $precioConIva;

            return [
                'code_reference' => $producto->codigo,
                'name' => $producto->descripcion,
                'quantity' => number_format((float) $detalle->cantidad, 2, '.', ''),
                'discount_rate' => '0.00',
                'price' => number_format($precioSinIva, 2, '.', ''),
                'unit_measure_code' => '94', // Unidad — ver nota de simplificación arriba
                'standard_code' => '999',    // Sin clasificar — ver nota de simplificación arriba
                'taxes' => $ivaPorcentaje > 0
                    ? [['code' => '01', 'rate' => number_format($ivaPorcentaje, 2, '.', '')]]
                    : [],
            ];
        })->all();
    }

    private function numberingRangeId(?string $codigoPrefijo): ?int
    {
        if (!$codigoPrefijo) {
            return null;
        }

        return Prefijo::where('codigo', $codigoPrefijo)->value('numbering_range_id_factus');
    }

    // ── HTTP + autenticación ─────────────────────────────────────────────

    /**
     * $deleteEndpoint (con el placeholder {reference_code}) es el endpoint
     * "Eliminar no validada" de este mismo tipo de documento. Factus
     * documenta que un reintento con el mismo reference_code puede
     * responder 409 "Se encontró una factura pendiente por enviar a la
     * DIAN" (p. ej. si un intento anterior sí llegó pero nuestro lado no
     * alcanzó a recibir la respuesta antes del timeout) — hay que eliminar
     * ese borrador antes de poder reintentar, o quedaría reintentando el
     * mismo 409 para siempre.
     */
    private function enviar(string $endpoint, array $payload, ?string $deleteEndpoint = null): ResultadoFacturaElectronica
    {
        try {
            $respuesta = Http::withToken($this->token())
                ->timeout(30)
                ->acceptJson()
                ->post("{$this->baseUrl}{$endpoint}", $payload);
        } catch (\Throwable $e) {
            return new ResultadoFacturaElectronica(estado: 'error', mensaje: $e->getMessage());
        }

        if ($respuesta->status() === 409 && $deleteEndpoint) {
            $referenceCode = $payload['reference_code'];
            $urlEliminar = $this->baseUrl . str_replace('{reference_code}', $referenceCode, $deleteEndpoint);

            try {
                Http::withToken($this->token())->timeout(30)->delete($urlEliminar);
            } catch (\Throwable) {
                // No crítico: si la limpieza falla, el próximo reintento
                // programado la vuelve a intentar de todas formas.
            }

            return new ResultadoFacturaElectronica(
                estado: 'error',
                mensaje: "Factus reportó un borrador sin validar pendiente para '{$referenceCode}' (probablemente un intento anterior sí llegó pero no confirmó la respuesta a tiempo). Se solicitó eliminarlo — el próximo reintento debería completarse limpio."
            );
        }

        if (!$respuesta->successful()) {
            return new ResultadoFacturaElectronica(
                estado: 'error',
                mensaje: "Factus respondió {$respuesta->status()}: {$respuesta->body()}"
            );
        }

        $datos = $respuesta->json('data') ?? [];

        return new ResultadoFacturaElectronica(
            estado: ($datos['is_validated'] ?? false) ? 'aceptada' : 'enviada',
            cufe: $datos['cufe'] ?? null,
            numeroProveedor: $datos['number'] ?? null,
            pdfUrl: $datos['links']['public_url'] ?? null,
            qrTexto: $datos['links']['qr'] ?? null,
            mensaje: $respuesta->json('message'),
        );
    }

    /** Factus vence el token cada hora; se cachea con margen de 10 minutos. */
    private function token(): string
    {
        return Cache::remember('factus_access_token', now()->addMinutes(50), function () {
            $respuesta = Http::asForm()->acceptJson()->post("{$this->baseUrl}/oauth/token", [
                'grant_type' => 'password',
                'client_id' => config('services.factus.client_id'),
                'client_secret' => config('services.factus.client_secret'),
                'username' => config('services.factus.username'),
                'password' => config('services.factus.password'),
            ]);

            if (!$respuesta->successful()) {
                throw new \RuntimeException("No se pudo autenticar con Factus: {$respuesta->body()}");
            }

            return $respuesta->json('access_token');
        });
    }
}
