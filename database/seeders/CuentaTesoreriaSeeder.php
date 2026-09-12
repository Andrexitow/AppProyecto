<?php

namespace Database\Seeders;

use App\Models\ConfiguracionContable;
use App\Models\CuentaTesoreria;
use Illuminate\Database\Seeder;

/**
 * Crea las cuentas de tesorería iniciales apuntando a las MISMAS cuentas
 * contables que ya usan las ventas/compras (CUENTA_CAJA, CUENTA_BANCO), para
 * que el módulo de Tesorería muestre desde el día uno los saldos que ya
 * vienen acumulando esas cuentas por la operación normal del negocio.
 */
class CuentaTesoreriaSeeder extends Seeder
{
    public function run(): void
    {
        $mapeos = [
            ['nombre' => 'Caja General', 'tipo' => 'CAJA', 'clave' => 'CUENTA_CAJA'],
            ['nombre' => 'Banco Principal', 'tipo' => 'BANCO', 'clave' => 'CUENTA_BANCO'],
        ];

        foreach ($mapeos as $mapeo) {
            $cuentaContableId = ConfiguracionContable::where('clave', $mapeo['clave'])->value('cuenta_contable_id');
            if (!$cuentaContableId) {
                $this->command->warn("No se pudo crear '{$mapeo['nombre']}': la configuración {$mapeo['clave']} no tiene cuenta contable asignada todavía.");
                continue;
            }

            CuentaTesoreria::updateOrCreate(
                ['nombre' => $mapeo['nombre']],
                ['tipo' => $mapeo['tipo'], 'cuenta_contable_id' => $cuentaContableId, 'activa' => true]
            );
        }
    }
}
