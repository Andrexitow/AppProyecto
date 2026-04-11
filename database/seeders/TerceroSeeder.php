<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TerceroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('terceros')->insert([
            [
                'tipo' => 'persona',
                'nombre' => 'Andres',
                'apellido' => 'Vargas',
                'cedula' => '1098765432',
                'razon_social' => null,
                'nit' => null,
                'email' => 'andres@example.com',
                'celular' => '3001234567',
                'direccion' => 'Calle 1 #10-20',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tipo' => 'persona',
                'nombre' => 'Camila',
                'apellido' => 'Lopez',
                'cedula' => '1012345678',
                'razon_social' => null,
                'nit' => null,
                'email' => 'camila@example.com',
                'celular' => '3019876543',
                'direccion' => 'Carrera 5 #20-30',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tipo' => 'empresa',
                'nombre' => null,
                'apellido' => null,
                'cedula' => null,
                'razon_social' => 'Taller Mecánico El Rápido SAS',
                'nit' => '900123456-7',
                'email' => 'contacto@tallerrapido.com',
                'celular' => '3100000000',
                'direccion' => 'Zona Industrial',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tipo' => 'empresa',
                'nombre' => null,
                'apellido' => null,
                'cedula' => null,
                'razon_social' => 'Repuestos Santander LTDA',
                'nit' => '800765432-1',
                'email' => 'ventas@repuestossantander.com',
                'celular' => '3111111111',
                'direccion' => 'Av Principal #45-10',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tipo' => 'persona',
                'nombre' => 'Juan',
                'apellido' => 'Perez',
                'cedula' => '1122334455',
                'razon_social' => null,
                'nit' => null,
                'email' => 'juan@example.com',
                'celular' => '3022222222',
                'direccion' => 'Calle 8 #15-40',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
