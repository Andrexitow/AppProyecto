<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\GrupoMenu;

class ProductoSeeder extends Seeder
{
    public function run()
    {
        // Helper para obtener grupo_menu_id por nombre
        $g = fn(string $nombre) => GrupoMenu::where('nombre', $nombre)->value('id');

        DB::table('productos')->insert([

            /*
            |--------------------------------------------------------------------------
            | ENTRADAS Y ANTOJOS
            |--------------------------------------------------------------------------
            */

            ['codigo' => 'EA001', 'descripcion' => 'Ceviche de Chicharrón',  'und_detal' => 'Unidad',  'categoria' => 'Entradas y Antojos', 'grupo_menu_id' => $g('ENTRADAS'), 'Precio' => 26000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'EA002', 'descripcion' => 'Mazorcada',              'und_detal' => 'Unidad',  'categoria' => 'Entradas y Antojos', 'grupo_menu_id' => $g('ENTRADAS'), 'Precio' => 20000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'EA003', 'descripcion' => 'Chinchulines',           'und_detal' => 'Unidad',  'categoria' => 'Entradas y Antojos', 'grupo_menu_id' => $g('ENTRADAS'), 'Precio' => 20000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'EA004', 'descripcion' => 'Chorizo Caramelizado',   'und_detal' => 'Unidad',  'categoria' => 'Entradas y Antojos', 'grupo_menu_id' => $g('ENTRADAS'), 'Precio' => 22000, 'created_at' => now(), 'updated_at' => now()],

            /*
            |--------------------------------------------------------------------------
            | DEL MAR Y PATACONES
            |--------------------------------------------------------------------------
            */

            ['codigo' => 'DM001', 'descripcion' => 'Mojarra',            'und_detal' => 'Unidad', 'categoria' => 'Del Mar y Patacones', 'grupo_menu_id' => $g('DEL MAR'), 'Precio' => 34000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'DM002', 'descripcion' => 'Trucha Marinera',    'und_detal' => 'Unidad', 'categoria' => 'Del Mar y Patacones', 'grupo_menu_id' => $g('DEL MAR'), 'Precio' => 38000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'DM003', 'descripcion' => 'Robalo Apanado',     'und_detal' => 'Unidad', 'categoria' => 'Del Mar y Patacones', 'grupo_menu_id' => $g('DEL MAR'), 'Precio' => 35000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'DM004', 'descripcion' => 'Patacón Marinero',   'und_detal' => 'Unidad', 'categoria' => 'Del Mar y Patacones', 'grupo_menu_id' => $g('DEL MAR'), 'Precio' => 35000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'DM005', 'descripcion' => 'Patacón de Carnes',  'und_detal' => 'Unidad', 'categoria' => 'Del Mar y Patacones', 'grupo_menu_id' => $g('DEL MAR'), 'Precio' => 38000, 'created_at' => now(), 'updated_at' => now()],

            /*
            |--------------------------------------------------------------------------
            | CORTES CKEMIUM A LA PARRILLA
            |--------------------------------------------------------------------------
            */

            ['codigo' => 'CP001', 'descripcion' => 'Tomahawk',      'und_detal' => 'Unidad', 'categoria' => 'Cortes CKemium', 'grupo_menu_id' => $g('PARRILLA'), 'Precio' => 79900, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'CP002', 'descripcion' => 'Lomo Fino',     'und_detal' => 'Unidad', 'categoria' => 'Cortes CKemium', 'grupo_menu_id' => $g('PARRILLA'), 'Precio' => 49900, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'CP003', 'descripcion' => 'Punta de Anca', 'und_detal' => 'Unidad', 'categoria' => 'Cortes CKemium', 'grupo_menu_id' => $g('PARRILLA'), 'Precio' => 44900, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'CP004', 'descripcion' => 'Ojo de Bife',   'und_detal' => 'Unidad', 'categoria' => 'Cortes CKemium', 'grupo_menu_id' => $g('PARRILLA'), 'Precio' => 44900, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'CP005', 'descripcion' => 'Chatas',        'und_detal' => 'Unidad', 'categoria' => 'Cortes CKemium', 'grupo_menu_id' => $g('PARRILLA'), 'Precio' => 42900, 'created_at' => now(), 'updated_at' => now()],

            /*
            |--------------------------------------------------------------------------
            | CERDO Y POLLO
            |--------------------------------------------------------------------------
            */

            ['codigo' => 'CY001', 'descripcion' => 'Chicharrón de Cerdo',  'und_detal' => 'Unidad', 'categoria' => 'Cerdo y Pollo', 'grupo_menu_id' => $g('CERDO Y POLLO'), 'Precio' => 36000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'CY002', 'descripcion' => 'Chuleta de Cerdo',     'und_detal' => 'Unidad', 'categoria' => 'Cerdo y Pollo', 'grupo_menu_id' => $g('CERDO Y POLLO'), 'Precio' => 35000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'CY003', 'descripcion' => 'Costillas de Cerdo',   'und_detal' => 'Unidad', 'categoria' => 'Cerdo y Pollo', 'grupo_menu_id' => $g('CERDO Y POLLO'), 'Precio' => 32000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'CY004', 'descripcion' => 'Pechuga Marinera',     'und_detal' => 'Unidad', 'categoria' => 'Cerdo y Pollo', 'grupo_menu_id' => $g('CERDO Y POLLO'), 'Precio' => 36000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'CY005', 'descripcion' => 'Pechuga Gratinada',    'und_detal' => 'Unidad', 'categoria' => 'Cerdo y Pollo', 'grupo_menu_id' => $g('CERDO Y POLLO'), 'Precio' => 33000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'CY006', 'descripcion' => 'Pechuga Champiñón',    'und_detal' => 'Unidad', 'categoria' => 'Cerdo y Pollo', 'grupo_menu_id' => $g('CERDO Y POLLO'), 'Precio' => 33000, 'created_at' => now(), 'updated_at' => now()],

            /*
            |--------------------------------------------------------------------------
            | TÍPICOS FESTIVOS
            |--------------------------------------------------------------------------
            */

            ['codigo' => 'TF001', 'descripcion' => 'Carne Oreada',       'und_detal' => 'Unidad', 'categoria' => 'Típicos Festivos', 'grupo_menu_id' => $g('TIPICOS'), 'Precio' => 38000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'TF002', 'descripcion' => 'Sobrebarriga',        'und_detal' => 'Unidad', 'categoria' => 'Típicos Festivos', 'grupo_menu_id' => $g('TIPICOS'), 'Precio' => 38000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'TF003', 'descripcion' => 'Sancocho Mixto',      'und_detal' => 'Unidad', 'categoria' => 'Típicos Festivos', 'grupo_menu_id' => $g('TIPICOS'), 'Precio' => 39900, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'TF004', 'descripcion' => 'Mute Santandereano',  'und_detal' => 'Unidad', 'categoria' => 'Típicos Festivos', 'grupo_menu_id' => $g('TIPICOS'), 'Precio' => 25000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'TF005', 'descripcion' => 'Cabro al Horno',      'und_detal' => 'Unidad', 'categoria' => 'Típicos Festivos', 'grupo_menu_id' => $g('TIPICOS'), 'Precio' => 38000, 'created_at' => now(), 'updated_at' => now()],

            /*
            |--------------------------------------------------------------------------
            | PICADAS Y PINCHOS
            |--------------------------------------------------------------------------
            */

            ['codigo' => 'PP001', 'descripcion' => 'Picada x4',      'und_detal' => 'Porción', 'categoria' => 'Picadas y Pinchos', 'grupo_menu_id' => $g('PICADAS'), 'Precio' => 100000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'PP002', 'descripcion' => 'Picada x2',      'und_detal' => 'Porción', 'categoria' => 'Picadas y Pinchos', 'grupo_menu_id' => $g('PICADAS'), 'Precio' => 60000,  'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'PP003', 'descripcion' => 'Pincho Triple',  'und_detal' => 'Unidad',  'categoria' => 'Picadas y Pinchos', 'grupo_menu_id' => $g('PICADAS'), 'Precio' => 28000,  'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'PP004', 'descripcion' => 'Pincho de Carne','und_detal' => 'Unidad',  'categoria' => 'Picadas y Pinchos', 'grupo_menu_id' => $g('PICADAS'), 'Precio' => 26000,  'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'PP005', 'descripcion' => 'Pincho Mixto',   'und_detal' => 'Unidad',  'categoria' => 'Picadas y Pinchos', 'grupo_menu_id' => $g('PICADAS'), 'Precio' => 27000,  'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'PP006', 'descripcion' => 'Desgranado',     'und_detal' => 'Unidad',  'categoria' => 'Picadas y Pinchos', 'grupo_menu_id' => $g('PICADAS'), 'Precio' => 29900,  'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'PP007', 'descripcion' => 'Morrongo',       'und_detal' => 'Unidad',  'categoria' => 'Picadas y Pinchos', 'grupo_menu_id' => $g('PICADAS'), 'Precio' => 29900,  'created_at' => now(), 'updated_at' => now()],

            /*
            |--------------------------------------------------------------------------
            | BURGER MANIA
            |--------------------------------------------------------------------------
            */

            ['codigo' => 'BG001', 'descripcion' => 'Burger Master',  'und_detal' => 'Unidad', 'categoria' => 'Burger Mania', 'grupo_menu_id' => $g('BURGER'), 'Precio' => 35000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'BG002', 'descripcion' => 'Mexicana',       'und_detal' => 'Unidad', 'categoria' => 'Burger Mania', 'grupo_menu_id' => $g('BURGER'), 'Precio' => 35000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'BG003', 'descripcion' => 'Doble',          'und_detal' => 'Unidad', 'categoria' => 'Burger Mania', 'grupo_menu_id' => $g('BURGER'), 'Precio' => 35000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'BG004', 'descripcion' => 'Burger 360',     'und_detal' => 'Unidad', 'categoria' => 'Burger Mania', 'grupo_menu_id' => $g('BURGER'), 'Precio' => 29900, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'BG005', 'descripcion' => 'Burger Clásica', 'und_detal' => 'Unidad', 'categoria' => 'Burger Mania', 'grupo_menu_id' => $g('BURGER'), 'Precio' => 22000, 'created_at' => now(), 'updated_at' => now()],

            /*
            |--------------------------------------------------------------------------
            | INFANTIL Y RÁPIDOS
            |--------------------------------------------------------------------------
            */

            ['codigo' => 'IR001', 'descripcion' => 'Choripapa',       'und_detal' => 'Unidad',  'categoria' => 'Infantil y Rápidos', 'grupo_menu_id' => $g('INFANTIL'), 'Precio' => 25000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'IR002', 'descripcion' => 'Nuggets de Pollo','und_detal' => 'Porción', 'categoria' => 'Infantil y Rápidos', 'grupo_menu_id' => $g('INFANTIL'), 'Precio' => 28000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'IR003', 'descripcion' => 'MiniPechuga',     'und_detal' => 'Unidad',  'categoria' => 'Infantil y Rápidos', 'grupo_menu_id' => $g('INFANTIL'), 'Precio' => 25000, 'created_at' => now(), 'updated_at' => now()],

            /*
            |--------------------------------------------------------------------------
            | COCTELES
            |--------------------------------------------------------------------------
            */

            ['codigo' => 'CK001', 'descripcion' => 'Mojito Cubano',      'und_detal' => 'Unidad', 'categoria' => 'Cocteles', 'grupo_menu_id' => $g('COCTELES'), 'Precio' => 22000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'CK002', 'descripcion' => 'Daiquiri de Fresa',  'und_detal' => 'Unidad', 'categoria' => 'Cocteles', 'grupo_menu_id' => $g('COCTELES'), 'Precio' => 22000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'CK003', 'descripcion' => 'Cuba Libre',         'und_detal' => 'Unidad', 'categoria' => 'Cocteles', 'grupo_menu_id' => $g('COCTELES'), 'Precio' => 22000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'CK004', 'descripcion' => 'Caipiríssima',       'und_detal' => 'Unidad', 'categoria' => 'Cocteles', 'grupo_menu_id' => $g('COCTELES'), 'Precio' => 22000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'CK005', 'descripcion' => 'Piña Colada',        'und_detal' => 'Unidad', 'categoria' => 'Cocteles', 'grupo_menu_id' => $g('COCTELES'), 'Precio' => 28000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'CK006', 'descripcion' => 'Tequila Sunrise',    'und_detal' => 'Unidad', 'categoria' => 'Cocteles', 'grupo_menu_id' => $g('COCTELES'), 'Precio' => 22000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'CK007', 'descripcion' => 'Margarita Clásico',  'und_detal' => 'Unidad', 'categoria' => 'Cocteles', 'grupo_menu_id' => $g('COCTELES'), 'Precio' => 22000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'CK008', 'descripcion' => 'Margarita Frozen',   'und_detal' => 'Unidad', 'categoria' => 'Cocteles', 'grupo_menu_id' => $g('COCTELES'), 'Precio' => 22000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'CK009', 'descripcion' => 'Gin and Tonic',      'und_detal' => 'Unidad', 'categoria' => 'Cocteles', 'grupo_menu_id' => $g('COCTELES'), 'Precio' => 22000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'CK010', 'descripcion' => 'Tom Collins',        'und_detal' => 'Unidad', 'categoria' => 'Cocteles', 'grupo_menu_id' => $g('COCTELES'), 'Precio' => 22000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'CK011', 'descripcion' => 'Padrino',            'und_detal' => 'Unidad', 'categoria' => 'Cocteles', 'grupo_menu_id' => $g('COCTELES'), 'Precio' => 25000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'CK012', 'descripcion' => 'Moscow Mule',        'und_detal' => 'Unidad', 'categoria' => 'Cocteles', 'grupo_menu_id' => $g('COCTELES'), 'Precio' => 25000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'CK013', 'descripcion' => 'Caipiroska',         'und_detal' => 'Unidad', 'categoria' => 'Cocteles', 'grupo_menu_id' => $g('COCTELES'), 'Precio' => 22000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'CK014', 'descripcion' => 'Cosmopolitan',       'und_detal' => 'Unidad', 'categoria' => 'Cocteles', 'grupo_menu_id' => $g('COCTELES'), 'Precio' => 25000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'CK015', 'descripcion' => 'Cabeza de Jabalí',   'und_detal' => 'Unidad', 'categoria' => 'Cocteles', 'grupo_menu_id' => $g('COCTELES'), 'Precio' => 37000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'CK016', 'descripcion' => 'Aperol SCKitz',      'und_detal' => 'Unidad', 'categoria' => 'Cocteles', 'grupo_menu_id' => $g('COCTELES'), 'Precio' => 25000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'CK017', 'descripcion' => 'Jager-Bomb',         'und_detal' => 'Unidad', 'categoria' => 'Cocteles', 'grupo_menu_id' => $g('COCTELES'), 'Precio' => 22000, 'created_at' => now(), 'updated_at' => now()],

            /*
            |--------------------------------------------------------------------------
            | COCTELES DE LA CASA
            |--------------------------------------------------------------------------
            */

            ['codigo' => 'CC001', 'descripcion' => 'Patada de Obispo',   'und_detal' => 'Unidad', 'categoria' => 'Cocteles de la Casa', 'grupo_menu_id' => $g('COCTELES CASA'), 'Precio' => 39000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'CC002', 'descripcion' => 'Nos Vemos en el Piso','und_detal' => 'Unidad', 'categoria' => 'Cocteles de la Casa', 'grupo_menu_id' => $g('COCTELES CASA'), 'Precio' => 39000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'CC003', 'descripcion' => 'Entre las Sábanas',  'und_detal' => 'Unidad', 'categoria' => 'Cocteles de la Casa', 'grupo_menu_id' => $g('COCTELES CASA'), 'Precio' => 39000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'CC004', 'descripcion' => 'Garrotero',          'und_detal' => 'Unidad', 'categoria' => 'Cocteles de la Casa', 'grupo_menu_id' => $g('COCTELES CASA'), 'Precio' => 35000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'CC005', 'descripcion' => 'Amarre',             'und_detal' => 'Unidad', 'categoria' => 'Cocteles de la Casa', 'grupo_menu_id' => $g('COCTELES CASA'), 'Precio' => 35000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'CC006', 'descripcion' => 'Mi Moza',            'und_detal' => 'Unidad', 'categoria' => 'Cocteles de la Casa', 'grupo_menu_id' => $g('COCTELES CASA'), 'Precio' => 28000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'CC007', 'descripcion' => '¿Qué Pasó Ayer?',   'und_detal' => 'Unidad', 'categoria' => 'Cocteles de la Casa', 'grupo_menu_id' => $g('COCTELES CASA'), 'Precio' => 28000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'CC008', 'descripcion' => 'Beso Francés',       'und_detal' => 'Unidad', 'categoria' => 'Cocteles de la Casa', 'grupo_menu_id' => $g('COCTELES CASA'), 'Precio' => 29000, 'created_at' => now(), 'updated_at' => now()],

            /*
            |--------------------------------------------------------------------------
            | PECERAS
            |--------------------------------------------------------------------------
            */

            ['codigo' => 'PC001', 'descripcion' => 'Jinete Sin Cabeza', 'und_detal' => 'Unidad', 'categoria' => 'Peceras', 'grupo_menu_id' => $g('PECERAS'), 'Precio' => 120000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'PC002', 'descripcion' => 'Huracán 360',       'und_detal' => 'Unidad', 'categoria' => 'Peceras', 'grupo_menu_id' => $g('PECERAS'), 'Precio' => 140000, 'created_at' => now(), 'updated_at' => now()],

            /*
            |--------------------------------------------------------------------------
            | SODAS ITALIANAS
            |--------------------------------------------------------------------------
            */

            ['codigo' => 'SD001', 'descripcion' => 'Soda Frutos Rojos',    'und_detal' => 'Unidad', 'categoria' => 'Sodas Italianas', 'grupo_menu_id' => $g('SODAS'), 'Precio' => 14000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'SD002', 'descripcion' => 'Soda Frutos Amarillos','und_detal' => 'Unidad', 'categoria' => 'Sodas Italianas', 'grupo_menu_id' => $g('SODAS'), 'Precio' => 14000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'SD003', 'descripcion' => 'Soda Maracuyá',        'und_detal' => 'Unidad', 'categoria' => 'Sodas Italianas', 'grupo_menu_id' => $g('SODAS'), 'Precio' => 14000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'SD004', 'descripcion' => 'Soda Mangobiche',      'und_detal' => 'Unidad', 'categoria' => 'Sodas Italianas', 'grupo_menu_id' => $g('SODAS'), 'Precio' => 14000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'SD005', 'descripcion' => 'Soda Lulo',            'und_detal' => 'Unidad', 'categoria' => 'Sodas Italianas', 'grupo_menu_id' => $g('SODAS'), 'Precio' => 14000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'SD006', 'descripcion' => 'Soda Maracumix',       'und_detal' => 'Unidad', 'categoria' => 'Sodas Italianas', 'grupo_menu_id' => $g('SODAS'), 'Precio' => 14000, 'created_at' => now(), 'updated_at' => now()],

            /*
            |--------------------------------------------------------------------------
            | MICHELADAS  (nuevo — del PDF)
            |--------------------------------------------------------------------------
            */

            ['codigo' => 'MC001', 'descripcion' => 'Michelada Tradicional', 'und_detal' => 'Unidad', 'categoria' => 'Micheladas', 'grupo_menu_id' => $g('MICHELADAS'), 'Precio' => 4000,  'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'MC002', 'descripcion' => 'Michelada Mango',       'und_detal' => 'Unidad', 'categoria' => 'Micheladas', 'grupo_menu_id' => $g('MICHELADAS'), 'Precio' => 9000,  'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'MC003', 'descripcion' => 'Michelada Cereza',      'und_detal' => 'Unidad', 'categoria' => 'Micheladas', 'grupo_menu_id' => $g('MICHELADAS'), 'Precio' => 9000,  'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'MC004', 'descripcion' => 'Michelada Mexicana',    'und_detal' => 'Unidad', 'categoria' => 'Micheladas', 'grupo_menu_id' => $g('MICHELADAS'), 'Precio' => 9000,  'created_at' => now(), 'updated_at' => now()],

            /*
            |--------------------------------------------------------------------------
            | CERVEZAS
            |--------------------------------------------------------------------------
            */

            ['codigo' => 'CV001', 'descripcion' => 'Club Colombia', 'und_detal' => 'Unidad', 'categoria' => 'Cervezas', 'grupo_menu_id' => $g('CERVEZAS'), 'Precio' => 8000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'CV002', 'descripcion' => 'Poker',         'und_detal' => 'Unidad', 'categoria' => 'Cervezas', 'grupo_menu_id' => $g('CERVEZAS'), 'Precio' => 7000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'CV003', 'descripcion' => 'Águila',        'und_detal' => 'Unidad', 'categoria' => 'Cervezas', 'grupo_menu_id' => $g('CERVEZAS'), 'Precio' => 7000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'CV004', 'descripcion' => 'Águila Light',  'und_detal' => 'Unidad', 'categoria' => 'Cervezas', 'grupo_menu_id' => $g('CERVEZAS'), 'Precio' => 7000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'CV005', 'descripcion' => 'Costeña',       'und_detal' => 'Unidad', 'categoria' => 'Cervezas', 'grupo_menu_id' => $g('CERVEZAS'), 'Precio' => 7000, 'created_at' => now(), 'updated_at' => now()],

            /*
            |--------------------------------------------------------------------------
            | CERVEZAS IMPORTADAS
            |--------------------------------------------------------------------------
            */

            ['codigo' => 'CI001', 'descripcion' => 'Corona',        'und_detal' => 'Unidad', 'categoria' => 'Cervezas Importadas', 'grupo_menu_id' => $g('CERVEZAS IMPORTADAS'), 'Precio' => 12000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'CI002', 'descripcion' => 'Coronita',      'und_detal' => 'Unidad', 'categoria' => 'Cervezas Importadas', 'grupo_menu_id' => $g('CERVEZAS IMPORTADAS'), 'Precio' => 8000,  'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'CI003', 'descripcion' => 'Budweiser',     'und_detal' => 'Unidad', 'categoria' => 'Cervezas Importadas', 'grupo_menu_id' => $g('CERVEZAS IMPORTADAS'), 'Precio' => 7000,  'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'CI004', 'descripcion' => 'Heineken',      'und_detal' => 'Unidad', 'categoria' => 'Cervezas Importadas', 'grupo_menu_id' => $g('CERVEZAS IMPORTADAS'), 'Precio' => 7000,  'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'CI005', 'descripcion' => 'Stella Artois', 'und_detal' => 'Unidad', 'categoria' => 'Cervezas Importadas', 'grupo_menu_id' => $g('CERVEZAS IMPORTADAS'), 'Precio' => 12000, 'created_at' => now(), 'updated_at' => now()],

            /*
            |--------------------------------------------------------------------------
            | VINOS  (nuevo — del PDF)
            |--------------------------------------------------------------------------
            */

            ['codigo' => 'VN001', 'descripcion' => 'Chandon Extra Brut',     'und_detal' => 'Botella', 'categoria' => 'Vinos', 'grupo_menu_id' => $g('VINOS'), 'Precio' => 140000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'VN002', 'descripcion' => 'Intocables',             'und_detal' => 'Botella', 'categoria' => 'Vinos', 'grupo_menu_id' => $g('VINOS'), 'Precio' => 150000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'VN003', 'descripcion' => 'Ramon Bilbao Verdejo',   'und_detal' => 'Botella', 'categoria' => 'Vinos', 'grupo_menu_id' => $g('VINOS'), 'Precio' => 150000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'VN004', 'descripcion' => 'Jarra de Sangría',       'und_detal' => 'Jarra',   'categoria' => 'Vinos', 'grupo_menu_id' => $g('VINOS'), 'Precio' => 65000,  'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'VN005', 'descripcion' => 'JP Chenet Rosé',         'und_detal' => 'Botella', 'categoria' => 'Vinos', 'grupo_menu_id' => $g('VINOS'), 'Precio' => 130000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'VN006', 'descripcion' => 'JP Chenet Muscat Gold',  'und_detal' => 'Botella', 'categoria' => 'Vinos', 'grupo_menu_id' => $g('VINOS'), 'Precio' => 140000, 'created_at' => now(), 'updated_at' => now()],

            /*
            |--------------------------------------------------------------------------
            | AGUARDIENTE  (nuevo — del PDF)
            |--------------------------------------------------------------------------
            */

            ['codigo' => 'AG001', 'descripcion' => 'Aguardiente Azul y Verde 375ml',          'und_detal' => 'Botella', 'categoria' => 'Aguardiente', 'grupo_menu_id' => $g('AGUARDIENTE'), 'Precio' => 70000,  'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'AG002', 'descripcion' => 'Antioqueño Azul Tetrapack 1050ml',        'und_detal' => 'Tetrapack', 'categoria' => 'Aguardiente', 'grupo_menu_id' => $g('AGUARDIENTE'), 'Precio' => 140000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'AG003', 'descripcion' => 'Antioqueño Verde Tetrapack 1050ml',       'und_detal' => 'Tetrapack', 'categoria' => 'Aguardiente', 'grupo_menu_id' => $g('AGUARDIENTE'), 'Precio' => 140000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'AG004', 'descripcion' => 'Aguardiente Manzanares Tetrapack 1050ml', 'und_detal' => 'Tetrapack', 'categoria' => 'Aguardiente', 'grupo_menu_id' => $g('AGUARDIENTE'), 'Precio' => 145000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'AG005', 'descripcion' => 'Aguardiente Manzanares 375ml',            'und_detal' => 'Botella', 'categoria' => 'Aguardiente', 'grupo_menu_id' => $g('AGUARDIENTE'), 'Precio' => 80000,  'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'AG006', 'descripcion' => 'Aguardiente Amarillo Real 750ml',         'und_detal' => 'Botella', 'categoria' => 'Aguardiente', 'grupo_menu_id' => $g('AGUARDIENTE'), 'Precio' => 120000, 'created_at' => now(), 'updated_at' => now()],

            /*
            |--------------------------------------------------------------------------
            | RON  (nuevo — del PDF)
            |--------------------------------------------------------------------------
            */

            ['codigo' => 'RN001', 'descripcion' => 'Ron Medellín 3 años Tetrapack 1050ml', 'und_detal' => 'Tetrapack', 'categoria' => 'Ron', 'grupo_menu_id' => $g('RON'), 'Precio' => 140000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'RN002', 'descripcion' => 'Ron Medellín 3 años 750ml',            'und_detal' => 'Botella',   'categoria' => 'Ron', 'grupo_menu_id' => $g('RON'), 'Precio' => 120000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'RN003', 'descripcion' => 'Ron Medellín 5 años 750ml',            'und_detal' => 'Botella',   'categoria' => 'Ron', 'grupo_menu_id' => $g('RON'), 'Precio' => 140000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'RN004', 'descripcion' => 'Ron Medellín 8 años 750ml',            'und_detal' => 'Botella',   'categoria' => 'Ron', 'grupo_menu_id' => $g('RON'), 'Precio' => 160000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'RN005', 'descripcion' => 'Ron Caldas 3 años 750ml',              'und_detal' => 'Botella',   'categoria' => 'Ron', 'grupo_menu_id' => $g('RON'), 'Precio' => 120000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'RN006', 'descripcion' => 'Ron Caldas 5 años 750ml',              'und_detal' => 'Botella',   'categoria' => 'Ron', 'grupo_menu_id' => $g('RON'), 'Precio' => 140000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'RN007', 'descripcion' => 'Ron Caldas 8 años 750ml',              'und_detal' => 'Botella',   'categoria' => 'Ron', 'grupo_menu_id' => $g('RON'), 'Precio' => 160000, 'created_at' => now(), 'updated_at' => now()],

            /*
            |--------------------------------------------------------------------------
            | VODKA  (nuevo — del PDF)
            |--------------------------------------------------------------------------
            */

            ['codigo' => 'VK001', 'descripcion' => 'Smirnoff de Lulo 750ml',     'und_detal' => 'Botella', 'categoria' => 'Vodka', 'grupo_menu_id' => $g('VODKA'), 'Precio' => 90000,  'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'VK002', 'descripcion' => 'Smirnoff Red 750ml',         'und_detal' => 'Botella', 'categoria' => 'Vodka', 'grupo_menu_id' => $g('VODKA'), 'Precio' => 130000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'VK003', 'descripcion' => 'Smirnoff Tamarindo 750ml',   'und_detal' => 'Botella', 'categoria' => 'Vodka', 'grupo_menu_id' => $g('VODKA'), 'Precio' => 90000,  'created_at' => now(), 'updated_at' => now()],

            /*
            |--------------------------------------------------------------------------
            | WHISKY  (nuevo — del PDF)
            |--------------------------------------------------------------------------
            */

            ['codigo' => 'WK001', 'descripcion' => "Buchanan's De Luxe 750ml",    'und_detal' => 'Botella', 'categoria' => 'Whisky', 'grupo_menu_id' => $g('WHISKY'), 'Precio' => 260000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'WK002', 'descripcion' => "Buchanan's De Luxe 375ml",    'und_detal' => 'Botella', 'categoria' => 'Whisky', 'grupo_menu_id' => $g('WHISKY'), 'Precio' => 180000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'WK003', 'descripcion' => "Buchanan's Master 750ml",     'und_detal' => 'Botella', 'categoria' => 'Whisky', 'grupo_menu_id' => $g('WHISKY'), 'Precio' => 300000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'WK004', 'descripcion' => "Buchanan's 18 años 750ml",    'und_detal' => 'Botella', 'categoria' => 'Whisky', 'grupo_menu_id' => $g('WHISKY'), 'Precio' => 500000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'WK005', 'descripcion' => "Buchanan's Two Souls 750ml",  'und_detal' => 'Botella', 'categoria' => 'Whisky', 'grupo_menu_id' => $g('WHISKY'), 'Precio' => 350000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'WK006', 'descripcion' => 'Red Label 750ml',             'und_detal' => 'Botella', 'categoria' => 'Whisky', 'grupo_menu_id' => $g('WHISKY'), 'Precio' => 110000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'WK007', 'descripcion' => 'Black Label 750ml',           'und_detal' => 'Botella', 'categoria' => 'Whisky', 'grupo_menu_id' => $g('WHISKY'), 'Precio' => 230000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'WK008', 'descripcion' => 'Old Parr 12 años 750ml',      'und_detal' => 'Botella', 'categoria' => 'Whisky', 'grupo_menu_id' => $g('WHISKY'), 'Precio' => 230000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'WK009', 'descripcion' => 'Old Parr 12 años 500ml',      'und_detal' => 'Botella', 'categoria' => 'Whisky', 'grupo_menu_id' => $g('WHISKY'), 'Precio' => 180000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'WK010', 'descripcion' => 'Old Parr 18 años 750ml',      'und_detal' => 'Botella', 'categoria' => 'Whisky', 'grupo_menu_id' => $g('WHISKY'), 'Precio' => 350000, 'created_at' => now(), 'updated_at' => now()],

            /*
            |--------------------------------------------------------------------------
            | GINEBRA  (nuevo — del PDF)
            |--------------------------------------------------------------------------
            */

            ['codigo' => 'GN001', 'descripcion' => 'Tanqueray 750ml', 'und_detal' => 'Botella', 'categoria' => 'Ginebra', 'grupo_menu_id' => $g('GINEBRA'), 'Precio' => 230000, 'created_at' => now(), 'updated_at' => now()],

            /*
            |--------------------------------------------------------------------------
            | LICORES CREMOSOS  (nuevo — del PDF)
            |--------------------------------------------------------------------------
            */

            ['codigo' => 'LC001', 'descripcion' => 'Baileys 700ml',    'und_detal' => 'Botella', 'categoria' => 'Licores Cremosos', 'grupo_menu_id' => $g('LICORES CREMOSOS'), 'Precio' => 150000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'LC002', 'descripcion' => 'Trago de Baileys', 'und_detal' => 'Unidad',  'categoria' => 'Licores Cremosos', 'grupo_menu_id' => $g('LICORES CREMOSOS'), 'Precio' => 18000,  'created_at' => now(), 'updated_at' => now()],

            /*
            |--------------------------------------------------------------------------
            | TEQUILAS  (nuevo — del PDF)
            |--------------------------------------------------------------------------
            */

            ['codigo' => 'TQ001', 'descripcion' => 'Don Julio Blanco 750ml',    'und_detal' => 'Botella', 'categoria' => 'Tequilas', 'grupo_menu_id' => $g('TEQUILAS'), 'Precio' => 320000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'TQ002', 'descripcion' => 'Don Julio Añejo 750ml',     'und_detal' => 'Botella', 'categoria' => 'Tequilas', 'grupo_menu_id' => $g('TEQUILAS'), 'Precio' => 410000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'TQ003', 'descripcion' => 'Don Julio Reposado 750ml',  'und_detal' => 'Botella', 'categoria' => 'Tequilas', 'grupo_menu_id' => $g('TEQUILAS'), 'Precio' => 350000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'TQ004', 'descripcion' => "Don Julio 70' 750ml",       'und_detal' => 'Botella', 'categoria' => 'Tequilas', 'grupo_menu_id' => $g('TEQUILAS'), 'Precio' => 520000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'TQ005', 'descripcion' => 'José Cuervo Especial 750ml','und_detal' => 'Botella', 'categoria' => 'Tequilas', 'grupo_menu_id' => $g('TEQUILAS'), 'Precio' => 160000, 'created_at' => now(), 'updated_at' => now()],

            /*
            |--------------------------------------------------------------------------
            | CUBETAZOS  (nuevo — del PDF)
            |--------------------------------------------------------------------------
            */

            ['codigo' => 'CB001', 'descripcion' => 'Cubetazo Poker / Águila / Costeña / Budweiser / Heineken (x10)', 'und_detal' => 'Cubeta', 'categoria' => 'Cubetazos', 'grupo_menu_id' => $g('CUBETAZOS'), 'Precio' => 60000,  'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'CB002', 'descripcion' => 'Cubetazo Corona (x10)',                                          'und_detal' => 'Cubeta', 'categoria' => 'Cubetazos', 'grupo_menu_id' => $g('CUBETAZOS'), 'Precio' => 120000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'CB003', 'descripcion' => 'Cubetazo Coronita (x10)',                                        'und_detal' => 'Cubeta', 'categoria' => 'Cubetazos', 'grupo_menu_id' => $g('CUBETAZOS'), 'Precio' => 70000,  'created_at' => now(), 'updated_at' => now()],

            /*
            |--------------------------------------------------------------------------
            | OTROS (bebidas embotelladas)
            |--------------------------------------------------------------------------
            */

            ['codigo' => 'OT001', 'descripcion' => 'Gaseosa Coca Cola 400ml',  'und_detal' => 'Unidad', 'categoria' => 'Otros', 'grupo_menu_id' => null, 'Precio' => 5000,  'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'OT002', 'descripcion' => 'Gaseosa Postobón 400ml',   'und_detal' => 'Unidad', 'categoria' => 'Otros', 'grupo_menu_id' => null, 'Precio' => 5000,  'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'OT003', 'descripcion' => 'Té Hatsu',                 'und_detal' => 'Unidad', 'categoria' => 'Otros', 'grupo_menu_id' => null, 'Precio' => 6000,  'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'OT004', 'descripcion' => 'Soda Hatsu',               'und_detal' => 'Unidad', 'categoria' => 'Otros', 'grupo_menu_id' => null, 'Precio' => 6000,  'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'OT005', 'descripcion' => 'Gatorade',                 'und_detal' => 'Unidad', 'categoria' => 'Otros', 'grupo_menu_id' => null, 'Precio' => 8000,  'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'OT006', 'descripcion' => 'Electrolit',               'und_detal' => 'Unidad', 'categoria' => 'Otros', 'grupo_menu_id' => null, 'Precio' => 15000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'OT007', 'descripcion' => 'Red Bull',                 'und_detal' => 'Unidad', 'categoria' => 'Otros', 'grupo_menu_id' => null, 'Precio' => 12000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'OT008', 'descripcion' => 'Bretaña',                  'und_detal' => 'Unidad', 'categoria' => 'Otros', 'grupo_menu_id' => null, 'Precio' => 6000,  'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'OT009', 'descripcion' => 'Speed Max',                'und_detal' => 'Unidad', 'categoria' => 'Otros', 'grupo_menu_id' => null, 'Precio' => 6000,  'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'OT010', 'descripcion' => 'Ginger',                   'und_detal' => 'Unidad', 'categoria' => 'Otros', 'grupo_menu_id' => null, 'Precio' => 6000,  'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'OT011', 'descripcion' => 'Agua Cristal',             'und_detal' => 'Unidad', 'categoria' => 'Otros', 'grupo_menu_id' => null, 'Precio' => 5000,  'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'OT012', 'descripcion' => 'Agua Cristal con Gas',     'und_detal' => 'Unidad', 'categoria' => 'Otros', 'grupo_menu_id' => null, 'Precio' => 5000,  'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'OT013', 'descripcion' => 'Smirnoff Red (unidad)',    'und_detal' => 'Unidad', 'categoria' => 'Otros', 'grupo_menu_id' => null, 'Precio' => 13000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'OT014', 'descripcion' => 'Smirnoff Ice Apple',       'und_detal' => 'Unidad', 'categoria' => 'Otros', 'grupo_menu_id' => null, 'Precio' => 13000, 'created_at' => now(), 'updated_at' => now()],

        ]);
    }
}