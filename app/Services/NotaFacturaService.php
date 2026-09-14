<?php

namespace App\Services;

use App\DataTransferObjects\ContabilidadData;
use App\Models\Factura;
use App\Models\NotaFactura;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Notas crédito/débito de factura (hallazgo #4 de la auditoría DIAN):
 * NOTA_CREDITO/NOTA_DEBITO ya existían parametrizadas contablemente, pero no
 * había ningún flujo real para emitirlas — FacturaController::anular()
 * incluso le decía al usuario "haz una nota crédito" para un caso que no se
 * podía hacer.
 *
 * Alcance de esta primera versión: corrección contable/de inventario
 * interna, sobre una o más líneas de una factura ya emitida (devolución
 * parcial, corrección de precio). NO transmite nada a la DIAN — eso requiere
 * la integración con un proveedor tecnológico habilitado (hallazgo #3 de la
 * misma auditoría, deliberadamente fuera de alcance aquí).
 *
 * Reutiliza a propósito el mismo cálculo fiscal y de reparto de pagos que
 * FacturacionContableService usa para las ventas (mismo IVA por producto,
 * mismo reparto proporcional entre Caja/Banco/Clientes según cómo se pagó
 * originalmente la factura), para que la nota sea una reversión simétrica
 * exacta y no una reimplementación paralela con reglas ligeramente distintas.
 */
class NotaFacturaService
{
    public function __construct(
        private readonly FacturacionContableService $facturacionContableService,
        private readonly ContabilidadService $contabilidadService,
        private readonly PeriodoContableService $periodoContableService,
    ) {
    }

    /**
     * @param  array<int, array{factura_detalle_id: int, cantidad: float}>  $lineas
     */
    public function emitir(Factura $factura, string $tipo, array $lineas, string $motivo, bool $restaurarInventario, int $usuarioId): NotaFactura
    {
        if (!in_array($tipo, ['credito', 'debito'], true)) {
            throw ValidationException::withMessages(['tipo' => "Tipo de nota inválido: '{$tipo}'."]);
        }

        if ($factura->estado === 'anulada') {
            throw ValidationException::withMessages(['factura' => 'No se puede emitir una nota sobre una factura anulada.']);
        }

        $this->periodoContableService->assertAbierto(now());

        return DB::transaction(function () use ($factura, $tipo, $lineas, $motivo, $restaurarInventario, $usuarioId) {
            $factura->loadMissing('detalles.producto.integracionContable.procesoContable', 'pagos', 'caja');

            $detallesNota = $this->validarYArmarLineas($factura, $tipo, $lineas);

            // Mismo cálculo fiscal que una venta (agrupado por integración
            // contable): así la nota queda simétrica a como se facturó.
            $desglose = $this->facturacionContableService->calcularDesglose(
                $detallesNota->map(fn ($d) => (object) ['subtotal' => $d->subtotal, 'producto' => $d->producto])
            );

            $subtotalNota = (float) $desglose->sum('base');
            $ivaNota = (float) $desglose->sum('iva');
            $restaura = $tipo === 'credito' && $restaurarInventario;

            $nota = NotaFactura::create([
                'factura_id' => $factura->id,
                'tipo' => $tipo,
                'fecha' => now()->toDateString(),
                'motivo' => $motivo,
                'subtotal' => $subtotalNota,
                'iva' => $ivaNota,
                'total' => round($subtotalNota + $ivaNota, 2),
                'restaura_inventario' => $restaura,
                'user_id' => $usuarioId,
            ]);

            foreach ($detallesNota as $d) {
                $nota->detalles()->create([
                    'factura_detalle_id' => $d->facturaDetalle->id,
                    'producto_id' => $d->producto->id,
                    'cantidad' => $d->cantidad,
                    'precio_unitario' => $d->precioUnitario,
                    'subtotal' => $d->subtotal,
                ]);
            }

            $costosPorIntegracion = $restaura
                ? $this->restaurarInventarioYCalcularCosto($factura, $detallesNota)
                : collect();

            // El reembolso se reparte entre Caja/Banco/Clientes EXACTAMENTE
            // en la misma proporción con la que se pagó la factura original
            // (ver FacturacionContableService::repartirPagosPorGrupo): si se
            // pagó 60% efectivo / 40% tarjeta, se devuelve en esa misma
            // proporción, sin pedirle al usuario un dato que ya se conoce.
            $pagosPorGrupo = $this->facturacionContableService->repartirPagosPorGrupo(
                $this->facturacionContableService->resolverPagos($factura),
                $desglose
            );

            $procesoCodigo = $tipo === 'credito' ? 'NOTA_CREDITO' : 'NOTA_DEBITO';

            foreach ($desglose as $integracionId => $grupo) {
                $this->procesarGrupoNota($factura, $nota, $procesoCodigo, $grupo, [
                    'COSTO' => (float) ($costosPorIntegracion[$integracionId] ?? 0),
                    ...($pagosPorGrupo[$integracionId] ?? []),
                ], $usuarioId, $motivo);
            }

            return $nota->fresh('detalles');
        });
    }

    private function validarYArmarLineas(Factura $factura, string $tipo, array $lineas): Collection
    {
        if (empty($lineas)) {
            throw ValidationException::withMessages(['lineas' => 'Debe incluir al menos una línea.']);
        }

        return collect($lineas)->map(function (array $linea) use ($factura, $tipo) {
            $facturaDetalle = $factura->detalles->firstWhere('id', $linea['factura_detalle_id'] ?? null);

            if (!$facturaDetalle) {
                throw ValidationException::withMessages(['lineas' => 'Una de las líneas seleccionadas no pertenece a esta factura.']);
            }

            $cantidad = (float) ($linea['cantidad'] ?? 0);
            if ($cantidad <= 0) {
                throw ValidationException::withMessages(['lineas' => 'La cantidad debe ser mayor a cero.']);
            }

            // Solo se puede notar (crédito o débito) hasta lo que la línea
            // original tiene disponible, descontando lo que otras notas del
            // MISMO tipo ya hayan cubierto — así dos notas crédito parciales
            // no pueden sumar más que lo que en verdad se vendió.
            $yaNotado = (float) $facturaDetalle->notaDetalles()
                ->whereHas('nota', fn ($q) => $q->where('tipo', $tipo))
                ->sum('cantidad');
            $disponible = (float) $facturaDetalle->cantidad - $yaNotado;

            if ($cantidad > $disponible + 0.0001) {
                throw ValidationException::withMessages([
                    'lineas' => "La cantidad a notar de '{$facturaDetalle->producto->descripcion}' ({$cantidad}) supera lo disponible ({$disponible}).",
                ]);
            }

            $precioUnitario = (float) $facturaDetalle->precio_unitario;

            return (object) [
                'facturaDetalle' => $facturaDetalle,
                'producto' => $facturaDetalle->producto,
                'cantidad' => $cantidad,
                'precioUnitario' => $precioUnitario,
                'subtotal' => round($cantidad * $precioUnitario, 2),
            ];
        });
    }

    /**
     * Reingresa a bodega lo devuelto y calcula su costo (por integración)
     * para la línea de reversa de Costo de Ventas / Inventario.
     *
     * Ajusta `inventarios.stock` directamente en vez de dejar un movimiento
     * de kardex — el mismo patrón ya establecido en FacturaController::
     * anular()/revertirAnulacion() para reversiones. Usa el costo promedio
     * VIGENTE (no el de la venta original): es como KardexService::registrar()
     * ya trata cualquier entrada sin costo explícito (p. ej. traslados).
     */
    private function restaurarInventarioYCalcularCosto(Factura $factura, Collection $detallesNota): Collection
    {
        $bodegaId = $factura->caja?->bodega_id;
        if (!$bodegaId) {
            return collect();
        }

        $costosPorIntegracion = [];

        foreach ($detallesNota as $d) {
            if (!$d->producto->afecta_inventario) {
                continue;
            }

            $inventario = DB::table('inventarios')
                ->where('producto_id', $d->producto->id)
                ->where('bodega_id', $bodegaId)
                ->lockForUpdate()
                ->first();

            $costoPromedio = (float) ($inventario->costo_promedio ?? 0);

            if ($inventario) {
                DB::table('inventarios')->where('id', $inventario->id)->increment('stock', $d->cantidad);
            } else {
                DB::table('inventarios')->insert([
                    'producto_id' => $d->producto->id,
                    'bodega_id' => $bodegaId,
                    'stock' => $d->cantidad,
                    'costo_promedio' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $integracionId = $d->producto->integracion_contable_id;
            $costosPorIntegracion[$integracionId] = ($costosPorIntegracion[$integracionId] ?? 0) + round($d->cantidad * $costoPromedio, 2);
        }

        return collect($costosPorIntegracion);
    }

    private function procesarGrupoNota(Factura $factura, NotaFactura $nota, string $procesoCodigo, array $grupo, array $valoresExtra, int $usuarioId, string $motivo): void
    {
        $integracion = $grupo['integracion'];
        if (!$integracion) {
            return;
        }

        $comprobante = $this->contabilidadService->procesar($procesoCodigo, new ContabilidadData(
            modulo: 'POS',
            valores: array_merge([
                'SUBTOTAL' => $grupo['base'],
                'IVA' => $grupo['iva'],
            ], $valoresExtra),
            terceroId: $factura->cliente_id,
            usuarioId: $usuarioId,
            // 'NOTA-{numero_factura}' (no el numero_factura desnudo) + el id de
            // la NOTA: facturas.id y notas_factura.id son autoincrementos
            // INDEPENDIENTES, así que un nota->id numéricamente igual al id de
            // la propia factura podía, combinado con el mismo documento_origen
            // de la venta, engancharse con el comprobante de la venta original
            // en cualquier consulta que solo filtre por (documento_origen,
            // documento_origen_id) sin además filtrar por proceso_contable_id.
            // El prefijo hace que el string por sí solo ya sea inequívoco.
            documento: 'NOTA-' . $factura->numero_factura,
            documentoId: $nota->id,
            observacion: ($procesoCodigo === 'NOTA_CREDITO' ? 'Nota crédito' : 'Nota débito') . " sobre {$factura->numero_factura} - {$integracion->nombre}: {$motivo}",
            referenciaGrupo: $integracion->codigo,
        ));

        $nota->update(['numero' => $comprobante->numero]);
    }
}
