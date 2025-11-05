@extends('layouts.app')

@section('title', 'Editar Cuenta de Cobro - CuentasCobro')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full mb-4 shadow-lg">
                <i class="fas fa-edit text-white text-2xl"></i>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Editar Cuenta de Cobro</h1>
            <p class="text-xl text-gray-600">Modifica los datos de la cuenta de cobro #{{ $cuenta->id }}</p>
        </div>

        <!-- Main Form -->
        <div class="glass-card p-8">
            <form action="{{ route('cuentas-cobro.update', $cuenta->id) }}" method="POST" id="editForm" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Current Status Indicator -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-4 mb-6">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                        <span class="text-blue-800 font-medium">Estado actual: 
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-sm ml-2">
                                {{ ucfirst($cuenta->estado) }}
                            </span>
                        </span>
                    </div>
                </div>

                <!-- Form Fields Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    
                    <!-- Fecha de Emisión -->
                    <div class="space-y-2">
                        <label for="fecha_emision" class="block text-sm font-semibold text-gray-700">
                            <i class="fas fa-calendar-alt mr-2 text-blue-500"></i>
                            Fecha de Emisión
                        </label>
                        <input 
                            type="date" 
                            class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 hover:border-blue-300" 
                            id="fecha_emision" 
                            name="fecha_emision" 
                            value="{{ $cuenta->fecha_emision }}" 
                            required
                        >
                        <div class="text-red-500 text-sm hidden" id="fecha_emision_error"></div>
                    </div>

                    <!-- Estado -->
                    <div class="space-y-2">
                        <label for="estado" class="block text-sm font-semibold text-gray-700">
                            <i class="fas fa-flag mr-2 text-green-500"></i>
                            Estado
                        </label>
                        <select 
                            class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 hover:border-blue-300" 
                            id="estado" 
                            name="estado" 
                            required
                        >
                            <option value="borrador" {{ $cuenta->estado === 'borrador' ? 'selected' : '' }}>
                                📝 Borrador
                            </option>
                            <option value="pendiente" {{ $cuenta->estado === 'pendiente' ? 'selected' : '' }}>
                                ⏳ Pendiente
                            </option>
                            <option value="aprobado" {{ $cuenta->estado === 'aprobado' ? 'selected' : '' }}>
                                ✅ Aprobado
                            </option>
                            <option value="pagado" {{ $cuenta->estado === 'pagado' ? 'selected' : '' }}>
                                💰 Pagado
                            </option>
                            <option value="rechazado" {{ $cuenta->estado === 'rechazado' ? 'selected' : '' }}>
                                ❌ Rechazado
                            </option>
                        </select>
                        <div class="text-red-500 text-sm hidden" id="estado_error"></div>
                    </div>

                </div>

                <!-- Proyecto/Servicio (Full Width) -->
                <div class="space-y-2">
                    <label for="proyecto_servicio" class="block text-sm font-semibold text-gray-700">
                        <i class="fas fa-project-diagram mr-2 text-purple-500"></i>
                        Proyecto/Servicio
                    </label>
                    <input 
                        type="text" 
                        class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 hover:border-blue-300" 
                        id="proyecto_servicio" 
                        name="proyecto_servicio" 
                        value="{{ $cuenta->proyecto_servicio }}" 
                        placeholder="Describe el proyecto o servicio realizado"
                        required
                    >
                    <div class="text-red-500 text-sm hidden" id="proyecto_servicio_error"></div>
                </div>

                <!-- Valor -->
                <div class="space-y-2">
                    <label for="valor" class="block text-sm font-semibold text-gray-700">
                        <i class="fas fa-dollar-sign mr-2 text-green-500"></i>
                        Valor Total
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">$</span>
                        <input 
                            type="number" 
                            class="w-full pl-8 pr-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 hover:border-blue-300" 
                            id="valor" 
                            name="valor" 
                            value="{{ $cuenta->valor }}" 
                            placeholder="0.00"
                            step="0.01"
                            min="0"
                            required
                        >
                    </div>
                    <div class="text-red-500 text-sm hidden" id="valor_error"></div>
                    <div class="text-gray-500 text-sm" id="valor_formato">Formato: 0,000.00</div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                    <button 
                        type="submit" 
                        class="flex-1 bg-gradient-to-r from-blue-500 to-indigo-600 text-white px-6 py-3 rounded-xl hover:from-blue-600 hover:to-indigo-700 focus:ring-4 focus:ring-blue-300 transition-all duration-300 transform hover:scale-105 font-semibold"
                        id="submitBtn"
                    >
                        <i class="fas fa-save mr-2"></i>
                        Guardar Cambios
                    </button>
                    
                    <a 
                        href="{{ route('cuentas-cobro.index') }}" 
                        class="flex-1 bg-gray-500 text-white px-6 py-3 rounded-xl hover:bg-gray-600 focus:ring-4 focus:ring-gray-300 transition-all duration-300 transform hover:scale-105 font-semibold text-center"
                    >
                        <i class="fas fa-times mr-2"></i>
                        Cancelar
                    </button>
                </div>

            </form>
        </div>

        <!-- Change History (if applicable) -->
        <div class="glass-card p-6 mt-8">
            <h3 class="text-xl font-bold text-gray-900 mb-4">
                <i class="fas fa-history mr-2 text-blue-500"></i>
                Información de la Cuenta
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Creada:</span>
                    <span class="font-medium">{{ $cuenta->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Última modificación:</span>
                    <span class="font-medium">{{ $cuenta->updated_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">ID:</span>
                    <span class="font-medium">#{{ $cuenta->id }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Contratista:</span>
                    <span class="font-medium">{{ $cuenta->user->name ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editForm');
    const submitBtn = document.getElementById('submitBtn');
    const valorInput = document.getElementById('valor');
    const valorFormato = document.getElementById('valor_formato');

    // Format currency as user types
    valorInput.addEventListener('input', function() {
        const value = parseFloat(this.value);
        if (!isNaN(value)) {
            valorFormato.textContent = `Formato: ${value.toLocaleString('es-CO', {
                style: 'currency',
                currency: 'COP',
                minimumFractionDigits: 2
            })}`;
            valorFormato.classList.remove('text-gray-500');
            valorFormato.classList.add('text-green-600');
        } else {
            valorFormato.textContent = 'Formato: 0,000.00';
            valorFormato.classList.remove('text-green-600');
            valorFormato.classList.add('text-gray-500');
        }
    });

    // Form validation
    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        // Reset errors
        document.querySelectorAll('[id$="_error"]').forEach(el => {
            el.classList.add('hidden');
            el.textContent = '';
        });

        // Validate fecha_emision
        const fechaEmision = document.getElementById('fecha_emision').value;
        if (!fechaEmision) {
            showError('fecha_emision_error', 'La fecha de emisión es requerida');
            isValid = false;
        }

        // Validate proyecto_servicio
        const proyectoServicio = document.getElementById('proyecto_servicio').value.trim();
        if (!proyectoServicio || proyectoServicio.length < 5) {
            showError('proyecto_servicio_error', 'El proyecto/servicio debe tener al menos 5 caracteres');
            isValid = false;
        }

        // Validate valor
        const valor = parseFloat(document.getElementById('valor').value);
        if (!valor || valor <= 0) {
            showError('valor_error', 'El valor debe ser mayor a 0');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            submitBtn.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i>Corrige los errores';
            submitBtn.classList.remove('from-blue-500', 'to-indigo-600');
            submitBtn.classList.add('from-red-500', 'to-red-600');
            
            setTimeout(() => {
                submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i>Guardar Cambios';
                submitBtn.classList.remove('from-red-500', 'to-red-600');
                submitBtn.classList.add('from-blue-500', 'to-indigo-600');
            }, 3000);
        } else {
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Guardando...';
            submitBtn.disabled = true;
        }
    });

    function showError(elementId, message) {
        const errorElement = document.getElementById(elementId);
        errorElement.textContent = message;
        errorElement.classList.remove('hidden');
    }

    // Status change warning
    const estadoSelect = document.getElementById('estado');
    const originalEstado = '{{ $cuenta->estado }}';
    
    estadoSelect.addEventListener('change', function() {
        if (this.value !== originalEstado) {
            const warning = document.createElement('div');
            warning.className = 'mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-yellow-800 text-sm';
            warning.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i>Cambiar el estado puede afectar el flujo de aprobación.';
            
            // Remove existing warning
            const existingWarning = estadoSelect.parentNode.querySelector('.bg-yellow-50');
            if (existingWarning) {
                existingWarning.remove();
            }
            
            estadoSelect.parentNode.appendChild(warning);
        }
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
</style>
@endsection