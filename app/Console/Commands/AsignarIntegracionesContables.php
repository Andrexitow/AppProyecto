<?php

namespace App\Console\Commands;

use App\Models\Producto;
use App\Models\IntegracionContable;
use Illuminate\Console\Command;

class AsignarIntegracionesContables extends Command
{
    protected $signature = 'app:asignar-integraciones';

    protected $description = 'Asigna integracion_contable_id a productos según su categoría (ejecutar una sola vez)';

    public function handle(): void
    {
        $mapa = [
            'CERVEZAS'               => ['Cervezas', 'Cervezas Importadas', 'Micheladas', 'Cubetazos', 'Peceras'],
            'LICORES'                => ['Aguardiente', 'Ron', 'Vodka', 'Whisky', 'Ginebra', 'Tequilas', 'Licores Cremosos', 'Vinos'],
            'COCTELES'               => ['Cocteles', 'Cocteles de la Casa'],
            'COMIDA'                 => ['Entradas y Antojos', 'Del Mar y Patacones', 'Cortes CKemium', 'Cerdo y Pollo', 'Típicos Festivos', 'Picadas y Pinchos', 'Burger Mania', 'Infantil y Rápidos'],
            'BEBIDAS_NO_ALCOHOLICAS' => ['Sodas Italianas'],
            'OTROS'                   => ['Otros'],
        ];

        foreach ($mapa as $codigoIntegracion => $categorias) {
            $integracion = IntegracionContable::where('codigo', $codigoIntegracion)->first();

            if (!$integracion) {
                $this->warn("No existe la integración {$codigoIntegracion}, se salta.");
                continue;
            }

            $actualizados = Producto::whereIn('categoria', $categorias)
                ->update(['integracion_contable_id' => $integracion->id]);

            $this->info("{$codigoIntegracion}: {$actualizados} productos actualizados.");
        }

        $restantes = Producto::whereNull('integracion_contable_id')->count();
        $this->line("Productos sin asignar restantes: {$restantes}");

        if ($restantes > 0) {
            $categoriasFaltantes = Producto::whereNull('integracion_contable_id')
                ->distinct()
                ->pluck('categoria');

            $this->warn('Categorías sin mapear: ' . $categoriasFaltantes->implode(', '));
        }
    }
}