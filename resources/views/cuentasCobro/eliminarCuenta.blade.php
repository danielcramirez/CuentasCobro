@extends('layouts.app')

@section('title', 'Eliminar Cuenta de Cobro - CuentasCobro')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-red-50 via-orange-50 to-pink-50 py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-red-500 to-pink-600 rounded-full mb-4 shadow-lg animate-pulse">
                <i class="fas fa-trash-alt text-white text-3xl"></i>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Eliminar Cuenta de Cobro</h1>
            <p class="text-xl text-gray-600">Esta acción no se puede deshacer</p>
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
                        <span class="text-gray-600 font-medium">Contratista:</span>
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
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                    <button 
                        type="submit" 
                        id="deleteBtn"
                        class="flex-1 bg-gradient-to-r from-red-500 to-pink-600 text-white px-6 py-3 rounded-xl hover:from-red-600 hover:to-pink-700 focus:ring-4 focus:ring-red-300 transition-all duration-300 font-bold disabled:opacity-50 disabled:cursor-not-allowed"
                        disabled
                    >
                        <i class="fas fa-trash-alt mr-2"></i>
                        ELIMINAR CUENTA DE COBRO
                    </button>
                    
                    <a 
                        href="{{ route('cuentas-cobro.index') }}" 
                        class="flex-1 bg-gray-500 text-white px-6 py-3 rounded-xl hover:bg-gray-600 focus:ring-4 focus:ring-gray-300 transition-all duration-300 font-semibold text-center"
                    >
                        <i class="fas fa-arrow-left mr-2"></i>
                        Cancelar y Volver
                    </a>
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