<?php

namespace App\Console\Commands;

use App\Services\FacturacionElectronicaService;
use Illuminate\Console\Command;

/**
 * Transmite al proveedor de facturación electrónica todo lo que quedó
 * pendiente (o falló) desde el POS. No hace nada mientras
 * services.factura_electronica.habilitada sea false — ver
 * FacturacionElectronicaService.
 */
class ProcesarFacturacionElectronicaPendiente extends Command
{
    protected $signature = 'facturacion-electronica:procesar';

    protected $description = 'Transmite a la DIAN (vía el proveedor configurado) las facturas y notas pendientes';

    public function handle(FacturacionElectronicaService $servicio): int
    {
        $procesados = $servicio->procesarPendientes();

        $this->info("Documentos electrónicos procesados: {$procesados}");

        return self::SUCCESS;
    }
}
