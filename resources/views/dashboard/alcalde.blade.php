@extends('dashboard.dashboard')
@section('dashboardRoles')
<!-- Parcial: Alcalde - Estadísticas y Acciones -->
<section aria-labelledby="alcalde-stats" class="row mx-2 md:mx-8 lg:mx-10">
    <h2 id="alcalde-stats" class="sr-only">Estadísticas - Alcalde</h2>
    <!-- Estadísticas Generales -->
    <div class="col-xl-3 col-md-6 mb-4">
    <article class="card h-100 py-2" aria-labelledby="total-users" style="border-left: 0.25rem solid var(--color-primary);">
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
    <article class="card h-100 py-2" aria-labelledby="users-with-roles" style="border-left: 0.25rem solid var(--color-primary);">
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
    <article class="card h-100 py-2" aria-labelledby="total-roles" style="border-left: 0.25rem solid var(--color-primary);">
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
    <article class="card h-100 py-2" aria-labelledby="without-role" style="border-left: 0.25rem solid var(--color-primary);">
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
</section>

<!-- Acciones Rápidas para Alcalde -->
<section aria-labelledby="alcalde-actions" class="row mx-2 md:mx-8 lg:mx-10">
    <h2 id="alcalde-actions" class="sr-only">Acciones Rápidas - Alcalde</h2>
    <div class="col-lg-6 mb-4">
        <article class="card " aria-labelledby="gestion-roles">
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
        <article class="card " aria-labelledby="recent-users">
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
</section>
@endsection