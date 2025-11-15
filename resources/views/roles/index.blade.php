@extends('layouts.app')

@section('title', 'Gestión de Roles - CuentasCobro')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
            <div>
                <div class="flex items-center mb-2">
                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-users-cog text-white text-xl"></i>
                    </div>
                    <h1 class="text-4xl font-bold text-gray-900">Gestión de Roles</h1>
                </div>
                <p class="text-xl text-gray-600">Administra los roles y permisos del sistema</p>
            </div>
            
            @if(Auth::user()->hasRole('alcalde'))
            <a href="{{ route('roles.create') }}" 
               class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-6 py-3 rounded-xl hover:from-indigo-600 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 font-semibold shadow-lg">
                <i class="fas fa-plus mr-2"></i>
                Nuevo Rol
            </a>
            @endif
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
        <div class="glass-card p-4 mb-8 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-check text-white"></i>
                </div>
                <div class="flex-1">
                    <p class="text-green-800 font-medium">{{ session('success') }}</p>
                </div>
                <button onclick="this.parentElement.parentElement.style.display='none'" class="text-green-600 hover:text-green-800">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="glass-card p-4 mb-8 bg-gradient-to-r from-red-50 to-pink-50 border border-red-200">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-exclamation-triangle text-white"></i>
                </div>
                <div class="flex-1">
                    <p class="text-red-800 font-medium">{{ session('error') }}</p>
                </div>
                <button onclick="this.parentElement.parentElement.style.display='none'" class="text-red-600 hover:text-red-800">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        @endif

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="glass-card p-6 text-center transform hover:scale-105 transition-all duration-300">
                <div class="w-12 h-12 bg-gradient-to-br from-indigo-400 to-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-users-cog text-white text-xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-1">{{ $roles->count() }}</h3>
                <p class="text-gray-600 text-sm">Total de Roles</p>
            </div>
            
            <div class="glass-card p-6 text-center transform hover:scale-105 transition-all duration-300">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-user-check text-white text-xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-1">{{ $userCount ?? 0 }}</h3>
                <p class="text-gray-600 text-sm">Usuarios Activos</p>
            </div>
            
            <div class="glass-card p-6 text-center transform hover:scale-105 transition-all duration-300">
                <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-shield-alt text-white text-xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-1">{{ $activeRoles ?? $roles->count() }}</h3>
                <p class="text-gray-600 text-sm">Roles Activos</p>
            </div>
            
            <div class="glass-card p-6 text-center transform hover:scale-105 transition-all duration-300">
                <div class="w-12 h-12 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-key text-white text-xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-1">{{ $totalPermissions ?? 0 }}</h3>
                <p class="text-gray-600 text-sm">Permisos Configurados</p>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="glass-card p-6 mb-8">
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input 
                            type="text" 
                            id="searchRoles"
                            placeholder="Buscar roles..." 
                            class="w-full pl-10 pr-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-300"
                        >
                    </div>
                </div>
                <select id="filterStatus" class="px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="">Todos los estados</option>
                    <option value="activo">Activos</option>
                    <option value="inactivo">Inactivos</option>
                </select>
            </div>
        </div>

        <!-- Roles Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="rolesGrid">
            @forelse($roles as $role)
            <div class="glass-card p-6 hover:shadow-xl transition-all duration-300 transform hover:scale-105 role-card" data-role-name="{{ strtolower($role->nombre) }}">
                <!-- Role Header -->
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br 
                            @if($role->nombre === 'alcalde') from-red-400 to-red-600
                            @elseif($role->nombre === 'contratista') from-blue-400 to-blue-600
                            @elseif($role->nombre === 'supervisor') from-green-400 to-green-600
                            @elseif($role->nombre === 'tesoreria') from-yellow-400 to-orange-500
                            @elseif($role->nombre === 'ordenador_gasto') from-purple-400 to-purple-600
                            @elseif($role->nombre === 'contratacion') from-pink-400 to-pink-600
                            @else from-gray-400 to-gray-600
                            @endif
                            rounded-full flex items-center justify-center mr-3">
                            <i class="
                                @if($role->nombre === 'alcalde') fas fa-crown
                                @elseif($role->nombre === 'contratista') fas fa-user-tie
                                @elseif($role->nombre === 'supervisor') fas fa-clipboard-check
                                @elseif($role->nombre === 'tesoreria') fas fa-coins
                                @elseif($role->nombre === 'ordenador_gasto') fas fa-money-check-alt
                                @elseif($role->nombre === 'contratacion') fas fa-handshake
                                @else fas fa-user
                                @endif
                                text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">{{ ucfirst($role->nombre) }}</h3>
                            <p class="text-gray-600 text-sm">{{ $role->descripcion ?? 'Sin descripción' }}</p>
                        </div>
                    </div>
                    
                    <!-- Status Badge -->
                    <span class="px-3 py-1 rounded-full text-xs font-medium
                        @if($role->activo ?? true) bg-green-100 text-green-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ ($role->activo ?? true) ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>

                <!-- Role Stats -->
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                        <p class="text-2xl font-bold text-gray-900">{{ $role->users_count ?? 0 }}</p>
                        <p class="text-gray-600 text-xs">Usuarios</p>
                    </div>
                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                        <p class="text-2xl font-bold text-gray-900">{{ $role->permissions_count ?? 0 }}</p>
                        <p class="text-gray-600 text-xs">Permisos</p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-2">
                    <a href="{{ route('roles.show', $role->id) }}" 
                       class="flex-1 bg-blue-500 text-white px-3 py-2 rounded-lg hover:bg-blue-600 transition-colors text-center text-sm font-medium">
                        <i class="fas fa-eye mr-1"></i>
                        Ver
                    </a>
                    
                    @if(Auth::user()->hasRole('alcalde'))
                    <a href="{{ route('roles.edit', $role->id) }}" 
                       class="flex-1 bg-yellow-500 text-white px-3 py-2 rounded-lg hover:bg-yellow-600 transition-colors text-center text-sm font-medium">
                        <i class="fas fa-edit mr-1"></i>
                        Editar
                    </a>
                    
                    @if($role->nombre !== 'alcalde')
                    <button onclick="confirmDelete({{ $role->id }}, '{{ $role->nombre }}')" 
                            class="bg-red-500 text-white px-3 py-2 rounded-lg hover:bg-red-600 transition-colors text-sm font-medium">
                        <i class="fas fa-trash"></i>
                    </button>
                    @endif
                    @endif
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-12">
                <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-users-cog text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-600 mb-2">No hay roles registrados</h3>
                <p class="text-gray-500 mb-4">Comienza creando el primer rol del sistema</p>
                @if(Auth::user()->hasRole('alcalde'))
                <a href="{{ route('roles.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Crear Primer Rol
                </a>
                @endif
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($roles->hasPages())
        <div class="mt-8 flex justify-center">
            {{ $roles->links() }}
        </div>
        @endif

    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl p-6 max-w-md mx-4 transform scale-95 transition-transform">
        <div class="text-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-trash-alt text-red-600 text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Confirmar Eliminación</h3>
            <p class="text-gray-600 mb-6">¿Estás seguro de que quieres eliminar el rol <strong id="roleToDelete"></strong>? Esta acción no se puede deshacer.</p>
            
            <div class="flex gap-3">
                <button onclick="closeDeleteModal()" class="flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition-colors">
                    Cancelar
                </button>
                <form id="deleteForm" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors">
                        Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchRoles');
    const filterStatus = document.getElementById('filterStatus');
    const roleCards = document.querySelectorAll('.role-card');

    // Search functionality
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        filterRoles();
    });

    filterStatus.addEventListener('change', function() {
        filterRoles();
    });

    function filterRoles() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusFilter = filterStatus.value.toLowerCase();

        roleCards.forEach(card => {
            const roleName = card.dataset.roleName;
            const statusBadge = card.querySelector('.px-3.py-1');
            const cardStatus = statusBadge ? statusBadge.textContent.toLowerCase() : 'activo';

            const matchesSearch = roleName.includes(searchTerm);
            const matchesStatus = !statusFilter || cardStatus.includes(statusFilter);

            if (matchesSearch && matchesStatus) {
                card.style.display = 'block';
                card.classList.add('animate-fadeIn');
            } else {
                card.style.display = 'none';
            }
        });
    }
});

function confirmDelete(roleId, roleName) {
    document.getElementById('roleToDelete').textContent = roleName;
    document.getElementById('deleteForm').action = `/roles/${roleId}`;
    document.getElementById('deleteModal').classList.remove('hidden');
    document.getElementById('deleteModal').querySelector('.transform').classList.remove('scale-95');
    document.getElementById('deleteModal').querySelector('.transform').classList.add('scale-100');
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.querySelector('.transform').classList.remove('scale-100');
    modal.querySelector('.transform').classList.add('scale-95');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 150);
}
</script>

<style>
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.glass-card {
    animation: fadeInUp 0.6s ease-out;
}

.glass-card:nth-child(1) { animation-delay: 0.1s; }
.glass-card:nth-child(2) { animation-delay: 0.2s; }
.glass-card:nth-child(3) { animation-delay: 0.3s; }
.glass-card:nth-child(4) { animation-delay: 0.4s; }

.animate-fadeIn {
    animation: fadeIn 0.3s ease-out;
}
</style>
@endsection
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
                                    <h4 class="mb-0">6</h4>
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
                                                @case('alcalde')
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
                                            @if(Auth::user()->hasRole('alcalde'))
                                            <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-outline-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if(!in_array($role->name, ['contratista', 'supervisor', 'alcalde', 'ordenador_gasto', 'tesoreria', 'contratacion']) && $role->users_count == 0)
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
                        @if(Auth::user()->hasRole('alcalde'))
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
