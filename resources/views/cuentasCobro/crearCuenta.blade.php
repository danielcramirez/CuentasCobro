@extends('layouts.app')

@section('title', 'Crear Cuenta de Cobro - CuentasCobro')

@section('content')
<!-- Contenedor principal con padding superior para el navbar fijo -->
<div class="pt-24 pb-8 px-4 sm:px-6 lg:px-8 min-h-screen">
    <!-- Header de la página -->
    <div class="max-w-4xl mx-auto mb-8">
        <div class="glass-card p-6 slide-up">
            <div class="flex items-center space-x-4">
                <div class="gradient-primary w-16 h-16 rounded-2xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-file-plus text-white text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 font-poppins">Crear Cuenta de Cobro</h1>
                    <p class="text-gray-600">Complete el formulario para generar una nueva solicitud de pago</p>
                </div>
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
                                class="inline-flex items-center justify-center px-6 py-3 border-2 border-blue-300 text-blue-700 bg-blue-50 rounded-xl hover:bg-blue-100 hover:border-blue-400 transition-all duration-300 font-medium">
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
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Variables
    const form = document.getElementById('cuenta-form');
    const progressBar = document.getElementById('progress-bar');
    const progressText = document.getElementById('progress-text');
    const valorInput = document.getElementById('valor');
    const descuentosInput = document.getElementById('descuentos');
    const fileInput = document.getElementById('documentos');
    const uploadZone = document.getElementById('upload-zone');
    const fileList = document.getElementById('file-list');
    const selectedFiles = document.getElementById('selected-files');
    
    // Actualizar progreso del formulario
    function updateProgress() {
        const formData = new FormData(form);
        const totalFields = 4; // Campos requeridos principales
        let filledFields = 0;
        
        if (formData.get('fecha_emision')) filledFields++;
        if (formData.get('proyecto_servicio')) filledFields++;
        if (formData.get('valor') && parseFloat(formData.get('valor')) > 0) filledFields++;
        if (fileInput.files.length > 0) filledFields++;
        
        const percentage = Math.round((filledFields / totalFields) * 100);
        progressBar.style.width = percentage + '%';
        progressText.textContent = percentage + '% completado';
    }
    
    // Actualizar resumen financiero
    function updateFinancialSummary() {
        const valor = parseFloat(valorInput.value) || 0;
        const descuentos = parseFloat(descuentosInput.value) || 0;
        const total = valor - descuentos;
        
        document.getElementById('resumen-base').textContent = '$' + valor.toLocaleString('es-CO', {minimumFractionDigits: 2});
        document.getElementById('resumen-descuentos').textContent = '$' + descuentos.toLocaleString('es-CO', {minimumFractionDigits: 2});
        document.getElementById('resumen-total').textContent = '$' + total.toLocaleString('es-CO', {minimumFractionDigits: 2});
    }
    
    // Gestión de archivos
    function handleFiles(files) {
        selectedFiles.innerHTML = '';
        fileList.classList.remove('hidden');
        
        Array.from(files).forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'file-item flex items-center justify-between p-3 bg-white rounded-lg border border-gray-200';
            fileItem.innerHTML = `
                <div class="flex items-center space-x-3">
                    <i class="fas fa-file text-blue-500"></i>
                    <div>
                        <p class="text-sm font-medium text-gray-800">${file.name}</p>
                        <p class="text-xs text-gray-500">${(file.size / 1024 / 1024).toFixed(2)} MB</p>
                    </div>
                </div>
                <button type="button" onclick="removeFile(${index})" class="text-red-500 hover:text-red-700 transition-colors duration-200">
                    <i class="fas fa-times"></i>
                </button>
            `;
            selectedFiles.appendChild(fileItem);
        });
    }
    
    // Event listeners
    form.addEventListener('input', updateProgress);
    valorInput.addEventListener('input', updateFinancialSummary);
    descuentosInput.addEventListener('input', updateFinancialSummary);
    
    fileInput.addEventListener('change', function() {
        handleFiles(this.files);
        updateProgress();
    });
    
    // Drag and drop
    uploadZone.addEventListener('click', () => fileInput.click());
    
    uploadZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('upload-dragover');
    });
    
    uploadZone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.classList.remove('upload-dragover');
    });
    
    uploadZone.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('upload-dragover');
        const files = e.dataTransfer.files;
        fileInput.files = files;
        handleFiles(files);
        updateProgress();
    });
    
    // Confirmación de envío
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        document.getElementById('confirmation-modal').classList.remove('hidden');
    });
    
    // Inicializar
    updateFinancialSummary();
    updateProgress();
});

// Función para remover archivo
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
        handleFiles(fileInput.files);
    }
}

// Funciones del modal
function closeModal() {
    document.getElementById('confirmation-modal').classList.add('hidden');
}

function confirmSubmit() {
    document.getElementById('cuenta-form').submit();
}
</script>
@endpush