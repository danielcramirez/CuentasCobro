<?php

namespace Database\Seeders;

use App\Models\Roles;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OperativeUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contratistaRole = Roles::where('name', 'contratista')->first();
        $apoyoSupervisionRole = Roles::where('name', 'apoyo a la supervisión')->first();

        if (!$contratistaRole || !$apoyoSupervisionRole) {
            $this->command->error('No se encontraron los roles contratista o apoyo a la supervisión. Ejecuta RoleSeeder primero.');
            return;
        }

        $contratista = User::updateOrCreate(
            ['email' => 'contratista@cuentascobro.local'],
            [
                'name' => 'Usuario Contratista',
                'email' => 'contratista@cuentascobro.local',
                'password' => Hash::make('cosa1234'),
                'role_id' => $contratistaRole->id,
                'email_verified_at' => now(),
            ]
        );

        $apoyoSupervision = User::updateOrCreate(
            ['email' => 'apoyo@cuentascobro.local'],
            [
                'name' => 'Usuario Apoyo Supervisión',
                'email' => 'apoyo@cuentascobro.local',
                'password' => Hash::make('cosa1234'),
                'role_id' => $apoyoSupervisionRole->id,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Usuarios operativos creados/actualizados:');
        $this->command->info('Contratista: ' . $contratista->email . ' / cosa1234');
        $this->command->info('Apoyo Supervisión: ' . $apoyoSupervision->email . ' / cosa1234');
    }
}
