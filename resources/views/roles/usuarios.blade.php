@extends('layouts.app')

@section('title', 'Gestión de Usuarios y Roles')

@section('content')
<div class="pt-32 pb-8 px-4 sm:px-6 lg:px-8 min-h-screen">
    <div class="max-w-7xl mx-auto">
        {{-- Header --}}
        <div class="glass-card p-6 mb-6 slide-up">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">
                        <i class="fas fa-users-cog text-blue-600 mr-3"></i>
                        Gestión de Usuarios y Roles
                    </h1>
                    <p class="text-gray-600">
                        Administra los roles de cada usuario en el sistema
                    </p>
                </div>
                <div class="mt-4 lg:mt-0">
                    <a href="{{ route('dashboard') }}" class="btn-secondary">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Volver al Dashboard
                    </a>
                </div>
            </div>
        </div>

        {{-- Mensajes de éxito/error --}}
        @if(session('success'))
            <div id="success-alert" class="glass-card p-4 mb-6 border-l-4 border-green-500 bg-green-50 slide-up">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                        <p class="text-green-700 font-medium">{{ session('success') }}</p>
                    </div>
                    <button onclick="closeAlert('success-alert')" class="text-green-500 hover:text-green-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div id="error-alert" class="glass-card p-4 mb-6 border-l-4 border-red-500 bg-red-50 slide-up">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                        <p class="text-red-700 font-medium">{{ session('error') }}</p>
                    </div>
                    <button onclick="closeAlert('error-alert')" class="text-red-500 hover:text-red-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        {{-- Estadísticas --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="glass-card p-6 fade-in">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm mb-1">Total Usuarios</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $totalUsuarios }}</p>
                    </div>
                    <div class="bg-gradient-to-br from-blue-500 to-indigo-600 w-14 h-14 rounded-xl flex items-center justify-center">
                        <i class="fas fa-users text-white text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="glass-card p-6 fade-in" style="animation-delay: 0.1s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm mb-1">Con Rol Asignado</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $usuariosConRol }}</p>
                    </div>
                    <div class="bg-gradient-to-br from-green-500 to-emerald-600 w-14 h-14 rounded-xl flex items-center justify-center">
                        <i class="fas fa-user-check text-white text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="glass-card p-6 fade-in" style="animation-delay: 0.2s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm mb-1">Sin Rol Asignado</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $usuariosSinRol }}</p>
                    </div>
                    <div class="bg-gradient-to-br from-orange-500 to-red-600 w-14 h-14 rounded-xl flex items-center justify-center">
                        <i class="fas fa-user-times text-white text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabla de usuarios --}}
        <div class="glass-card overflow-hidden slide-up">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-list mr-2 text-blue-600"></i>
                    Lista de Usuarios
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Usuario
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Email
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Rol Actual
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fecha Registro
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($usuarios as $usuario)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold">
                                            {{ strtoupper(substr($usuario->name, 0, 1)) }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $usuario->name }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $usuario->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($usuario->role)
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            <i class="fas fa-shield-alt mr-1"></i>
                                            {{ ucfirst($usuario->role->name) }}
                                        </span>
                                    @else
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            <i class="fas fa-user mr-1"></i>
                                            Sin rol
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $usuario->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <button 
                                        onclick="openRoleModal({{ $usuario->id }}, '{{ $usuario->name }}', {{ $usuario->role_id ?? 'null' }})"
                                        class="btn-primary inline-flex items-center px-4 py-2 text-sm">
                                        <i class="fas fa-user-edit mr-2"></i>
                                        {{ $usuario->role ? 'Modificar Rol' : 'Asignar Rol' }}
                                    </button>
                                    
                                    @if($usuario->role)
                                        <button 
                                            onclick="removeUserRole({{ $usuario->id }}, '{{ $usuario->name }}')"
                                            class="btn-danger inline-flex items-center px-4 py-2 text-sm ml-2">
                                            <i class="fas fa-user-times mr-2"></i>
                                            Remover Rol
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    <i class="fas fa-inbox text-4xl mb-2"></i>
                                    <p>No hay usuarios registrados en el sistema.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            @if($usuarios->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $usuarios->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Modal para asignar/modificar rol --}}
<div id="roleModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="glass-card max-w-md w-full p-6 transform transition-all scale-95" id="roleModalContent">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-800">
                <i class="fas fa-user-cog mr-2 text-blue-600"></i>
                <span id="modalTitle">Asignar Rol</span>
            </h3>
            <button onclick="closeRoleModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form id="roleForm" onsubmit="submitRoleForm(event)">
            <input type="hidden" id="userId" name="user_id">
            
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">
                    Usuario
                </label>
                <p id="userName" class="text-gray-900 font-semibold text-lg"></p>
            </div>

            <div class="mb-6">
                <label for="roleSelect" class="block text-gray-700 font-medium mb-2">
                    Seleccionar Rol
                </label>
                <select 
                    id="roleSelect" 
                    name="role_id" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    required>
                    <option value="">-- Seleccione un rol --</option>
                    @foreach($roles as $rol)
                        <option value="{{ $rol->id }}">
                            {{ ucfirst($rol->name) }} - {{ $rol->description }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-end space-x-3">
                <button 
                    type="button" 
                    onclick="closeRoleModal()" 
                    class="btn-secondary px-6 py-2">
                    Cancelar
                </button>
                <button 
                    type="submit" 
                    class="btn-primary px-6 py-2">
                    <i class="fas fa-save mr-2"></i>
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
    .fade-in {
        animation: fadeInUp 0.6s ease-out;
    }
    .slide-up {
        animation: slideUp 0.8s ease-out;
    }
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
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(40px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    #roleModal.active {
        display: flex !important;
    }
    
    #roleModal.active #roleModalContent {
        transform: scale(1);
    }
</style>
@endpush

@push('scripts')
<script>
    // Token CSRF
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Abrir modal para asignar/modificar rol
    function openRoleModal(userId, userName, currentRoleId) {
        document.getElementById('userId').value = userId;
        document.getElementById('userName').textContent = userName;
        document.getElementById('roleSelect').value = currentRoleId || '';
        
        // Cambiar título del modal
        const modalTitle = document.getElementById('modalTitle');
        modalTitle.textContent = currentRoleId ? 'Modificar Rol' : 'Asignar Rol';
        
        // Mostrar modal
        const modal = document.getElementById('roleModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    }

    // Cerrar modal
    function closeRoleModal() {
        const modal = document.getElementById('roleModal');
        modal.classList.remove('active');
        setTimeout(() => {
            modal.classList.add('hidden');
            document.getElementById('roleForm').reset();
        }, 300);
    }

    // Cerrar modal al hacer clic fuera
    document.getElementById('roleModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeRoleModal();
        }
    });

    // Enviar formulario de asignación de rol
    async function submitRoleForm(event) {
        event.preventDefault();
        
        const userId = document.getElementById('userId').value;
        const roleId = document.getElementById('roleSelect').value;
        
        if (!roleId) {
            showNotification('Por favor selecciona un rol', 'error');
            return;
        }

        try {
            const response = await fetch('{{ route("roles.assign") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    user_id: userId,
                    role_id: roleId
                })
            });

            const data = await response.json();

            if (data.success) {
                showNotification('Rol asignado correctamente', 'success');
                closeRoleModal();
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification(data.error || 'Error al asignar el rol', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('Error al procesar la solicitud', 'error');
        }
    }

    // Remover rol de usuario
    async function removeUserRole(userId, userName) {
        if (!confirm(`¿Estás seguro de remover el rol del usuario ${userName}?`)) {
            return;
        }

        try {
            const response = await fetch('{{ route("roles.remove") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    user_id: userId
                })
            });

            const data = await response.json();

            if (data.success) {
                showNotification('Rol removido correctamente', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification(data.error || 'Error al remover el rol', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('Error al procesar la solicitud', 'error');
        }
    }

    // Mostrar notificación
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-24 right-6 glass-card p-4 shadow-xl transform translate-x-full transition-transform duration-500 z-50 ${
            type === 'success' ? 'border-l-4 border-green-500 bg-green-50' : 
            type === 'error' ? 'border-l-4 border-red-500 bg-red-50' : 
            'border-l-4 border-blue-500 bg-blue-50'
        }`;
        
        notification.innerHTML = `
            <div class="flex items-center space-x-3">
                <div class="${
                    type === 'success' ? 'bg-green-500' : 
                    type === 'error' ? 'bg-red-500' : 
                    'bg-blue-500'
                } w-10 h-10 rounded-full flex items-center justify-center">
                    <i class="fas ${
                        type === 'success' ? 'fa-check' : 
                        type === 'error' ? 'fa-times' : 
                        'fa-info'
                    } text-white"></i>
                </div>
                <div>
                    <p class="font-medium ${
                        type === 'success' ? 'text-green-800' : 
                        type === 'error' ? 'text-red-800' : 
                        'text-blue-800'
                    }">${message}</p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="${
                    type === 'success' ? 'text-green-400 hover:text-green-600' : 
                    type === 'error' ? 'text-red-400 hover:text-red-600' : 
                    'text-blue-400 hover:text-blue-600'
                }">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => notification.remove(), 500);
        }, 5000);
    }

    // Cerrar alertas
    function closeAlert(alertId) {
        const alert = document.getElementById(alertId);
        if (alert) {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-20px)';
            setTimeout(() => alert.remove(), 300);
        }
    }

    // Auto-cerrar alertas después de 5 segundos
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            const successAlert = document.getElementById('success-alert');
            const errorAlert = document.getElementById('error-alert');
            
            if (successAlert) closeAlert('success-alert');
            if (errorAlert) closeAlert('error-alert');
        }, 5000);
    });
</script>
@endpush
