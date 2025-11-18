@extends('layouts.app')

@section('title', 'Eliminar Cuenta de Cobro - CuentasCobro')

@section('content')
<div class="min-h-screen pt-24 pb-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        
        <!-- Breadcrumb de navegación -->
        <div class="mb-6">
            <nav class="flex items-center space-x-2 text-sm text-gray-500" aria-label="Breadcrumb">
                <a href="{{ route('dashboard') }}" class="hover:text-gray-700 transition-colors flex items-center">
                    <i class="fas fa-home mr-1"></i>
                    Inicio
                </a>
                <i class="fas fa-chevron-right text-gray-300"></i>
                @if(auth()->user()->hasRole('contratista'))
                    <a href="{{ route('contratista.dashboard') }}" class="hover:text-gray-700 transition-colors">
                        Dashboard Contratista
                    </a>
                    <i class="fas fa-chevron-right text-gray-300"></i>
                @endif
                <a href="{{ route('cuentas-cobro.mostrar') }}" class="hover:text-gray-700 transition-colors">
                    Cuentas de Cobro
                </a>
                <i class="fas fa-chevron-right text-gray-300"></i>
                <span class="text-gray-700 font-medium">Eliminar #{{ $cuenta->id }}</span>
            </nav>
        </div>
        
        <!-- Header mejorado -->
        <div class="glass-card p-6 mb-8 slide-up">
            <div class="flex items-center justify-center">
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-red-500 to-pink-600 rounded-full mb-4 shadow-lg">
                        <i class="fas fa-trash-alt text-white text-2xl sm:text-3xl"></i>
                    </div>
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 mb-2">Eliminar Cuenta de Cobro</h1>
                    <p class="text-lg sm:text-xl text-gray-600">Cuenta #{{ $cuenta->id }} - Esta acción no se puede deshacer</p>
                </div>
            </div>
        </div>

        <!-- Warning Card -->
        <div class="bg-gradient-to-r from-red-50 to-pink-50 border-2 border-red-200 rounded-xl p-6 mb-8">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-gradient-to-br from-red-400 to-red-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-lg font-bold text-red-900 mb-2">⚠️ Advertencia Importante</h3>
                    <p class="text-red-800 mb-2">
                        Estás a punto de eliminar permanentemente esta cuenta de cobro. 
                        Esta acción <strong>no se puede deshacer</strong> y se perderán todos los datos asociados.
                    </p>
                    <ul class="text-red-700 text-sm space-y-1">
                        <li>• Se eliminará toda la información de la cuenta</li>
                        <li>• Se perderán los documentos adjuntos</li>
                        <li>• No será posible recuperar los datos</li>
                        <li>• Se afectarán los reportes financieros</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Account Details -->
        <div class="glass-card p-6 mb-8">
            <h3 class="text-xl font-bold text-gray-900 mb-6">
                <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                Detalles de la Cuenta de Cobro
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-600 font-medium">ID:</span>
                        <span class="text-gray-900 font-bold">#{{ $cuenta->id }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-600 font-medium">Fecha de Emisión:</span>
                        <span class="text-gray-900 font-bold">{{ \Carbon\Carbon::parse($cuenta->fecha_emision)->format('d/m/Y') }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-600 font-medium">Estado:</span>
                        <span class="px-3 py-1 rounded-full text-sm font-bold
                            @if($cuenta->estado === 'pagado') bg-green-100 text-green-800
                            @elseif($cuenta->estado === 'pendiente') bg-yellow-100 text-yellow-800
                            @elseif($cuenta->estado === 'aprobado') bg-blue-100 text-blue-800
                            @elseif($cuenta->estado === 'rechazado') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($cuenta->estado) }}
                        </span>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-600 font-medium block mb-1">Proyecto/Servicio:</span>
                        <span class="text-gray-900 font-bold">{{ $cuenta->proyecto_servicio }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-600 font-medium">Valor:</span>
                        <span class="text-gray-900 font-bold text-xl">
                            ${{ number_format($cuenta->valor, 2, ',', '.') }}
                        </span>
                    </div>
                      <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-600 font-medium">{{ $cuenta->user->role ? ucfirst($cuenta->user->role->name) : 'Usuario' }}:</span>
                        <span class="text-gray-900 font-bold">{{ $cuenta->user->name ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Confirmation Form -->
        <div class="glass-card p-8">
            <h3 class="text-xl font-bold text-gray-900 mb-6">Confirmar Eliminación</h3>
            
            <form action="{{ route('cuentas-cobro.destroy', $cuenta->id) }}" method="POST" id="deleteForm">
                @csrf
                @method('DELETE')
                
                <!-- Confirmation Checkbox -->
                <div class="mb-6">
                    <label class="flex items-start space-x-3 cursor-pointer">
                        <input type="checkbox" id="confirmDelete" class="mt-1 w-5 h-5 text-red-600 border-2 border-gray-300 rounded focus:ring-red-500" required>
                        <span class="text-gray-700">
                            <strong>Confirmo que entiendo las consecuencias</strong> y deseo eliminar permanentemente esta cuenta de cobro. 
                            Acepto que esta acción no se puede deshacer.
                        </span>
                    </label>
                </div>

                <!-- Security Input -->
                <div class="mb-6">
                    <label for="deleteConfirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                        Para confirmar, escribe <strong class="text-red-600">ELIMINAR</strong> en el campo de abajo:
                    </label>
                    <input 
                        type="text" 
                        id="deleteConfirmation" 
                        name="delete_confirmation"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent" 
                        placeholder="Escribe ELIMINAR aquí"
                        required
                    >
                    <div class="text-red-500 text-sm mt-1 hidden" id="confirmationError">
                        Debes escribir exactamente "ELIMINAR" para continuar
                    </div>
                </div>                <!-- Action Buttons mejorados -->
                <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center space-y-4 lg:space-y-0 pt-6 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                        @if(auth()->user()->hasRole('contratista'))
                            <a href="{{ route('contratista.dashboard') }}" 
                               class="inline-flex items-center justify-center px-4 sm:px-6 py-3 border-2 border-purple-300 text-purple-700 bg-purple-50 rounded-xl hover:bg-purple-100 hover:border-purple-400 transition-all duration-300 font-medium text-sm sm:text-base">
                                <i class="fas fa-home mr-2"></i>
                                <span class="hidden sm:inline">Dashboard</span>
                                <span class="sm:hidden">Inicio</span>
                            </a>
                        @endif
                        <a href="{{ route('cuentas-cobro.mostrar') }}" 
                           class="inline-flex items-center justify-center px-4 sm:px-6 py-3 border-2 border-gray-300 text-gray-700 bg-white rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all duration-300 font-medium text-sm sm:text-base">
                            <i class="fas fa-arrow-left mr-2"></i>
                            <span class="hidden sm:inline">Cancelar y Volver</span>
                            <span class="sm:hidden">Cancelar</span>
                        </a>
                    </div>
                    
                    <div class="flex justify-center sm:justify-end">
                        <button 
                            type="submit" 
                            id="deleteBtn"
                            class="inline-flex items-center justify-center px-6 sm:px-8 py-3 bg-gradient-to-r from-red-500 to-pink-600 text-white rounded-xl hover:from-red-600 hover:to-pink-700 focus:ring-4 focus:ring-red-300 transition-all duration-300 font-bold disabled:opacity-50 disabled:cursor-not-allowed text-sm sm:text-base"
                            disabled>
                            <i class="fas fa-trash-alt mr-2"></i>
                            <span class="hidden sm:inline">ELIMINAR CUENTA DE COBRO</span>
                            <span class="sm:hidden">ELIMINAR</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Additional Safety Notice -->
        <div class="mt-8 text-center">
            <div class="inline-flex items-center px-4 py-2 bg-blue-50 border border-blue-200 rounded-full text-blue-800 text-sm">
                <i class="fas fa-info-circle mr-2"></i>
                ¿Necesitas ayuda? Contacta al administrador del sistema
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const confirmCheckbox = document.getElementById('confirmDelete');
    const deleteConfirmation = document.getElementById('deleteConfirmation');
    const deleteBtn = document.getElementById('deleteBtn');
    const confirmationError = document.getElementById('confirmationError');
    const deleteForm = document.getElementById('deleteForm');

    function checkFormValidity() {
        const isChecked = confirmCheckbox.checked;
        const isTextCorrect = deleteConfirmation.value.trim().toUpperCase() === 'ELIMINAR';
        
        deleteBtn.disabled = !(isChecked && isTextCorrect);
        
        if (deleteConfirmation.value && !isTextCorrect) {
            confirmationError.classList.remove('hidden');
        } else {
            confirmationError.classList.add('hidden');
        }
    }

    confirmCheckbox.addEventListener('change', checkFormValidity);
    deleteConfirmation.addEventListener('input', checkFormValidity);

    // Form submission with additional confirmation
    deleteForm.addEventListener('submit', function(e) {
        if (!confirm('⚠️ ÚLTIMA ADVERTENCIA: ¿Estás absolutamente seguro de que quieres eliminar esta cuenta de cobro? Esta acción NO se puede deshacer.')) {
            e.preventDefault();
            return false;
        }
        
        deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Eliminando...';
        deleteBtn.disabled = true;
    });

    // Add countdown effect to delete button when enabled
    deleteBtn.addEventListener('mouseenter', function() {
        if (!this.disabled) {
            this.classList.add('animate-pulse');
        }
    });

    deleteBtn.addEventListener('mouseleave', function() {
        this.classList.remove('animate-pulse');
    });
});
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

.glass-card {
    animation: fadeInUp 0.6s ease-out;
}

.glass-card:nth-child(2) { animation-delay: 0.2s; }
.glass-card:nth-child(3) { animation-delay: 0.4s; }
</style>
@endsection