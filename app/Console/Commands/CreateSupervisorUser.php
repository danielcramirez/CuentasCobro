<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Roles;
use Illuminate\Support\Facades\Hash;

class CreateSupervisorUser extends Command
{
    protected $signature = 'user:create-supervisor {name} {email} {password}';
    protected $description = 'Create a supervisor user';

    public function handle()
    {
        $supervisorRole = Roles::where('name', 'supervisor')->first();
        
        if (!$supervisorRole) {
            $this->error('Supervisor role not found. Run php artisan db:seed --class=RoleSeeder first.');
            return 1;
        }

        $supervisor = User::create([
            'name' => $this->argument('name'),
            'email' => $this->argument('email'),
            'password' => Hash::make($this->argument('password')),
            'role_id' => $supervisorRole->id,
            'email_verified_at' => now(),
        ]);

        $this->info("✅ Supervisor user created successfully:");
        $this->info("   👤 Name: {$supervisor->name}");
        $this->info("   📧 Email: {$supervisor->email}");
        $this->info("   🔑 Password: {$this->argument('password')}");
        $this->info("   👔 Role: Supervisor");

        return 0;
    }
}
