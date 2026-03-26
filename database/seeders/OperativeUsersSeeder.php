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
        $password = 'cosa1234';

        $usuariosOperativos = [
            [
                'role' => 'contratista',
                'name' => 'Usuario Contratista',
                'email' => 'contratista@cuentascobro.local',
            ],
            [
                'role' => 'apoyo a la supervisión',
                'name' => 'Usuario Apoyo Supervisión',
                'email' => 'apoyo@cuentascobro.local',
            ],
            [
                'role' => 'supervisor',
                'name' => 'Usuario Supervisor',
                'email' => 'supervisor@cuentascobro.local',
            ],
            [
                'role' => 'central de cuentas',
                'name' => 'Usuario Central de Cuentas',
                'email' => 'tesoreria@cuentascobro.local',
            ],
            [
                'role' => 'fiduprevisora',
                'name' => 'Usuario Fiduprevisora',
                'email' => 'fiduprevisora@cuentascobro.local',
            ],
        ];

        $roles = Roles::whereIn('name', array_column($usuariosOperativos, 'role'))
            ->get()
            ->keyBy('name');

        $faltantes = [];
        foreach ($usuariosOperativos as $item) {
            if (!$roles->has($item['role'])) {
                $faltantes[] = $item['role'];
            }
        }

        if (!empty($faltantes)) {
            $this->command->error('No se encontraron estos roles: ' . implode(', ', $faltantes) . '. Ejecuta RoleSeeder primero.');
            return;
        }

        $usuariosCreados = [];

        foreach ($usuariosOperativos as $item) {
            $usuario = User::updateOrCreate(
                ['email' => $item['email']],
                [
                    'name' => $item['name'],
                    'email' => $item['email'],
                    'password' => Hash::make($password),
                    'role_id' => $roles[$item['role']]->id,
                    'email_verified_at' => now(),
                ]
            );

            $usuariosCreados[] = [
                'role' => $item['role'],
                'email' => $usuario->email,
            ];
        }

        $this->command->info('Usuarios operativos creados/actualizados (password: ' . $password . '):');
        foreach ($usuariosCreados as $usuario) {
            $this->command->info(ucfirst($usuario['role']) . ': ' . $usuario['email']);
        }
    }
}
