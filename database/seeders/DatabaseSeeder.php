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
        // 1. Primero lo básico e independiente
        $this->call(PermisoSeeder::class); // Crea la lista de qué se puede hacer

        // 2. Crear Roles y asignarles los permisos (puedes hacerlo en un Seeder aparte o aquí)
        $this->call(RolSeeder::class);

        // 3. Ahora que existen los Roles, creamos los Usuarios
        $this->call(UserSeeder::class);

        // 4. Datos maestros del negocio
        $this->call(ProductoSeeder::class);
        $this->call(BodegaSeeder::class);
        $this->call(TerceroSeeder::class);

        // 5. Datos que dependen de productos, bodegas y usuarios (como el stock inicial)
        $this->call(InventarioSeeder::class);
        $this->call([GastrobarSeeder::class]);
    }
}
