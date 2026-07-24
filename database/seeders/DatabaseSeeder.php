<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Permisos y roles
        $this->call(PermisoSeeder::class);
        $this->call(RolSeeder::class);

        // 2. Datos base
        $this->call(BodegaSeeder::class);

        $this->call(CajaSeeder::class);
        $this->call(UserSeeder::class);


        // 5. Productos y demás
        $this->call(GrupoMenuSeeder::class);
        $this->call(ProductoSeeder::class);
        $this->call(TerceroSeeder::class);
        $this->call(InventarioSeeder::class);
        $this->call(GastrobarSeeder::class);
        $this->call(CategoriasPosSeeder::class);
        $this->call(TipoDocumentoContableSeeder::class);
        $this->call(ConfiguracionContableSeeder::class);
        $this->call(ProcesoContableSeeder::class);
        $this->call(PlantillaContableSeeder::class);
        $this->call(PucSeeder::class);
        $this->call(ParametrizacionInicialContableSeeder::class);
    }
}
