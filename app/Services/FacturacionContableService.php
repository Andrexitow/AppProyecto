<?php

namespace App\Services;

use App\Models\ComprobanteContable;
use App\Models\Factura;
use App\Models\MetodoPagoContable;
use App\Models\MovimientoInventario;
use App\DataTransferObjects\ContabilidadData;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class FacturacionContableService
{
    public function __construct(
        private readonly ContabilidadService $contabilidadService,
        private readonly PeriodoContableService $periodoContableService
    ) {}

    /**
     * Reversa la contabilización de una factura al anularla. Antes,
     * FacturaController::anular() reponía el stock con SQL crudo pero dejaba
     * el/los comprobantes en CONTABILIZADO para siempre — la misma clase de
     * bug que ya se había corregido en CompraContableService::prepararReversion().
     * Marcar (no borrar) los comprobantes como ANULADO es lo que permite que
     * revertirAnulacion() pueda llamar a contabilizar() de nuevo sin chocar
     * con el chequeo de "ya está contabilizado" de ContabilidadService.
     */
    public function anular(Factura $factura): void
    {
        $this->periodoContableService->assertAbierto($factura->created_at);

        // documento_origen_id solo (el id numérico de la factura) no basta:
        // una Compra con el mismo id numérico también sería documento_origen_id
        // igual, solo que con documento_origen='COMPRA' en vez del número de
        // factura. Se filtran ambos para no anular el comprobante equivocado.
        ComprobanteContable::where('documento_origen', $factura->numero_factura)
            ->where('documento_origen_id', $factura->id)
            ->where('estado', 'CONTABILIZADO')
            ->update(['estado' => 'ANULADO']);

        $factura->update([
            'total_pagado' => 0,
            'saldo_pendiente' => 0,
            'estado_pago' => 'pendiente',
        ]);
    }

    /**
     * Agrupa cualquier colección de detalles (PedidoDetalle o FacturaDetalle,
     * ambos exponen ->subtotal y ->producto->integracionContable) por
     * integración contable, y calcula base/IVA por grupo.
     *
     * Único punto de verdad para el cálculo fiscal. Se usa tanto para
     * calcular los totales de la factura ANTES de guardarla, como para
     * contabilizar DESPUÉS de guardarla.
     */
    public function calcularDesglose(Collection $detalles): Collection
    {
        return $detalles
            ->groupBy(fn($detalle) => $detalle->producto->integracion_contable_id)
            ->map(function (Collection $detallesGrupo) {
                $integracion = $detallesGrupo->first()->producto->integracionContable;

                $base = 0;
                $iva  = 0;

                foreach ($detallesGrupo as $detalle) {

                    $subtotal = $detalle->subtotal;

                    $producto = $detalle->producto;

                    $porcentajeIva = (float) ($producto->iva_ventas ?? 0);

                    $ivaLinea = 0;

                    if ($porcentajeIva > 0) {

                        $ivaLinea = round(
                            $subtotal * $porcentajeIva /
                                (100 + $porcentajeIva),
                            2
                        );
                    }

                    $base += ($subtotal - $ivaLinea);

                    $iva += $ivaLinea;
                }

                return [
                    'integracion' => $integracion,
                    'base'        => $base,
                    'iva'         => $iva,
                ];
            });
    }

    /**
     * Totales agregados (para poblar Factura::subtotal / Factura::impuestos
     * antes de hacer el create()).
     */
    public function calcularTotales(Collection $detalles): array
    {
        $desglose = $this->calcularDesglose($detalles);

        return [
            'base' => (float) $desglose->sum('base'),
            'iva'  => (float) $desglose->sum('iva'),
        ];
    }

    /**
     * Contabiliza una factura ya persistida (con sus detalles ya creados).
     * Un asiento por cada integración contable involucrada.
     */
    public function contabilizar(Factura $factura): void
    {
        $factura->loadMissing('detalles.producto.integracionContable.procesoContable', 'pagos');

        $desglose = $this->calcularDesglose($factura->detalles);
        $costosPorIntegracion = $this->calcularCostoPorIntegracion($factura);
        $pagos = $this->resolverPagos($factura);
        $pagosPorGrupo = $this->repartirPagosPorGrupo($pagos, $desglose);

        foreach ($desglose as $integracionId => $grupo) {
            $grupo['costo'] = (float) ($costosPorIntegracion[$integracionId] ?? 0);
            $grupo['pagos'] = $pagosPorGrupo[$integracionId] ?? [];
            $this->procesarGrupo($factura, $grupo);
        }

        $this->contabilizarPropina($factura, $pagos, $pagosPorGrupo);
    }

    /**
     * La propina se contabiliza aparte de la venta, como ingreso propio del
     * negocio (decisión de negocio: no es del mesero hasta que el negocio
     * se la pague — ver salida de caja con concepto "Pago de propina" y el
     * tercero/mesero como quien la recibe). Antes quedaba fuera de la
     * contabilidad por completo: el efectivo sí entraba a caja, pero no
     * había ningún registro de cuánto se había cobrado en propinas.
     *
     * No hay una línea de "propina" en $desglose (no es precio de ningún
     * producto), así que se calcula por diferencia: resolverPagos() incluye
     * la propina en el total pagado, pero repartirPagosPorGrupo() solo
     * consumió de ahí lo que cada grupo de venta necesitaba (base+iva). Lo
     * que sobra de cada forma de pago, después de cubrir todos los grupos,
     * es exactamente la propina — ya repartida por forma de pago.
     */
    private function contabilizarPropina(Factura $factura, Collection $pagos, array $pagosPorGrupo): void
    {
        $propina = round((float) $factura->propina, 2);

        if ($propina <= 0.001) {
            return;
        }

        $totalPorClave = [];
        foreach ($pagos as $pago) {
            $clave = $this->claveCuentaDePago($pago['metodo_pago']);
            $totalPorClave[$clave] = ($totalPorClave[$clave] ?? 0) + (float) $pago['valor'];
        }

        $asignadoPorClave = [];
        foreach ($pagosPorGrupo as $montos) {
            foreach ($montos as $clave => $valor) {
                $asignadoPorClave[$clave] = ($asignadoPorClave[$clave] ?? 0) + $valor;
            }
        }

        $sobrantePorClave = [];
        foreach ($totalPorClave as $clave => $total) {
            $sobra = round($total - ($asignadoPorClave[$clave] ?? 0), 2);
            if ($sobra > 0.001) {
                $sobrantePorClave[$clave] = $sobra;
            }
        }

        if (empty($sobrantePorClave)) {
            // No debería pasar si propina > 0 (implicaría que la venta ni
            // siquiera cubrió su propio subtotal+iva), pero no hay nada
            // seguro que contabilizar si pasara — mejor no reventar la venta.
            Log::warning("Factura {$factura->numero_factura}: hay propina (\${$propina}) pero no se pudo determinar de qué forma de pago salió. No se contabilizó la propina.");
            return;
        }

        $this->contabilidadService->procesar(
            'VENTA_PROPINA',
            new ContabilidadData(
                modulo: 'POS',
                valores: array_merge(['PROPINA' => $propina], $sobrantePorClave),
                terceroId: $factura->cliente_id,
                usuarioId: $factura->user_id,
                documento: $factura->numero_factura,
                documentoId: $factura->id,
                observacion: "Propina venta POS {$factura->numero_factura}",
            )
        );
    }

    /**
     * Formas de pago de la factura: si se vendió 'mixto' (factura_pagos con
     * el desglose real capturado en el cierre de mesa), se usan esas líneas;
     * si no, se sintetiza una sola línea con el método único de siempre.
     * Unifica ambos casos en un solo camino de cálculo.
     */
    public function resolverPagos(Factura $factura): Collection
    {
        if ($factura->pagos->isNotEmpty()) {
            return $factura->pagos->map(fn ($pago) => [
                'metodo_pago' => $pago->metodo_pago,
                'valor' => (float) $pago->valor,
            ]);
        }

        return collect([[
            'metodo_pago' => $factura->metodo_pago,
            // Factura::total incluye la propina (no así los grupos de
            // calcularDesglose, que solo suman productos); el sobrante queda
            // simplemente sin consumir en el reparto, sin desbalancear nada.
            'valor' => (float) $factura->total,
        ]]);
    }

    /**
     * Reparte los pagos de la factura entre los grupos de integración
     * contable en "cascada": se toma de cada forma de pago hasta agotarla
     * antes de pasar a la siguiente. Al ser resta exacta de dinero (sin
     * dividir ni prorratear), lo repartido en cada grupo siempre cuadra
     * exacto con su TOTAL — no hay redondeos que absorber.
     *
     * Devuelve, por integracion_id, [clave_configuracion => monto], p. ej.
     * ['CUENTA_CAJA' => 30000, 'CUENTA_BANCO' => 20000].
     */
    public function repartirPagosPorGrupo(Collection $pagos, Collection $desglose): array
    {
        $restantes = $pagos->map(fn ($pago) => [
            'clave' => $this->claveCuentaDePago($pago['metodo_pago']),
            'restante' => (float) $pago['valor'],
        ])->values()->all();

        $resultado = [];

        foreach ($desglose as $integracionId => $grupo) {
            $necesario = round($grupo['base'] + $grupo['iva'], 2);
            $montos = [];

            foreach ($restantes as &$linea) {
                if ($necesario <= 0.001) {
                    break;
                }
                if ($linea['restante'] <= 0.001) {
                    continue;
                }

                $tomar = min($linea['restante'], $necesario);
                $montos[$linea['clave']] = ($montos[$linea['clave']] ?? 0) + $tomar;
                $linea['restante'] -= $tomar;
                $necesario -= $tomar;
            }
            unset($linea);

            $resultado[$integracionId] = $montos;
        }

        return $resultado;
    }

    /**
     * Cuenta contable (Caja/Banco/Clientes) de un método de pago.
     * A diferencia del comportamiento anterior, un método no parametrizado
     * SE RECHAZA (no se registra silenciosamente como Caja) — hay que
     * mapearlo primero en Métodos de Pago.
     */
    private function claveCuentaDePago(string $metodoPago): string
    {
        $mapeo = MetodoPagoContable::where('metodo_pago', $metodoPago)
            ->where('estado', true)
            ->first();

        if (!$mapeo) {
            throw new \RuntimeException(
                "El método de pago '{$metodoPago}' no está parametrizado contablemente. Configúrelo en Métodos de Pago antes de facturar con él."
            );
        }

        return $mapeo->configuracion_clave;
    }

    /**
     * Costo de ventas por grupo de integración contable, tomado de los
     * movimientos de kardex (SALIDA) que `LegacyDocumentSyncService::factura()`
     * ya dejó registrados con el costo promedio vigente al momento de la
     * venta. Se agrupa por integración para poder generar, junto al ingreso,
     * el Débito Costo de Ventas / Crédito Inventario de cada grupo.
     *
     * Productos con afecta_inventario = false no generan movimiento de
     * kardex, así que su costo queda en 0 (correcto: no hay salida real de
     * inventario que reconocer).
     */
    private function calcularCostoPorIntegracion(Factura $factura): Collection
    {
        if (!$factura->documento_id) {
            return collect();
        }

        return MovimientoInventario::where('documento_id', $factura->documento_id)
            ->with('producto')
            ->get()
            ->groupBy(fn ($movimiento) => $movimiento->producto?->integracion_contable_id)
            ->map(fn (Collection $grupo) => (float) $grupo->sum(fn ($m) => ($m->tipo === 'SALIDA' ? 1 : -1) * $m->valor_movimiento));
    }

    private function procesarGrupo(Factura $factura, array $grupo): void
    {
        $integracion = $grupo['integracion'];

        if (!$integracion) {
            Log::warning(
                "Factura {$factura->numero_factura}: hay productos sin integracion_contable_id asignada. Ese grupo NO se contabilizó."
            );
            return;
        }

        $procesoContable = $integracion->procesoContable;

        if (!$procesoContable) {
            Log::warning(
                "Factura {$factura->numero_factura}: la integración '{$integracion->nombre}' no tiene proceso_contable_id válido. Ese grupo NO se contabilizó."
            );
            return;
        }

        // Las claves de $grupo['pagos'] (CUENTA_CAJA / CUENTA_BANCO / CUENTA_CLIENTES)
        // son, a propósito, las mismas que su origen_valor en la plantilla: cada
        // línea de pago se debita a la cuenta que su propio nombre indica.
        $valores = array_merge([
            'SUBTOTAL' => $grupo['base'],
            'IVA'      => $grupo['iva'],
            'COSTO'    => $grupo['costo'] ?? 0.0,
        ], $grupo['pagos'] ?? []);

        $this->contabilidadService->procesar(
            $procesoContable->codigo,
            new ContabilidadData(
                modulo: 'POS',
                valores: $valores,
                terceroId: $factura->cliente_id,
                usuarioId: $factura->user_id,
                documento: $factura->numero_factura,
                documentoId: $factura->id,
                observacion: "Venta POS {$factura->numero_factura} - {$integracion->nombre}",
                referenciaGrupo: $integracion->codigo
            )
        );
    }
}
