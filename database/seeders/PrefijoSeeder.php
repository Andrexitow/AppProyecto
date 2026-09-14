<?php

namespace Database\Seeders;

use App\Models\Prefijo;
use Illuminate\Database\Seeder;

/**
 * Precarga el catálogo con los prefijos que YA estaban en uso como texto
 * libre en Cajas/Documentos/Facturas antes de existir esta tabla, para que
 * ninguna caja ni documento existente se quede sin un prefijo válido al
 * pasar el campo de texto libre a un selector.
 */
class PrefijoSeeder extends Seeder
{
    public function run(): void
    {
        $prefijos = [
            ['codigo' => 'FR', 'nombre' => 'Factura Restaurante', 'descripcion' => 'Ventas de la caja del restaurante'],
            ['codigo' => 'FD', 'nombre' => 'Factura Discoteca', 'descripcion' => 'Ventas de la caja de la discoteca'],
            ['codigo' => 'FC', 'nombre' => 'Factura de Compra', 'descripcion' => 'Documentos de compra a proveedores'],
            ['codigo' => 'TR', 'nombre' => 'Traslado', 'descripcion' => 'Traslados entre bodegas'],
        ];

        foreach ($prefijos as $prefijo) {
            Prefijo::updateOrCreate(['codigo' => $prefijo['codigo']], $prefijo);
        }
    }
}
