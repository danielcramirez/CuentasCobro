<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CrearUsuario;
use App\Http\Controllers\RolControler;
use App\Http\Controllers\CuentaCobroController;
use App\Http\Controllers\ContratistaDashboardController;

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

    // Dashboard específico para contratista
    Route::middleware(['check.role:contratista'])->group(function () {
        Route::get('/contratista/dashboard', [ContratistaDashboardController::class, 'index'])->name('contratista.dashboard');
        Route::get('/api/contratista/dashboard-data', [ContratistaDashboardController::class, 'getDashboardData'])->name('api.contratista.dashboard');
    });

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

    // Rutas adicionales para Cuentas de Cobro
    Route::prefix('cuentas-cobro')->name('cuentas-cobro.')->group(function () {
        Route::post('/{id}/cambiar-estado', [CuentaCobroController::class, 'cambiarEstado'])->name('cambiar-estado');
        Route::get('/estadisticas', [CuentaCobroController::class, 'estadisticas'])->name('estadisticas');
        Route::get('/{id}/descargar', [CuentaCobroController::class, 'descargar'])->name('descargar');
    });


    
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

// Rutas adicionales que requieren roles específicos (placeholders para futuro uso)
// NOTA: Las rutas principales de dashboard están definidas arriba usando controladores