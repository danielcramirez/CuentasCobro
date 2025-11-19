<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Roles;
use Illuminate\Support\Facades\Hash;

class AlcaldeUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar el rol de Alcalde
        $alcaldeRole = Roles::where('name', 'alcalde')->first();

        if (!$alcaldeRole) {
            $this->command->error('El rol "alcalde" no existe. Ejecuta primero el RoleSeeder.');
            return;
        }

        // Crear usuario Alcalde si no existe
        $alcalde = User::where('email', 'daniel00250@hotmail.com')->first();

        if (!$alcalde) {
            $alcalde = User::create([
                'name' => 'Daniel Ramirez',
                'email' => 'daniel00250@hotmail.com',
                'password' => Hash::make('12345678'),
                'role_id' => $alcaldeRole->id,
                'email_verified_at' => now(),
            ]);

            $this->command->info('Usuario Alcalde creado exitosamente.');
        } else {
            $this->command->info('El usuario Alcalde ya existe.');
        }
    }
}
