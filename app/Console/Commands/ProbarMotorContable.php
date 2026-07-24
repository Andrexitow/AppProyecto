<?php

namespace App\Console\Commands;

use App\DataTransferObjects\ContabilidadData;
use App\Services\ContabilidadService;
use Illuminate\Console\Command;

class ProbarMotorContable extends Command
{
    protected $signature = 'app:probar-motor-contable';

    protected $description = 'Prueba del motor contable';

    public function handle(ContabilidadService $service)
    {
        $this->info('================================');
        $this->info('PRUEBA DEL MOTOR CONTABLE');
        $this->info('================================');

        try {

            $datos = new ContabilidadData(

                modulo: 'FACTURACION',

                valores: [

                    'SUBTOTAL' => 100000,

                    'IVA' => 19000,

                    'TOTAL' => 119000,

                ],

                terceroId: 1,

                usuarioId: 1,

                documento: 'FV-000001',

                documentoId: 1,

                observacion: 'Prueba automática'

            );

            $comprobante = $service->procesar(
                'VENTA_CONTADO',
                $datos
            );

            $this->info("✔ Comprobante generado");

            $this->newLine();

            $this->table(

                [
                    'Cuenta',
                    'Débito',
                    'Crédito'
                ],

                $comprobante
                    ->movimientos
                    ->map(function ($m) {

                        return [

                            $m->cuenta->codigo .
                            ' - ' .
                            $m->cuenta->nombre,

                            $m->debito,

                            $m->credito

                        ];

                    })

            );

            $this->newLine();

            $this->info("Débitos : {$comprobante->total_debito}");

            $this->info("Créditos: {$comprobante->total_credito}");

            if ($comprobante->estaCuadrado()) {

                $this->info("✔ PARTIDA DOBLE CORRECTA");

            }

        } catch (\Throwable $e) {

            $this->error($e->getMessage());

            $this->error($e->getFile());

            $this->error($e->getLine());

        }
    }
}