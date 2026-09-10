<?php

namespace Database\Seeders;

use App\Models\GrupoMenu;
use App\Models\Impresora;
use Illuminate\Database\Seeder;

class GrupoMenuSeeder extends Seeder
{
    public function run(): void
    {
        $impBarra = Impresora::updateOrCreate(
            ['nombre' => 'IMPRESORA BARRA'],
            [
                'ip' => '192.168.110.100',
                'puerto' => '9100'
            ]
        );

        $impCocina = Impresora::updateOrCreate(
            ['nombre' => 'IMPRESORA COCINA'],
            [
                'ip' => '192.168.110.39',
                'puerto' => '9100'
            ]
        );

        $impBarraDisco = Impresora::updateOrCreate(
            ['nombre' => 'IMPRESORA BARRA DISCOTECA'],
            [
                'ip' => '192.168.110.56',
                'puerto' => '9100'
            ]
        );

        $grupos = [

            /*
            |--------------------------------------------------------------------------
            | COCINA
            |--------------------------------------------------------------------------
            */

            // ['nombre' => 'ENTRADAS',              'impresora_id' => $impCocina->id],
            // ['nombre' => 'DEL MAR',               'impresora_id' => $impCocina->id],
            // ['nombre' => 'PARRILLA',              'impresora_id' => $impCocina->id],
            // ['nombre' => 'CERDO Y POLLO',         'impresora_id' => $impCocina->id],
            // ['nombre' => 'TIPICOS',               'impresora_id' => $impCocina->id],
            // ['nombre' => 'PICADAS',               'impresora_id' => $impCocina->id],
            ['nombre' => 'BURGER',                'impresora_id' => $impCocina->id],
            // ['nombre' => 'INFANTIL',              'impresora_id' => $impCocina->id],

            /*
            |--------------------------------------------------------------------------
            | BARRA
            |--------------------------------------------------------------------------
            */

            // ['nombre' => 'COCTELES',              'impresora_id' => $impBarra->id],
            // ['nombre' => 'COCTELES CASA',         'impresora_id' => $impBarra->id],
            // ['nombre' => 'PECERAS',               'impresora_id' => $impBarra->id],
            // ['nombre' => 'SODAS',                 'impresora_id' => $impBarra->id],
            // ['nombre' => 'MICHELADAS',            'impresora_id' => $impBarra->id],

            /*
            |--------------------------------------------------------------------------
            | DISCOTECA / LICORES
            |--------------------------------------------------------------------------
            */

            ['nombre' => 'CERVEZAS',              'impresora_id' => $impBarraDisco->id],
            // ['nombre' => 'CERVEZAS IMPORTADAS',   'impresora_id' => $impBarraDisco->id],
            // ['nombre' => 'VINOS',                 'impresora_id' => $impBarraDisco->id],
            // ['nombre' => 'AGUARDIENTE',           'impresora_id' => $impBarraDisco->id],
            // ['nombre' => 'RON',                   'impresora_id' => $impBarraDisco->id],
            // ['nombre' => 'VODKA',                 'impresora_id' => $impBarraDisco->id],
            // ['nombre' => 'WHISKY',                'impresora_id' => $impBarraDisco->id],
            // ['nombre' => 'GINEBRA',               'impresora_id' => $impBarraDisco->id],
            // ['nombre' => 'LICORES CREMOSOS',      'impresora_id' => $impBarraDisco->id],
            // ['nombre' => 'TEQUILAS',              'impresora_id' => $impBarraDisco->id],
            // ['nombre' => 'CUBETAZOS',             'impresora_id' => $impBarraDisco->id],
        ];

        foreach ($grupos as $grupo) {
            GrupoMenu::updateOrCreate(
                ['nombre' => $grupo['nombre']],
                $grupo
            );
        }
    }
}