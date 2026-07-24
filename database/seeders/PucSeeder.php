<?php

namespace Database\Seeders;

use App\Models\CuentaContable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PucSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            $archivo = database_path('data/puc_colombia.csv');

            if (!file_exists($archivo)) {
                throw new \Exception("No existe el archivo: {$archivo}");
            }

            $filas = array_map('str_getcsv', file($archivo));

            // Eliminar encabezado
            $encabezado = array_shift($filas);

            foreach ($filas as $fila) {

                CuentaContable::updateOrCreate(

                    [
                        'codigo' => trim($fila[0])
                    ],

                    [
                        'nombre' => trim($fila[1]),
                        'nivel' => (int) $fila[2],
                        'clasificacion' => trim($fila[3]),
                        'naturaleza' => trim($fila[4]),
                        'tipo' => trim($fila[5]),
                        'permite_movimientos' => (bool) $fila[6],
                        'estado' => true
                    ]

                );
            }

            // Segunda pasada para asignar padres

            $cuentas = CuentaContable::orderByRaw('LENGTH(codigo) ASC')->get();

            foreach ($cuentas as $cuenta) {

                $codigo = $cuenta->codigo;

                $padre = null;

                while (strlen($codigo) > 1) {

                    $codigo = substr($codigo, 0, -1);

                    $padre = CuentaContable::where('codigo', $codigo)->first();

                    if ($padre) {
                        break;
                    }
                }

                $cuenta->update([
                    'cuenta_padre_id' => $padre?->id
                ]);
            }

        });
    }
}