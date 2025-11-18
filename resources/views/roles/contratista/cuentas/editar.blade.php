@extends('layouts.app')
@section('title', 'Editar Cuenta de Cobro - Contratista')

@section('content')
<!-- Contenedor principal con padding superior para el navbar fijo -->
<div class="pt-24 pb-8 px-4 sm:px-6 lg:px-8 min-h-screen">
    <!-- Breadcrumb de navegación -->
    <div class="max-w-4xl mx-auto mb-4">
        <nav class="flex items-center space-x-2 text-sm text-gray-500" aria-label="Breadcrumb">
            <a href="{{ route('dashboard') }}" class="hover:text-gray-700 transition-colors flex items-center">
                <i class="fas fa-home mr-1"></i>
                Inicio
            </a>
            <i class="fas fa-chevron-right text-gray-300"></i>
            <a href="{{ route('contratista.dashboard') }}" class="hover:text-gray-700 transition-colors">
                Dashboard Contratista
            </a>
            <i class="fas fa-chevron-right text-gray-300"></i>
            <a href="{{ route('contratista.cuentas.index') }}" class="hover:text-gray-700 transition-colors">
                Mis Cuentas
            </a>
            <i class="fas fa-chevron-right text-gray-300"></i>
            <a href="{{ route('contratista.cuentas.ver', $cuenta->id) }}" class="hover:text-gray-700 transition-colors">
                Cuenta #{{ $cuenta->id }}
            </a>
            <i class="fas fa-chevron-right text-gray-300"></i>
            <span class="text-gray-700 font-medium">Editar</span>
        </nav>
    </div>
    
    <!-- Header de la página -->
    <div class="max-w-4xl mx-auto mb-8">
        <div class="glass-card p-6 slide-up">
            <div class="flex items-center space-x-4">
                <div class="gradient-primary w-16 h-16 rounded-2xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-edit text-white text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 font-poppins">Editar Cuenta de Cobro #{{ $cuenta->id }}</h1>
                    <p class="text-gray-600">Modifica los datos de tu cuenta de cobro</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Mensajes de error y éxito -->
    @if(session('success'))
        <div class="max-w-4xl mx-auto mb-6">
            <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="max-w-4xl mx-auto mb-6">
            <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Hay errores en el formulario:</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Información del estado actual -->
    <div class="max-w-4xl mx-auto mb-6">
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-yellow-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">
                        Esta cuenta está en estado: <strong>{{ ucfirst($cuenta->estado) }}</strong>
                    </h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        @if($cuenta->estado === 'borrador')
                            <p>Puedes editar todos los campos. La cuenta se mantendrá como borrador hasta que decidas enviarla.</p>
                        @elseif($cuenta->estado === 'rechazado')
                            <p>Esta cuenta fue rechazada. Realiza las correcciones necesarias y podrás enviarla nuevamente.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario de edición -->
    <div class="max-w-4xl mx-auto">
        <form action="{{ route('contratista.cuentas.update', $cuenta->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Panel principal del formulario -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Información básica -->
                    <div class="glass-card p-6">
                        <div class="flex items-center mb-6">
                            <div class="bg-blue-100 p-3 rounded-xl mr-4">
                                <i class="fas fa-info-circle text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-semibold text-gray-800">Información Básica</h2>
                                <p class="text-gray-600 text-sm">Datos principales de la cuenta de cobro</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Fecha de emisión -->
                            <div>
                                <label for="fecha_emision" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-calendar-alt mr-2 text-blue-500"></i>
                                    Fecha de Emisión *
                                </label>
                                <input type="date" 
                                       id="fecha_emision" 
                                       name="fecha_emision" 
                                       value="{{ old('fecha_emision', $cuenta->fecha_emision->format('Y-m-d')) }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('fecha_emision') border-red-300 @enderror"
                                       required>
                                @error('fecha_emision')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Valor -->
                            <div>
                                <label for="valor" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-dollar-sign mr-2 text-green-500"></i>
                                    Valor Total *
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">$</span>
                                    <input type="number" 
                                           id="valor" 
                                           name="valor" 
                                           value="{{ old('valor', $cuenta->valor) }}"
                                           placeholder="0"
                                           min="0"
                                           step="0.01"
                                           class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('valor') border-red-300 @enderror"
                                           required>
                                </div>
                                @error('valor')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Descripción del proyecto/servicio -->
                    <div class="glass-card p-6">
                        <div class="flex items-center mb-6">
                            <div class="bg-purple-100 p-3 rounded-xl mr-4">
                                <i class="fas fa-project-diagram text-purple-600 text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-semibold text-gray-800">Descripción del Servicio</h2>
                                <p class="text-gray-600 text-sm">Detalles del trabajo realizado</p>
                            </div>
                        </div>

                        <!-- Proyecto/Servicio -->
                        <div class="mb-6">
                            <label for="proyecto_servicio" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-briefcase mr-2 text-purple-500"></i>
                                Proyecto o Servicio *
                            </label>
                            <input type="text" 
                                   id="proyecto_servicio" 
                                   name="proyecto_servicio" 
                                   value="{{ old('proyecto_servicio', $cuenta->proyecto_servicio) }}"
                                   placeholder="Ej: Desarrollo de aplicación web, Consultoría IT, etc."
                                   maxlength="255"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('proyecto_servicio') border-red-300 @enderror"
                                   required>
                            @error('proyecto_servicio')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Descripción detallada -->
                        <div>
                            <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-align-left mr-2 text-purple-500"></i>
                                Descripción Detallada
                            </label>
                            <textarea id="descripcion" 
                                      name="descripcion" 
                                      rows="4"
                                      placeholder="Descripción detallada de los servicios prestados, actividades realizadas, entregables, etc."
                                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-none @error('descripcion') border-red-300 @enderror">{{ old('descripcion', $cuenta->descripcion) }}</textarea>
                            @error('descripcion')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-sm text-gray-500">Incluya todos los detalles relevantes del trabajo realizado</p>
                        </div>
                    </div>

                    <!-- Archivos adjuntos -->
                    <div class="glass-card p-6">
                        <div class="flex items-center mb-6">
                            <div class="bg-green-100 p-3 rounded-xl mr-4">
                                <i class="fas fa-paperclip text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-semibold text-gray-800">Documentos de Soporte</h2>
                                <p class="text-gray-600 text-sm">Actualizar archivos adjuntos</p>
                            </div>
                        </div>

                        <!-- Archivo actual -->
                        @if($cuenta->ruta_archivo)
                            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <i class="fas fa-file-pdf text-red-500 mr-3 text-xl"></i>
                                        <div>
                                            <p class="font-semibold text-gray-800">Archivo actual</p>
                                            <p class="text-sm text-gray-600">{{ basename($cuenta->ruta_archivo) }}</p>
                                        </div>
                                    </div>
                                    <a href="{{ route('cuentas-cobro.descargar', $cuenta->id) }}" 
                                       target="_blank"
                                       class="bg-green-600 text-white px-3 py-2 rounded-lg hover:bg-green-700 transition-colors text-sm flex items-center">
                                        <i class="fas fa-download mr-2"></i>
                                        Ver actual
                                    </a>
                                </div>
                            </div>
                        @endif

                        <!-- Subir nuevos archivos -->
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 hover:border-blue-400 transition-colors">
                            <div class="text-center">
                                <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-cloud-upload-alt text-gray-400 text-2xl"></i>
                                </div>
                                <label for="documentos" class="cursor-pointer">
                                    <span class="text-lg font-medium text-gray-700">
                                        @if($cuenta->ruta_archivo)
                                            Reemplazar archivo existente
                                        @else
                                            Seleccionar archivos
                                        @endif
                                    </span>
                                    <p class="text-gray-500 mt-1">o arrastrar y soltar aquí</p>
                                </label>
                                <input type="file" 
                                       id="documentos" 
                                       name="documentos[]" 
                                       multiple
                                       accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                       class="hidden"
                                       onchange="displaySelectedFiles(this)">
                                <p class="text-xs text-gray-400 mt-3">
                                    Formatos permitidos: PDF, DOC, DOCX, JPG, PNG (Máximo 10MB por archivo)
                                </p>
                            </div>
                            
                            <!-- Lista de archivos seleccionados -->
                            <div id="selected-files" class="mt-4 hidden">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Archivos seleccionados:</h4>
                                <ul id="files-list" class="space-y-2"></ul>
                            </div>
                        </div>
                        
                        @error('documentos')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @error('documentos.*')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Panel lateral -->
                <div class="space-y-6">
                    <!-- Resumen -->
                    <div class="glass-card p-6 sticky top-24">
                        <div class="flex items-center mb-4">
                            <div class="bg-yellow-100 p-2 rounded-lg mr-3">
                                <i class="fas fa-clipboard-list text-yellow-600"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800">Resumen</h3>
                        </div>

                        <div class="space-y-4">
                            <!-- Usuario -->
                            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-500">Contratista</p>
                                </div>
                            </div>

                            <!-- Estado actual -->
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm text-gray-600">Estado:</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($cuenta->estado === 'borrador') 
                                        bg-gray-100 text-gray-800
                                    @elseif($cuenta->estado === 'rechazado')
                                        bg-red-100 text-red-800
                                    @endif
                                ">
                                    <i class="fas fa-{{ $cuenta->estado === 'borrador' ? 'edit' : 'times-circle' }} mr-1"></i>
                                    {{ ucfirst($cuenta->estado) }}
                                </span>
                            </div>

                            <!-- Fecha -->
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm text-gray-600">Fecha:</span>
                                <span class="text-sm font-medium text-gray-800" id="fecha-display">
                                    {{ $cuenta->fecha_emision->format('d/m/Y') }}
                                </span>
                            </div>

                            <!-- Valor -->
                            <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg border border-green-200">
                                <span class="text-sm text-green-700 font-medium">Valor total:</span>
                                <span class="text-lg font-bold text-green-800" id="valor-display">
                                    ${{ number_format($cuenta->valor, 0, ',', '.') }}
                                </span>
                            </div>

                            <!-- ID de la cuenta -->
                            <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg border border-blue-200">
                                <span class="text-sm text-blue-700 font-medium">ID Cuenta:</span>
                                <span class="text-sm font-bold text-blue-800">#{{ $cuenta->id }}</span>
                            </div>
                        </div>

                        <!-- Información adicional -->
                        <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <div class="flex items-start">
                                <i class="fas fa-info-circle text-blue-500 mt-0.5 mr-2"></i>
                                <div class="text-sm text-blue-700">
                                    <p class="font-medium mb-1">Importante:</p>
                                    <ul class="list-disc list-inside space-y-1 text-xs">
                                        <li>Los cambios se guardarán automáticamente</li>
                                        <li>La cuenta mantendrá su estado actual</li>
                                        <li>Revisa bien antes de guardar</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="max-w-4xl mx-auto">
                <div class="glass-card p-6">
                    <div class="flex flex-col sm:flex-row items-center justify-between space-y-4 sm:space-y-0 sm:space-x-4">
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-shield-alt text-green-500 mr-2"></i>
                            Los cambios se aplicarán inmediatamente al guardar.
                        </div>
                        
                        <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3">
                            <a href="{{ route('contratista.cuentas.ver', $cuenta->id) }}" 
                               class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors font-medium text-center">
                                <i class="fas fa-times mr-2"></i>
                                Cancelar
                            </a>
                            
                            <button type="submit" 
                                    class="gradient-primary text-white px-8 py-3 rounded-xl hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300 font-medium">
                                <i class="fas fa-save mr-2"></i>
                                Guardar Cambios
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 4px 15px 0 rgba(31, 38, 135, 0.07);
        border-radius: 16px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .slide-up {
        animation: slideUp 0.6s ease-out;
    }
    
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .file-item {
        display: flex;
        align-items: center;
        justify-content: between;
        padding: 8px 12px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 14px;
    }

    .file-item .file-name {
        flex: 1;
        color: #374151;
        margin-right: 8px;
    }

    .file-item .file-size {
        color: #6b7280;
        font-size: 12px;
        margin-right: 8px;
    }

    .file-item .remove-file {
        color: #ef4444;
        cursor: pointer;
        padding: 4px;
        border-radius: 4px;
        transition: background-color 0.2s;
    }

    .file-item .remove-file:hover {
        background-color: #fee2e2;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Actualizar fecha en tiempo real
    const fechaInput = document.getElementById('fecha_emision');
    const fechaDisplay = document.getElementById('fecha-display');
    
    fechaInput.addEventListener('change', function() {
        const fecha = new Date(this.value);
        fechaDisplay.textContent = fecha.toLocaleDateString('es-ES');
    });

    // Actualizar valor en tiempo real
    const valorInput = document.getElementById('valor');
    const valorDisplay = document.getElementById('valor-display');
    
    valorInput.addEventListener('input', function() {
        const valor = parseInt(this.value) || 0;
        valorDisplay.textContent = '$' + valor.toLocaleString('es-CO');
    });

    // Drag and drop para archivos
    const dropZone = document.querySelector('.border-dashed');
    const fileInput = document.getElementById('documentos');

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
        document.body.addEventListener(eventName, preventDefaults, false);
    });

    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, unhighlight, false);
    });

    dropZone.addEventListener('drop', handleDrop, false);

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    function highlight(e) {
        dropZone.classList.add('border-blue-400', 'bg-blue-50');
    }

    function unhighlight(e) {
        dropZone.classList.remove('border-blue-400', 'bg-blue-50');
    }

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        
        fileInput.files = files;
        displaySelectedFiles(fileInput);
    }
});

// Funciones para manejo de archivos (mismas que en crear)
function displaySelectedFiles(input) {
    const selectedFilesDiv = document.getElementById('selected-files');
    const filesList = document.getElementById('files-list');
    
    if (input.files.length > 0) {
        selectedFilesDiv.classList.remove('hidden');
        filesList.innerHTML = '';
        
        Array.from(input.files).forEach((file, index) => {
            const fileItem = document.createElement('li');
            fileItem.className = 'file-item';
            
            const fileName = document.createElement('span');
            fileName.className = 'file-name';
            fileName.textContent = file.name;
            
            const fileSize = document.createElement('span');
            fileSize.className = 'file-size';
            fileSize.textContent = formatFileSize(file.size);
            
            const removeButton = document.createElement('span');
            removeButton.className = 'remove-file';
            removeButton.innerHTML = '<i class="fas fa-times"></i>';
            removeButton.onclick = () => removeFile(index);
            
            fileItem.appendChild(fileName);
            fileItem.appendChild(fileSize);
            fileItem.appendChild(removeButton);
            
            filesList.appendChild(fileItem);
        });
    } else {
        selectedFilesDiv.classList.add('hidden');
    }
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function removeFile(index) {
    const fileInput = document.getElementById('documentos');
    const dt = new DataTransfer();
    
    Array.from(fileInput.files).forEach((file, i) => {
        if (i !== index) {
            dt.items.add(file);
        }
    });
    
    fileInput.files = dt.files;
    displaySelectedFiles(fileInput);
}
</script>
@endpush
@endsection
