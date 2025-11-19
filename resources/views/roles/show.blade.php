@extends('layouts.app')

@section('title', 'Detalles del Rol - CuentasCobro')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 py-8 pt-32">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Main content -->
        <div>
            <!-- Header con navegación -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
                <div>
                    <nav aria-label="breadcrumb" class="mb-2">
                        <ol class="breadcrumb bg-transparent p-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('roles.index') }}" class="text-indigo-600 hover:underline">
                                    <i class="fas fa-users-cog mr-1"></i>Roles
                                </a>
                            </li>
                            <li class="breadcrumb-item active">{{ collect(explode('_', $role->name))->map(fn($w) => ucfirst($w))->implode(' ') }}</li>
                        </ol>
                    </nav>
                    <h2 class="text-3xl font-bold text-gray-900 mb-0 flex items-center">
                        @switch($role->name)
                            @case('contratista')<i class="fas fa-user-tie text-primary mr-2"></i>@break
                            @case('supervisor')<i class="fas fa-user-check text-success mr-2"></i>@break
                            @case('alcalde')<i class="fas fa-crown text-warning mr-2"></i>@break
                            @case('ordenador_gasto')<i class="fas fa-money-check-alt text-info mr-2"></i>@break
                            @case('tesoreria')<i class="fas fa-coins text-success mr-2"></i>@break
                            @case('contratacion')<i class="fas fa-handshake text-primary mr-2"></i>@break
                            @default<i class="fas fa-user mr-2"></i>
                        @endswitch
                        Detalles del Rol: {{ collect(explode('_', $role->name))->map(fn($w) => ucfirst($w))->implode(' ') }}
                    </h2>
                </div>
                
                <div class="flex gap-2 mt-4 md:mt-0">
                    <a href="{{ route('roles.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors flex items-center">
                        <i class="fas fa-arrow-left mr-1"></i> Volver
                    </a>
                    @if(Auth::user()->hasRole('alcalde'))
                    <a href="{{ route('roles.edit', $role->id) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition-colors flex items-center">
                        <i class="fas fa-edit mr-1"></i> Editar Rol
                    </a>
                    @endif
                </div>
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

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Información del Rol -->
                <div class="glass-card p-6">
                    <h5 class="text-lg font-bold text-indigo-700 mb-4 flex items-center">
                        <i class="fas fa-info-circle mr-2"></i> Información del Rol
                    </h5>
                    <div class="mb-3">
                        <span class="text-xs text-gray-500 font-semibold">ID del Rol:</span>
                        <span class="badge bg-secondary ml-2">{{ $role->id }}</span>
                    </div>
                    <div class="mb-3">
                        <span class="text-xs text-gray-500 font-semibold">Nombre:</span>
                        <span class="ml-2 font-bold">{{ collect(explode('_', $role->name))->map(fn($w) => ucfirst($w))->implode(' ') }}</span>
                    </div>
                    <div class="mb-3">
                        <span class="text-xs text-gray-500 font-semibold">Descripción:</span>
                        <span class="ml-2 text-gray-700">{{ $role->description ?? 'Sin descripción' }}</span>
                    </div>
                    <div class="mb-3">
                        <span class="text-xs text-gray-500 font-semibold">Usuarios Asignados:</span>
                        @if($users->total() > 0)
                            <span class="badge bg-success ml-2">{{ $users->total() }} usuarios</span>
                        @else
                            <span class="badge bg-secondary ml-2">Sin usuarios</span>
                        @endif
                    </div>
                    <div class="mb-3">
                        <span class="text-xs text-gray-500 font-semibold">Permisos:</span>
                        @if($role->permissions && count($role->permissions) > 0)
                            <span class="badge bg-info ml-2">{{ count($role->permissions) }} permisos</span>
                        @else
                            <span class="badge bg-secondary ml-2">Sin permisos</span>
                        @endif
                    </div>
                    <div class="mb-3">
                        <span class="text-xs text-gray-500 font-semibold">Fecha de Creación:</span>
                        <span class="ml-2 text-gray-700"><i class="fas fa-calendar mr-1"></i>{{ $role->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 font-semibold">Última Actualización:</span>
                        <span class="ml-2 text-gray-700"><i class="fas fa-clock mr-1"></i>{{ $role->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>

                <!-- Permisos del Rol -->
                <div class="glass-card p-6 col-span-2">
                    <h5 class="text-lg font-bold text-blue-700 mb-4 flex items-center">
                        <i class="fas fa-key mr-2"></i> Permisos Asignados
                        @if($role->permissions && count($role->permissions) > 0)
                        <span class="badge bg-light text-dark ml-2">{{ count($role->permissions) }}</span>
                        @endif
                    </h5>
                    @if($role->permissions && count($role->permissions) > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @php
                        $permissionCategories = [
                            'Cuentas de Cobro' => ['create_cuenta_cobro', 'view_cuenta_cobro', 'view_own_cuenta_cobro', 'view_all_cuenta_cobro', 'edit_own_cuenta_cobro', 'review_cuenta_cobro', 'approve_cuenta_cobro', 'reject_cuenta_cobro', 'final_approval'],
                            'Documentos' => ['upload_documents', 'view_documents'],
                            'Contratos' => ['view_contract_info', 'manage_contracts', 'contract_validation'],
                            'Pagos' => ['authorize_payment', 'process_payment', 'generate_checks', 'bank_transfers', 'payment_confirmation', 'generate_payment_orders'],
                            'Presupuesto' => ['view_budget', 'manage_budget'],
                            'Reportes' => ['view_reports', 'financial_reports', 'view_financial_reports', 'contract_reports'],
                            'Administración' => ['manage_users', 'manage_contractors', 'contractor_registration', 'system_admin'],
                            'Otros' => ['add_comments', 'request_corrections', 'override_decisions']
                        ];
                        @endphp
                        @foreach($permissionCategories as $category => $categoryPermissions)
                            @php
                            $roleHasPermissionsInCategory = array_intersect($role->permissions, $categoryPermissions);
                            @endphp
                            @if(count($roleHasPermissionsInCategory) > 0)
                            <div class="border rounded p-4 h-full">
                                <h6 class="font-bold text-blue-600 mb-2 flex items-center">
                                    @switch($category)
                                        @case('Cuentas de Cobro')<i class="fas fa-file-invoice mr-1"></i>@break
                                        @case('Documentos')<i class="fas fa-file-alt mr-1"></i>@break
                                        @case('Contratos')<i class="fas fa-handshake mr-1"></i>@break
                                        @case('Pagos')<i class="fas fa-money-bill mr-1"></i>@break
                                        @case('Presupuesto')<i class="fas fa-chart-line mr-1"></i>@break
                                        @case('Reportes')<i class="fas fa-chart-bar mr-1"></i>@break
                                        @case('Administración')<i class="fas fa-cogs mr-1"></i>@break
                                        @default<i class="fas fa-ellipsis-h mr-1"></i>
                                    @endswitch
                                    {{ $category }}
                                </h6>
                                @foreach($roleHasPermissionsInCategory as $permission)
                                <span class="badge bg-light text-dark border mr-1 mb-1">
                                    <i class="fas fa-check text-success mr-1"></i>
                                    {{ collect(explode('_', $permission))->map(fn($w) => ucfirst($w))->implode(' ') }}
                                </span>
                                @endforeach
                            </div>
                            @endif
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-key fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Sin permisos asignados</h5>
                        <p class="text-muted">Este rol no tiene permisos específicos configurados.</p>
                        @if(Auth::user()->hasRole('alcalde'))
                        <a href="{{ route('roles.edit', $role->id) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition-colors">
                            <i class="fas fa-edit mr-1"></i> Asignar Permisos
                        </a>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Usuarios con este rol -->
            <div class="glass-card p-6 mb-8">
                <h5 class="text-lg font-bold text-green-700 mb-4 flex items-center">
                    <i class="fas fa-users mr-2"></i> Usuarios con este Rol
                    @if($users->total() > 0)
                    <span class="badge bg-light text-dark ml-2">{{ $users->total() }}</span>
                    @endif
                </h5>
                @if($users->count() > 0)
                <div class="overflow-x-auto">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th><i class="fas fa-hashtag mr-1"></i>ID</th>
                                <th><i class="fas fa-user mr-1"></i>Nombre</th>
                                <th><i class="fas fa-envelope mr-1"></i>Email</th>
                                <th><i class="fas fa-calendar mr-1"></i>Fecha Registro</th>
                                @if(Auth::user()->hasAnyRole(['alcalde', 'contratacion']))
                                <th class="text-center"><i class="fas fa-cogs mr-1"></i>Acciones</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td><span class="badge bg-secondary">{{ $user->id }}</span></td>
                                <td><i class="fas fa-user text-primary mr-2"></i><strong>{{ $user->name }}</strong></td>
                                <td><i class="fas fa-envelope text-muted mr-2"></i>{{ $user->email }}</td>
                                <td><small class="text-muted"><i class="fas fa-calendar mr-1"></i>{{ $user->created_at->format('d/m/Y') }}</small></td>
                                @if(Auth::user()->hasAnyRole(['alcalde', 'contratacion']))
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-danger" onclick="removeRole({{ $user->id }}, '{{ $user->name }}')" title="Remover rol">
                                        <i class="fas fa-user-minus"></i>
                                    </button>
                                </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Paginación -->
                @if($users->hasPages())
                <div class="flex justify-center mt-4">
                    {{ $users->links() }}
                </div>
                @endif
                @else
                <div class="text-center py-4">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Sin usuarios asignados</h5>
                    <p class="text-muted">No hay usuarios con este rol actualmente.</p>
                </div>
                @endif
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
    
    .breadcrumb-item + .breadcrumb-item::before {
        content: ">";
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
    
    // Función para remover rol de usuario
    function removeRole(userId, userName) {
        if (confirm(`¿Estás seguro de remover el rol de ${userName}?`)) {
            // Aquí puedes implementar la llamada AJAX
            fetch(`/roles/remove-role`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    user_id: userId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al remover el rol');
            });
        }
    }
</script>
@endpush
@endsection
