<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Roles;
use Illuminate\Support\Facades\Hash;

class SupervisorUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar el rol de supervisor
        $supervisorRole = Roles::where('name', 'supervisor')->first();
        
        if (!$supervisorRole) {
            $this->command->error('El rol de supervisor no existe. Ejecuta primero el RoleSeeder.');
            return;
        }

        // Crear usuarios supervisores de prueba
        $supervisors = [
            [
                'name' => 'Ana García Supervisor',
                'email' => 'supervisor@cuentascobro.com',
                'password' => 'supervisor123'
            ],
            [
                'name' => 'Carlos Mendez',
                'email' => 'carlos.supervisor@cuentascobro.com', 
                'password' => 'supervisor123'
            ]
        ];

        foreach ($supervisors as $supervisorData) {
            $supervisor = User::firstOrCreate(
                ['email' => $supervisorData['email']],
                [
                    'name' => $supervisorData['name'],
                    'email' => $supervisorData['email'],
                    'password' => Hash::make($supervisorData['password']),
                    'role_id' => $supervisorRole->id,
                    'email_verified_at' => now(),
                ]
            );

            if ($supervisor->wasRecentlyCreated) {
                $this->command->info('✅ Usuario supervisor creado exitosamente:');
                $this->command->info("   👤 Nombre: {$supervisor->name}");
                $this->command->info("   📧 Email: {$supervisor->email}");
                $this->command->info("   🔑 Contraseña: {$supervisorData['password']}");
                $this->command->info('   👔 Rol: Supervisor');
            } else {
                $this->command->info("ℹ️  El usuario supervisor {$supervisor->email} ya existe.");
                
                // Actualizar el rol si es necesario
                if ($supervisor->role_id !== $supervisorRole->id) {
                    $supervisor->update(['role_id' => $supervisorRole->id]);
                    $this->command->info('✅ Rol actualizado a Supervisor.');
                }
            }
        }

        $this->command->info('');
        $this->command->info('🎯 Supervisores disponibles para pruebas:');
        $this->command->info('   📧 supervisor@cuentascobro.com / supervisor123');
        $this->command->info('   📧 carlos.supervisor@cuentascobro.com / supervisor123');
    }
}
