@extends('layouts.app')
@section('title', 'Crear Cuenta de Cobro - CuentasCobro')

@section('content')
<!-- Contenedor principal con padding superior para el navbar fijo -->
<div class="pt-24 pb-8 px-4 sm:px-6 lg:px-8 min-h-screen">
    <!-- Header de la página -->
    <div class="max-w-4xl mx-auto mb-8">
        <div class="glass-card p-6 slide-up">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="gradient-primary w-16 h-16 rounded-2xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-file-plus text-white text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800 font-poppins">Crear Cuenta de Cobro</h1>
                        <p class="text-gray-600">Complete el formulario para generar una nueva solicitud de pago</p>
                    </div>
                </div>
                <!-- Breadcrumb de navegación -->
                <nav class="hidden md:flex" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2 text-sm">
                        <li>
                            <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                                <i class="fas fa-home"></i>
                            </a>
                        </li>
                        <li>
                            <span class="text-gray-400">/</span>
                        </li>
                        <li>
                            <a href="{{ route('cuentas-cobro.mostrar') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                                Cuentas de Cobro
                            </a>
                        </li>
                        <li>
                            <span class="text-gray-400">/</span>
                        </li>
                        <li class="text-gray-900 font-medium">Crear</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    
    <!-- Formulario principal -->
    <div class="max-w-4xl mx-auto">
        <div class="glass-card p-8 bounce-in">
            <!-- Indicador de progreso -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm font-medium text-gray-600">Progreso del formulario</span>
                    <span class="text-sm font-medium text-primary-600" id="progress-text">0% completado</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-gradient-to-r from-primary-500 to-secondary-500 h-2 rounded-full transition-all duration-500" style="width: 0%" id="progress-bar"></div>
                </div>
            </div>
            
            <!-- Mensajes de error -->
            @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded-lg slide-up">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Se encontraron errores:</h3>
                            <ul class="mt-2 text-sm text-red-700 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li class="flex items-center">
                                        <i class="fas fa-dot-circle text-xs mr-2"></i>
                                        {{ $error }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
            
            <!-- Formulario -->
            <form action="{{ route('cuentas-cobro.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="cuenta-form">
                @csrf
                
                <!-- Sección 1: Información Básica -->
                <div class="form-section" data-section="1">
                    <div class="border-b border-gray-200 pb-4 mb-6">
                        <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                            <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                            Información Básica
                        </h2>
                        <p class="text-gray-600 text-sm mt-1">Datos generales de la cuenta de cobro</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Fecha de emisión -->
                        <div class="space-y-2">
                            <label for="fecha_emision" class="flex items-center text-sm font-medium text-gray-700">
                                <i class="fas fa-calendar text-blue-500 mr-2"></i>
                                Fecha de Emisión <span class="text-red-500 ml-1">*</span>
                            </label>
                            <input type="date" 
                                   class="w-full px-4 py-3 bg-white/70 border-2 border-transparent rounded-xl focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-100 transition-all duration-300 @error('fecha_emision') border-red-300 @enderror" 
                                   id="fecha_emision" 
                                   name="fecha_emision" 
                                   value="{{ old('fecha_emision', date('Y-m-d')) }}"
                                   required>
                            @error('fecha_emision')
                                <p class="text-red-500 text-sm flex items-center mt-1">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        
                        <!-- Número de cuenta (generado automáticamente) -->
                        <div class="space-y-2">
                            <label class="flex items-center text-sm font-medium text-gray-700">
                                <i class="fas fa-hashtag text-green-500 mr-2"></i>
                                Número de Cuenta
                            </label>
                            <div class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl text-gray-600 font-mono">
                                CC-{{ date('Y') }}-{{ str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT) }}
                            </div>
                            <p class="text-xs text-gray-500">Se generará automáticamente al crear la cuenta</p>
                        </div>
                    </div>
                </div>
                
                <!-- Sección 2: Detalles del Proyecto -->
                <div class="form-section" data-section="2">
                    <div class="border-b border-gray-200 pb-4 mb-6">
                        <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                            <i class="fas fa-project-diagram text-purple-500 mr-2"></i>
                            Detalles del Proyecto
                        </h2>
                        <p class="text-gray-600 text-sm mt-1">Información específica del servicio o proyecto</p>
                    </div>
                    
                    <div class="space-y-6">
                        <!-- Proyecto/Servicio -->
                        <div class="space-y-2">
                            <label for="proyecto_servicio" class="flex items-center text-sm font-medium text-gray-700">
                                <i class="fas fa-briefcase text-purple-500 mr-2"></i>
                                Nombre del Proyecto/Servicio <span class="text-red-500 ml-1">*</span>
                            </label>
                            <input type="text" 
                                   class="w-full px-4 py-3 bg-white/70 border-2 border-transparent rounded-xl focus:border-purple-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-purple-100 transition-all duration-300 @error('proyecto_servicio') border-red-300 @enderror" 
                                   id="proyecto_servicio" 
                                   name="proyecto_servicio" 
                                   value="{{ old('proyecto_servicio') }}"
                                   placeholder="Ej: Desarrollo de sistema web, Consultoría técnica..."
                                   required>
                            @error('proyecto_servicio')
                                <p class="text-red-500 text-sm flex items-center mt-1">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        
                        <!-- Descripción detallada -->
                        <div class="space-y-2">
                            <label for="descripcion" class="flex items-center text-sm font-medium text-gray-700">
                                <i class="fas fa-align-left text-indigo-500 mr-2"></i>
                                Descripción Detallada
                            </label>
                            <textarea class="w-full px-4 py-3 bg-white/70 border-2 border-transparent rounded-xl focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-100 transition-all duration-300 resize-none @error('descripcion') border-red-300 @enderror" 
                                      id="descripcion" 
                                      name="descripcion" 
                                      rows="4"
                                      placeholder="Describa detalladamente los servicios prestados, actividades realizadas, entregables, etc.">{{ old('descripcion') }}</textarea>
                            @error('descripcion')
                                <p class="text-red-500 text-sm flex items-center mt-1">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <!-- Sección 3: Información Financiera -->
                <div class="form-section" data-section="3">
                    <div class="border-b border-gray-200 pb-4 mb-6">
                        <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                            <i class="fas fa-dollar-sign text-green-500 mr-2"></i>
                            Información Financiera
                        </h2>
                        <p class="text-gray-600 text-sm mt-1">Detalles del monto y conceptos de pago</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Valor base -->
                        <div class="space-y-2">
                            <label for="valor" class="flex items-center text-sm font-medium text-gray-700">
                                <i class="fas fa-money-bill-wave text-green-500 mr-2"></i>
                                Valor Base <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-semibold">$</span>
                                <input type="number" 
                                       class="w-full pl-8 pr-4 py-3 bg-white/70 border-2 border-transparent rounded-xl focus:border-green-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-green-100 transition-all duration-300 @error('valor') border-red-300 @enderror" 
                                       id="valor" 
                                       name="valor" 
                                       value="{{ old('valor') }}"
                                       step="0.01"
                                       min="0"
                                       placeholder="0.00"
                                       required>
                            </div>
                            @error('valor')
                                <p class="text-red-500 text-sm flex items-center mt-1">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        
                        <!-- Descuentos (opcional) -->
                        <div class="space-y-2">
                            <label for="descuentos" class="flex items-center text-sm font-medium text-gray-700">
                                <i class="fas fa-percentage text-orange-500 mr-2"></i>
                                Descuentos/Retenciones
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-semibold">$</span>
                                <input type="number" 
                                       class="w-full pl-8 pr-4 py-3 bg-white/70 border-2 border-transparent rounded-xl focus:border-orange-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-orange-100 transition-all duration-300" 
                                       id="descuentos" 
                                       name="descuentos" 
                                       value="{{ old('descuentos', 0) }}"
                                       step="0.01"
                                       min="0"
                                       placeholder="0.00">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Resumen financiero -->
                    <div class="mt-6 bg-gradient-to-r from-green-50 to-emerald-100 p-4 rounded-xl border border-green-200">
                        <h3 class="text-sm font-semibold text-green-800 mb-2">Resumen Financiero</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Valor Base:</span>
                                <span class="font-semibold text-gray-800" id="resumen-base">$0.00</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Descuentos:</span>
                                <span class="font-semibold text-gray-800" id="resumen-descuentos">$0.00</span>
                            </div>
                            <div class="flex justify-between border-t border-green-300 pt-2 md:border-t-0 md:pt-0">
                                <span class="text-green-800 font-semibold">Total a Pagar:</span>
                                <span class="font-bold text-green-800" id="resumen-total">$0.00</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Sección 4: Documentos -->
                <div class="form-section" data-section="4">
                    <div class="border-b border-gray-200 pb-4 mb-6">
                        <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                            <i class="fas fa-paperclip text-indigo-500 mr-2"></i>
                            Documentos Adjuntos
                        </h2>
                        <p class="text-gray-600 text-sm mt-1">Suba los documentos que respaldan esta cuenta de cobro</p>
                    </div>
                    
                    <div class="space-y-4">
                        <!-- Zona de subida de archivos -->
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-indigo-400 hover:bg-indigo-50 transition-all duration-300" id="upload-zone">
                            <div class="space-y-4">
                                <div class="mx-auto w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-cloud-upload-alt text-indigo-500 text-2xl"></i>
                                </div>
                                <div>
                                    <p class="text-lg font-medium text-gray-700">Arrastra archivos aquí o haz clic para seleccionar</p>
                                    <p class="text-sm text-gray-500">PDF, DOC, DOCX, JPG, PNG (Máximo 10MB por archivo)</p>
                                </div>
                                <input type="file" 
                                       class="hidden" 
                                       id="documentos" 
                                       name="documentos[]" 
                                       multiple
                                       accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                <button type="button" 
                                        class="gradient-primary text-white px-6 py-2 rounded-lg font-medium hover:shadow-lg transition-all duration-300"
                                        onclick="document.getElementById('documentos').click()">
                                    <i class="fas fa-folder-open mr-2"></i>
                                    Seleccionar Archivos
                                </button>
                            </div>
                        </div>
                        
                        <!-- Lista de archivos seleccionados -->
                        <div id="file-list" class="space-y-2 hidden">
                            <h3 class="text-sm font-medium text-gray-700">Archivos seleccionados:</h3>
                            <div id="selected-files" class="space-y-2"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Botones de acción -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-4 sm:space-y-0 pt-8 border-t border-gray-200">
                    <a href="{{ route('cuentas-cobro.mostrar') }}" 
                       class="inline-flex items-center justify-center px-6 py-3 border-2 border-gray-300 text-gray-700 bg-white rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all duration-300 font-medium">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Cancelar
                    </a>
                    
                    <div class="flex space-x-4">
                        <button type="button" 
                                class="inline-flex items-center justify-center px-6 py-3 border-2 border-blue-300 text-blue-700 bg-blue-50 rounded-xl hover:bg-blue-100 hover:border-blue-400 transition-all duration-300 font-medium"
                                id="draft-btn">
                            <i class="fas fa-save mr-2"></i>
                            Guardar como Borrador
                        </button>
                        
                        <button type="submit" 
                                class="inline-flex items-center justify-center px-8 py-3 gradient-primary text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300"
                                id="submit-btn">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Crear Cuenta de Cobro
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de confirmación -->
<div id="confirmation-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>
        <div class="relative glass-card p-6 w-full max-w-md">
            <div class="text-center">
                <div class="mx-auto w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-check text-green-500 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">¿Confirmar creación?</h3>
                <p class="text-gray-600 mb-6">Se creará la cuenta de cobro con la información proporcionada</p>
                <div class="flex space-x-4">
                    <button type="button" onclick="closeModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-200">
                        Cancelar
                    </button>
                    <button type="button" onclick="confirmSubmit()" class="flex-1 px-4 py-2 gradient-primary text-white rounded-lg hover:shadow-lg transition-all duration-200">
                        Confirmar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notifications Container -->
<div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>
@endsection

@push('styles')
<style>
    /* Estilos específicos para el formulario */
    .form-section {
        transition: all 0.3s ease;
    }
    
    /* Efectos de dragover para zona de archivos */
    .upload-dragover {
        border-color: #6366f1 !important;
        background-color: #eef2ff !important;
        transform: scale(1.02);
    }
    
    /* Animación de archivo seleccionado */
    .file-item {
        animation: slideInUp 0.3s ease-out;
    }
    
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Efectos de validación en tiempo real */
    .field-valid {
        border-color: #10b981 !important;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }
    
    .field-invalid {
        border-color: #ef4444 !important;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }
    
    /* Animaciones de progreso */
    .progress-complete {
        background: linear-gradient(45deg, #10b981, #059669);
        box-shadow: 0 0 15px rgba(16, 185, 129, 0.4);
    }
    
    .progress-partial {
        background: linear-gradient(45deg, #3b82f6, #1d4ed8);
        box-shadow: 0 0 15px rgba(59, 130, 246, 0.4);
    }
    
    .progress-start {
        background: linear-gradient(45deg, #f59e0b, #d97706);
        box-shadow: 0 0 15px rgba(245, 158, 11, 0.4);
    }
    
    /* Toast notifications */
    .toast-enter {
        animation: toastSlideIn 0.3s ease-out;
    }
    
    .toast-exit {
        animation: toastSlideOut 0.3s ease-in;
    }
    
    @keyframes toastSlideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes toastSlideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    /* Efectos de hover mejorados para botones */
    .btn-primary:not(:disabled):hover {
        transform: translateY(-2px) scale(1.02);
        box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
    }
    
    .btn-secondary:hover {
        transform: translateY(-1px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    /* Animación de pulso para elementos importantes */
    .pulse-success {
        animation: pulseGreen 2s infinite;
    }
    
    @keyframes pulseGreen {
        0% {
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        }
        70% {
            box-shadow: 0 0 0 10px rgba(16, 185, 129, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
        }
    }
    
    /* Mejoras para el modal */
    .modal-backdrop {
        backdrop-filter: blur(8px);
        background: rgba(0, 0, 0, 0.4);
    }
    
    .modal-content {
        transform: scale(0.95);
        transition: transform 0.3s ease-out;
    }
    
    .modal-content.show {
        transform: scale(1);
    }
    
    /* Efectos de carga */
    .loading-spinner {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }
    
    /* Efectos de escritura para el auto-guardado */
    .auto-save-indicator {
        animation: fadeInOut 2s ease-in-out;
    }
    
    @keyframes fadeInOut {
        0%, 100% { opacity: 0; }
        50% { opacity: 1; }
    }
    
    /* Mejoras responsive */
    @media (max-width: 768px) {
        .form-section {
            padding: 1rem;
        }
        
        .glass-card {
            margin: 0.5rem;
            padding: 1rem !important;
        }
        
        .upload-zone {
            padding: 2rem 1rem !important;
        }
    }
    
    /* Estados de formulario */
    .form-submitting {
        pointer-events: none;
        opacity: 0.7;
    }
    
    .form-submitting * {
        cursor: not-allowed !important;
    }
    
    /* Indicador de progreso mejorado */
    #progress-bar {
        position: relative;
        overflow: hidden;
    }
    
    #progress-bar::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        background-image: linear-gradient(
            -45deg,
            rgba(255, 255, 255, 0.2) 25%,
            transparent 25%,
            transparent 50%,
            rgba(255, 255, 255, 0.2) 50%,
            rgba(255, 255, 255, 0.2) 75%,
            transparent 75%,
            transparent
        );
        background-size: 50px 50px;
        animation: moveStripes 2s linear infinite;
    }
    
    @keyframes moveStripes {
        0% {
            background-position: 0 0;
        }
        100% {
            background-position: 50px 50px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Variables principales
    const form = document.getElementById('cuenta-form');
    const progressBar = document.getElementById('progress-bar');
    const progressText = document.getElementById('progress-text');
    const valorInput = document.getElementById('valor');
    const descuentosInput = document.getElementById('descuentos');
    const fileInput = document.getElementById('documentos');
    const uploadZone = document.getElementById('upload-zone');
    const fileList = document.getElementById('file-list');
    const selectedFiles = document.getElementById('selected-files');
    const submitBtn = document.getElementById('submit-btn');
    const draftBtn = document.getElementById('draft-btn');
    const modal = document.getElementById('confirmation-modal');
    
    // Estado del formulario
    let formState = {
        isSubmitting: false,
        isDraft: false,
        hasUnsavedChanges: false
    };
    
    // Sistema de notificaciones toast
    function showToast(message, type = 'success', duration = 3000) {
        const toast = document.createElement('div');
        const icons = {
            success: 'fas fa-check-circle text-green-500',
            error: 'fas fa-exclamation-circle text-red-500',
            warning: 'fas fa-exclamation-triangle text-yellow-500',
            info: 'fas fa-info-circle text-blue-500'
        };
        
        const colors = {
            success: 'bg-green-50 border-green-200',
            error: 'bg-red-50 border-red-200',
            warning: 'bg-yellow-50 border-yellow-200',
            info: 'bg-blue-50 border-blue-200'
        };
        
        toast.className = `glass-card p-4 mb-2 border-l-4 ${colors[type]} transform translate-x-full transition-all duration-300 ease-out shadow-lg`;
        toast.innerHTML = `
            <div class="flex items-center">
                <i class="${icons[type]} mr-3 text-lg"></i>
                <p class="text-sm font-medium text-gray-800">${message}</p>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        document.getElementById('toast-container').appendChild(toast);
        
        // Animación de entrada
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
        }, 100);
        
        // Auto-remover
        if (duration > 0) {
            setTimeout(() => {
                toast.classList.add('translate-x-full');
                setTimeout(() => toast.remove(), 300);
            }, duration);
        }
    }
    
    // Validación en tiempo real
    function validateField(field) {
        const value = field.value.trim();
        const isValid = field.checkValidity();
        const errorElement = field.parentElement.querySelector('.field-error');
        
        // Remover error anterior
        if (errorElement) {
            errorElement.remove();
        }
        
        // Resetear estilos
        field.classList.remove('border-red-300', 'border-green-300');
        
        if (value && isValid) {
            field.classList.add('border-green-300');
            return true;
        } else if (value && !isValid) {
            field.classList.add('border-red-300');
            showFieldError(field, getValidationMessage(field));
            return false;
        }
        
        return true;
    }
    
    function showFieldError(field, message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'field-error text-red-500 text-sm flex items-center mt-1 animate-pulse';
        errorDiv.innerHTML = `<i class="fas fa-exclamation-triangle mr-1"></i>${message}`;
        field.parentElement.appendChild(errorDiv);
    }
    
    function getValidationMessage(field) {
        const fieldName = field.name;
        const value = field.value;
        
        switch (fieldName) {
            case 'proyecto_servicio':
                return value.length < 10 ? 'El nombre del proyecto debe tener al menos 10 caracteres' : '';
            case 'valor':
                return parseFloat(value) <= 0 ? 'El valor debe ser mayor a cero' : '';
            case 'fecha_emision':
                const today = new Date();
                const inputDate = new Date(value);
                return inputDate > today ? 'La fecha no puede ser futura' : '';
            default:
                return 'Campo requerido';
        }
    }
    
    // Actualizar progreso del formulario con animación
    function updateProgress() {
        const requiredFields = ['fecha_emision', 'proyecto_servicio', 'valor'];
        let filledFields = 0;
        
        requiredFields.forEach(fieldName => {
            const field = document.querySelector(`[name="${fieldName}"]`);
            if (field && field.value.trim() && field.checkValidity()) {
                filledFields++;
            }
        });
        
        // Bonus por archivos
        if (fileInput.files.length > 0) filledFields += 0.5;
        
        const totalFields = requiredFields.length + 0.5;
        const percentage = Math.round((filledFields / totalFields) * 100);
        
        // Animación suave del progreso
        progressBar.style.width = percentage + '%';
        progressText.textContent = percentage + '% completado';
        
        // Cambiar color según progreso
        if (percentage >= 90) {
            progressBar.className = progressBar.className.replace(/from-\w+-500 to-\w+-500/, 'from-green-500 to-emerald-500');
        } else if (percentage >= 60) {
            progressBar.className = progressBar.className.replace(/from-\w+-500 to-\w+-500/, 'from-blue-500 to-indigo-500');
        } else {
            progressBar.className = progressBar.className.replace(/from-\w+-500 to-\w+-500/, 'from-yellow-500 to-orange-500');
        }
        
        // Habilitar/deshabilitar botón de envío
        const isComplete = percentage >= 75;
        submitBtn.disabled = !isComplete;
        
        if (isComplete) {
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            submitBtn.classList.add('hover:shadow-xl', 'transform', 'hover:-translate-y-1');
        } else {
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            submitBtn.classList.remove('hover:shadow-xl', 'transform', 'hover:-translate-y-1');
        }
        
        return percentage;
    }
    
    // Actualizar resumen financiero con animación
    function updateFinancialSummary() {
        const valor = parseFloat(valorInput.value) || 0;
        const descuentos = parseFloat(descuentosInput.value) || 0;
        const total = Math.max(0, valor - descuentos);
        
        // Formatear moneda colombiana
        const formatCurrency = (amount) => {
            return new Intl.NumberFormat('es-CO', {
                style: 'currency',
                currency: 'COP',
                minimumFractionDigits: 0
            }).format(amount);
        };
        
        // Actualizar con animación
        animateValue(document.getElementById('resumen-base'), formatCurrency(valor));
        animateValue(document.getElementById('resumen-descuentos'), formatCurrency(descuentos));
        animateValue(document.getElementById('resumen-total'), formatCurrency(total));
        
        // Validar descuentos no mayores al valor
        if (descuentos > valor && valor > 0) {
            showToast('Los descuentos no pueden ser mayores al valor base', 'warning');
            descuentosInput.value = valor;
        }
    }
    
    // Animar valores numéricos
    function animateValue(element, newValue) {
        element.style.transform = 'scale(1.1)';
        element.style.transition = 'all 0.2s ease';
        
        setTimeout(() => {
            element.textContent = newValue;
            element.style.transform = 'scale(1)';
        }, 100);
    }
    
    // Gestión avanzada de archivos
    function handleFiles(files) {
        const maxSize = 10 * 1024 * 1024; // 10MB
        const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/jpeg', 'image/png'];
        const validFiles = [];
        
        Array.from(files).forEach(file => {
            if (file.size > maxSize) {
                showToast(`El archivo "${file.name}" es muy grande (máximo 10MB)`, 'error');
                return;
            }
            
            if (!allowedTypes.includes(file.type)) {
                showToast(`Tipo de archivo no permitido: "${file.name}"`, 'error');
                return;
            }
            
            validFiles.push(file);
        });
        
        if (validFiles.length === 0) {
            fileList.classList.add('hidden');
            return;
        }
        
        displayFiles(validFiles);
        showToast(`${validFiles.length} archivo(s) agregado(s) correctamente`, 'success');
    }
    
    function displayFiles(files) {
        selectedFiles.innerHTML = '';
        fileList.classList.remove('hidden');
        
        files.forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'file-item flex items-center justify-between p-4 glass-card mb-2 hover:shadow-md transition-all duration-200';
            
            const fileIcon = getFileIcon(file.type);
            const fileSize = (file.size / 1024 / 1024).toFixed(2);
            
            fileItem.innerHTML = `
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                        <i class="${fileIcon} text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800 truncate max-w-xs">${file.name}</p>
                        <p class="text-xs text-gray-500">${fileSize} MB</p>
                    </div>
                </div>
                <button type="button" 
                        onclick="removeFile(${index})" 
                        class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-all duration-200">
                    <i class="fas fa-trash-alt"></i>
                </button>
            `;
            
            selectedFiles.appendChild(fileItem);
        });
    }
    
    function getFileIcon(fileType) {
        const iconMap = {
            'application/pdf': 'fas fa-file-pdf',
            'application/msword': 'fas fa-file-word',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document': 'fas fa-file-word',
            'image/jpeg': 'fas fa-file-image',
            'image/png': 'fas fa-file-image'
        };
        return iconMap[fileType] || 'fas fa-file';
    }
    
    // Event listeners mejorados
    form.addEventListener('input', (e) => {
        formState.hasUnsavedChanges = true;
        validateField(e.target);
        updateProgress();
        if (e.target === valorInput || e.target === descuentosInput) {
            updateFinancialSummary();
        }
    });
    
    // Auto-guardado periódico (cada 30 segundos)
    let autoSaveInterval = setInterval(() => {
        if (formState.hasUnsavedChanges && !formState.isSubmitting) {
            saveDraft();
        }
    }, 30000);
    
    function saveDraft() {
        const formData = new FormData(form);
        localStorage.setItem('cuentaCobro_draft', JSON.stringify({
            fecha_emision: formData.get('fecha_emision'),
            proyecto_servicio: formData.get('proyecto_servicio'),
            descripcion: formData.get('descripcion'),
            valor: formData.get('valor'),
            descuentos: formData.get('descuentos'),
            timestamp: Date.now()
        }));
        formState.hasUnsavedChanges = false;
        showToast('Borrador guardado automáticamente', 'info', 2000);
    }
    
    // Cargar borrador al iniciar
    function loadDraft() {
        const draft = localStorage.getItem('cuentaCobro_draft');
        if (draft) {
            const data = JSON.parse(draft);
            const hoursPassed = (Date.now() - data.timestamp) / (1000 * 60 * 60);
            
            if (hoursPassed < 24) { // Borrador válido por 24 horas
                if (confirm('Se encontró un borrador guardado. ¿Desea cargarlo?')) {
                    Object.keys(data).forEach(key => {
                        if (key !== 'timestamp') {
                            const field = document.querySelector(`[name="${key}"]`);
                            if (field && data[key]) {
                                field.value = data[key];
                            }
                        }
                    });
                    showToast('Borrador cargado correctamente', 'success');
                    updateProgress();
                    updateFinancialSummary();
                }
            } else {
                localStorage.removeItem('cuentaCobro_draft');
            }
        }
    }
    
    // Manejo del envío del formulario
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (formState.isSubmitting) return;
        
        // Validación final
        const isValid = validateForm();
        if (!isValid) {
            showToast('Por favor corrija los errores antes de continuar', 'error');
            return;
        }
        
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.style.opacity = '1';
            modal.querySelector('.glass-card').style.transform = 'scale(1)';
        }, 50);
    });
    
    function validateForm() {
        let isValid = true;
        const requiredFields = form.querySelectorAll('[required]');
        
        requiredFields.forEach(field => {
            if (!validateField(field)) {
                isValid = false;
            }
        });
        
        return isValid;
    }
    
    // Drag and drop mejorado
    uploadZone.addEventListener('click', () => fileInput.click());
    
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        uploadZone.addEventListener(eventName, preventDefaults, false);
    });
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    ['dragenter', 'dragover'].forEach(eventName => {
        uploadZone.addEventListener(eventName, highlight, false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        uploadZone.addEventListener(eventName, unhighlight, false);
    });
    
    function highlight() {
        uploadZone.classList.add('upload-dragover');
    }
    
    function unhighlight() {
        uploadZone.classList.remove('upload-dragover');
    }
    
    uploadZone.addEventListener('drop', handleDrop, false);
    
    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        fileInput.files = files;
        handleFiles(files);
        updateProgress();
    }
    
    fileInput.addEventListener('change', function() {
        handleFiles(this.files);
        updateProgress();
    });
    
    // Botón de borrador
    draftBtn.addEventListener('click', function() {
        saveDraft();
        showToast('Borrador guardado correctamente', 'success');
    });
    
    // Advertencia antes de salir
    window.addEventListener('beforeunload', function(e) {
        if (formState.hasUnsavedChanges && !formState.isSubmitting) {
            e.preventDefault();
            e.returnValue = '';
        }
    });
    
    // Inicialización
    loadDraft();
    updateFinancialSummary();
    updateProgress();
    
    // Limpiar intervalo al salir
    window.addEventListener('unload', () => {
        clearInterval(autoSaveInterval);
    });
});

// Funciones globales
function removeFile(index) {
    const fileInput = document.getElementById('documentos');
    const dt = new DataTransfer();
    const files = Array.from(fileInput.files);
    
    files.splice(index, 1);
    files.forEach(file => dt.items.add(file));
    
    fileInput.files = dt.files;
    
    if (files.length === 0) {
        document.getElementById('file-list').classList.add('hidden');
    } else {
        // Re-renderizar archivos
        const event = new Event('change');
        fileInput.dispatchEvent(event);
    }
    
    // Actualizar progreso
    document.dispatchEvent(new Event('DOMContentLoaded'));
}

function closeModal() {
    const modal = document.getElementById('confirmation-modal');
    modal.style.opacity = '0';
    modal.querySelector('.glass-card').style.transform = 'scale(0.95)';
    
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

function confirmSubmit() {
    const form = document.getElementById('cuenta-form');
    const submitBtn = document.getElementById('submit-btn');
    
    // Cambiar estado del botón
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creando...';
    
    // Limpiar borrador
    localStorage.removeItem('cuentaCobro_draft');
    
    // Enviar formulario
    form.submit();
}
</script>
@endpush