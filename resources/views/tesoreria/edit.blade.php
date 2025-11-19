@extends('layouts.dashboard')

@section('title', 'Editar Cuenta de Cobro')

@section('breadcrumb')
    @include('components.navigation.breadcrumb', [
        'items' => [
            ['label' => 'Dashboard Tesorería', 'url' => route('tesoreria.dashboard')],
            ['label' => 'Cuentas', 'url' => route('tesoreria.cuentas')],
            ['label' => 'Editar #' . $cuenta->id]
        ]
    ])
@endsection

@section('dashboard-header')
    <div class="glass-card p-6 slide-up">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="gradient-secondary w-16 h-16 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-edit text-white text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Editar Cuenta #{{ $cuenta->id }}</h1>
                    <p class="text-gray-600">Modifica los datos de la cuenta de cobro</p>
                </div>
            </div>
            
            <a href="{{ route('tesoreria.show', $cuenta->id) }}" 
               class="inline-flex items-center px-4 py-2 border-2 border-gray-300 text-gray-700 bg-white rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all font-medium">
                <i class="fas fa-arrow-left mr-2"></i>
                Volver
            </a>
        </div>
    </div>
@endsection

@section('dashboard-content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Formulario de Edición -->
        <div class="lg:col-span-2">
            <div class="glass-card p-6">
                <form action="{{ route('tesoreria.update', $cuenta->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <!-- Proyecto/Servicio -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-project-diagram mr-1"></i>
                                Proyecto / Servicio <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="proyecto_servicio" 
                                   value="{{ old('proyecto_servicio', $cuenta->proyecto_servicio) }}"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('proyecto_servicio') border-red-500 @enderror">
                            @error('proyecto_servicio')
                                <p class="mt-1 text-sm text-red-500">
                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Valor -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-dollar-sign mr-1"></i>
                                Valor <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">$</span>
                                <input type="number" 
                                       name="valor" 
                                       value="{{ old('valor', $cuenta->valor) }}"
                                       required
                                       min="0"
                                       step="0.01"
                                       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('valor') border-red-500 @enderror">
                            </div>
                            @error('valor')
                                <p class="mt-1 text-sm text-red-500">
                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Fecha de Emisión -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar-alt mr-1"></i>
                                Fecha de Emisión <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   name="fecha_emision" 
                                   value="{{ old('fecha_emision', \Carbon\Carbon::parse($cuenta->fecha_emision)->format('Y-m-d')) }}"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('fecha_emision') border-red-500 @enderror">
                            @error('fecha_emision')
                                <p class="mt-1 text-sm text-red-500">
                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Observaciones -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-comment-alt mr-1"></i>
                                Observaciones
                            </label>
                            <textarea name="observaciones" 
                                      rows="4"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('observaciones') border-red-500 @enderror"
                                      placeholder="Agrega observaciones o comentarios...">{{ old('observaciones', $cuenta->observaciones) }}</textarea>
                            @error('observaciones')
                                <p class="mt-1 text-sm text-red-500">
                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Botones -->
                        <div class="flex justify-end gap-3 pt-4 border-t">
                            <a href="{{ route('tesoreria.show', $cuenta->id) }}" 
                               class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all font-medium">
                                <i class="fas fa-times mr-2"></i>
                                Cancelar
                            </a>
                            <button type="submit" 
                                    class="px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all font-medium shadow-lg hover:shadow-xl">
                                <i class="fas fa-save mr-2"></i>
                                Guardar Cambios
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Información de Solo Lectura -->
            <div class="glass-card p-6">
                <h2 class="text-xl font-semibold mb-4 flex items-center">
                    <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                    Información
                </h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Contratista</label>
                        <p class="text-sm font-semibold text-gray-800">{{ $cuenta->user->name }}</p>
                        <p class="text-xs text-gray-500">{{ $cuenta->user->email }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Estado</label>
                        <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                            <i class="fas fa-circle text-xs mr-1"></i>
                            Pendiente Tesorería
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Fecha de Creación</label>
                        <p class="text-sm text-gray-800">{{ $cuenta->created_at->format('d/m/Y H:i') }}</p>
                        <p class="text-xs text-gray-500">{{ $cuenta->created_at->diffForHumans() }}</p>
                    </div>
                    @if($cuenta->archivo_adjunto)
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Archivo Adjunto</label>
                        <a href="{{ Storage::url($cuenta->archivo_adjunto) }}" 
                           target="_blank"
                           class="text-blue-600 hover:text-blue-800 text-sm inline-flex items-center">
                            <i class="fas fa-file-pdf mr-1"></i>Ver documento
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Ayuda -->
            <div class="glass-card p-4 bg-gradient-to-br from-blue-50 to-indigo-50 border-l-4 border-blue-500">
                <div class="flex items-start">
                    <i class="fas fa-lightbulb text-blue-500 text-xl mt-1 mr-3 flex-shrink-0"></i>
                    <div>
                        <h3 class="font-semibold text-blue-800 mb-1">Nota Importante</h3>
                        <p class="text-sm text-blue-700">
                            Solo puedes modificar los datos de la cuenta. El estado no cambiará. 
                            Para aprobar o rechazar, usa los botones correspondientes en la vista de detalle.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Historial Resumido -->
            <div class="glass-card p-6 bg-gray-50">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center">
                    <i class="fas fa-history mr-2 text-gray-600"></i>
                    Historial
                </h3>
                <div class="space-y-2 text-sm">
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                        Aprobada por Supervisor
                    </div>
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-check-circle text-blue-500 mr-2"></i>
                        Aprobada por Contratación
                    </div>
                    <div class="flex items-center text-orange-600 font-medium">
                        <i class="fas fa-clock text-orange-500 mr-2"></i>
                        Pendiente Tesorería
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
