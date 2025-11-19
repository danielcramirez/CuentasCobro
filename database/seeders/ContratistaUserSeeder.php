<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Roles;
use Illuminate\Support\Facades\Hash;

class ContratistaUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar el rol de Contratista
        $contratistaRole = Roles::where('name', 'contratista')->first();

        if (!$contratistaRole) {
            $this->command->error('El rol "contratista" no existe. Ejecuta primero el RoleSeeder.');
            return;
        }

        // Crear usuario Contratista si no existe
        $contratista = User::where('email', 'johndoe@example.com')->first();

        if (!$contratista) {
            $contratista = User::create([
                'name' => 'john doe',
                'email' => 'johndoe@example.com',
                'password' => Hash::make('12345678'),
                'role_id' => $contratistaRole->id,
                'email_verified_at' => now(),
            ]);

            $this->command->info('Usuario Contratista creado exitosamente.');
        } else {
            $this->command->info('El usuario Contratista ya existe.');
        }
    }
}
