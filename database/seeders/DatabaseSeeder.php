<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,              // Primero los roles
            AdminUserSeeder::class,         // Usuario administrador
            SupervisorUserSeeder::class,    // Usuarios supervisores
            TesoreriaUserSeeder::class,     // Usuarios de tesorería
            AlcaldeUserSeeder::class,       // Usuario alcalde
            OrdenadorGastoUserSeeder::class, // Usuario ordenador del gasto
            ContratistaUserSeeder::class,   // Usuario contratista
            ContratacionUserSeeder::class,  // Usuario contratación
        ]);
    }
}
