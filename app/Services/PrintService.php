<?php

namespace App\Services;

use App\Models\ComandaPendiente;
use Illuminate\Support\Facades\Log;

/**
 * IMPORTANTE:
 * Esta versión de PrintService YA NO se conecta directamente a la impresora
 * (NetworkPrintConnector). Hostinger (y cualquier hosting compartido en la nube)
 * no tiene ruta de red hacia la IP local de la impresora del negocio (ej. 192.168.x.x),
 * por eso el socket se quedaba esperando y terminaba en 504 Gateway Timeout.
 *
 * Ahora cada método arma el texto del ticket/comanda y lo deja en la tabla
 * `comandas_pendientes`. Un agente local instalado en el negocio (PC/Raspberry Pi
 * conectado a la misma red de la impresora) consulta esa tabla via API cada
 * pocos segundos y es quien realmente imprime usando node-thermal-printer
 * o python-escpos.
 */
class PrintService
{
    public function imprimirFactura($factura, $impresora)
    {
        try {
            $lineaSimple   = str_repeat("-", 32) . "\n";
            $lineaDoble    = str_repeat("=", 32) . "\n";
            $lineaPunteada = str_repeat(".", 32) . "\n";

            $txt = "";
            $txt .= $lineaDoble;
            $txt .= "        APPSYSTEM S.A.S\n";
            $txt .= "      NIT: 901.456.789-1\n";
            $txt .= "    Piedecuesta, Santander\n";
            $txt .= "      Tel: 300 000 0000\n";
            $txt .= $lineaDoble;
            $txt .= "      FACTURA DE VENTA\n";
            $txt .= "No. " . $factura->numero_factura . "\n";
            $txt .= $lineaDoble;

            $txt .= "Fecha  : " . $factura->created_at->format('d/m/Y') . "\n";
            $txt .= "Hora   : " . $factura->created_at->format('h:i A') . "\n";
            $txt .= "Caja   : " . ($factura->caja->nombre ?? 'Principal') . "\n";
            $txt .= "Cajero : " . ($factura->user->name ?? 'Sistema') . "\n";

            $mesero = optional(optional($factura->mesa->pedidos->first())->mesero)->name ?? 'N/A';
            $txt .= "Mesero : " . $mesero . "\n";
            $txt .= $lineaPunteada;

            $nombreCliente = ($factura->cliente_id && $factura->cliente)
                ? strtoupper($factura->cliente->nombre)
                : 'CONSUMIDOR FINAL';

            $txt .= "CLIENTE: " . $nombreCliente . "\n";

            if ($factura->cliente && $factura->cliente->documento) {
                $txt .= "Doc.   : " . $factura->cliente->documento . "\n";
            }

            $txt .= $lineaDoble;

            $txt .= str_pad("CAN", 4) . str_pad("PRODUCTO", 18) . str_pad("VALOR", 10, " ", STR_PAD_LEFT) . "\n";
            $txt .= $lineaSimple;

            foreach ($factura->detalles as $detalle) {
                $nombre = substr($detalle->producto->descripcion, 0, 17);
                $txt .= str_pad($detalle->cantidad, 4)
                    . str_pad($nombre, 18)
                    . str_pad(number_format($detalle->subtotal, 0, ',', '.'), 10, " ", STR_PAD_LEFT) . "\n";

                if ($detalle->cantidad > 1) {
                    $unitario = $detalle->subtotal / $detalle->cantidad;
                    $txt .= str_pad("", 4) . "@ $" . number_format($unitario, 0, ',', '.') . " c/u\n";
                }
            }

            $txt .= $lineaDoble;

            $subtotal     = $factura->total;
            $propinaMonto = round($subtotal * 0.10);
            $pagoPropina  = !empty($factura->propina_aceptada) && $factura->propina_aceptada;
            $totalPagado  = $pagoPropina ? $subtotal + $propinaMonto : $subtotal;

            $txt .= "SUBTOTAL : $" . number_format($subtotal, 0, ',', '.') . "\n";

            if ($pagoPropina) {
                $txt .= "PROPINA (10%) : $" . number_format($propinaMonto, 0, ',', '.') . "\n";
            } else {
                $txt .= "Propina sug. : $" . number_format($propinaMonto, 0, ',', '.') . "\n";
                $txt .= "(No incluida en total)\n";
            }

            $txt .= $lineaSimple;
            $txt .= "TOTAL PAGADO\n";
            $txt .= "$" . number_format($totalPagado, 0, ',', '.') . "\n";
            $txt .= $lineaDoble;

            $txt .= "Metodo : " . strtoupper($factura->metodo_pago) . "\n";

            if ($factura->referencia_pago) {
                $txt .= "Ref.   : " . $factura->referencia_pago . "\n";
            }

            $txt .= $lineaPunteada;
            $txt .= $this->piePaginaTexto();
            $txt .= "\n\n\n\n";

            $this->encolar('factura', $impresora->id, $txt, null, $factura->id);

            return ['status' => 'success'];
        } catch (\Exception $e) {
            Log::error("Error encolando factura: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Encola un reporte de texto libre (ej. Existencias) hacia una impresora
     * de red concreta, igual que las comandas: el agente local instalado en
     * el negocio es quien de verdad abre el socket hacia $impresora->ip:puerto
     * y envía el ticket — este método solo lo deja listo en la cola.
     */
    public function imprimirInventario(string $texto, $impresora): array
    {
        try {
            $this->encolar('inventario', $impresora->id, $texto);

            return ['status' => 'success'];
        } catch (\Exception $e) {
            Log::error("Error encolando inventario: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Encola la comanda de cocina/bar (antes imprimía directo)
     */
    public function imprimirComanda($pedido, $items, $impresora, $nombreDestino)
    {
        try {
            $txt = "";
            $txt .= str_repeat("=", 42) . "\n";
            $txt .= "          ORDEN DE PISO\n";
            $txt .= str_repeat("-", 42) . "\n";
            $txt .= "MESA: " . ($pedido->mesa->numero ?? $pedido->mesa->nombre ?? 'S/N') . "\n";
            $txt .= "Mesero:  " . ($pedido->mesero->name ?? 'N/A') . "\n";
            $txt .= "Fecha:   " . now()->format('d/m/Y h:i A') . "\n";
            $txt .= str_repeat("-", 42) . "\n";

            foreach ($items as $detalle) {
                $txt .= str_pad($detalle->cantidad . " x ", 7) . strtoupper($detalle->producto->descripcion) . "\n";
                if (!empty($detalle->observacion)) {
                    $txt .= "   NOTA: " . $detalle->observacion . "\n";
                }
            }

            $txt .= str_repeat("-", 42) . "\n";
            $txt .= "DESTINO: " . strtoupper($nombreDestino) . "\n";
            $txt .= $this->piePaginaTexto();
            $txt .= "\n\n\n";

            $this->encolar('comanda', $impresora->id, $txt, $pedido->id, null, $items->pluck('id')->values()->all());
        } catch (\Exception $e) {
            Log::error("Error encolando comanda: " . $e->getMessage());
        }
    }

    /**
     * Comanda de anulación (cuando se elimina un item ya enviado)
     */
    public function imprimirComandaAnulacion($mesa, $producto, $cantidad, $observacion, $impresora, $nombreDestino)
    {
        try {
            $txt = "";
            $txt .= str_repeat("=", 42) . "\n";
            $txt .= "        *** ANULACION ***\n";
            $txt .= str_repeat("-", 42) . "\n";
            $txt .= "MESA: " . ($mesa->numero ?? $mesa->nombre ?? 'S/N') . "\n";
            $txt .= "Fecha: " . now()->format('d/m/Y h:i A') . "\n";
            $txt .= str_repeat("-", 42) . "\n";
            $txt .= $cantidad . " x " . strtoupper($producto->descripcion) . "\n";
            if (!empty($observacion)) {
                $txt .= "   NOTA: " . $observacion . "\n";
            }
            $txt .= str_repeat("-", 42) . "\n";
            $txt .= "DESTINO: " . strtoupper($nombreDestino) . "\n";
            $txt .= "\n\n\n";

            $this->encolar('anulacion', $impresora->id, $txt);
        } catch (\Exception $e) {
            Log::error("Error encolando anulación: " . $e->getMessage());
        }
    }

    /**
     * Reemplaza al cuerpo de procesarYEnviarComandas: mismo agrupamiento por
     * impresora, pero ahora encola en vez de imprimir directo.
     */
    public function procesarYEnviarComandas($pedido, string $puntoActual)
    {
        $destinosImpresos = [];
        $itemsPorImpresora = [];

        foreach ($pedido->detalles as $detalle) {
            $grupo = $detalle->producto?->grupoMenu;
            if (!$grupo) {
                continue;
            }

            $impresorasActivas = $grupo->impresoras->where('activa', true);
            $destinosDelPunto = $impresorasActivas->filter(function ($impresora) use ($puntoActual) {
                return strtoupper((string) $impresora->pivot->punto) === $puntoActual;
            });

            // No se pierde una comanda si el grupo tiene impresora activa,
            // pero aún no se parametrizó el punto específico del cajero.
            foreach (($destinosDelPunto->isNotEmpty() ? $destinosDelPunto : $impresorasActivas) as $impresora) {
                $itemsPorImpresora[$impresora->id]['impresora'] = $impresora;
                $itemsPorImpresora[$impresora->id]['items'][] = $detalle;
            }
        }

        foreach ($itemsPorImpresora as $grupoImpresion) {
            $impresora = $grupoImpresion['impresora'];
            $nombreDestino = $impresora->nombre;
            $destinosImpresos[] = $nombreDestino;

            $this->imprimirComanda($pedido, collect($grupoImpresion['items']), $impresora, $nombreDestino);
        }

        return [
            'success' => true,
            'destinos' => count($destinosImpresos) > 0 ? implode(', ', $destinosImpresos) : 'Ninguno (sin impresora asignada)'
        ];
    }

    /**
     * Comprobante de un movimiento de caja (ingreso/salida) para que la
     * persona que recibe o entrega el dinero lo firme físicamente.
     */
    public function imprimirComprobanteMovimiento($movimiento, $impresora, $usuario)
    {
        try {
            $txt = "";
            $txt .= str_repeat("=", 32) . "\n";
            $esSalida = strtolower($movimiento->tipo) === 'salida';
            $txt .= ($esSalida ? "   COMPROBANTE DE SALIDA\n" : "   COMPROBANTE DE INGRESO") . "\n";
            $txt .= "         DE CAJA\n";
            $txt .= str_repeat("=", 32) . "\n";
            $txt .= "Fecha  : " . $movimiento->created_at->format('d/m/Y') . "\n";
            $txt .= "Hora   : " . $movimiento->created_at->format('h:i A') . "\n";
            $txt .= "Cajero : " . ($usuario->name ?? 'N/A') . "\n";
            $txt .= str_repeat("-", 32) . "\n";
            $txt .= "Concepto:\n" . ($movimiento->conceptoCaja->nombre ?? $movimiento->concepto) . "\n";
            $txt .= str_repeat("-", 32) . "\n";
            $txt .= "VALOR: $" . number_format((float) $movimiento->valor, 0, ',', '.') . "\n";
            $txt .= str_repeat("-", 32) . "\n";

            if ($movimiento->tercero) {
                $txt .= ($esSalida ? "RECIBE:\n" : "ENTREGA:\n");
                $txt .= ($movimiento->tercero->nombre_completo ?? 'N/A') . "\n";
                $doc = $movimiento->tercero->cedula ?? $movimiento->tercero->nit ?? null;
                if ($doc) {
                    $txt .= "Doc.: " . $doc . "\n";
                }
                $txt .= str_repeat("-", 32) . "\n";
            }

            $txt .= "\n\n";
            $txt .= "_____________________________\n";
            $txt .= ($esSalida ? "Firma de quien recibe" : "Firma de quien entrega") . "\n";
            $txt .= $this->piePaginaTexto();
            $txt .= "\n\n\n\n";

            $this->encolar('movimiento_caja', $impresora->id, $txt);

            return ['status' => 'success'];
        } catch (\Exception $e) {
            Log::error("Error encolando comprobante de movimiento de caja: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Inserta el ticket de texto en la cola. El agente local lo recoge,
     * lo manda a node-thermal-printer / python-escpos, y marca como impreso.
     */
    private function encolar(string $tipo, int $impresoraId, string $contenido, $pedidoId = null, $facturaId = null, ?array $detalleIds = null)
    {
        ComandaPendiente::create([
            'pedido_id'    => $pedidoId,
            'factura_id'   => $facturaId,
            'tipo'         => $tipo,
            'impresora_id' => $impresoraId,
            'contenido'    => $contenido,
            'detalle_ids'  => $detalleIds,
            'estado'       => 'pendiente',
        ]);
    }

    private function piePaginaTexto(): string
    {
        $txt = str_repeat("-", 32) . "\n";
        $txt .= "Desarrollado por Andrexito.vip\n";
        $txt .= "Software de Gestion POS\n";
        $txt .= "(c) " . date('Y') . " Todos los derechos reservados\n";
        return $txt;
    }
}
