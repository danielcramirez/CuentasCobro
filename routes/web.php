<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CrearUsuario;
use App\Http\Controllers\RolControler;
use App\Http\Controllers\CuentaCobroController;

// Ruta raíz redirige al login
Route::get('/', function () {
    return redirect('/login');
});

// Rutas de autenticación
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas Crear usuarios
Route::get('/register', [CrearUsuario::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [CrearUsuario::class, 'register']);

// Rutas protegidas por autenticación
Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

    // Rutas de Cuentas de Cobro
    Route::resource('cuentas-cobro', CuentaCobroController::class)
        ->except(['show'])
        ->names([
        'index' => 'cuentas-cobro.mostrar',
        'create' => 'cuentas-cobro.crear',
        'store' => 'cuentas-cobro.store',
        'edit' => 'cuentas-cobro.edit',
        'destroy' => 'cuentas-cobro.destroy'
    ]);

    //Route::get('/cuentas-cobro', [CuentaCobroController::class, 'index'])->name('cuentas-cobro.mostrar');


    
    // Rutas de Roles (Resource Routes)
    Route::resource('roles', RolControler::class)->except(['show'])->names([
        'index' => 'roles.index',
        'create' => 'roles.create',
        'store' => 'roles.store',
        'edit' => 'roles.edit',
        'update' => 'roles.update',
        'destroy' => 'roles.destroy'
    ]);

    // Ruta personalizada para show (usando {role} en lugar de {id})
    Route::get('/roles/{role}', [RolControler::class, 'show'])->name('roles.show');
    
    // Rutas adicionales para gestión de roles y usuarios
    Route::prefix('roles')->name('roles.')->group(function () {
        // Asignar/remover roles a usuarios (AJAX)
        Route::post('/assign-role', [RolControler::class, 'assignRole'])->name('assign');
        Route::post('/remove-role', [RolControler::class, 'removeRole'])->name('remove');
        
        // Obtener usuarios sin rol (AJAX)
        Route::get('/users-without-role', [RolControler::class, 'getUsersWithoutRole'])->name('users.without.role');
    });
    
    // Rutas adicionales que podrías necesitar más adelante
    Route::prefix('admin')->middleware(['auth', 'check.role:alcalde'])->name('admin.')->group(function () {
        
        // Gestión de usuarios (futuras funcionalidades)
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', function() {
                return view('admin.users.index');
            })->name('index');
            
            Route::post('/{user}/assign-role', function() {
                // Asignar rol a usuario específico
            })->name('assign.role');
        });
        
        // Configuración del sistema (futuras funcionalidades)
        Route::get('/settings', function() {
            return view('admin.settings');
        })->name('settings');
    });
});

// Rutas que requieren roles específicos (ejemplos para futuro uso)
Route::middleware(['auth'])->group(function () {
    
    // Solo para contratistas
    Route::middleware(['check.role:contratista'])->prefix('contratista')->name('contratista.')->group(function () {
        Route::get('/dashboard', function() {
            return view('contratista.dashboard');
        })->name('dashboard');
    });
    
    // Solo para supervisores
    Route::middleware(['check.role:supervisor'])->prefix('supervisor')->name('supervisor.')->group(function () {
        Route::get('/dashboard', function() {
            return view('supervisor.dashboard');
        })->name('dashboard');
    });
    
    // Solo para roles administrativos (alcalde, ordenador del gasto)
    Route::middleware(['check.role:alcalde,ordenador_gasto'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/reports', function() {
            return view('admin.reports');
        })->name('reports');
    });
});