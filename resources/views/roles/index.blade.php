@extends('layouts.app')

@section('title', 'Gestión de Roles - CuentasCobro')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar (opcional, puedes agregarlo después) -->
        <div class="col-md-2">
            <!-- Sidebar content -->
        </div>
        
        <!-- Main content -->
        <div class="col-md-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold text-dark">
                    <i class="fas fa-users-cog me-2"></i>
                    Gestión de Roles
                </h2>
                
                @if(Auth::user()->hasRole('admin'))
                <a href="{{ route('roles.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>
                    Nuevo Rol
                </a>
                @endif
            </div>

            <!-- Mensajes de éxito/error -->
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <!-- Estadísticas de roles -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $roles->count() }}</h4>
                                    <small>Total de Roles</small>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-users-cog fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $roles->sum('users_count') }}</h4>
                                    <small>Usuarios con Rol</small>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-user-check fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $roles->where('users_count', 0)->count() }}</h4>
                                    <small>Roles Sin Usuarios</small>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-user-times fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">4</h4>
                                    <small>Roles del Sistema</small>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-cogs fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de roles -->
            <div class="card shadow">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>
                        Lista de Roles
                    </h5>
                </div>
                <div class="card-body">
                    @if($roles->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th><i class="fas fa-hashtag me-1"></i>ID</th>
                                    <th><i class="fas fa-tag me-1"></i>Nombre</th>
                                    <th><i class="fas fa-info-circle me-1"></i>Descripción</th>
                                    <th><i class="fas fa-users me-1"></i>Usuarios</th>
                                    <th><i class="fas fa-key me-1"></i>Permisos</th>
                                    <th><i class="fas fa-calendar me-1"></i>Creado</th>
                                    @if(Auth::user()->isAdmin())
                                    <th class="text-center"><i class="fas fa-cogs me-1"></i>Acciones</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($roles as $role)
                                <tr>
                                    <td>
                                        <span class="badge bg-secondary">{{ $role->id }}</span>
                                    </td>
                                    <td>
                                        <strong class="text-capitalize">
                                            @switch($role->name)
                                                @case('contratista')
                                                    <i class="fas fa-user-tie text-primary me-1"></i>
                                                    @break
                                                @case('supervisor')
                                                    <i class="fas fa-user-check text-success me-1"></i>
                                                    @break
                                                @case('admin')
                                                    <i class="fas fa-crown text-warning me-1"></i>
                                                    @break
                                                @case('ordenador_gasto')
                                                    <i class="fas fa-money-check-alt text-info me-1"></i>
                                                    @break
                                                @case('tesoreria')
                                                    <i class="fas fa-coins text-success me-1"></i>
                                                    @break
                                                @case('contratacion')
                                                    <i class="fas fa-handshake text-primary me-1"></i>
                                                    @break
                                                @default
                                                    <i class="fas fa-user me-1"></i>
                                            @endswitch
                                            {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                        </strong>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $role->description }}</small>
                                    </td>
                                    <td>
                                        @if($role->users_count > 0)
                                            <span class="badge bg-success">{{ $role->users_count }} usuarios</span>
                                        @else
                                            <span class="badge bg-secondary">Sin usuarios</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($role->permissions && count($role->permissions) > 0)
                                            <span class="badge bg-info">{{ count($role->permissions) }} permisos</span>
                                            <button class="btn btn-sm btn-outline-info ms-1" type="button" data-bs-toggle="collapse" data-bs-target="#permissions-{{ $role->id }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        @else
                                            <span class="badge bg-secondary">Sin permisos</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $role->created_at->format('d/m/Y') }}</small>
                                    </td>
                                    @if(Auth::user()->isAdmin())
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('roles.show', $role->id) }}" class="btn btn-outline-info" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if(Auth::user()->hasRole('admin'))
                                            <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-outline-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if(!in_array($role->name, ['contratista', 'apoyo a la supervisión', 'supervisor', 'admin']) && $role->users_count == 0)
                                            <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar este rol?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @endif
                                            @endif
                                        </div>
                                    </td>
                                    @endif
                                </tr>
                                
                                <!-- Colapso para mostrar permisos -->
                                @if($role->permissions && count($role->permissions) > 0)
                                <tr class="collapse" id="permissions-{{ $role->id }}">
                                    <td colspan="{{ Auth::user()->isAdmin() ? '7' : '6' }}" class="bg-light">
                                        <div class="p-2">
                                            <strong class="text-muted">Permisos asignados:</strong>
                                            <div class="mt-2">
                                                @foreach($role->permissions as $permission)
                                                <span class="badge bg-light text-dark border me-1 mb-1">
                                                    <i class="fas fa-key me-1"></i>
                                                    {{ ucfirst(str_replace('_', ' ', $permission)) }}
                                                </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-users-cog fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">No hay roles registrados</h4>
                        <p class="text-muted">Comienza creando el primer rol del sistema.</p>
                        @if(Auth::user()->hasRole('admin'))
                        <a href="{{ route('roles.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>
                            Crear Primer Rol
                        </a>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    
    .table th {
        border-top: none;
        font-weight: 600;
        font-size: 0.875rem;
    }
    
    .badge {
        font-size: 0.75rem;
    }
    
    .btn-group-sm > .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.775rem;
    }
</style>
@endpush

@push('scripts')
<script>
    // Auto-ocultar alertas después de 5 segundos (sin jQuery)
    setTimeout(function () {
        document.querySelectorAll('.alert').forEach(function (el) {
            el.style.transition = 'opacity 0.5s ease';
            el.style.opacity = '0';
            setTimeout(function(){ el.style.display = 'none'; }, 600);
        });
    }, 5000);
</script>
@endpush
@endsection
