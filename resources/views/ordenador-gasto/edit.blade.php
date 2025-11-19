@extends('layouts.dashboard')

@section('title', 'Editar Cuenta de Cobro #' . $cuenta->id)

@section('breadcrumb')
    @include('components.navigation.breadcrumb', [
        'items' => [
            ['label' => 'Dashboard', 'route' => 'ordenador-gasto.dashboard'],
            ['label' => 'Cuentas', 'route' => 'ordenador-gasto.cuentas'],
            ['label' => 'Editar #' . $cuenta->id]
        ]
    ])
@endsection

@section('dashboard-header')
    <div class="glass-card p-6 slide-up">
        <div class="flex items-center space-x-4">
            <div class="gradient-secondary w-16 h-16 rounded-2xl flex items-center justify-center">
                <i class="fas fa-edit text-white text-2xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Editar Cuenta de Cobro</h1>
                <p class="text-gray-600">Modificar datos antes de aprobar - ID: #{{ $cuenta->id }}</p>
            </div>
        </div>
    </div>
@endsection

@section('dashboard-content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Formulario de edición -->
        <div class="lg:col-span-2">
            <div class="glass-card p-6">
                <div class="mb-6 p-4 bg-yellow-50 border-l-4 border-yellow-500 rounded">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-yellow-600 mt-1 mr-3"></i>
                        <div>
                            <h3 class="font-semibold text-yellow-800">Nota Importante</h3>
                            <p class="text-sm text-yellow-700">Esta edición solo modifica los datos de la cuenta. El estado no cambiará hasta que apruebe o rechace la cuenta.</p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('ordenador-gasto.update', $cuenta->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Información del contratista (solo lectura) -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h3 class="font-semibold text-gray-800 mb-3">Información del Contratista</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Nombre</label>
                                <p class="font-medium text-gray-800">{{ $cuenta->user->name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Email</label>
                                <p class="font-medium text-gray-800">{{ $cuenta->user->email }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Campos editables -->
                    <div class="space-y-4">
                        <div>
                            <label for="proyecto_servicio" class="block text-sm font-medium text-gray-700 mb-2">
                                Proyecto/Servicio <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="proyecto_servicio" 
                                   name="proyecto_servicio" 
                                   value="{{ old('proyecto_servicio', $cuenta->proyecto_servicio) }}"
                                   required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('proyecto_servicio') border-red-500 @enderror">
                            @error('proyecto_servicio')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="valor" class="block text-sm font-medium text-gray-700 mb-2">
                                Valor (COP) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   id="valor" 
                                   name="valor" 
                                   value="{{ old('valor', $cuenta->valor) }}"
                                   required
                                   min="0"
                                   step="0.01"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('valor') border-red-500 @enderror">
                            @error('valor')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="fecha_emision" class="block text-sm font-medium text-gray-700 mb-2">
                                Fecha de Emisión <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   id="fecha_emision" 
                                   name="fecha_emision" 
                                   value="{{ old('fecha_emision', $cuenta->fecha_emision) }}"
                                   required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('fecha_emision') border-red-500 @enderror">
                            @error('fecha_emision')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-2">
                                Observaciones
                            </label>
                            <textarea id="observaciones" 
                                      name="observaciones" 
                                      rows="4"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('observaciones') border-red-500 @enderror"
                                      placeholder="Observaciones adicionales (opcional)">{{ old('observaciones', $cuenta->observaciones) }}</textarea>
                            @error('observaciones')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Estado actual (solo lectura) -->
                    <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                        <label class="block text-sm text-gray-600 mb-1">Estado Actual</label>
                        @php
                            $badgeClass = match($cuenta->estado) {
                                'borrador' => 'bg-gray-100 text-gray-800',
                                'pendiente_supervisor' => 'bg-yellow-100 text-yellow-800',
                                'pendiente_contratacion' => 'bg-blue-100 text-blue-800',
                                'pendiente_tesoreria' => 'bg-purple-100 text-purple-800',
                                'pendiente_ordenador' => 'bg-orange-100 text-orange-800',
                                'aprobada' => 'bg-green-100 text-green-800',
                                'rechazada' => 'bg-red-100 text-red-800',
                                'pagada' => 'bg-indigo-100 text-indigo-800',
                                default => 'bg-gray-100 text-gray-800'
                            };
                            $estadoTexto = match($cuenta->estado) {
                                'borrador' => 'Borrador',
                                'pendiente_supervisor' => 'Pendiente Supervisor',
                                'pendiente_contratacion' => 'Pendiente Contratación',
                                'pendiente_tesoreria' => 'Pendiente Tesorería',
                                'pendiente_ordenador' => 'Pendiente Ordenador',
                                'aprobada' => 'Aprobada',
                                'rechazada' => 'Rechazada',
                                'pagada' => 'Pagada',
                                default => ucfirst($cuenta->estado)
                            };
                        @endphp
                        <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full {{ $badgeClass }}">
                            {{ $estadoTexto }}
                        </span>
                    </div>

                    <!-- Archivo adjunto (solo lectura) -->
                    @if($cuenta->archivo_adjunto)
                    <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                        <label class="block text-sm text-gray-600 mb-2">Documento Adjunto</label>
                        <a href="{{ Storage::url($cuenta->archivo_adjunto) }}" 
                           target="_blank" 
                           class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm">
                            <i class="fas fa-file-pdf mr-2"></i>
                            Ver Documento PDF
                        </a>
                        <p class="text-xs text-gray-500 mt-2">
                            <i class="fas fa-info-circle mr-1"></i>
                            El documento adjunto no se puede modificar desde esta vista
                        </p>
                    </div>
                    @endif

                    <!-- Botones de acción -->
                    <div class="mt-8 flex flex-wrap gap-3">
                        <button type="submit" 
                                class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                            <i class="fas fa-save mr-2"></i>Guardar Cambios
                        </button>
                        <a href="{{ route('ordenador-gasto.show', $cuenta->id) }}" 
                           class="px-6 py-3 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors font-semibold">
                            <i class="fas fa-times mr-2"></i>Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Información actual -->
            <div class="glass-card p-6">
                <h3 class="font-semibold mb-4">Datos Actuales</h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-xs text-gray-600">Valor Actual</label>
                        <p class="font-bold text-green-600 text-lg">${{ number_format($cuenta->valor, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Fecha Actual</label>
                        <p class="font-semibold text-gray-800">
                            {{ $cuenta->fecha_emision ? \Carbon\Carbon::parse($cuenta->fecha_emision)->format('d/m/Y') : 'N/A' }}
                        </p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Creada el</label>
                        <p class="font-semibold text-gray-800">{{ $cuenta->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Ayuda -->
            <div class="glass-card p-6 bg-gradient-to-br from-blue-50 to-indigo-50">
                <h3 class="font-semibold mb-3 flex items-center text-blue-800">
                    <i class="fas fa-question-circle mr-2"></i>
                    ¿Necesitas ayuda?
                </h3>
                <p class="text-sm text-gray-700 mb-3">
                    Puedes editar los datos de la cuenta antes de aprobarla. Los cambios se guardarán pero el estado no cambiará.
                </p>
                <p class="text-sm text-gray-700">
                    Para aprobar o rechazar la cuenta, regresa a la vista de detalle después de guardar los cambios.
                </p>
            </div>

            <!-- Navegación -->
            <div class="glass-card p-6">
                <h3 class="font-semibold mb-4">Navegación</h3>
                <div class="space-y-2">
                    <a href="{{ route('ordenador-gasto.show', $cuenta->id) }}" 
                       class="block w-full px-4 py-2 text-center bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Volver a Detalles
                    </a>
                    <a href="{{ route('ordenador-gasto.cuentas') }}" 
                       class="block w-full px-4 py-2 text-center bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        <i class="fas fa-list mr-2"></i>Ver Listado
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
