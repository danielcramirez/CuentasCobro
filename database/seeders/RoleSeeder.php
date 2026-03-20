<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Roles;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'contratista',
                'description' => 'Contratista - Presenta cuentas de cobro',
                'permissions' => [
                    'create_cuenta_cobro',
                    'view_own_cuenta_cobro',
                    'edit_own_cuenta_cobro',
                    'upload_documents',
                    'view_contract_info'
                ]
            ],
            [
                'name' => 'apoyo a la supervisión',
                'description' => 'Apoyo a la Supervisión - Revisa y valida las cuentas de cobro',
                'permissions' => [
                    'view_cuenta_cobro',
                    'review_cuenta_cobro',
                    'approve_cuenta_cobro',
                    'reject_cuenta_cobro',
                    'add_comments',
                    'request_corrections'
                ]
            ],
            [
                'name' => 'supervisor',
                'description' => 'Supervisión - Aprobación ejecutiva final',
                'permissions' => [
                    'view_all_cuenta_cobro',
                    'final_approval',
                    'override_decisions'
                ]
            ],
            [
                'name' => 'admin',
                'description' => 'Administrador - Gestión total del sistema',
                'permissions' => [
                    'manage_users',
                    'manage_roles',
                    'view_reports',
                    'system_admin'
                ]
            ]
        ];

        foreach ($roles as $roleData) {
            Roles::firstOrCreate(
                ['name' => $roleData['name']],
                [
                    'description' => $roleData['description'],
                    'permissions' => $roleData['permissions']
                ]
            );
        }

        $this->command->info('Roles creados exitosamente.');
    }
}