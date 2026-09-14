<?php

namespace Database\Seeders;

use App\Models\ConfiguracionContable;
use App\Models\CuentaContable;
use Illuminate\Database\Seeder;

class ParametrizacionInicialContableSeeder extends Seeder
{
    public function run(): void
    {
        $configuraciones = [

            'CUENTA_CAJA' => '110505',

            'CUENTA_BANCO' => '111005',

            'CUENTA_CLIENTES' => '130505',

            'CUENTA_PROVEEDORES' => '220505',

            'CUENTA_INVENTARIO' => '143505',

            'CUENTA_COSTO_VENTAS' => '613505',

            'CUENTA_VENTAS' => '413505',

            'CUENTA_IVA_GENERADO' => '240805',

            'CUENTA_IVA_DESCONTABLE' => '240810',

            'CUENTA_RETEFUENTE' => '236540',

            'CUENTA_RETEIVA' => '236705',

            'CUENTA_RETEICA' => '236805',

            'CUENTA_PERDIDA_BAJA_ACTIVOS' => '539540',

            'CUENTA_PROVISION_CARTERA' => '139905',

            'CUENTA_GASTO_PROVISION_CARTERA' => '519505',

        ];

        foreach ($configuraciones as $clave => $codigoCuenta) {

            $cuenta = CuentaContable::where('codigo', $codigoCuenta)->first();

            if (!$cuenta) {
                $this->command->warn("No existe la cuenta {$codigoCuenta}");
                continue;
            }

            ConfiguracionContable::where('clave', $clave)
                ->update([
                    'cuenta_contable_id' => $cuenta->id
                ]);

            $this->command->info("{$clave} → {$codigoCuenta}");
        }
    }
}