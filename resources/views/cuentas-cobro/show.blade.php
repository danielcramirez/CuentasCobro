@extends('layouts.dashboard')

@section('title', 'Detalle Cuenta de Cobro #' . $cuenta->id)

@section('breadcrumb')
    @include('components.navigation.breadcrumb', [
        'items' => [
            ['label' => 'Dashboard', 'route' => 'dashboard'],
            ['label' => 'Cuentas de Cobro', 'route' => 'cuentas-cobro.mostrar'],
            ['label' => 'Detalle #' . $cuenta->id]
        ]
    ])
@endsection

@section('dashboard-header')
    <div class="glass-card p-6 slide-up">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            <div class="flex items-center space-x-4">
                <div class="gradient-secondary w-16 h-16 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-file-invoice text-white text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Cuenta de Cobro #{{ $cuenta->id }}</h1>
                    <p class="text-gray-600">Revisión y aprobación de cuenta</p>
                </div>
            </div>
            <div>
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
                <span class="px-4 py-2 text-sm font-semibold rounded-full {{ $badgeClass }}">
                    {{ $estadoTexto }}
                </span>
            </div>
        </div>
    </div>
@endsection

@section('dashboard-content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Información principal -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Datos de la cuenta -->
            <div class="glass-card p-6">
                <h2 class="text-xl font-semibold mb-4 flex items-center">
                    <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                    Información de la Cuenta
                </h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm text-gray-600">Contratista</label>
                        <p class="font-semibold text-gray-800">{{ $cuenta->user->name }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">Email</label>
                        <p class="font-semibold text-gray-800">{{ $cuenta->user->email }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">Proyecto/Servicio</label>
                        <p class="font-semibold text-gray-800">{{ $cuenta->proyecto_servicio }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">Valor</label>
                        <p class="font-bold text-2xl text-green-600">${{ number_format($cuenta->valor, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">Fecha Emisión</label>
                        <p class="font-semibold text-gray-800">
                            {{ $cuenta->fecha_emision ? \Carbon\Carbon::parse($cuenta->fecha_emision)->format('d/m/Y') : 'N/A' }}
                        </p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">Fecha Creación</label>
                        <p class="font-semibold text-gray-800">{{ $cuenta->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @if($cuenta->observaciones)
                    <div class="col-span-2">
                        <label class="text-sm text-gray-600">Observaciones</label>
                        <p class="font-semibold text-gray-800">{{ $cuenta->observaciones }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Archivo adjunto con visor -->
            @if($cuenta->archivo_adjunto)
            <div class="glass-card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold flex items-center">
                        <i class="fas fa-file-pdf text-red-500 mr-3"></i>
                        Documento Adjunto
                    </h2>
                    <div class="flex space-x-2">
                        <a href="{{ Storage::url($cuenta->archivo_adjunto) }}" 
                           target="_blank"
                           class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all font-medium text-sm">
                            <i class="fas fa-external-link-alt mr-2"></i>
                            Abrir en Nueva Pestaña
                        </a>
                        <a href="{{ Storage::url($cuenta->archivo_adjunto) }}" 
                           download
                           class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-all font-medium text-sm">
                            <i class="fas fa-download mr-2"></i>
                            Descargar PDF
                        </a>
                    </div>
                </div>
                
                <!-- Visor de PDF embebido -->
                <div class="bg-gray-100 rounded-xl overflow-hidden border-2 border-gray-300" style="height: 800px;">
                    <iframe 
                        src="{{ Storage::url($cuenta->archivo_adjunto) }}" 
                        class="w-full h-full"
                        frameborder="0"
                        type="application/pdf">
                        <p class="p-4 text-center text-gray-600">
                            Tu navegador no puede mostrar el PDF. 
                            <a href="{{ Storage::url($cuenta->archivo_adjunto) }}" class="text-blue-600 hover:underline" download>
                                Haz clic aquí para descargarlo
                            </a>
                        </p>
                    </iframe>
                </div>
            </div>
            @endif

            <!-- Historial de aprobaciones -->
            <div class="glass-card p-6">
                <h2 class="text-xl font-semibold mb-4 flex items-center">
                    <i class="fas fa-history mr-2 text-blue-600"></i>
                    Historial de Aprobaciones
                </h2>
                <div class="space-y-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white">
                            <i class="fas fa-check text-xs"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-800">Creada</p>
                            <p class="text-sm text-gray-600">{{ $cuenta->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    @if($cuenta->aprobado_supervisor_at)
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-white">
                            <i class="fas fa-check text-xs"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-800">Aprobada por Supervisor</p>
                            <p class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($cuenta->aprobado_supervisor_at)->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    @endif

                    @if($cuenta->aprobado_contratacion_at)
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-white">
                            <i class="fas fa-check text-xs"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-800">Aprobada por Contratación</p>
                            <p class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($cuenta->aprobado_contratacion_at)->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    @endif

                    @if($cuenta->aprobado_tesoreria_at)
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-white">
                            <i class="fas fa-check text-xs"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-800">Aprobada por Tesorería</p>
                            <p class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($cuenta->aprobado_tesoreria_at)->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    @endif

                    @if($cuenta->aprobado_ordenador_at)
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-white">
                            <i class="fas fa-check text-xs"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-800">Aprobada por Ordenador de Gasto</p>
                            <p class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($cuenta->aprobado_ordenador_at)->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Comentarios de rechazo -->
            @if($cuenta->comentarios_rechazo)
            <div class="glass-card p-6 border-l-4 border-red-500 bg-red-50">
                <h2 class="text-xl font-semibold mb-2 flex items-center text-red-800">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Comentarios de Rechazo
                </h2>
                <p class="text-gray-800">{{ $cuenta->comentarios_rechazo }}</p>
            </div>
            @endif

            <!-- Botones de acción según rol -->
            @php
                $user = auth()->user();
                $canApprove = false;
                $canEdit = false;
                $approveRoute = null;
                $editRoute = route('cuentas-cobro.edit', $cuenta->id);
                
                // Supervisor
                if($user->hasRole('supervisor') && $cuenta->estado === 'pendiente_supervisor') {
                    $canApprove = true;
                    $canEdit = true;
                    $approveRoute = route('cuentas-cobro.aprobar-supervisor', $cuenta->id);
                }
                // Contratación
                elseif($user->hasRole('contratacion') && $cuenta->estado === 'pendiente_contratacion') {
                    $canApprove = true;
                    $canEdit = true;
                    $approveRoute = route('cuentas-cobro.aprobar-contratacion', $cuenta->id);
                }
                // Tesorería
                elseif($user->hasRole('tesoreria') && $cuenta->estado === 'pendiente_tesoreria') {
                    $canApprove = true;
                    $canEdit = true;
                    $approveRoute = route('cuentas-cobro.aprobar-tesoreria', $cuenta->id);
                }
                // Ordenador de Gasto
                elseif($user->hasRole('ordenador_gasto') && $cuenta->estado === 'pendiente_ordenador') {
                    $canApprove = true;
                    $canEdit = true;
                    $approveRoute = route('cuentas-cobro.aprobar-ordenador', $cuenta->id);
                }
                // Contratista
                elseif($user->hasRole('contratista') && $cuenta->user_id === $user->id && in_array($cuenta->estado, ['borrador', 'rechazada'])) {
                    $canEdit = true;
                }
            @endphp

            @if($canApprove || $canEdit)
            <div class="glass-card p-6 bg-gradient-to-r from-orange-50 to-red-50">
                <h2 class="text-xl font-semibold mb-4">Acciones Disponibles</h2>
                <div class="flex flex-wrap gap-3">
                    @if($canApprove)
                    <form action="{{ $approveRoute }}" method="POST" class="inline-block">
                        @csrf
                        <button type="submit" 
                                class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-semibold">
                            <i class="fas fa-check-circle mr-2"></i>Aprobar Cuenta
                        </button>
                    </form>

                    <button onclick="document.getElementById('rechazarModal').classList.remove('hidden')" 
                            class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-semibold">
                        <i class="fas fa-times-circle mr-2"></i>Rechazar
                    </button>
                    @endif

                    @if($canEdit)
                    <a href="{{ $editRoute }}" 
                       class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                        <i class="fas fa-edit mr-2"></i>Editar Datos
                    </a>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar derecho -->
        <div class="space-y-6">
            <!-- Resumen rápido -->
            <div class="glass-card p-6">
                <h3 class="font-semibold mb-4">Resumen</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center pb-2 border-b">
                        <span class="text-gray-600">Estado:</span>
                        <span class="font-semibold">{{ $estadoTexto }}</span>
                    </div>
                    <div class="flex justify-between items-center pb-2 border-b">
                        <span class="text-gray-600">Valor:</span>
                        <span class="font-bold text-green-600">${{ number_format($cuenta->valor, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center pb-2 border-b">
                        <span class="text-gray-600">ID:</span>
                        <span class="font-semibold">#{{ $cuenta->id }}</span>
                    </div>
                </div>
            </div>

            <!-- Navegación rápida -->
            <div class="glass-card p-6">
                <h3 class="font-semibold mb-4">Acciones Rápidas</h3>
                <div class="space-y-2">
                    <a href="{{ route('cuentas-cobro.mostrar') }}" 
                       class="block w-full px-4 py-2 text-center bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Volver al Listado
                    </a>
                    <a href="{{ route('dashboard') }}" 
                       class="block w-full px-4 py-2 text-center bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        <i class="fas fa-home mr-2"></i>Ir al Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de rechazo -->
    <div id="rechazarModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6">
            <h3 class="text-xl font-bold mb-4">Rechazar Cuenta de Cobro</h3>
            <form action="{{ route('cuentas-cobro.rechazar', $cuenta->id) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Motivo del rechazo <span class="text-red-500">*</span>
                    </label>
                    <textarea name="comentarios" 
                              rows="4" 
                              required
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                              placeholder="Explique las razones del rechazo..."></textarea>
                </div>
                <div class="flex space-x-3">
                    <button type="submit" 
                            class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        Confirmar Rechazo
                    </button>
                    <button type="button" 
                            onclick="document.getElementById('rechazarModal').classList.add('hidden')"
                            class="flex-1 px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
