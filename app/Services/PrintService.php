<?php

namespace App\Services;

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Illuminate\Support\Facades\Log;


class PrintService
{
    public function imprimirFactura($factura, $impresora)
    {
        try {
            $connector = new NetworkPrintConnector($impresora->ip, $impresora->puerto ?? 9100);
            $printer = new Printer($connector);

            /* 1. ENCABEZADO DE LA EMPRESA */
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->setTextSize(2, 2);
            $printer->text("APPSYSTEM S.A.S\n");
            $printer->setTextSize(1, 1);
            $printer->setEmphasis(false);
            $printer->text("NIT: 901.456.789-1\n");
            $printer->text("Piedecuesta, Santander\n");
            $printer->text("Tel: 300 000 0000\n");
            $printer->text(str_repeat("=", 32) . "\n");

            /* 2. INFORMACIÓN DE LA VENTA Y PERSONAL */
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->setEmphasis(true);
            $printer->text("FACTURA: " . $factura->numero_factura . "\n");
            $printer->setEmphasis(false);

            // Fecha y Hora en formato 12h (am/pm)
            $printer->text("Fecha:   " . $factura->created_at->format('d/m/Y h:i A') . "\n");

            // Datos de personal y caja
            $printer->text("Caja:    " . ($factura->caja->nombre ?? 'Principal') . "\n");
            $printer->text("Cajero:  " . ($factura->user->name ?? 'Sistema') . "\n");

            // Intenta obtener el mesero desde la relación de la mesa
            $mesero = $factura->mesa->pedidos->first()->mesero->name ?? 'N/A';
            $printer->text("Mesero:  " . $mesero . "\n");

            // Cliente
            $nombreCliente = ($factura->cliente_id && $factura->cliente_id != 1)
                ? ($factura->cliente->nombre ?? 'CONSUMIDOR FINAL')
                : 'CONSUMIDOR FINAL';
            $printer->text("Cliente: " . strtoupper($nombreCliente) . "\n");
            $printer->text(str_repeat("-", 32) . "\n");

            /* 3. DETALLE DE PRODUCTOS */
            $printer->setEmphasis(true);
            $printer->text(
                str_pad("CAN", 4) .
                    str_pad("PRODUCTO", 18) .
                    str_pad("TOTAL", 10, " ", STR_PAD_LEFT) . "\n"
            );
            $printer->setEmphasis(false);

            foreach ($factura->detalles as $detalle) {
                $nombre = substr($detalle->producto->descripcion, 0, 17);
                $printer->text(
                    str_pad($detalle->cantidad, 4) .
                        str_pad($nombre, 18) .
                        str_pad(number_format($detalle->subtotal, 0, ',', '.'), 10, " ", STR_PAD_LEFT) . "\n"
                );
            }
            $printer->text(str_repeat("-", 32) . "\n");

            /* 4. TOTALES Y PROPINA SUGERIDA */
            $subtotalFactura = $factura->total;
            $propina = $subtotalFactura * 0.10;
            $totalConPropina = $subtotalFactura + $propina;

            $printer->setJustification(Printer::JUSTIFY_RIGHT);
            $printer->text("SUBTOTAL: $" . number_format($subtotalFactura, 0, ',', '.') . "\n");
            $printer->text("PROPINA SUG. (10%): $" . number_format($propina, 0, ',', '.') . "\n");

            $printer->setTextSize(1, 2);
            $printer->setEmphasis(true);
            $printer->text("TOTAL A PAGAR: $" . number_format($totalConPropina, 0, ',', '.') . "\n");
            $printer->setEmphasis(false);
            $printer->setTextSize(1, 1);
            $printer->text(str_repeat("=", 32) . "\n");

            /* 5. FORMA DE PAGO */
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("METODO PAGO: " . strtoupper($factura->metodo_pago) . "\n");
            if ($factura->referencia_pago) {
                $printer->text("REF: " . $factura->referencia_pago . "\n");
            }

            /* 6. PIE DE PÁGINA Y CRÉDITOS */
            $this->agregarPiePaginaSoftware($printer);

            $printer->feed(3);
            $printer->cut();
            $printer->close();

            return ['status' => 'success'];
        } catch (\Exception $e) {
            Log::error("Error imprimiendo factura: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Imprime la Comanda para Cocina/Bar
     */
    public function imprimirComanda($pedido, $items, $impresora, $nombreDestino)
    {
        try {
            $connector = new NetworkPrintConnector($impresora->ip, $impresora->puerto ?? 9100);
            $printer = new Printer($connector);

            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->setTextSize(2, 2);
            $printer->text("ORDEN DE PISO\n");
            $printer->setTextSize(1, 1);
            $printer->text(str_repeat("-", 42) . "\n");

            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->setTextSize(2, 2);
            $printer->text("MESA: " . ($pedido->mesa->numero ?? $pedido->mesa->nombre ?? 'S/N') . "\n");
            $printer->setTextSize(1, 1);
            $printer->setEmphasis(false);

            $printer->text("Mesero:  " . ($pedido->mesero->name ?? 'N/A') . "\n");
            $printer->text("Fecha:   " . now()->format('d/m/Y h:i A') . "\n");
            $printer->text(str_repeat("-", 42) . "\n");

            foreach ($items as $detalle) {
                $printer->setEmphasis(true);
                $printer->text(str_pad($detalle->cantidad . " x ", 7));
                $printer->setEmphasis(false);
                $printer->text(strtoupper($detalle->producto->descripcion) . "\n");

                if (!empty($detalle->observacion)) {
                    $printer->text("   NOTA: " . $detalle->observacion . "\n");
                }
            }

            $printer->text(str_repeat("-", 42) . "\n");
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->text("DESTINO: " . strtoupper($nombreDestino) . "\n");

            $this->agregarPiePaginaSoftware($printer);

            $printer->feed(3);
            $printer->cut();
            $printer->close();
        } catch (\Exception $e) {
            Log::error("Error imprimiendo comanda: " . $e->getMessage());
        }
    }

    /**
     * Bloque de créditos AppSystem
     */
    private function agregarPiePaginaSoftware($printer)
    {
        $printer->feed(1);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("--------------------------------\n");
        $printer->setEmphasis(true);
        $printer->text("Desarrollado por AppSystem\n");
        $printer->setEmphasis(false);
        $printer->text("Software de Gestion POS\n");
        $printer->text("© " . date('Y') . " Todos los derechos reservados\n");
    }

    // Dentro de app/Services/PrintService.php

    public function procesarYEnviarComandas($pedido)
    {
        // Agrupamos los productos por el ID de su impresora
        $productosPorImpresora = $pedido->detalles->groupBy(function ($detalle) {
            return $detalle->producto->grupoMenu->impresora->id ?? null;
        });

        foreach ($productosPorImpresora as $impresoraId => $items) {
            if ($impresoraId) {
                // Obtenemos el objeto impresora desde el primer item del grupo
                $impresora = $items->first()->producto->grupoMenu->impresora;
                $nombreDestino = $impresora->nombre; // Ejemplo: "COCINA" o "BAR"

                $this->imprimirComanda(
                    $pedido,
                    $items,
                    $impresora,
                    $nombreDestino
                );
            }
        }
        return true;
    }
}
