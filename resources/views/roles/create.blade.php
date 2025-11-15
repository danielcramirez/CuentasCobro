@extends('layouts.app')

@section('title', 'Crear Nuevo Rol - CuentasCobro')

@section('content')
<!-- Modern Layout with TailwindCSS + Bootstrap Integration -->
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Enhanced Header Section -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <!-- Breadcrumb Navigation -->
            <nav class="flex mb-4" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('roles.index') }}" 
                           class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                            </svg>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <a href="{{ route('roles.index') }}" 
                               class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 transition-colors duration-200 md:ml-2">
                                <i class="fas fa-users-cog mr-2"></i>Gestión de Roles
                            </a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Crear Nuevo Rol</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <!-- Page Title and Actions -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-plus-circle text-white text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">
                            Crear Nuevo Rol
                        </h1>
                        <p class="text-sm text-gray-600 mt-1">
                            Configure permisos y responsabilidades para el nuevo rol del sistema
                        </p>
                    </div>
                </div>
                
                <div class="mt-4 sm:mt-0">
                    <a href="{{ route('roles.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Volver al Listado
                    </a>
                </div>
            </div>
        </div>

        <!-- Enhanced Error Messages -->
        @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-400 p-6 rounded-xl shadow-sm mb-8">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">
                        ¡Errores de Validación!
                    </h3>
                    <div class="mt-2 text-sm text-red-700">
                        <p class="mb-2">Por favor corrija los siguientes errores antes de continuar:</p>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="ml-auto pl-3">
                    <button type="button" class="inline-flex text-red-400 hover:text-red-600 focus:outline-none" onclick="this.parentElement.parentElement.parentElement.style.display='none'">
                        <span class="sr-only">Cerrar</span>
                        <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        @endif

        <!-- Modern Form Layout -->
        <form action="{{ route('roles.store') }}" method="POST" id="createRoleForm" class="space-y-8">
            @csrf
            
            <!-- Main Form Container -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column - Role Information -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Basic Information Card -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-4">
                            <h3 class="text-lg font-semibold text-white flex items-center">
                                <i class="fas fa-info-circle mr-3"></i>
                                Información Básica
                            </h3>
                            <p class="text-blue-100 text-sm mt-1">Datos principales del rol</p>
                        </div>
                        <div class="p-6 space-y-6">
                            <!-- Role Name Field -->
                            <div class="space-y-2">
                                <label for="name" class="block text-sm font-semibold text-gray-700 flex items-center">
                                    <i class="fas fa-tag mr-2 text-blue-500"></i>
                                    Nombre del Rol
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <input type="text" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('name') border-red-500 ring-2 ring-red-200 @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}" 
                                       placeholder="Ej: coordinador, auditor, supervisor..."
                                       required>
                                <div class="flex items-center text-xs text-gray-500">
                                    <i class="fas fa-lightbulb mr-1"></i>
                                    <span>Solo letras minúsculas y guiones bajos permitidos</span>
                                </div>
                                @error('name')
                                <p class="text-red-600 text-sm flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>

                            <!-- Role Description Field -->
                            <div class="space-y-2">
                                <label for="description" class="block text-sm font-semibold text-gray-700 flex items-center">
                                    <i class="fas fa-align-left mr-2 text-blue-500"></i>
                                    Descripción del Rol
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <textarea class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 resize-none @error('description') border-red-500 ring-2 ring-red-200 @enderror" 
                                          id="description" 
                                          name="description" 
                                          rows="4" 
                                          placeholder="Describe las responsabilidades, funciones y alcance de este rol dentro del sistema..."
                                          required>{{ old('description') }}</textarea>
                                <div class="flex items-center justify-between text-xs text-gray-500">
                                    <span class="flex items-center">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Máximo 500 caracteres
                                    </span>
                                    <span id="charCount" class="text-gray-400">0/500</span>
                                </div>
                                @error('description')
                                <p class="text-red-600 text-sm flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Permissions Summary Card -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="bg-gradient-to-r from-emerald-500 to-teal-600 px-6 py-4">
                            <h3 class="text-lg font-semibold text-white flex items-center">
                                <i class="fas fa-chart-pie mr-3"></i>
                                Resumen de Permisos
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="text-center p-4 bg-blue-50 rounded-lg">
                                    <div class="text-2xl font-bold text-blue-600" id="selectedCount">0</div>
                                    <div class="text-sm text-blue-700">Seleccionados</div>
                                </div>
                                <div class="text-center p-4 bg-gray-50 rounded-lg">
                                    <div class="text-2xl font-bold text-gray-600">{{ count($availablePermissions) }}</div>
                                    <div class="text-sm text-gray-700">Disponibles</div>
                                </div>
                            </div>
                            <!-- Progress Bar -->
                            <div class="mt-4">
                                <div class="flex justify-between text-sm text-gray-600 mb-1">
                                    <span>Progreso de Configuración</span>
                                    <span id="progressPercentage">0%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-gradient-to-r from-blue-500 to-emerald-500 h-2 rounded-full transition-all duration-300" style="width: 0%" id="progressBar"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Permissions Management -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <!-- Permissions Header -->
                        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-white flex items-center">
                                        <i class="fas fa-key mr-3"></i>
                                        Gestión de Permisos
                                    </h3>
                                    <p class="text-indigo-100 text-sm mt-1">Seleccione los permisos que tendrá este rol</p>
                                </div>
                                <div class="flex space-x-2 mt-3 sm:mt-0">
                                    <button type="button" 
                                            class="px-4 py-2 bg-white/20 text-white rounded-lg hover:bg-white/30 transition-all duration-200 text-sm font-medium flex items-center"
                                            onclick="selectAllPermissions()">
                                        <i class="fas fa-check-square mr-2"></i>
                                        Seleccionar Todo
                                    </button>
                                    <button type="button" 
                                            class="px-4 py-2 bg-white/10 text-white rounded-lg hover:bg-white/20 transition-all duration-200 text-sm font-medium flex items-center"
                                            onclick="clearAllPermissions()">
                                        <i class="fas fa-square mr-2"></i>
                                        Limpiar Todo
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Permissions Content -->
                        <div class="p-6">
                            @php
                            $permissionCategories = [
                                'Cuentas de Cobro' => [
                                    'icon' => 'fas fa-file-invoice',
                                    'color' => 'blue',
                                    'permissions' => [
                                        'create_cuenta_cobro' => 'Crear cuentas de cobro',
                                        'view_cuenta_cobro' => 'Ver cuentas de cobro',
                                        'view_own_cuenta_cobro' => 'Ver propias cuentas de cobro',
                                        'view_all_cuenta_cobro' => 'Ver todas las cuentas de cobro',
                                        'edit_own_cuenta_cobro' => 'Editar propias cuentas de cobro',
                                        'review_cuenta_cobro' => 'Revisar cuentas de cobro',
                                        'approve_cuenta_cobro' => 'Aprobar cuentas de cobro',
                                        'reject_cuenta_cobro' => 'Rechazar cuentas de cobro',
                                        'final_approval' => 'Aprobación final'
                                    ]
                                ],
                                'Documentos' => [
                                    'icon' => 'fas fa-file-alt',
                                    'color' => 'green',
                                    'permissions' => [
                                        'upload_documents' => 'Subir documentos',
                                        'view_documents' => 'Ver documentos'
                                    ]
                                ],
                                'Contratos' => [
                                    'icon' => 'fas fa-handshake',
                                    'color' => 'purple',
                                    'permissions' => [
                                        'view_contract_info' => 'Ver información de contratos',
                                        'manage_contracts' => 'Gestionar contratos',
                                        'contract_validation' => 'Validación de contratos'
                                    ]
                                ],
                                'Pagos' => [
                                    'icon' => 'fas fa-money-bill',
                                    'color' => 'emerald',
                                    'permissions' => [
                                        'authorize_payment' => 'Autorizar pagos',
                                        'process_payment' => 'Procesar pagos',
                                        'generate_checks' => 'Generar cheques',
                                        'bank_transfers' => 'Transferencias bancarias',
                                        'payment_confirmation' => 'Confirmación de pagos',
                                        'generate_payment_orders' => 'Generar órdenes de pago'
                                    ]
                                ],
                                'Presupuesto' => [
                                    'icon' => 'fas fa-chart-line',
                                    'color' => 'orange',
                                    'permissions' => [
                                        'view_budget' => 'Ver presupuesto',
                                        'manage_budget' => 'Gestionar presupuesto'
                                    ]
                                ],
                                'Reportes' => [
                                    'icon' => 'fas fa-chart-bar',
                                    'color' => 'red',
                                    'permissions' => [
                                        'view_reports' => 'Ver reportes',
                                        'financial_reports' => 'Reportes financieros',
                                        'view_financial_reports' => 'Ver reportes financieros',
                                        'contract_reports' => 'Reportes de contratos'
                                    ]
                                ],
                                'Administración' => [
                                    'icon' => 'fas fa-cogs',
                                    'color' => 'gray',
                                    'permissions' => [
                                        'manage_users' => 'Gestionar usuarios',
                                        'manage_contractors' => 'Gestionar contratistas',
                                        'contractor_registration' => 'Registro de contratistas',
                                        'system_admin' => 'Administrador del sistema'
                                    ]
                                ],
                                'Otros' => [
                                    'icon' => 'fas fa-ellipsis-h',
                                    'color' => 'indigo',
                                    'permissions' => [
                                        'add_comments' => 'Agregar comentarios',
                                        'request_corrections' => 'Solicitar correcciones',
                                        'override_decisions' => 'Anular decisiones'
                                    ]
                                ]
                            ];
                            @endphp

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @foreach($permissionCategories as $category => $categoryData)
                                <div class="border border-gray-200 rounded-xl p-5 hover:shadow-md transition-all duration-200 bg-gray-50/50">
                                    <!-- Category Header -->
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-{{ $categoryData['color'] }}-100 rounded-lg flex items-center justify-center mr-3">
                                                <i class="{{ $categoryData['icon'] }} text-{{ $categoryData['color'] }}-600"></i>
                                            </div>
                                            <h4 class="font-semibold text-gray-800">{{ $category }}</h4>
                                        </div>
                                        <button type="button" 
                                                class="px-3 py-1 text-xs font-medium text-{{ $categoryData['color'] }}-700 bg-{{ $categoryData['color'] }}-100 rounded-full hover:bg-{{ $categoryData['color'] }}-200 transition-colors duration-200"
                                                onclick="toggleCategoryPermissions('{{ strtolower(str_replace(' ', '_', $category)) }}')">
                                            <i class="fas fa-check-square mr-1"></i>
                                            Toggle
                                        </button>
                                    </div>
                                    
                                    <!-- Permissions List -->
                                    <div class="space-y-3">
                                        @foreach($categoryData['permissions'] as $permission => $description)
                                        <label class="flex items-start cursor-pointer group">
                                            <div class="flex items-center h-5">
                                                <input class="permission-checkbox {{ strtolower(str_replace(' ', '_', $category)) }}-permission w-4 h-4 text-{{ $categoryData['color'] }}-600 bg-gray-100 border-gray-300 rounded focus:ring-{{ $categoryData['color'] }}-500 focus:ring-2 transition-all duration-200" 
                                                       type="checkbox" 
                                                       id="permission_{{ $permission }}" 
                                                       name="permissions[]" 
                                                       value="{{ $permission }}"
                                                       {{ in_array($permission, old('permissions', [])) ? 'checked' : '' }}
                                                       onchange="updatePermissionCount()">
                                            </div>
                                            <div class="ml-3 text-sm">
                                                <span class="font-medium text-gray-700 group-hover:text-{{ $categoryData['color'] }}-600 transition-colors duration-200">
                                                    {{ $description }}
                                                </span>
                                            </div>
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Action Buttons -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                    <!-- Help Text -->
                    <div class="flex items-center text-sm text-gray-600">
                        <div class="flex-shrink-0 w-5 h-5 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-info text-blue-600 text-xs"></i>
                        </div>
                        <span>
                            Los campos marcados con <span class="text-red-500 font-medium">*</span> son obligatorios.
                            <br class="sm:hidden">
                            <span class="hidden sm:inline">•</span> Asegúrese de seleccionar al menos un permiso.
                        </span>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex space-x-3">
                        <a href="{{ route('roles.index') }}" 
                           class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                            <i class="fas fa-times mr-2"></i>
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="inline-flex items-center px-8 py-3 border border-transparent rounded-lg text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-0.5">
                            <i class="fas fa-save mr-2"></i>
                            Crear Rol
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
    /* Enhanced Custom Styles with TailwindCSS Integration */
    .gradient-bg {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .card-hover {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .card-hover:hover {
        transform: translateY(-2px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    
    /* Checkbox animations */
    .permission-checkbox {
        transition: all 0.2s ease-in-out;
    }
    
    .permission-checkbox:checked {
        transform: scale(1.1);
    }
    
    /* Form input focus states */
    .form-input-focus:focus {
        ring-offset-width: 2px;
        ring-offset-color: #fff;
        ring-width: 2px;
        ring-color: #3b82f6;
        border-color: transparent;
    }
    
    /* Progress bar animation */
    @keyframes progressGrow {
        from { width: 0%; }
        to { width: var(--progress-width); }
    }
    
    .progress-animated {
        animation: progressGrow 0.5s ease-out;
    }
    
    /* Breadcrumb improvements */
    .breadcrumb-item + .breadcrumb-item::before {
        content: ">";
        font-weight: bold;
        color: #6b7280;
    }
    
    /* Button hover effects */
    .btn-gradient:hover {
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        transform: translateY(-1px);
    }
    
    /* Category card responsive improvements */
    @media (max-width: 768px) {
        .category-grid {
            grid-template-columns: 1fr;
        }
    }
    
    /* Smooth scrolling for form sections */
    html {
        scroll-behavior: smooth;
    }
    
    /* Loading state for form submission */
    .form-loading .btn-submit {
        opacity: 0.7;
        cursor: not-allowed;
    }
    
    .form-loading .btn-submit::after {
        content: '';
        display: inline-block;
        width: 16px;
        height: 16px;
        margin-left: 8px;
        border: 2px solid #ffffff;
        border-radius: 50%;
        border-top-color: transparent;
        animation: spin 1s ease-in-out infinite;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    /* Error state styling */
    .error-shake {
        animation: shake 0.5s ease-in-out;
    }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
    
    /* Toast notification positioning */
    .toast-container {
        position: fixed;
        top: 1rem;
        right: 1rem;
        z-index: 9999;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize components
    initializeFormValidation();
    initializeCharacterCounter();
    initializePermissionManagement();
    initializeProgressTracking();
    initializeAutoSaveIndicators();
    
    // Auto-hide error alerts after 8 seconds with fade animation
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert, .bg-red-50');
        alerts.forEach(function(alert) {
            if (alert.classList.contains('bg-red-50')) {
                alert.style.transition = 'all 0.5s ease-out';
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(() => alert.remove(), 500);
            }
        });
    }, 8000);
});

// Enhanced Character Counter for Description Field
function initializeCharacterCounter() {
    const descriptionField = document.getElementById('description');
    const charCount = document.getElementById('charCount');
    const maxLength = 500;
    
    if (descriptionField && charCount) {
        descriptionField.addEventListener('input', function() {
            const currentLength = this.value.length;
            charCount.textContent = `${currentLength}/${maxLength}`;
            
            // Visual feedback based on character count
            if (currentLength > maxLength * 0.9) {
                charCount.className = 'text-red-500 font-semibold';
            } else if (currentLength > maxLength * 0.7) {
                charCount.className = 'text-yellow-500 font-medium';
            } else {
                charCount.className = 'text-gray-400';
            }
        });
        
        // Trigger initial count
        descriptionField.dispatchEvent(new Event('input'));
    }
}

// Enhanced Permission Management System
function initializePermissionManagement() {
    updatePermissionCount();
    
    // Add visual feedback when permissions are selected
    const checkboxes = document.querySelectorAll('.permission-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const label = this.closest('label');
            if (this.checked) {
                label.classList.add('bg-blue-50', 'border-blue-200', 'rounded-lg', 'p-2', 'transition-all', 'duration-200');
            } else {
                label.classList.remove('bg-blue-50', 'border-blue-200', 'rounded-lg', 'p-2');
            }
            updatePermissionCount();
            updateProgressBar();
        });
    });
}

// Advanced Progress Tracking
function initializeProgressTracking() {
    updateProgressBar();
}

function updateProgressBar() {
    const totalPermissions = document.querySelectorAll('input[name="permissions[]"]').length;
    const selectedPermissions = document.querySelectorAll('input[name="permissions[]"]:checked').length;
    const progressBar = document.getElementById('progressBar');
    const progressPercentage = document.getElementById('progressPercentage');
    
    if (totalPermissions > 0 && progressBar && progressPercentage) {
        const percentage = Math.round((selectedPermissions / totalPermissions) * 100);
        progressBar.style.width = `${percentage}%`;
        progressPercentage.textContent = `${percentage}%`;
        
        // Dynamic color based on progress
        progressBar.className = 'h-2 rounded-full transition-all duration-300';
        if (percentage < 25) {
            progressBar.classList.add('bg-red-400');
        } else if (percentage < 50) {
            progressBar.classList.add('bg-yellow-400');
        } else if (percentage < 75) {
            progressBar.classList.add('bg-blue-400');
        } else {
            progressBar.classList.add('bg-gradient-to-r', 'from-green-400', 'to-blue-500');
        }
    }
}

// Enhanced Permission Counter with Animation
function updatePermissionCount() {
    const checkedPermissions = document.querySelectorAll('input[name="permissions[]"]:checked');
    const selectedCountElement = document.getElementById('selectedCount');
    
    if (selectedCountElement) {
        const count = checkedPermissions.length;
        selectedCountElement.style.transform = 'scale(1.2)';
        selectedCountElement.textContent = count;
        
        setTimeout(() => {
            selectedCountElement.style.transform = 'scale(1)';
        }, 200);
    }
}

// Enhanced Select All Permissions
function selectAllPermissions() {
    const checkboxes = document.querySelectorAll('input[name="permissions[]"]');
    let delay = 0;
    
    checkboxes.forEach((checkbox, index) => {
        setTimeout(() => {
            checkbox.checked = true;
            checkbox.dispatchEvent(new Event('change'));
        }, delay);
        delay += 30; // Staggered animation
    });
    
    // Show success feedback
    showToast('Todos los permisos han sido seleccionados', 'success');
}

// Enhanced Clear All Permissions
function clearAllPermissions() {
    const checkboxes = document.querySelectorAll('input[name="permissions[]"]');
    let delay = 0;
    
    checkboxes.forEach((checkbox, index) => {
        setTimeout(() => {
            checkbox.checked = false;
            checkbox.dispatchEvent(new Event('change'));
        }, delay);
        delay += 20; // Faster clear animation
    });
    
    showToast('Todos los permisos han sido deseleccionados', 'info');
}

// Enhanced Category Toggle with Smart Logic
function toggleCategoryPermissions(category) {
    const categoryCheckboxes = document.querySelectorAll(`.${category}-permission`);
    const checkedCount = Array.from(categoryCheckboxes).filter(cb => cb.checked).length;
    const shouldCheck = checkedCount < categoryCheckboxes.length / 2; // Smart toggle logic
    
    let delay = 0;
    categoryCheckboxes.forEach((checkbox, index) => {
        setTimeout(() => {
            checkbox.checked = shouldCheck;
            checkbox.dispatchEvent(new Event('change'));
        }, delay);
        delay += 50;
    });
    
    const action = shouldCheck ? 'seleccionados' : 'deseleccionados';
    showToast(`Permisos de ${category.replace('_', ' ')} ${action}`, 'info');
}

// Enhanced Form Validation
function initializeFormValidation() {
    const form = document.getElementById('createRoleForm');
    const nameInput = document.getElementById('name');
    const descriptionInput = document.getElementById('description');
    
    // Real-time name validation and formatting
    if (nameInput) {
        nameInput.addEventListener('input', function(e) {
            let value = e.target.value;
            // Auto-format: lowercase and underscores only
            const formattedValue = value.toLowerCase()
                                       .replace(/\s+/g, '_')
                                       .replace(/[^a-z_]/g, '');
            
            if (value !== formattedValue) {
                e.target.value = formattedValue;
            }
            
            // Visual validation feedback
            const isValid = /^[a-z_]+$/.test(formattedValue) && formattedValue.length > 0;
            updateFieldValidation(e.target, isValid);
        });
    }
    
    // Form submission with enhanced validation
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (validateForm()) {
                showLoadingState();
                // Add slight delay for better UX
                setTimeout(() => {
                    form.submit();
                }, 500);
            }
        });
    }
}

// Enhanced Form Validation Function
function validateForm() {
    const name = document.getElementById('name').value.trim();
    const description = document.getElementById('description').value.trim();
    const selectedPermissions = document.querySelectorAll('input[name="permissions[]"]:checked').length;
    
    let isValid = true;
    const errors = [];
    
    // Validate name
    if (!name) {
        errors.push('El nombre del rol es obligatorio');
        isValid = false;
    } else if (!/^[a-z_]+$/.test(name)) {
        errors.push('El nombre del rol solo puede contener letras minúsculas y guiones bajos');
        isValid = false;
    }
    
    // Validate description
    if (!description) {
        errors.push('La descripción del rol es obligatoria');
        isValid = false;
    } else if (description.length > 500) {
        errors.push('La descripción no puede exceder 500 caracteres');
        isValid = false;
    }
    
    // Validate permissions
    if (selectedPermissions === 0) {
        errors.push('Debe seleccionar al menos un permiso para el rol');
        isValid = false;
    }
    
    if (!isValid) {
        showValidationErrors(errors);
        return false;
    }
    
    return true;
}

// Visual Field Validation Feedback
function updateFieldValidation(field, isValid) {
    const validClasses = ['border-green-500', 'ring-2', 'ring-green-200'];
    const invalidClasses = ['border-red-500', 'ring-2', 'ring-red-200'];
    
    // Remove all validation classes
    field.classList.remove(...validClasses, ...invalidClasses);
    
    // Add appropriate classes
    if (field.value.trim()) {
        field.classList.add(...(isValid ? validClasses : invalidClasses));
    }
}

// Show Validation Errors
function showValidationErrors(errors) {
    const errorHtml = errors.map(error => `<li class="flex items-center"><i class="fas fa-times-circle mr-2"></i>${error}</li>`).join('');
    
    showToast(`
        <div class="text-sm">
            <strong class="block mb-2">Errores de Validación:</strong>
            <ul class="space-y-1">${errorHtml}</ul>
        </div>
    `, 'error', 5000);
    
    // Add shake animation to form
    const form = document.getElementById('createRoleForm');
    form.classList.add('error-shake');
    setTimeout(() => form.classList.remove('error-shake'), 500);
}

// Loading State Management
function showLoadingState() {
    const submitBtn = document.querySelector('button[type="submit"]');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creando Rol...';
        document.body.classList.add('form-loading');
    }
}

// Auto-save Indicators (Visual feedback for user actions)
function initializeAutoSaveIndicators() {
    const inputs = document.querySelectorAll('input, textarea');
    inputs.forEach(input => {
        input.addEventListener('change', function() {
            showAutoSaveIndicator();
        });
    });
}

function showAutoSaveIndicator() {
    // Show a subtle indicator that changes are being tracked
    const indicator = document.createElement('div');
    indicator.className = 'fixed top-4 right-4 bg-blue-500 text-white px-3 py-1 rounded-full text-xs opacity-75 transition-all duration-300';
    indicator.innerHTML = '<i class="fas fa-check mr-1"></i>Cambios guardados';
    document.body.appendChild(indicator);
    
    setTimeout(() => {
        indicator.style.opacity = '0';
        setTimeout(() => indicator.remove(), 300);
    }, 2000);
}

// Enhanced Toast Notification System
function showToast(message, type = 'info', duration = 3000) {
    const toast = document.createElement('div');
    const bgColor = {
        'success': 'bg-green-500',
        'error': 'bg-red-500',
        'warning': 'bg-yellow-500',
        'info': 'bg-blue-500'
    }[type] || 'bg-gray-500';
    
    toast.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg transform translate-x-full transition-transform duration-300 z-50 max-w-sm`;
    toast.innerHTML = `
        <div class="flex items-center">
            <div class="flex-1">${message}</div>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-3 text-white hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // Trigger animation
    setTimeout(() => {
        toast.style.transform = 'translateX(0)';
    }, 100);
    
    // Auto remove
    setTimeout(() => {
        toast.style.transform = 'translateX(full)';
        setTimeout(() => toast.remove(), 300);
    }, duration);
}
</script>
@endpush
@endsection
