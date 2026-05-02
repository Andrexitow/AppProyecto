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
            ['nombre' => 'COCINA',     'impresora_id' => $impCocina->id],
            ['nombre' => 'PARRILLA',   'impresora_id' => $impCocina->id],
            ['nombre' => 'BURGER',     'impresora_id' => $impCocina->id],
            ['nombre' => 'CERVEZAS',    'impresora_id' => $impBarraDisco->id],

            ['nombre' => 'BARRA',      'impresora_id' => $impBarra->id],
            ['nombre' => 'COCTELERIA', 'impresora_id' => $impBarra->id],
        ];

        foreach ($grupos as $grupo) {
            GrupoMenu::updateOrCreate(
                ['nombre' => $grupo['nombre']],
                $grupo
            );
        }
    }
}