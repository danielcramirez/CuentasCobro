@extends('layouts.dashboard')

@section('title', 'Editar Cuenta de Cobro - CuentasCobro')

@section('content')
<div class="pt-32 pb-8 px-4 sm:px-6 lg:px-8 min-h-screen">
    <div class="max-w-4xl mx-auto">
        <x-breadcrumbs 
            :items="[
                ['name' => 'Inicio', 'route' => auth()->user()->hasRole('contratista') ? 'contratista.dashboard' : 'dashboard'],
                ['name' => 'Cuentas de Cobro', 'route' => 'cuentas-cobro.mostrar'],
                ['name' => 'Editar Cuenta #' . $cuenta->id]
            ]" 
        />

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
                            @if($cuenta->estado === 'borrador') bg-gray-400
                            @elseif($cuenta->estado === 'pendiente_supervisor') bg-yellow-400
                            @elseif($cuenta->estado === 'pendiente_contratacion') bg-orange-400
                            @elseif($cuenta->estado === 'pendiente_tesoreria') bg-purple-400
                            @elseif($cuenta->estado === 'pendiente_ordenador') bg-indigo-400
                            @elseif($cuenta->estado === 'aprobada') bg-green-400
                            @elseif($cuenta->estado === 'rechazada') bg-red-400
                            @elseif($cuenta->estado === 'pagada') bg-blue-400
                            @else bg-gray-400
                            @endif
                        "></div>
                        <span class="text-xs sm:text-sm font-medium text-gray-700">
                            Estado: <span class="text-blue-600">{{ str_replace('_', ' ', ucfirst($cuenta->estado)) }}</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Form -->
        <div class="glass-card p-8">
            <form action="{{ route('cuentas-cobro.update', $cuenta->id) }}" method="POST" id="editForm" class="space-y-6" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Current Status Indicator -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-4 mb-6">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                        <span class="text-blue-800 font-medium">Estado actual: 
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-sm ml-2">
                                {{ str_replace('_', ' ', ucfirst($cuenta->estado)) }}
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
                            disabled
                        >
                            <option value="borrador" {{ $cuenta->estado === 'borrador' ? 'selected' : '' }}>
                                📝 Borrador
                            </option>
                            <option value="pendiente_supervisor" {{ $cuenta->estado === 'pendiente_supervisor' ? 'selected' : '' }}>
                                ⏳ Pendiente Supervisor
                            </option>
                            <option value="pendiente_contratacion" {{ $cuenta->estado === 'pendiente_contratacion' ? 'selected' : '' }}>
                                📋 Pendiente Contratación
                            </option>
                            <option value="pendiente_tesoreria" {{ $cuenta->estado === 'pendiente_tesoreria' ? 'selected' : '' }}>
                                💼 Pendiente Tesorería
                            </option>
                            <option value="pendiente_ordenador" {{ $cuenta->estado === 'pendiente_ordenador' ? 'selected' : '' }}>
                                👔 Pendiente Ordenador
                            </option>
                            <option value="aprobada" {{ $cuenta->estado === 'aprobada' ? 'selected' : '' }}>
                                ✅ Aprobada
                            </option>
                            <option value="rechazada" {{ $cuenta->estado === 'rechazada' ? 'selected' : '' }}>
                                ❌ Rechazada
                            </option>
                            <option value="pagada" {{ $cuenta->estado === 'pagada' ? 'selected' : '' }}>
                                💰 Pagada
                            </option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">El estado no puede ser modificado directamente. Cambia mediante el flujo de aprobación.</p>
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

                </div>

                <!-- Archivo Adjunto Actual -->
                @if($cuenta->archivo_adjunto)
                <div class="space-y-2" id="archivoActualContainer">
                    <label class="block text-sm font-semibold text-gray-700">
                        <i class="fas fa-file-check mr-2 text-green-500"></i>
                        Archivo Actual
                    </label>
                    <div class="bg-green-50 border-2 border-green-300 rounded-xl p-4">
                        <div class="flex items-center justify-between flex-wrap gap-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-file-pdf text-white text-xl"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800">{{ basename($cuenta->archivo_adjunto) }}</p>
                                    <p class="text-sm text-gray-600">
                                        <i class="fas fa-clock mr-1"></i>
                                        Subido el {{ $cuenta->created_at->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <a href="{{ Storage::url($cuenta->archivo_adjunto) }}" 
                                   target="_blank"
                                   class="inline-flex items-center px-3 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors text-sm">
                                    <i class="fas fa-eye mr-1"></i>
                                    Ver
                                </a>
                                <button type="button"
                                        onclick="eliminarArchivo({{ $cuenta->id }})"
                                        class="inline-flex items-center px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors text-sm">
                                    <i class="fas fa-trash mr-1"></i>
                                    Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Subir Archivo -->
                <div class="col-span-1 lg:col-span-2 space-y-2">
                    <label for="archivo_adjunto" class="block text-sm font-semibold text-gray-700">
                        <i class="fas fa-upload mr-2 text-indigo-500"></i>
                        @if($cuenta->archivo_adjunto)
                            <span class="text-orange-600">Reemplazar Archivo (Opcional)</span>
                        @else
                            <span class="text-red-600">Subir Archivo (Requerido)</span>
                        @endif
                    </label>
                    @if($cuenta->archivo_adjunto)
                        <div class="bg-blue-50 border-2 border-blue-300 rounded-lg p-3 mb-2">
                            <p class="text-sm text-blue-800 font-medium">
                                <i class="fas fa-info-circle mr-2"></i>
                                <strong>Nota:</strong> Ya tienes un archivo cargado. Si subes uno nuevo, se reemplazará automáticamente.
                            </p>
                        </div>
                    @endif
                    <div class="relative">
                        <input 
                            type="file" 
                            class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 hover:border-blue-300 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" 
                            id="archivo_adjunto" 
                            name="archivo_adjunto"
                            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                            {{ !$cuenta->archivo_adjunto ? 'required' : '' }}
                        >
                    </div>
                    <p class="text-xs text-gray-500">
                        Formatos permitidos: PDF, DOC, DOCX, JPG, PNG (Máx. 10MB)
                        @if($cuenta->archivo_adjunto)
                            <br><span class="text-blue-600 font-bold">✓ Si subes un nuevo archivo, reemplazará el actual</span>
                        @endif
                    </p>
                    <div class="text-red-500 text-sm hidden" id="archivo_error"></div>
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
// Función para eliminar archivo con AJAX
function eliminarArchivo(cuentaId) {
    if (!confirm('¿Estás seguro de que deseas eliminar el archivo actual?\n\nEsta acción no se puede deshacer.')) {
        return;
    }
    
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch(`/cuentas-cobro/${cuentaId}/eliminar-archivo`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': token,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Eliminar el contenedor del archivo
            const archivoContainer = document.getElementById('archivoActualContainer');
            if (archivoContainer) {
                archivoContainer.remove();
            }
            
            // Hacer el campo de archivo requerido
            const archivoInput = document.getElementById('archivo_adjunto');
            if (archivoInput) {
                archivoInput.required = true;
            }
            
            // Actualizar el label
            const labelSpan = document.querySelector('label[for="archivo_adjunto"] span');
            if (labelSpan) {
                labelSpan.textContent = 'Subir Archivo (Requerido)';
                labelSpan.classList.remove('text-orange-600');
                labelSpan.classList.add('text-red-600');
            }
            
            // Mostrar mensaje de éxito
            alert('✓ ' + data.message);
        } else {
            alert('❌ Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ Error al eliminar el archivo. Por favor, intenta nuevamente.');
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editForm');
    const submitBtn = document.getElementById('submitBtn');
    const valorInput = document.getElementById('valor');
    const valorFormato = document.getElementById('valor_formato');
    const archivoInput = document.getElementById('archivo_adjunto');

    // Validar tamaño de archivo
    if (archivoInput) {
        archivoInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const maxSize = 10 * 1024 * 1024; // 10MB en bytes
                if (file.size > maxSize) {
                    showError('archivo_error', 'El archivo no debe superar los 10MB');
                    this.value = '';
                    return;
                }
                
                // Validar extensión
                const allowedExtensions = /(\.pdf|\.doc|\.docx|\.jpg|\.jpeg|\.png)$/i;
                if (!allowedExtensions.exec(file.name)) {
                    showError('archivo_error', 'Formato de archivo no permitido');
                    this.value = '';
                    return;
                }
                
                // Mostrar mensaje de confirmación
                const archivoError = document.getElementById('archivo_error');
                archivoError.textContent = `✓ Archivo seleccionado: ${file.name}`;
                archivoError.classList.remove('hidden', 'text-red-500');
                archivoError.classList.add('text-green-600');
            }
        });
    }

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