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

            $lineaSimple  = str_repeat("-", 32) . "\n";
            $lineaDoble   = str_repeat("=", 32) . "\n";
            $lineaPunteada = str_repeat(".", 32) . "\n";

            /* ============================================================
         * 1. ENCABEZADO DE LA EMPRESA
         * ============================================================ */
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setTextSize(2, 2);
            $printer->setEmphasis(true);
            $printer->text("APPSYSTEM S.A.S\n");
            $printer->setTextSize(1, 1);
            $printer->setEmphasis(false);
            $printer->feed(1);
            $printer->text("NIT: 901.456.789-1\n");
            $printer->text("Piedecuesta, Santander\n");
            $printer->text("Tel: 300 000 0000\n");
            $printer->feed(1);
            $printer->text($lineaDoble);

            /* ============================================================
         * 2. TÍTULO DEL DOCUMENTO
         * ============================================================ */
            $printer->setEmphasis(true);
            $printer->setTextSize(1, 2);
            $printer->text("FACTURA DE VENTA\n");
            $printer->setTextSize(1, 1);
            $printer->setEmphasis(false);
            $printer->text("No. " . $factura->numero_factura . "\n");
            $printer->text($lineaDoble);

            /* ============================================================
         * 3. INFORMACIÓN DE LA VENTA
         * ============================================================ */
            $printer->setJustification(Printer::JUSTIFY_LEFT);

            $printer->text("Fecha  : " . $factura->created_at->format('d/m/Y') . "\n");
            $printer->text("Hora   : " . $factura->created_at->format('h:i A') . "\n");
            $printer->text("Caja   : " . ($factura->caja->nombre ?? 'Principal') . "\n");
            $printer->text("Cajero : " . ($factura->user->name ?? 'Sistema') . "\n");

            // Mesero desde la mesa
            $mesero = optional(
                optional($factura->mesa->pedidos->first())->mesero
            )->name ?? 'N/A';
            $printer->text("Mesero : " . $mesero . "\n");

            $printer->text($lineaPunteada);

            // ── Cliente ──────────────────────────────────────────────────
            // Usa la relación directa; si no hay cliente asignado, muestra
            // CONSUMIDOR FINAL
            $nombreCliente = ($factura->cliente_id && $factura->cliente)
                ? strtoupper($factura->cliente->nombre)
                : 'CONSUMIDOR FINAL';

            $printer->setEmphasis(true);
            $printer->text("CLIENTE: " . $nombreCliente . "\n");
            $printer->setEmphasis(false);

            if ($factura->cliente && $factura->cliente->documento) {
                $printer->text("Doc.   : " . $factura->cliente->documento . "\n");
            }

            $printer->text($lineaDoble);

            /* ============================================================
         * 4. DETALLE DE PRODUCTOS
         * ============================================================ */
            $printer->setEmphasis(true);
            $printer->text(
                str_pad("CAN",  4)                              .
                    str_pad("PRODUCTO",  18)                        .
                    str_pad("VALOR", 10, " ", STR_PAD_LEFT) . "\n"
            );
            $printer->setEmphasis(false);
            $printer->text($lineaSimple);

            foreach ($factura->detalles as $detalle) {
                $nombre = substr($detalle->producto->descripcion, 0, 17);
                $printer->text(
                    str_pad($detalle->cantidad, 4)                                              .
                        str_pad($nombre, 18)                                                        .
                        str_pad(number_format($detalle->subtotal, 0, ',', '.'), 10, " ", STR_PAD_LEFT) . "\n"
                );

                // Muestra precio unitario en línea secundaria si cantidad > 1
                if ($detalle->cantidad > 1) {
                    $unitario = $detalle->subtotal / $detalle->cantidad;
                    $printer->text(
                        str_pad("", 4) .
                            str_pad("@ $" . number_format($unitario, 0, ',', '.') . " c/u", 28) . "\n"
                    );
                }
            }

            $printer->text($lineaDoble);

            /* ============================================================
         * 5. TOTALES
         * ============================================================ */
            $subtotal      = $factura->total;
            $propinaMonto  = round($subtotal * 0.10);
            // Se asume que la factura guarda si el cliente aceptó pagar propina.
            // Ajusta el campo según tu modelo (ej: $factura->propina_aceptada, $factura->con_propina, etc.)
            $pagoPropina   = !empty($factura->propina_aceptada) && $factura->propina_aceptada;
            $totalPagado   = $pagoPropina ? $subtotal + $propinaMonto : $subtotal;

            $printer->setJustification(Printer::JUSTIFY_RIGHT);

            $printer->text("SUBTOTAL : $" . number_format($subtotal, 0, ',', '.') . "\n");

            if ($pagoPropina) {
                $printer->text("PROPINA (10%) : $" . number_format($propinaMonto, 0, ',', '.') . "\n");
            } else {
                $printer->text("Propina sug. : $" . number_format($propinaMonto, 0, ',', '.') . "\n");
                $printer->text("(No incluida en total)\n");
            }

            $printer->text($lineaSimple);
            $printer->setTextSize(1, 2);
            $printer->setEmphasis(true);
            $printer->text("TOTAL PAGADO\n");
            $printer->text("$" . number_format($totalPagado, 0, ',', '.') . "\n");
            $printer->setEmphasis(false);
            $printer->setTextSize(1, 1);
            $printer->text($lineaDoble);

            /* ============================================================
         * 6. FORMA DE PAGO
         * ============================================================ */
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("Metodo : " . strtoupper($factura->metodo_pago) . "\n");

            if ($factura->referencia_pago) {
                $printer->text("Ref.   : " . $factura->referencia_pago . "\n");
            }

            $printer->text($lineaPunteada);

            /* ============================================================
         * 7. PIE DE PÁGINA
         * ============================================================ */
            $this->agregarPiePaginaSoftware($printer);

            $printer->feed(4);
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
        $printer->text("Desarrollado por Andrexito.vip\n");
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

//     public function procesarCierreCaja(Request $request)
// {
//     try {

//         $user = Auth::user();

//         /*
//         |--------------------------------------------------------------------------
//         | VALIDACIONES
//         |--------------------------------------------------------------------------
//         */

//         if (
//             !$request->fecha_inicio ||
//             !$request->hora_inicio ||
//             !$request->fecha_fin ||
//             !$request->hora_fin
//         ) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Faltan fechas u horas del cierre.'
//             ], 400);
//         }

//         $desde = \Carbon\Carbon::parse($request->fecha_inicio . ' ' . $request->hora_inicio);
//         $hasta = \Carbon\Carbon::parse($request->fecha_fin . ' ' . $request->hora_fin);

//         /*
//         |--------------------------------------------------------------------------
//         | CAJA / IMPRESORA
//         |--------------------------------------------------------------------------
//         */

//         $caja = DB::table('cajas')
//             ->where('id', $user->caja_id)
//             ->first();

//         $impresora = DB::table('impresoras')
//             ->where('id', $caja->impresora_id ?? 0)
//             ->first();

//         /*
//         |--------------------------------------------------------------------------
//         | FACTURAS
//         |--------------------------------------------------------------------------
//         */

//         $facturas = DB::table('facturas')
//             ->where('user_id', $user->id)
//             ->whereBetween('created_at', [$desde, $hasta])
//             ->orderBy('id')
//             ->get();

//         $cantidadFacturas = $facturas->count();

//         $facturaInicial = $facturas->first();
//         $facturaFinal   = $facturas->last();

//         /*
//         |--------------------------------------------------------------------------
//         | VENTAS SIN PROPINA
//         |--------------------------------------------------------------------------
//         */

//         $ventas = DB::table('facturas')
//             ->where('user_id', $user->id)
//             ->whereBetween('created_at', [$desde, $hasta])
//             ->selectRaw("
//                 SUM(CASE WHEN metodo_pago = 'efectivo' THEN total - propina ELSE 0 END) as efectivo,
//                 SUM(CASE WHEN metodo_pago = 'qr' THEN total - propina ELSE 0 END) as qr,
//                 SUM(CASE WHEN metodo_pago = 'tarjeta' THEN total - propina ELSE 0 END) as tarjeta,
//                 SUM(CASE WHEN metodo_pago = 'transferencia' THEN total - propina ELSE 0 END) as transferencia,
//                 SUM(total - propina) as total_ventas
//             ")
//             ->first();

//         /*
//         |--------------------------------------------------------------------------
//         | PROPINAS
//         |--------------------------------------------------------------------------
//         */

//         $propinas = DB::table('facturas')
//             ->where('user_id', $user->id)
//             ->whereBetween('created_at', [$desde, $hasta])
//             ->selectRaw("
//                 SUM(CASE WHEN metodo_pago = 'efectivo' THEN propina ELSE 0 END) as efectivo,
//                 SUM(CASE WHEN metodo_pago = 'qr' THEN propina ELSE 0 END) as qr,
//                 SUM(CASE WHEN metodo_pago = 'tarjeta' THEN propina ELSE 0 END) as tarjeta,
//                 SUM(CASE WHEN metodo_pago = 'transferencia' THEN propina ELSE 0 END) as transferencia,
//                 SUM(propina) as total_propinas
//             ")
//             ->first();

//         /*
//         |--------------------------------------------------------------------------
//         | MOVIMIENTOS DE CAJA
//         |--------------------------------------------------------------------------
//         */

//         $movimientos = DB::table('movimientos_caja')
//             ->whereBetween('created_at', [$desde, $hasta])
//             ->get();

//         $totalEntradas = $movimientos
//             ->where('tipo', 'entrada')
//             ->sum('valor');

//         $totalSalidas = $movimientos
//             ->where('tipo', 'salida')
//             ->sum('valor');

//         /*
//         |--------------------------------------------------------------------------
//         | ARQUEO
//         |--------------------------------------------------------------------------
//         */

//         $arqueoEfectivo = ($ventas->efectivo ?? 0) + ($propinas->efectivo ?? 0);
//         $arqueoQr = ($ventas->qr ?? 0) + ($propinas->qr ?? 0);
//         $arqueoTarjeta = ($ventas->tarjeta ?? 0) + ($propinas->tarjeta ?? 0);
//         $arqueoTransferencia = ($ventas->transferencia ?? 0) + ($propinas->transferencia ?? 0);

//         /*
//         |--------------------------------------------------------------------------
//         | CONTEO FISICO
//         |--------------------------------------------------------------------------
//         */

//         $m100 = intval($request->m100 ?? 0) * 100;
//         $m200 = intval($request->m200 ?? 0) * 200;
//         $m500 = intval($request->m500 ?? 0) * 500;
//         $m1000 = intval($request->m1000 ?? 0) * 1000;

//         $b2000 = intval($request->b2000 ?? 0) * 2000;
//         $b5000 = intval($request->b5000 ?? 0) * 5000;
//         $b10000 = intval($request->b10000 ?? 0) * 10000;
//         $b20000 = intval($request->b20000 ?? 0) * 20000;
//         $b50000 = intval($request->b50000 ?? 0) * 50000;
//         $b100000 = intval($request->b100000 ?? 0) * 100000;

//         $totalFisico =
//             $m100 +
//             $m200 +
//             $m500 +
//             $m1000 +
//             $b2000 +
//             $b5000 +
//             $b10000 +
//             $b20000 +
//             $b50000 +
//             $b100000;

//         /*
//         |--------------------------------------------------------------------------
//         | TOTAL ESPERADO
//         |--------------------------------------------------------------------------
//         */

//         $baseInicial = floatval($request->base_caja ?? 0);

//         $efectivoEsperado =
//             $baseInicial +
//             $arqueoEfectivo +
//             $totalEntradas -
//             $totalSalidas;

//         $diferencia = $totalFisico - $efectivoEsperado;

//         /*
//         |--------------------------------------------------------------------------
//         | IMPRESION
//         |--------------------------------------------------------------------------
//         */

//         $txt = "";

//         $txt .= "========================================\n";
//         $txt .= "             APPSYSTEM                 \n";
//         $txt .= "         NIT: 901.456.789-1            \n";
//         $txt .= "========================================\n";
//         $txt .= "           CIERRE DE CAJA              \n";
//         $txt .= "========================================\n";

//         $txt .= "DESDE: " . $desde->format('d/m/Y H:i') . "\n";
//         $txt .= "HASTA: " . $hasta->format('d/m/Y H:i') . "\n";

//         $txt .= "FACTURA INI: " . ($facturaInicial->prefijo ?? 'N/A') . ($facturaInicial->numero ?? '') . "\n";
//         $txt .= "FACTURA FIN: " . ($facturaFinal->prefijo ?? 'N/A') . ($facturaFinal->numero ?? '') . "\n";

//         $txt .= "CANT FACTURAS: " . $cantidadFacturas . "\n";
//         $txt .= "USUARIO: " . strtoupper($user->name) . "\n";
//         $txt .= "CAJA: " . ($caja->nombre ?? 'PRINCIPAL') . "\n";
//         $txt .= "IMPRESA: " . now()->format('d/m/Y H:i:s') . "\n";

//         /*
//         |--------------------------------------------------------------------------
//         | VENTAS
//         |--------------------------------------------------------------------------
//         */

//         $txt .= "----------------------------------------\n";
//         $txt .= "VENTAS\n";
//         $txt .= "----------------------------------------\n";

//         $txt .= "EFECTIVO:      $" . number_format($ventas->efectivo ?? 0, 0, ',', '.') . "\n";
//         $txt .= "QR:             $" . number_format($ventas->qr ?? 0, 0, ',', '.') . "\n";
//         $txt .= "TARJETA:        $" . number_format($ventas->tarjeta ?? 0, 0, ',', '.') . "\n";
//         $txt .= "TRANSFERENCIA: $" . number_format($ventas->transferencia ?? 0, 0, ',', '.') . "\n";

//         $txt .= "TOTAL VENTAS:  $" . number_format($ventas->total_ventas ?? 0, 0, ',', '.') . "\n";

//         /*
//         |--------------------------------------------------------------------------
//         | PROPINAS
//         |--------------------------------------------------------------------------
//         */

//         $txt .= "----------------------------------------\n";
//         $txt .= "PROPINAS\n";
//         $txt .= "----------------------------------------\n";

//         $txt .= "EFECTIVO:      $" . number_format($propinas->efectivo ?? 0, 0, ',', '.') . "\n";
//         $txt .= "QR:             $" . number_format($propinas->qr ?? 0, 0, ',', '.') . "\n";
//         $txt .= "TARJETA:        $" . number_format($propinas->tarjeta ?? 0, 0, ',', '.') . "\n";
//         $txt .= "TRANSFERENCIA: $" . number_format($propinas->transferencia ?? 0, 0, ',', '.') . "\n";

//         $txt .= "TOTAL PROPINA: $" . number_format($propinas->total_propinas ?? 0, 0, ',', '.') . "\n";

//         /*
//         |--------------------------------------------------------------------------
//         | MOVIMIENTOS
//         |--------------------------------------------------------------------------
//         */

//         $txt .= "----------------------------------------\n";
//         $txt .= "MOVIMIENTOS DE CAJA\n";
//         $txt .= "----------------------------------------\n";

//         foreach ($movimientos as $mov) {

//             $txt .= strtoupper($mov->tipo) . "\n";
//             $txt .= $mov->concepto . "\n";
//             $txt .= "$" . number_format($mov->valor, 0, ',', '.') . "\n";
//             $txt .= "----------------------------------------\n";
//         }

//         $txt .= "TOTAL ENTRADAS: $" . number_format($totalEntradas, 0, ',', '.') . "\n";
//         $txt .= "TOTAL SALIDAS:  $" . number_format($totalSalidas, 0, ',', '.') . "\n";

//         /*
//         |--------------------------------------------------------------------------
//         | ARQUEO
//         |--------------------------------------------------------------------------
//         */

//         $txt .= "----------------------------------------\n";
//         $txt .= "ARQUEO DE CAJA\n";
//         $txt .= "----------------------------------------\n";

//         $txt .= "EFECTIVO:      $" . number_format($arqueoEfectivo, 0, ',', '.') . "\n";
//         $txt .= "QR:             $" . number_format($arqueoQr, 0, ',', '.') . "\n";
//         $txt .= "TARJETA:        $" . number_format($arqueoTarjeta, 0, ',', '.') . "\n";
//         $txt .= "TRANSFERENCIA: $" . number_format($arqueoTransferencia, 0, ',', '.') . "\n";

//         /*
//         |--------------------------------------------------------------------------
//         | CONTEO FISICO
//         |--------------------------------------------------------------------------
//         */

//         $txt .= "----------------------------------------\n";
//         $txt .= "CONTEO FISICO\n";
//         $txt .= "----------------------------------------\n";

//         $txt .= "MONEDA 100:    $" . number_format($m100, 0, ',', '.') . "\n";
//         $txt .= "MONEDA 200:    $" . number_format($m200, 0, ',', '.') . "\n";
//         $txt .= "MONEDA 500:    $" . number_format($m500, 0, ',', '.') . "\n";
//         $txt .= "MONEDA 1000:   $" . number_format($m1000, 0, ',', '.') . "\n";

//         $txt .= "BILLETE 2000:  $" . number_format($b2000, 0, ',', '.') . "\n";
//         $txt .= "BILLETE 5000:  $" . number_format($b5000, 0, ',', '.') . "\n";
//         $txt .= "BILLETE 10000: $" . number_format($b10000, 0, ',', '.') . "\n";
//         $txt .= "BILLETE 20000: $" . number_format($b20000, 0, ',', '.') . "\n";
//         $txt .= "BILLETE 50000: $" . number_format($b50000, 0, ',', '.') . "\n";
//         $txt .= "BILLETE 100000:$" . number_format($b100000, 0, ',', '.') . "\n";

//         $txt .= "----------------------------------------\n";
//         $txt .= "TOTAL FISICO: $" . number_format($totalFisico, 0, ',', '.') . "\n";

//         /*
//         |--------------------------------------------------------------------------
//         | DIFERENCIA
//         |--------------------------------------------------------------------------
//         */

//         $txt .= "----------------------------------------\n";

//         if ($diferencia > 0) {

//             $txt .= "SOBRANTE: $" . number_format($diferencia, 0, ',', '.') . "\n";

//         } elseif ($diferencia < 0) {

//             $txt .= "FALTANTE: $" . number_format(abs($diferencia), 0, ',', '.') . "\n";

//         } else {

//             $txt .= "CAJA CUADRADA\n";
//         }

//         $txt .= "========================================\n";
//         $txt .= "\n\n\n";

//         /*
//         |--------------------------------------------------------------------------
//         | IMPRIMIR
//         |--------------------------------------------------------------------------
//         */

//         if ($impresora) {

//             $connector = new \Mike42\Escpos\PrintConnectors\NetworkPrintConnector(
//                 $impresora->ip,
//                 $impresora->puerto ?? 9100
//             );

//             $printer = new \Mike42\Escpos\Printer($connector);

//             $printer->text($txt);
//             $printer->cut();
//             $printer->close();
//         }

//         return response()->json([
//             'success' => true,
//             'estado_cuadre' => $diferencia == 0
//                 ? 'CUADRADO'
//                 : ($diferencia > 0 ? 'SOBRANTE' : 'FALTANTE'),
//             'diferencia' => number_format(abs($diferencia), 0, ',', '.')
//         ]);

//     } catch (\Exception $e) {

//         return response()->json([
//             'success' => false,
//             'message' => $e->getMessage() . ' linea ' . $e->getLine()
//         ], 500);
//     }
// }