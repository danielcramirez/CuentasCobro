@extends('layouts.app')

@section('title', 'Detalles del Rol - CuentasCobro')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar (opcional) -->
        <div class="col-md-2">
            <!-- Sidebar content -->
        </div>
        
        <!-- Main content -->
        <div class="col-md-10">
            <!-- Header con navegación -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('roles.index') }}" class="text-decoration-none">
                                    <i class="fas fa-users-cog me-1"></i>Roles
                                </a>
                            </li>
                            <li class="breadcrumb-item active">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</li>
                        </ol>
                    </nav>
                    <h2 class="fw-bold text-dark mb-0">
                        @switch($role->name)
                            @case('contratista')
                                <i class="fas fa-user-tie text-primary me-2"></i>
                                @break
                            @case('supervisor')
                                <i class="fas fa-user-check text-success me-2"></i>
                                @break
                            @case('alcalde')
                                <i class="fas fa-crown text-warning me-2"></i>
                                @break
                            @case('ordenador_gasto')
                                <i class="fas fa-money-check-alt text-info me-2"></i>
                                @break
                            @case('tesoreria')
                            @case('central de cuentas')
                                <i class="fas fa-coins text-success me-2"></i>
                                @break
                            @case('contratacion')
                                <i class="fas fa-handshake text-primary me-2"></i>
                                @break
                            @default
                                <i class="fas fa-user me-2"></i>
                        @endswitch
                        Detalles del Rol: {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                    </h2>
                </div>
                
                <div class="btn-group" role="group">
                    <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>
                        Volver
                    </a>
                    @if(Auth::user()->hasRole('alcalde'))
                    <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-1"></i>
                        Editar Rol
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

            <div class="row">
                <!-- Información del Rol -->
                <div class="col-lg-4 mb-4">
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                Información del Rol
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">ID del Rol:</label>
                                <div>
                                    <span class="badge bg-secondary fs-6">{{ $role->id }}</span>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Nombre:</label>
                                <div class="fs-5 fw-bold text-capitalize">
                                    {{ str_replace('_', ' ', $role->name) }}
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Descripción:</label>
                                <div class="text-muted">
                                    {{ $role->description ?? 'Sin descripción' }}
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Usuarios Asignados:</label>
                                <div>
                                    @if($users->total() > 0)
                                        <span class="badge bg-success fs-6">{{ $users->total() }} usuarios</span>
                                    @else
                                        <span class="badge bg-secondary fs-6">Sin usuarios</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Permisos:</label>
                                <div>
                                    @if($role->permissions && count($role->permissions) > 0)
                                        <span class="badge bg-info fs-6">{{ count($role->permissions) }} permisos</span>
                                    @else
                                        <span class="badge bg-secondary fs-6">Sin permisos</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Fecha de Creación:</label>
                                <div class="text-muted">
                                    <i class="fas fa-calendar me-1"></i>
                                    {{ $role->created_at->format('d/m/Y H:i') }}
                                </div>
                            </div>
                            
                            <div class="mb-0">
                                <label class="form-label fw-bold text-muted">Última Actualización:</label>
                                <div class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ $role->updated_at->format('d/m/Y H:i') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Permisos del Rol -->
                <div class="col-lg-8 mb-4">
                    <div class="card shadow">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-key me-2"></i>
                                Permisos Asignados
                                @if($role->permissions && count($role->permissions) > 0)
                                <span class="badge bg-light text-dark ms-2">{{ count($role->permissions) }}</span>
                                @endif
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($role->permissions && count($role->permissions) > 0)
                            <div class="row">
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
                                    <div class="col-md-6 mb-3">
                                        <div class="border rounded p-3 h-100">
                                            <h6 class="fw-bold text-primary mb-2">
                                                @switch($category)
                                                    @case('Cuentas de Cobro')
                                                        <i class="fas fa-file-invoice me-1"></i>
                                                        @break
                                                    @case('Documentos')
                                                        <i class="fas fa-file-alt me-1"></i>
                                                        @break
                                                    @case('Contratos')
                                                        <i class="fas fa-handshake me-1"></i>
                                                        @break
                                                    @case('Pagos')
                                                        <i class="fas fa-money-bill me-1"></i>
                                                        @break
                                                    @case('Presupuesto')
                                                        <i class="fas fa-chart-line me-1"></i>
                                                        @break
                                                    @case('Reportes')
                                                        <i class="fas fa-chart-bar me-1"></i>
                                                        @break
                                                    @case('Administración')
                                                        <i class="fas fa-cogs me-1"></i>
                                                        @break
                                                    @default
                                                        <i class="fas fa-ellipsis-h me-1"></i>
                                                @endswitch
                                                {{ $category }}
                                            </h6>
                                            @foreach($roleHasPermissionsInCategory as $permission)
                                            <span class="badge bg-light text-dark border me-1 mb-1">
                                                <i class="fas fa-check text-success me-1"></i>
                                                {{ ucfirst(str_replace('_', ' ', $permission)) }}
                                            </span>
                                            @endforeach
                                        </div>
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
                                <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-warning">
                                    <i class="fas fa-edit me-1"></i>
                                    Asignar Permisos
                                </a>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Usuarios con este rol -->
            <div class="row">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-users me-2"></i>
                                Usuarios con este Rol
                                @if($users->total() > 0)
                                <span class="badge bg-light text-dark ms-2">{{ $users->total() }}</span>
                                @endif
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($users->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th><i class="fas fa-hashtag me-1"></i>ID</th>
                                            <th><i class="fas fa-user me-1"></i>Nombre</th>
                                            <th><i class="fas fa-envelope me-1"></i>Email</th>
                                            <th><i class="fas fa-calendar me-1"></i>Fecha Registro</th>
                                            @if(Auth::user()->hasAnyRole(['alcalde', 'contratacion']))
                                            <th class="text-center"><i class="fas fa-cogs me-1"></i>Acciones</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($users as $user)
                                        <tr>
                                            <td>
                                                <span class="badge bg-secondary">{{ $user->id }}</span>
                                            </td>
                                            <td>
                                                <i class="fas fa-user text-primary me-2"></i>
                                                <strong>{{ $user->name }}</strong>
                                            </td>
                                            <td>
                                                <i class="fas fa-envelope text-muted me-2"></i>
                                                {{ $user->email }}
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar me-1"></i>
                                                    {{ $user->created_at->format('d/m/Y') }}
                                                </small>
                                            </td>
                                            @if(Auth::user()->hasAnyRole(['alcalde', 'contratacion']))
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-outline-danger" 
                                                        onclick="removeRole({{ $user->id }}, '{{ $user->name }}')"
                                                        title="Remover rol">
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
                            <div class="d-flex justify-content-center">
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
