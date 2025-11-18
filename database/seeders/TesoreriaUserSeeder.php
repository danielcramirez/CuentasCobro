<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Roles;

class TesoreriaUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Buscar el rol de tesorería
        $tesoreriaRole = Roles::where('name', 'tesoreria')->first();

        if (!$tesoreriaRole) {
            $this->command->error('El rol "tesoreria" no existe. Ejecuta primero RoleSeeder.');
            return;
        }

        // Crear usuario de tesorería principal
        $tesoreriaUser = User::updateOrCreate(
            ['email' => 'tesoreria@example.com'],
            [
                'name' => 'Ana García Tesorera',
                'email' => 'tesoreria@example.com',
                'password' => Hash::make('12345678'),
                'role_id' => $tesoreriaRole->id,
                'email_verified_at' => now(),
            ]
        );

        // Crear usuario auxiliar de tesorería
        $auxiliarTesoreria = User::updateOrCreate(
            ['email' => 'auxiliar.tesoreria@example.com'],
            [
                'name' => 'Carlos Ruiz Auxiliar',
                'email' => 'auxiliar.tesoreria@example.com',
                'password' => Hash::make('12345678'),
                'role_id' => $tesoreriaRole->id,
                'email_verified_at' => now(),
            ]
        );

        // Crear jefe de tesorería
        $jefeTesoreria = User::updateOrCreate(
            ['email' => 'jefe.tesoreria@example.com'],
            [
                'name' => 'María López Jefe Tesorería',
                'email' => 'jefe.tesoreria@example.com',
                'password' => Hash::make('12345678'),
                'role_id' => $tesoreriaRole->id,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Usuarios de tesorería creados exitosamente:');
        $this->command->line("- {$tesoreriaUser->name} ({$tesoreriaUser->email})");
        $this->command->line("- {$auxiliarTesoreria->name} ({$auxiliarTesoreria->email})");
        $this->command->line("- {$jefeTesoreria->name} ({$jefeTesoreria->email})");
        $this->command->info('Contraseña para todos: 12345678');
    }
}
