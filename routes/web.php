<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CrearUsuario;
use App\Http\Controllers\RolControler;
use App\Http\Controllers\CuentaCobroController;
use App\Http\Controllers\ContratistaDashboardController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\ContratacionController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TesoreriaController;

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

    // Rutas específicas para contratista
    Route::middleware(['check.role:contratista'])->prefix('contratista')->name('contratista.')->group(function () {
        // Dashboard del contratista
        Route::get('/dashboard', [ContratistaDashboardController::class, 'index'])->name('dashboard');
        
        // Gestión de cuentas de cobro del contratista
        Route::prefix('cuentas')->name('cuentas.')->group(function () {
            Route::get('/', [CuentaCobroController::class, 'indexContratista'])->name('index');
            Route::get('/crear', [CuentaCobroController::class, 'createContratista'])->name('crear');
            Route::post('/', [CuentaCobroController::class, 'storeContratista'])->name('store');
            Route::get('/{id}', [CuentaCobroController::class, 'showContratista'])->name('ver');
            Route::get('/{id}/editar', [CuentaCobroController::class, 'editContratista'])->name('editar');
            Route::put('/{id}', [CuentaCobroController::class, 'updateContratista'])->name('update');
            Route::get('/{id}/eliminar', [CuentaCobroController::class, 'deleteContratista'])->name('eliminar');
            Route::delete('/{id}', [CuentaCobroController::class, 'destroyContratista'])->name('destroy');
        });
    });

    // API routes para contratista (protegidas)
    Route::middleware(['auth', 'check.role:contratista'])->prefix('api/contratista')->name('api.contratista.')->group(function () {
        Route::get('/dashboard-data', [ContratistaDashboardController::class, 'getDashboardData'])->name('dashboard');
    });

    // Rutas específicas para supervisor
    Route::middleware(['auth', 'check.role:supervisor'])->prefix('supervisor')->name('supervisor.')->group(function () {
        // Dashboard del supervisor
        Route::get('/', [SupervisorController::class, 'index'])->name('index');
        Route::get('/dashboard', [SupervisorController::class, 'dashboard'])->name('dashboard');
        
        // Gestión de cuentas de cobro (supervisión)
        Route::prefix('cuentas-cobro')->name('cuentas-cobro.')->group(function () {
            Route::get('/', [SupervisorController::class, 'cuentasCobro'])->name('index');
            Route::get('/{id}', [SupervisorController::class, 'showCuentaCobro'])->name('show');
            Route::get('/{id}/editar', [SupervisorController::class, 'editCuentaCobro'])->name('edit');
            Route::put('/{id}', [SupervisorController::class, 'updateCuentaCobro'])->name('update');
        });
        
        // Gestión de contratistas
        Route::prefix('contratistas')->name('contratistas.')->group(function () {
            Route::get('/', [SupervisorController::class, 'contratistas'])->name('index');
            Route::get('/{id}', [SupervisorController::class, 'showContratista'])->name('show');
        });
    });

    // API routes para supervisor (protegidas)
    Route::middleware(['auth', 'check.role:supervisor'])->prefix('api/supervisor')->name('api.supervisor.')->group(function () {
        Route::get('/dashboard-data', [SupervisorController::class, 'getDashboardData'])->name('dashboard');
    });

    // Rutas específicas para contratación
    Route::middleware(['auth', 'check.role:contratacion'])->prefix('contratacion')->name('contratacion.')->group(function () {
        // Dashboard de contratación
        Route::get('/', [ContratacionController::class, 'index'])->name('index');
        Route::get('/dashboard', [ContratacionController::class, 'dashboard'])->name('dashboard');
        
        // Gestión de cuentas de cobro (contratación)
        Route::prefix('cuentas-cobro')->name('cuentas-cobro.')->group(function () {
            Route::get('/', [ContratacionController::class, 'cuentasCobro'])->name('index');
            Route::get('/{id}', [ContratacionController::class, 'showCuentaCobro'])->name('show');
            Route::get('/{id}/editar', [ContratacionController::class, 'editCuentaCobro'])->name('edit');
            Route::put('/{id}', [ContratacionController::class, 'updateCuentaCobro'])->name('update');
        });
    });

    // API routes para contratación (protegidas)
    Route::middleware(['auth', 'check.role:contratacion'])->prefix('api/contratacion')->name('api.contratacion.')->group(function () {
        Route::get('/dashboard-data', [ContratacionController::class, 'getDashboardData'])->name('dashboard');
    });

    // Rutas de Cuentas de Cobro
    Route::resource('cuentas-cobro', CuentaCobroController::class)
        ->names([
        'index' => 'cuentas-cobro.mostrar',
        'create' => 'cuentas-cobro.crear',
        'store' => 'cuentas-cobro.store',
        'show' => 'cuentas-cobro.ver',
        'edit' => 'cuentas-cobro.edit',
        'update' => 'cuentas-cobro.update',
        'destroy' => 'cuentas-cobro.destroy'
    ]);

    // Rutas adicionales para Cuentas de Cobro
    Route::prefix('cuentas-cobro')->name('cuentas-cobro.')->group(function () {
        Route::get('/{id}/eliminar', [CuentaCobroController::class, 'confirmarEliminacion'])->name('confirmar-eliminacion');
        Route::post('/{id}/cambiar-estado', [CuentaCobroController::class, 'cambiarEstado'])->name('cambiar-estado');
        Route::get('/estadisticas', [CuentaCobroController::class, 'estadisticas'])->name('estadisticas');
        Route::get('/{id}/descargar', [CuentaCobroController::class, 'descargar'])->name('descargar');
        
        // Flujo de aprobación
        Route::post('/{id}/enviar-revision', [CuentaCobroController::class, 'enviarRevision'])->name('enviar-revision');
        Route::post('/{id}/aprobar-supervisor', [CuentaCobroController::class, 'aprobarSupervisor'])->name('aprobar-supervisor');
        Route::post('/{id}/aprobar-contratacion', [CuentaCobroController::class, 'aprobarContratacion'])->name('aprobar-contratacion');
        Route::post('/{id}/aprobar-tesoreria', [CuentaCobroController::class, 'aprobarTesoreria'])->name('aprobar-tesoreria');
        Route::post('/{id}/aprobar-ordenador', [CuentaCobroController::class, 'aprobarOrdenador'])->name('aprobar-ordenador');
        Route::post('/{id}/rechazar', [CuentaCobroController::class, 'rechazar'])->name('rechazar');
        Route::post('/{id}/marcar-pagada', [CuentaCobroController::class, 'marcarPagada'])->name('marcar-pagada');
    });

    // Rutas de Notificaciones
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/unread', [NotificationController::class, 'getUnread'])->name('unread');
        Route::post('/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
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
    
    // Rutas adicionales para gestión de roles y usuarios (DEBEN IR ANTES de la ruta dinámica {role})
    Route::prefix('roles')->name('roles.')->group(function () {
        // Ver gestión de usuarios con roles
        Route::get('/usuarios', [RolControler::class, 'showUsuarios'])->name('usuarios');
        
        // Asignar/remover roles a usuarios (AJAX)
        Route::post('/assign-role', [RolControler::class, 'assignRole'])->name('assign');
        Route::post('/remove-role', [RolControler::class, 'removeRole'])->name('remove');
        
        // Obtener usuarios sin rol (AJAX)
        Route::get('/users-without-role', [RolControler::class, 'getUsersWithoutRole'])->name('users.without.role');
    });

    // Ruta personalizada para show (usando {role} en lugar de {id}) - DEBE IR AL FINAL
    Route::get('/roles/{role}', [RolControler::class, 'show'])->name('roles.show');
    
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

    // Rutas específicas para tesorería
    Route::middleware(['auth', 'check.role:tesoreria'])->prefix('tesoreria')->name('tesoreria.')->group(function () {
        // Dashboard de tesorería
        Route::get('/', [TesoreriaController::class, 'index'])->name('index');
        Route::get('/dashboard', [TesoreriaController::class, 'index'])->name('dashboard');
        
        // Gestión de cuentas
        Route::get('/cuentas', [TesoreriaController::class, 'cuentas'])->name('cuentas');
        Route::get('/pagos-realizados', [TesoreriaController::class, 'pagosRealizados'])->name('pagos-realizados');
        Route::get('/cuentas-pendientes', [TesoreriaController::class, 'pendientes'])->name('pendientes');
        
        // Acciones de pago y aprobación
        Route::post('/marcar-pagada/{id}', [TesoreriaController::class, 'marcarPagada'])->name('marcarPagada');
        Route::post('/estado/{id}', [TesoreriaController::class, 'actualizarEstado'])->name('actualizarEstado');
    });

    // API routes para tesorería (protegidas)
    Route::middleware(['auth', 'check.role:tesoreria'])->prefix('api/tesoreria')->name('api.tesoreria.')->group(function () {
        Route::get('/dashboard-data', [TesoreriaController::class, 'getDashboardData'])->name('dashboard');
    });

// Rutas adicionales que requieren roles específicos (placeholders para futuro uso)
// NOTA: Las rutas principales de dashboard están definidas arriba usando controladores