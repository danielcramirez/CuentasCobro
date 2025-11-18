@extends('layouts.app')

@section('title', 'Editar Cuenta de Cobro - CuentasCobro')

@section('content')
<div class="min-h-screen pt-24 pb-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        
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
                <span class="text-gray-700 font-medium">Editar #{{ $cuenta->id }}</span>
            </nav>
        </div>

        <!-- Header mejorado -->
        <div class="glass-card p-6 mb-8 slide-up">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="gradient-primary w-12 h-12 sm:w-16 sm:h-16 rounded-2xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-edit text-white text-lg sm:text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-800 font-poppins">Editar Cuenta de Cobro</h1>
                        <p class="text-sm sm:text-base text-gray-600">Cuenta #{{ $cuenta->id }} - {{ $cuenta->proyecto_servicio }}</p>
                    </div>
                </div>
                
                <!-- Estado actual -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-3">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 rounded-full
                            @if($cuenta->estado === 'pendiente') bg-yellow-400
                            @elseif($cuenta->estado === 'aprobado') bg-green-400
                            @elseif($cuenta->estado === 'rechazado') bg-red-400
                            @elseif($cuenta->estado === 'pagado') bg-blue-400
                            @else bg-gray-400
                            @endif
                        "></div>
                        <span class="text-xs sm:text-sm font-medium text-gray-700">
                            Estado: <span class="text-blue-600">{{ ucfirst($cuenta->estado) }}</span>
                        </span>
                    </div>
                </div>
            </div>
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
                    <!-- Esta parte solo debe verse para cualquier otro que no sea contratista -->
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

                <!-- Action Buttons mejorados -->
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
                            <span class="hidden sm:inline">Volver a Cuentas</span>
                            <span class="sm:hidden">Volver</span>
                        </a>
                    </div>
                    
                    <div class="flex justify-center sm:justify-end">
                        <button 
                            type="submit" 
                            class="inline-flex items-center justify-center px-6 sm:px-8 py-3 gradient-primary text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 text-sm sm:text-base"
                            id="submitBtn">
                            <i class="fas fa-save mr-2"></i>
                            <span class="hidden sm:inline">Guardar Cambios</span>
                            <span class="sm:hidden">Guardar</span>
                        </button>
                    </div>
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
                    <span class="text-gray-600">{{ $cuenta->user->role ? ucfirst($cuenta->user->role->name) : 'Usuario' }}:</span>
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

/* Ajuste extra para evitar solapamiento con navbar fijo en pantallas pequeñas */
@media (max-width: 768px) {
    .pt-32 {
        padding-top: 7rem !important;
    }
}
</style>
@endsection