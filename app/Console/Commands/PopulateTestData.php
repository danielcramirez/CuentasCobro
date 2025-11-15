<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Roles;
use App\Models\CuentaCobro;
use Illuminate\Support\Facades\Hash;

class PopulateTestData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dashboard:populate-test-data {--fresh : Delete existing data first}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate the database with test data for dashboard testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('fresh')) {
            $this->info('🗑️  Clearing existing data...');
            CuentaCobro::query()->delete();
            User::whereNotIn('email', ['admin@admin.com'])->delete();
        }

        $this->info('🚀 Populating test data for dashboard...');

        // Crear usuarios de prueba si no existen
        $this->createTestUsers();
        
        // Crear cuentas de cobro de prueba
        $this->createTestCuentasCobro();
        
        $this->info('✅ Test data populated successfully!');
        $this->showStatistics();
    }

    private function createTestUsers()
    {
        $this->info('👥 Creating test users...');

        $roles = Roles::all()->keyBy('name');
        
        $testUsers = [
            [
                'name' => 'Juan Contratista',
                'email' => 'contratista@test.com',
                'role' => 'contratista'
            ],
            [
                'name' => 'María Supervisora',
                'email' => 'supervisor@test.com',
                'role' => 'supervisor'
            ],
            [
                'name' => 'Carlos Tesorero',
                'email' => 'tesoreria@test.com',
                'role' => 'tesoreria'
            ],
            [
                'name' => 'Ana Ordenadora',
                'email' => 'ordenador@test.com',
                'role' => 'ordenador_gasto'
            ],
            [
                'name' => 'Luis Contratación',
                'email' => 'contratacion@test.com',
                'role' => 'contratacion'
            ],
            [
                'name' => 'Sin Rol Usuario',
                'email' => 'sinrol@test.com',
                'role' => null
            ]
        ];

        foreach ($testUsers as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password'),
                    'role_id' => $userData['role'] ? $roles[$userData['role']]->id ?? null : null
                ]
            );
            
            $this->line("  ✓ {$user->name} ({$user->email})");
        }
    }

    private function createTestCuentasCobro()
    {
        $this->info('📄 Creating test cuentas de cobro...');

        $contratistas = User::whereHas('role', function($query) {
            $query->where('name', 'contratista');
        })->get();

        if ($contratistas->isEmpty()) {
            $this->warn('No contractors found, skipping cuentas de cobro creation');
            return;
        }

        $estados = [
            CuentaCobro::ESTADO_BORRADOR,
            CuentaCobro::ESTADO_PENDIENTE,
            CuentaCobro::ESTADO_REVISION,
            CuentaCobro::ESTADO_APROBADO,
            CuentaCobro::ESTADO_RECHAZADO,
            CuentaCobro::ESTADO_PAGADO
        ];

        $proyectos = [
            'Desarrollo Sistema Web',
            'Consultoría IT',
            'Mantenimiento Software',
            'Análisis de Sistemas',
            'Capacitación Técnica',
            'Soporte Técnico',
            'Diseño UI/UX',
            'Testing y QA'
        ];

        foreach ($contratistas as $contratista) {
            $numCuentas = rand(3, 8);
            
            for ($i = 0; $i < $numCuentas; $i++) {
                $fechaEmision = now()->subDays(rand(1, 60));
                
                CuentaCobro::create([
                    'user_id' => $contratista->id,
                    'fecha_emision' => $fechaEmision,
                    'proyecto_servicio' => $proyectos[array_rand($proyectos)],
                    'valor' => rand(500000, 5000000), // Entre 500k y 5M
                    'estado' => $estados[array_rand($estados)],
                    'created_at' => $fechaEmision,
                    'updated_at' => $fechaEmision->addDays(rand(0, 10))
                ]);
            }
            
            $this->line("  ✓ Created {$numCuentas} cuentas for {$contratista->name}");
        }
    }

    private function showStatistics()
    {
        $this->info('📊 Current Statistics:');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Users', User::count()],
                ['Users with Roles', User::whereNotNull('role_id')->count()],
                ['Users without Roles', User::whereNull('role_id')->count()],
                ['Total Roles', Roles::count()],
                ['Total Cuentas de Cobro', CuentaCobro::count()],
                ['Pending Reviews', CuentaCobro::where('estado', CuentaCobro::ESTADO_PENDIENTE)->count()],
                ['Approved', CuentaCobro::where('estado', CuentaCobro::ESTADO_APROBADO)->count()],
                ['Paid', CuentaCobro::where('estado', CuentaCobro::ESTADO_PAGADO)->count()],
            ]
        );

        $this->newLine();
        $this->info('🔑 Test user credentials:');
        $this->line('Email: contratista@test.com | Password: password');
        $this->line('Email: supervisor@test.com | Password: password');
        $this->line('Email: tesoreria@test.com | Password: password');
        $this->newLine();
        $this->comment('You can now test the dashboard with real data!');
    }
}
