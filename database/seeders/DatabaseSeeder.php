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
            RoleSeeder::class,        // Primero los roles
            AdminUserSeeder::class,   // Luego el usuario administrador
            OperativeUsersSeeder::class, // Usuarios contratista y supervisor
        ]);
    }
}
