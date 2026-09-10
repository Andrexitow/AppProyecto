<?php

namespace App\Services;

use App\Models\Factura;
use App\Models\MetodoPagoContable;
use App\DataTransferObjects\ContabilidadData;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class FacturacionContableService
{
    public function __construct(
        private readonly ContabilidadService $contabilidadService
    ) {}

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
        $factura->loadMissing('detalles.producto.integracionContable.procesoContable');

        $desglose = $this->calcularDesglose($factura->detalles);

        // Resuelto UNA sola vez por factura: la misma cuenta (Caja o Banco)
        // aplica para todos los grupos de IVA de esa factura.
        $overridesCuenta = [
            'CUENTA_CAJA' => $this->resolverClaveCuentaPago($factura->metodo_pago),
        ];

        foreach ($desglose as $grupo) {
            $this->procesarGrupo($factura, $grupo, $overridesCuenta);
        }
    }

    /**
     * Traduce el método de pago de la factura a la clave de configuración
     * contable correspondiente (CUENTA_CAJA o CUENTA_BANCO).
     * Si no hay mapeo (ej. 'mixto', o uno nuevo no seedeado), cae a CUENTA_CAJA
     * por defecto y deja advertencia para revisión manual.
     */
    private function resolverClaveCuentaPago(string $metodoPago): string
    {
        $mapeo = MetodoPagoContable::where('metodo_pago', $metodoPago)
            ->where('estado', true)
            ->first();

        if (!$mapeo) {
            Log::warning(
                "No hay mapeo contable para el método de pago '{$metodoPago}'. Se usará CUENTA_CAJA por defecto; revisar manualmente."
            );
            return 'CUENTA_CAJA';
        }

        return $mapeo->configuracion_clave;
    }

    private function procesarGrupo(Factura $factura, array $grupo, array $overridesCuenta): void
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

        $this->contabilidadService->procesar(
            $procesoContable->codigo,
            new ContabilidadData(
                modulo: 'POS',
                valores: [
                    'TOTAL'    => $grupo['base'] + $grupo['iva'],
                    'SUBTOTAL' => $grupo['base'],
                    'IVA'      => $grupo['iva'],
                ],
                terceroId: $factura->cliente_id,
                usuarioId: $factura->user_id,
                documento: $factura->numero_factura,
                documentoId: $factura->id,
                observacion: "Venta POS {$factura->numero_factura} - {$integracion->nombre}",
                overridesCuenta: $overridesCuenta,
                referenciaGrupo: $integracion->codigo
            )
        );
    }
}
