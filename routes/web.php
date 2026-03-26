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
    
    // Rutas de Roles (Resource Routes)
    Route::middleware(['auth'])->group(function () {
        Route::resource('roles', RolControler::class)->except(['show'])->names([
            'index' => 'roles.index',
            'create' => 'roles.create',
            'store' => 'roles.store',
            'edit' => 'roles.edit',
            'update' => 'roles.update',
            'destroy' => 'roles.destroy'
        ]);
    });
    
    // Ruta personalizada para show (usando {role} en lugar de {id})
    Route::get('/roles/{role}', [RolControler::class, 'show'])->name('roles.show');

    // Flujo de cuentas de cobro
    Route::prefix('cuentas')->name('cuentas.')->group(function () {
        Route::get('/', [CuentaCobroController::class, 'index'])->name('index');
        Route::get('/create', [CuentaCobroController::class, 'create'])->middleware('check.role:contratista')->name('create');
        Route::post('/', [CuentaCobroController::class, 'store'])->middleware('check.role:contratista')->name('store');
        Route::get('/{cuentaCobro}', [CuentaCobroController::class, 'show'])->name('show');
        Route::post('/{cuentaCobro}/supervisor-review', [CuentaCobroController::class, 'supervisorReview'])->middleware('check.role:apoyo a la supervisión,apoyo a la supervision,apoyo a la supervicion')->name('supervisor.review');
        Route::post('/{cuentaCobro}/documento/{documento}/review', [CuentaCobroController::class, 'reviewDocumento'])->middleware('check.role:apoyo a la supervisión,apoyo a la supervision,apoyo a la supervicion')->name('documento.review');
        Route::post('/{cuentaCobro}/alcalde-review', [CuentaCobroController::class, 'mayorReview'])->middleware('check.role:admin')->name('alcalde.review');
        Route::post('/{cuentaCobro}/tesoreria-review', [CuentaCobroController::class, 'tesoreriaReview'])->middleware('check.role:central de cuentas,tesoreria')->name('tesoreria.review');
        Route::post('/{cuentaCobro}/fiduprevisora-review', [CuentaCobroController::class, 'fiduprevisoraReview'])->middleware('check.role:fiduprevisora')->name('fiduprevisora.review');
        Route::post('/{cuentaCobro}/fiduprevisora/documento/{documento}/review', [CuentaCobroController::class, 'reviewDocumentoFiduprevisora'])->middleware('check.role:fiduprevisora')->name('fiduprevisora.documento.review');
        Route::post('/{cuentaCobro}/resubmit', [CuentaCobroController::class, 'resubmit'])->middleware('check.role:contratista')->name('resubmit');
        Route::get('/{cuentaCobro}/download/{type}', [CuentaCobroController::class, 'download'])->name('download');
        Route::get('/{cuentaCobro}/preview/{type}', [CuentaCobroController::class, 'preview'])->name('preview');
        Route::get('/{cuentaCobro}/documento/{documento}/preview', [CuentaCobroController::class, 'previewDocumento'])->name('documento.preview');
        Route::get('/{cuentaCobro}/documento/{documento}/download', [CuentaCobroController::class, 'downloadDocumento'])->name('documento.download');
        Route::get('/{cuentaCobro}/documento-firmado/{numeroDocumento}/preview', [CuentaCobroController::class, 'previewFirmado'])->name('documento.firmado.preview');
        Route::get('/{cuentaCobro}/documento-firmado/{numeroDocumento}/download', [CuentaCobroController::class, 'downloadFirmado'])->name('documento.firmado.download');
        Route::post('/{cuentaCobro}/documentos-firmados', [CuentaCobroController::class, 'uploadFirmados'])->middleware('check.role:supervisor')->name('documentos.firmados.upload');
    });
    
    // Rutas adicionales para gestión de roles y usuarios
    Route::prefix('roles')->name('roles.')->group(function () {
        // Asignar/remover roles a usuarios (AJAX)
        Route::post('/assign-role', [RolControler::class, 'assignRole'])->name('assign');
        Route::post('/remove-role', [RolControler::class, 'removeRole'])->name('remove');
        
        // Obtener usuarios sin rol (AJAX)
        Route::get('/users-without-role', [RolControler::class, 'getUsersWithoutRole'])->name('users.without.role');
    });
    
    // Rutas adicionales que podrías necesitar más adelante
    Route::prefix('admin')->middleware(['auth', 'check.role:admin'])->name('admin.')->group(function () {
        
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
    
    // Solo para apoyo a la supervisión y supervisores
    Route::middleware(['check.role:apoyo a la supervisión,apoyo a la supervision,apoyo a la supervicion,supervisor'])->prefix('supervisor')->name('supervisor.')->group(function () {
        Route::get('/dashboard', function() {
            return view('supervisor.dashboard');
        })->name('dashboard');
    });
    
    // Solo para roles administrativos (admin)
    Route::middleware(['check.role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/reports', function() {
            return view('admin.reports');
        })->name('reports');
    });
});
