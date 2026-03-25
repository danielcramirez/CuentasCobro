@extends('layouts.app')

@section('title', 'Dashboard - CuentasCobro')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Header del Dashboard -->
        <div class="col-12 mb-4">
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

    @if($userRole === 'admin')
    <!-- Dashboard para Admin -->
    <div class="row">
        <!-- Estadísticas Generales -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
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
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
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
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
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
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
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
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Pendientes Supervisor
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingMayorApprovals ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-signature fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="mb-1">Aprobación final de pagos</h5>
                        <p class="mb-0 text-muted">Revisa cuentas y planillas ya aprobadas por supervisor.</p>
                    </div>
                    <a href="{{ route('cuentas.index') }}" class="btn btn-success">Revisar cuentas aprobadas por supervisor</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones Rápidas para Supervisor -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-users-cog me-2"></i>
                        Gestión de Roles
                    </h6>
                    <a href="{{ route('roles.index') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-eye me-1"></i>
                        Ver Todos
                    </a>
                </div>
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
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-users me-2"></i>
                        Usuarios Recientes
                    </h6>
                </div>
                <div class="card-body">
                    @if(isset($recentUsers) && $recentUsers->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <tbody>
                                @foreach($recentUsers as $recentUser)
                                <tr>
                                    <td>
                                        <i class="fas fa-user text-primary me-2"></i>
                                        <strong>{{ $recentUser->name }}</strong>
                                    </td>
                                    <td>
                                        @if($recentUser->role)
                                        <span class="badge bg-success">{{ $recentUser->role->name }}</span>
                                        @else
                                        <span class="badge bg-secondary">Sin rol</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $recentUser->created_at->diffForHumans() }}</small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-muted text-center">No hay usuarios registrados recientemente.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($userRole === 'supervisor')
    <!-- Dashboard para Supervisor -->
    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <h6 class="text-muted">Pendientes de revisar</h6>
                    <h3 class="mb-0">{{ $pendingReviews ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <h6 class="text-muted">Aprobadas hoy</h6>
                    <h3 class="mb-0 text-success">{{ $approvedToday ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <h6 class="text-muted">Rechazadas hoy</h6>
                    <h3 class="mb-0 text-danger">{{ $rejectedToday ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="mb-1">Gestión de revisión</h5>
                        <p class="mb-0 text-muted">Valida cuenta PDF y planilla PDF. Si rechazas, debes agregar motivo.</p>
                    </div>
                    <a href="{{ route('cuentas.index') }}" class="btn btn-primary">
                        Ir a cuentas de cobro
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($userRole === 'contratista')
    <!-- Dashboard para Contratista -->
    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <h6 class="text-muted">Mis cuentas</h6>
                    <h3 class="mb-0">{{ $myCuentasCobro ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <h6 class="text-muted">Pendientes</h6>
                    <h3 class="mb-0 text-warning">{{ $pendingApproval ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <h6 class="text-muted">Aprobadas</h6>
                    <h3 class="mb-0 text-success">{{ $approved ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <h6 class="text-muted">Rechazadas</h6>
                    <h3 class="mb-0 text-danger">{{ $rejected ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="mb-1">Cargar documentos</h5>
                        <p class="mb-0 text-muted">Sube cuenta de cobro PDF y pago de planilla PDF para revisión.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('cuentas.create') }}" class="btn btn-success">Nueva cuenta</a>
                        <a href="{{ route('cuentas.index') }}" class="btn btn-outline-primary">Ver historial</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(!$userRole)
    <!-- Usuario sin rol asignado -->
    <div class="row">
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
    </div>
    @endif

    @if(in_array($userRole, ['ordenador_gasto', 'tesoreria', 'contratacion']))
    <!-- Dashboard para otros roles -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
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
            </div>
        </div>
    </div>
    @endif
</div>

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
