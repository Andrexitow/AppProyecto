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
 *   (sin clasificar) — NussoraPos no tiene hoy un catálogo de unidades/UNSPSC
 *   por producto. Ajustar si un producto puntual lo necesita.
 * - Solo se mapea IVA (tax code "01"). ico_ventas/imp_saludable de Producto
 *   no se envían todavía — si algún producto del documento los tiene
 *   configurados, verificarImpuestosSoportados() rechaza la transmisión en
 *   vez de mandar una factura fiscal incompleta en silencio.
 * - Un cliente real (no "Consumidor Final") necesita municipality_code
 *   (código DANE) — confirmado contra el sandbox real (2026-09-14) que
 *   Factus SÍ lo exige (rechaza con 422 si falta), a pesar de que la
 *   documentación no lo marcaba como obligatorio. Tercero::codigo_municipio
 *   lo guarda; verificarClienteCompleto() rechaza la transmisión con un
 *   mensaje claro si un cliente real no lo tiene configurado.
 * - codigoMedioPago() es un mapeo simplificado del catálogo DIAN de medios
 *   de pago; revísalo si tu operación necesita distinguir tarjeta débito de
 *   crédito, o un medio no cubierto aquí.
 * - due_date de una venta a crédito se fija en 30 días desde la venta
 *   (NussoraPos no captura hoy un plazo de crédito por factura).
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

        if ($error = $this->verificarImpuestosSoportados($factura->detalles)) {
            return $error;
        }

        if ($error = $this->verificarClienteCompleto($factura->cliente)) {
            return $error;
        }

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

        if ($rangoId = $this->numberingRangeId($factura->caja?->prefijo, 'factura')) {
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

        if ($error = $this->verificarImpuestosSoportados($nota->detalles)) {
            return $error;
        }

        if ($error = $this->verificarClienteCompleto($factura->cliente)) {
            return $error;
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

        $tipoRango = $nota->tipo === 'credito' ? 'nota_credito' : 'nota_debito';
        if ($rangoId = $this->numberingRangeId($factura->caja?->prefijo, $tipoRango)) {
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
            // Ver mismo fallback cufe/cude en enviar().
            cufe: $datos['cufe'] ?? $datos['cude'] ?? $documento->cufe,
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

    /**
     * items() solo mapea IVA — si algún producto de este documento tiene
     * ICO o impuesto saludable configurado, transmitirlo igual dejaría la
     * representación electrónica incompleta (el cliente vería un total sin
     * ese impuesto). Mejor rechazar la transmisión con un mensaje claro que
     * mandar una factura fiscal que no cuadra. Hoy no hay ningún producto
     * con estos impuestos, así que esto no bloquea nada en la práctica —
     * pero avisa el día que alguien configure uno mientras Factus esté
     * habilitado, en vez de fallar en silencio.
     */
    private function verificarImpuestosSoportados(Collection $detalles): ?ResultadoFacturaElectronica
    {
        $producto = $detalles
            ->pluck('producto')
            ->filter()
            ->first(fn ($producto) => (float) ($producto->ico_ventas ?? 0) > 0 || (float) ($producto->imp_saludable ?? 0) > 0);

        if (!$producto) {
            return null;
        }

        return new ResultadoFacturaElectronica(
            estado: 'error',
            mensaje: "El producto '{$producto->descripcion}' tiene ICO o impuesto saludable configurado, y hoy no se transmiten a Factus (solo se mapea IVA). Quita esos impuestos del producto o contacta a soporte para completar el mapeo DIAN antes de facturar electrónicamente con él."
        );
    }

    /**
     * Confirmado contra el sandbox real de Factus (2026-09-14): a pesar de
     * que la documentación no lo marcaba como obligatorio, un cliente que
     * no sea "Consumidor Final" SIN código de municipio hace que Factus
     * rechace la factura con 422 "El campo código municipio es
     * obligatorio." Mejor avisar aquí, antes de gastar la llamada, con un
     * mensaje que diga exactamente qué falta y dónde arreglarlo.
     */
    private function verificarClienteCompleto(?Tercero $cliente): ?ResultadoFacturaElectronica
    {
        $esConsumidorFinal = !$cliente || $cliente->id === 1 || !($cliente->nit || $cliente->cedula);

        if ($esConsumidorFinal || filled($cliente->codigo_municipio)) {
            return null;
        }

        return new ResultadoFacturaElectronica(
            estado: 'error',
            mensaje: "El cliente '{$cliente->nombre_completo}' no tiene código de municipio (DANE) configurado — Factus lo exige para cualquier cliente que no sea Consumidor Final. Complétalo en Terceros antes de facturarle electrónicamente."
        );
    }

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
                // Factus exige due_date cuando payment_form=2. Antes era un
                // "30 días desde la venta" fijo; ahora se lee el vencimiento
                // ya fotografiado en la factura (fecha_vencimiento), que
                // FacturacionController calculó a partir del plazo pactado
                // con el cliente (Tercero::dias_credito) o la política
                // general si no tiene uno propio. El fallback a 30 días
                // solo cubre facturas a crédito emitidas antes de este
                // cambio, que no tienen fecha_vencimiento guardada.
                $linea['due_date'] = $factura->fecha_vencimiento?->toDateString()
                    ?? $factura->created_at->copy()->addDays(\App\Http\Controllers\FacturacionController::DIAS_CREDITO_POR_DEFECTO)->toDateString();
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
            // Confirmado contra el sandbox real (2026-09-14): a pesar de lo
            // que decía la documentación, Factus SÍ exige municipality_code
            // para un cliente real — verificarClienteCompleto() rechaza la
            // transmisión antes de llegar aquí si falta, así que si llegamos
            // a este punto ya sabemos que $cliente->codigo_municipio existe.
            'municipality_code' => $cliente->codigo_municipio,
            // responsibilities sí cae al default R-99-PN si se omite —
            // confirmado, no hizo falta para que pasara la validación.
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

    /**
     * Factura, nota crédito y nota débito NO comparten espacio de
     * numbering_range_id en Factus — confirmado contra el sandbox real
     * (2026-09-14): la cuenta de prueba tiene dos rangos activos de Nota
     * Crédito, así que usar ahí el mismo id que Factura (que sí tiene un
     * único rango) hace que Factus rechace una u otra transmisión según
     * cuál se haya guardado.
     */
    private function numberingRangeId(?string $codigoPrefijo, string $tipoDocumento = 'factura'): ?int
    {
        if (!$codigoPrefijo) {
            return null;
        }

        $columna = match ($tipoDocumento) {
            'nota_credito' => 'numbering_range_id_nota_credito_factus',
            'nota_debito' => 'numbering_range_id_nota_debito_factus',
            default => 'numbering_range_id_factus',
        };

        return Prefijo::where('codigo', $codigoPrefijo)->value($columna);
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
            // Confirmado contra el sandbox real (2026-09-14): una factura
            // trae 'cufe', pero una nota crédito/débito NO — Factus la
            // identifica con 'cude' en su lugar. Sin este fallback, cufe
            // quedaba vacío en toda nota, aunque se hubiera validado bien.
            cufe: $datos['cufe'] ?? $datos['cude'] ?? null,
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
