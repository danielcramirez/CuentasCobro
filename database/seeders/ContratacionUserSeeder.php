<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Roles;
use Illuminate\Support\Facades\Hash;

class ContratacionUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar el rol de Contratación
        $contratacionRole = Roles::where('name', 'contratacion')->first();

        if (!$contratacionRole) {
            $this->command->error('El rol "contratacion" no existe. Ejecuta primero el RoleSeeder.');
            return;
        }

        // Crear usuario Contratación si no existe
        $contratacion = User::where('email', 'contratacion@cuentascobro.com')->first();

        if (!$contratacion) {
            $contratacion = User::create([
                'name' => 'Jose Luis Peña',
                'email' => 'contratacion@cuentascobro.com',
                'password' => Hash::make('12345678'),
                'role_id' => $contratacionRole->id,
                'email_verified_at' => now(),
            ]);

            $this->command->info('Usuario Contratación creado exitosamente.');
        } else {
            $this->command->info('El usuario Contratación ya existe.');
        }
    }
}
