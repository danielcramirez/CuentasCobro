@extends('layouts.dashboard')

@section('title', 'Editar Cuenta de Cobro - Contratación')

@section('breadcrumb')
    @include('components.navigation.breadcrumb', [
        'items' => [
            ['label' => 'Dashboard Contratación', 'url' => route('contratacion.dashboard')],
            ['label' => 'Cuentas de Cobro', 'url' => route('contratacion.cuentas-cobro.index')],
            ['label' => 'Cuenta #' . $cuenta->id, 'url' => route('contratacion.cuentas-cobro.show', $cuenta->id)],
            ['label' => 'Editar']
        ]
    ])
@endsection

@section('dashboard-header')
    <div class="glass-card p-6 slide-up">
        <div class="flex items-center space-x-4">
            <div class="gradient-primary w-16 h-16 rounded-2xl flex items-center justify-center">
                <i class="fas fa-edit text-white text-2xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Editar Cuenta de Cobro #{{ $cuenta->id }}</h1>
                <p class="text-gray-600">Modifica los datos de la cuenta de cobro</p>
            </div>
        </div>
    </div>
@endsection

@section('dashboard-content')
    <!-- Información del estado actual -->
    <div class="mb-6">
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-yellow-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">
                        Estado actual: <strong>{{ ucfirst(str_replace('_', ' ', $cuenta->estado)) }}</strong>
                    </h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <p>Puedes editar los datos de esta cuenta. Los cambios no afectarán el estado de aprobación.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario de edición -->
    <form action="{{ route('contratacion.cuentas-cobro.update', $cuenta->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <!-- Panel principal del formulario -->
            <div class="xl:col-span-2 space-y-6">
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
                                   value="{{ old('fecha_emision', $cuenta->fecha_emision ? $cuenta->fecha_emision->format('Y-m-d') : '') }}"
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
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                                <input type="number" 
                                       id="valor" 
                                       name="valor" 
                                       value="{{ old('valor', $cuenta->valor) }}"
                                       step="0.01"
                                       min="0"
                                       class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('valor') border-red-300 @enderror"
                                       placeholder="0.00"
                                       required>
                            </div>
                            @error('valor')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Descripción del proyecto -->
                <div class="glass-card p-6">
                    <div class="flex items-center mb-6">
                        <div class="bg-purple-100 p-3 rounded-xl mr-4">
                            <i class="fas fa-project-diagram text-purple-600 text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800">Proyecto/Servicio</h2>
                            <p class="text-gray-600 text-sm">Descripción detallada del servicio prestado</p>
                        </div>
                    </div>

                    <div>
                        <label for="proyecto_servicio" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-file-alt mr-2 text-purple-500"></i>
                            Descripción del Proyecto/Servicio *
                        </label>
                        <textarea id="proyecto_servicio" 
                                  name="proyecto_servicio" 
                                  rows="6"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors resize-none @error('proyecto_servicio') border-red-300 @enderror"
                                  placeholder="Describe el proyecto, servicio prestado, actividades realizadas, entregables, etc."
                                  required>{{ old('proyecto_servicio', $cuenta->proyecto_servicio) }}</textarea>
                        @error('proyecto_servicio')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-sm text-gray-500">
                            <i class="fas fa-info-circle mr-1"></i>
                            Sé específico y detallado en la descripción
                        </p>
                    </div>
                </div>

                <!-- Observaciones -->
                <div class="glass-card p-6">
                    <div class="flex items-center mb-6">
                        <div class="bg-yellow-100 p-3 rounded-xl mr-4">
                            <i class="fas fa-comment-alt text-yellow-600 text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800">Observaciones</h2>
                            <p class="text-gray-600 text-sm">Notas adicionales o comentarios (opcional)</p>
                        </div>
                    </div>

                    <div>
                        <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-sticky-note mr-2 text-yellow-500"></i>
                            Observaciones
                        </label>
                        <textarea id="observaciones" 
                                  name="observaciones" 
                                  rows="4"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-colors resize-none @error('observaciones') border-red-300 @enderror"
                                  placeholder="Notas adicionales, aclaraciones, comentarios...">{{ old('observaciones', $cuenta->observaciones) }}</textarea>
                        @error('observaciones')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Panel lateral -->
            <div class="space-y-6">
                <!-- Información del contratista -->
                <div class="glass-card p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-user text-gray-500 mr-2"></i>
                        Contratista
                    </h3>
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Nombre</label>
                            <div class="mt-1 p-3 bg-gray-50 rounded-lg text-gray-800 font-medium">
                                {{ $cuenta->user->name }}
                            </div>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Email</label>
                            <div class="mt-1 p-3 bg-gray-50 rounded-lg text-gray-800">
                                {{ $cuenta->user->email }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Archivo adjunto actual -->
                @if($cuenta->ruta_archivo)
                    <div class="glass-card p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-paperclip text-green-500 mr-2"></i>
                            Archivo Actual
                        </h3>
                        <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-file-pdf text-white"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-800">{{ $cuenta->archivo_nombre }}</div>
                                        <div class="text-xs text-gray-600">Documento adjunto</div>
                                    </div>
                                </div>
                            </div>
                            @if($cuenta->archivo_url)
                                <a href="{{ $cuenta->archivo_url }}" target="_blank"
                                   class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-all text-sm font-medium">
                                    <i class="fas fa-download mr-2"></i>
                                    Descargar
                                </a>
                            @endif
                        </div>
                        <p class="mt-3 text-xs text-gray-500">
                            <i class="fas fa-info-circle mr-1"></i>
                            No se puede cambiar el archivo desde aquí
                        </p>
                    </div>
                @endif

                <!-- Botones de acción -->
                <div class="glass-card p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-save text-blue-500 mr-2"></i>
                        Guardar Cambios
                    </h3>
                    
                    <div class="space-y-3">
                        <button type="submit" 
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-xl font-medium transition-all flex items-center justify-center shadow-lg hover:shadow-xl">
                            <i class="fas fa-save mr-2"></i>
                            Guardar Cambios
                        </button>
                        
                        <a href="{{ route('contratacion.cuentas-cobro.show', $cuenta->id) }}" 
                           class="w-full inline-flex items-center justify-center px-4 py-3 border-2 border-gray-300 text-gray-700 bg-white rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all font-medium">
                            <i class="fas fa-times mr-2"></i>
                            Cancelar
                        </a>
                    </div>
                </div>

                <!-- Ayuda -->
                <div class="glass-card p-6 bg-gradient-to-br from-blue-50 to-indigo-50">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <i class="fas fa-lightbulb text-blue-500 text-xl"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-800 mb-2">Consejos</h4>
                            <ul class="text-xs text-gray-600 space-y-1">
                                <li>• Verifica que todos los datos sean correctos</li>
                                <li>• La descripción debe ser clara y detallada</li>
                                <li>• Los cambios no afectarán el estado de aprobación</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
