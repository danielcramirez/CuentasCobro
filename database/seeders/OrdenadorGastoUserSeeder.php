<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Roles;
use Illuminate\Support\Facades\Hash;

class OrdenadorGastoUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar el rol de Ordenador del Gasto
        $ordenadorRole = Roles::where('name', 'ordenador_gasto')->first();

        if (!$ordenadorRole) {
            $this->command->error('El rol "ordenador_gasto" no existe. Ejecuta primero el RoleSeeder.');
            return;
        }

        // Crear usuario Ordenador del Gasto si no existe
        $ordenador = User::where('email', 'organizador.gasto@cuentascobro.com')->first();

        if (!$ordenador) {
            $ordenador = User::create([
                'name' => 'Emmanuel kant',
                'email' => 'organizador.gasto@cuentascobro.com',
                'password' => Hash::make('12345678'),
                'role_id' => $ordenadorRole->id,
                'email_verified_at' => now(),
            ]);

            $this->command->info('Usuario Ordenador del Gasto creado exitosamente.');
        } else {
            $this->command->info('El usuario Ordenador del Gasto ya existe.');
        }
    }
}
