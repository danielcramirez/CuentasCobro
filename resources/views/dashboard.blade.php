@extends('layouts.app')

@section('title', 'Dashboard - CuentasCobro')

@section('content')
<main class="container-fluid">
    <header class="row mb-4" aria-labelledby="dashboard-heading">
        <!-- Header del Dashboard -->
        <div class="col-12">
            <h2 id="dashboard-heading" class="sr-only">Dashboard</h2>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-tachometer-alt me-2"></i>
                        Dashboard
                    </h1>
                    <p class="text-muted mb-0">
                        Bienvenido, <strong>{{ $user->name }}</strong>
                        @if($userRole)
                        - <span class="badge bg-primary">{{ ucfirst(str_replace('_', ' ', $userRole)) }}</span>
                        @endif
                    </p>
                </div>
                <div class="text-end">
                    <small class="text-muted">
                        <i class="fas fa-calendar me-1"></i>
                        {{ now()->format('d/m/Y H:i') }}
                    </small>
                </div>
            </div>
        </div>
    </div>

    @if($userRole === 'alcalde')
    <!-- Dashboard para Alcalde -->
    <section aria-labelledby="alcalde-stats" class="row">
        <h2 id="alcalde-stats" class="sr-only">Estadísticas - Alcalde</h2>
        <!-- Estadísticas Generales -->
        <div class="col-xl-3 col-md-6 mb-4">
            <article class="card border-left-primary shadow h-100 py-2" aria-labelledby="total-users">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Usuarios
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalUsers ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </article>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <article class="card border-left-success shadow h-100 py-2" aria-labelledby="users-with-roles">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Usuarios con Rol
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $usersWithRoles ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </article>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <article class="card border-left-info shadow h-100 py-2" aria-labelledby="total-roles">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Roles
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalRoles ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users-cog fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </article>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <article class="card border-left-warning shadow h-100 py-2" aria-labelledby="without-role">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Sin Rol Asignado
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $usersWithoutRoles ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-times fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </article>
        </div>
    </div>

    <!-- Acciones Rápidas para Alcalde -->
    <section aria-labelledby="alcalde-actions" class="row">
        <h2 id="alcalde-actions" class="sr-only">Acciones Rápidas - Alcalde</h2>
        <div class="col-lg-6 mb-4">
            <article class="card shadow" aria-labelledby="gestion-roles">
                <header class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h3 id="gestion-roles" class="m-0 h6 font-weight-bold text-primary">
                        <i class="fas fa-users-cog me-2"></i>
                        Gestión de Roles
                    </h3>
                    <a href="{{ route('roles.index') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-eye me-1"></i>
                        Ver Todos
                    </a>
                </header>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <a href="{{ route('roles.index') }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-list me-2"></i>
                                Ver Roles
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <a href="{{ route('roles.create') }}" class="btn btn-outline-success w-100">
                                <i class="fas fa-plus me-2"></i>
                                Crear Rol
                            </a>
                        </div>
                    </div>
                    
                    <!-- Resumen de roles del sistema -->
                    <div class="mt-3">
                        <h6 class="text-muted">Roles del Sistema:</h6>
                        <div class="row">
                            @if(isset($systemRoles) && isset($rolesStats))
                            @foreach($systemRoles as $role)
                            @php
                            $roleData = $rolesStats->where('name', $role)->first();
                            @endphp
                            <div class="col-6 mb-2">
                                <div class="d-flex justify-content-between">
                                    <span class="text-capitalize">{{ str_replace('_', ' ', $role) }}:</span>
                                    <span class="badge bg-secondary">
                                        {{ $roleData ? $roleData->users_count : 0 }}
                                    </span>
                                </div>
                            </div>
                            @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </article>
        </div>

        <div class="col-lg-6 mb-4">
            <article class="card shadow" aria-labelledby="recent-users">
                <header class="card-header py-3">
                    <h3 id="recent-users" class="m-0 h6 font-weight-bold text-primary">
                        <i class="fas fa-users me-2"></i>
                        Usuarios Recientes
                    </h3>
                </header>
                <div class="card-body">
                    @if(isset($recentUsers) && $recentUsers->count() > 0)
                    <div class="table-responsive">
                        <ul class="list-unstyled mb-0">
                            @foreach($recentUsers as $recentUser)
                            <li class="d-flex justify-content-between align-items-start py-2 border-bottom">
                                <div>
                                    <i class="fas fa-user text-primary me-2"></i>
                                    <strong>{{ $recentUser->name }}</strong>
                                </div>
                                <div class="text-end">
                                    <div>
                                        @if($recentUser->role)
                                        <span class="badge bg-success">{{ $recentUser->role->name }}</span>
                                        @else
                                        <span class="badge bg-secondary">Sin rol</span>
                                        @endif
                                    </div>
                                    <small class="text-muted d-block">{{ $recentUser->created_at->diffForHumans() }}</small>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @else
                    <p class="text-muted text-center">No hay usuarios registrados recientemente.</p>
                    @endif
                </div>
            </article>
        </div>
    </div>
    @endif

    @if($userRole === 'supervisor')
    <!-- Dashboard para Supervisor -->
    <section aria-labelledby="supervisor-panel" class="row">
        <h2 id="supervisor-panel" class="sr-only">Panel de Supervisor</h2>
        <div class="col-12">
            <article class="card shadow">
                <div class="card-body text-center py-5">
                    <i class="fas fa-user-check fa-4x text-success mb-3"></i>
                    <h4>Panel de Supervisor</h4>
                    <p class="text-muted">Aquí podrás revisar y aprobar cuentas de cobro.</p>
                    <p class="text-muted">Esta funcionalidad se implementará próximamente.</p>
                </div>
            </article>
        </div>
    </section>
    @endif

    @if($userRole === 'contratista')
    <!-- Dashboard para Contratista -->
    <section aria-labelledby="contratista-panel" class="row">
        <h2 id="contratista-panel" class="sr-only">Panel de Contratista</h2>
        <div class="col-12">
            <article class="card shadow">
                <div class="card-body text-center py-5">
                    <i class="fas fa-user-tie fa-4x text-primary mb-3"></i>
                    <h4>Panel de Contratista</h4>
                    <p class="text-muted">Aquí podrás crear y gestionar tus cuentas de cobro.</p>
                    <p class="text-muted">Esta funcionalidad se implementará próximamente.</p>
                </div>
            </article>
        </div>
    </section>
    @endif

    @if(!$userRole)
    <!-- Usuario sin rol asignado -->
    <aside class="row" aria-labelledby="no-role-alert">
        <h2 id="no-role-alert" class="sr-only">Sin rol asignado</h2>
        <div class="col-12">
            <div class="alert alert-warning" role="alert">
                <h4 class="alert-heading">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Sin rol asignado
                </h4>
                <p>No tienes un rol asignado en el sistema. Contacta al administrador para que te asigne un rol.</p>
                <hr>
                <p class="mb-0">Mientras tanto, puedes explorar las funcionalidades básicas del sistema.</p>
            </div>
        </div>
    </aside>
    @endif

    @if(in_array($userRole, ['ordenador_gasto', 'tesoreria', 'contratacion']))
    <!-- Dashboard para otros roles -->
    <section aria-labelledby="other-roles" class="row">
        <h2 id="other-roles" class="sr-only">Paneles por rol</h2>
        <div class="col-12">
            <article class="card shadow">
                <div class="card-body text-center py-5">
                    @switch($userRole)
                        @case('ordenador_gasto')
                            <i class="fas fa-money-check-alt fa-4x text-info mb-3"></i>
                            <h4>Panel de Ordenador del Gasto</h4>
                            <p class="text-muted">Aquí podrás autorizar pagos y gestionar presupuestos.</p>
                            @break
                        @case('tesoreria')
                            <i class="fas fa-coins fa-4x text-success mb-3"></i>
                            <h4>Panel de Tesorería</h4>
                            <p class="text-muted">Aquí podrás procesar pagos y generar reportes financieros.</p>
                            @break
                        @case('contratacion')
                            <i class="fas fa-handshake fa-4x text-primary mb-3"></i>
                            <h4>Panel de Contratación</h4>
                            <p class="text-muted">Aquí podrás gestionar contratos y contratistas.</p>
                            @break
                    @endswitch
                    <p class="text-muted">Esta funcionalidad se implementará próximamente.</p>
                </div>
            </article>
        </div>
    </section>
    @endif
    </main>

@push('styles')
<style>
    .border-left-primary {
        border-left: 0.25rem solid #4e73df !important;
    }
    .border-left-success {
        border-left: 0.25rem solid #1cc88a !important;
    }
    .border-left-info {
        border-left: 0.25rem solid #36b9cc !important;
    }
    .border-left-warning {
        border-left: 0.25rem solid #f6c23e !important;
    }
</style>
@endpush
@endsection