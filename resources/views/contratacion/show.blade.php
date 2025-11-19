@extends('layouts.dashboard')

@section('title', 'Revisar Cuenta de Cobro - Supervisor')

@section('breadcrumb')
    @include('components.navigation.breadcrumb', [
        'items' => [
            ['label' => 'Dashboard Supervisor', 'url' => route('supervisor.dashboard')],
            ['label' => 'Cuentas de Cobro', 'url' => route('supervisor.cuentas-cobro.index')],
            ['label' => 'Cuenta #' . $cuenta->id]
        ]
    ])
@endsection

@section('dashboard-header')
    <div class="glass-card p-6 slide-up">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="gradient-secondary w-16 h-16 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-file-invoice-dollar text-white text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Cuenta de Cobro #{{ $cuenta->id }}</h1>
                    <p class="text-gray-600">{{ $cuenta->proyecto_servicio }}</p>
                </div>
            </div>
            
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('supervisor.cuentas-cobro.index') }}" 
                   class="inline-flex items-center px-4 py-2 border-2 border-gray-300 text-gray-700 bg-white rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all font-medium">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Volver a Lista
                </a>
                
                @if($cuenta->archivo_url)
                    <a href="{{ $cuenta->archivo_url }}" target="_blank"
                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition-all font-medium">
                        <i class="fas fa-download mr-2"></i>
                        Descargar Archivo
                    </a>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('dashboard-content')
    <!-- Estado actual y valor -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="glass-card p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Estado Actual</h3>
                <i class="fas fa-flag text-gray-400"></i>
            </div>
            <div class="text-center">
                <span class="inline-flex items-center px-6 py-3 rounded-xl text-lg font-bold shadow-lg
                    @if($cuenta->estado === 'pagado') bg-green-100 text-green-800 border-2 border-green-300
                    @elseif($cuenta->estado === 'aprobado') bg-blue-100 text-blue-800 border-2 border-blue-300
                    @elseif($cuenta->estado === 'rechazado') bg-red-100 text-red-800 border-2 border-red-300
                    @elseif($cuenta->estado === 'revision') bg-yellow-100 text-yellow-800 border-2 border-yellow-300
                    @elseif($cuenta->estado === 'pendiente') bg-orange-100 text-orange-800 border-2 border-orange-300
                    @else bg-gray-100 text-gray-800 border-2 border-gray-300
                    @endif
                ">
                    <i class="fas fa-circle text-xs mr-2 animate-pulse"></i>
                    {{ strtoupper($cuenta->estado) }}
                </span>
            </div>
        </div>
        
        <div class="glass-card p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Valor</h3>
                <i class="fas fa-dollar-sign text-green-500"></i>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-green-600">
                    ${{ number_format($cuenta->valor, 0, ',', '.') }}
                </div>
                <div class="text-sm text-gray-500 mt-2">COP</div>
            </div>
        </div>
        
        <div class="glass-card p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Fecha Emisión</h3>
                <i class="fas fa-calendar-alt text-blue-500"></i>
            </div>
            <div class="text-center">
                <div class="text-xl font-semibold text-gray-800">
                    {{ \Carbon\Carbon::parse($cuenta->fecha_emision)->format('d/m/Y') }}
                </div>
                <div class="text-sm text-gray-500 mt-2">
                    {{ \Carbon\Carbon::parse($cuenta->fecha_emision)->diffForHumans() }}
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        <!-- Información principal -->
        <div class="xl:col-span-2 space-y-6">
            <!-- Detalles de la cuenta -->
            <div class="glass-card p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-file-invoice text-blue-500 mr-3"></i>
                    Detalles de la Cuenta
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Contratista</label>
                            <div class="mt-1 p-3 bg-gray-50 rounded-lg">
                                <div class="font-semibold text-gray-800">{{ $cuenta->user->name }}</div>
                                <div class="text-sm text-gray-600">{{ $cuenta->user->email }}</div>
                            </div>
                        </div>
                        
                        <div>
                            <label class="text-sm font-medium text-gray-500">Fecha de Creación</label>
                            <div class="mt-1 p-3 bg-gray-50 rounded-lg text-gray-800">
                                {{ $cuenta->created_at->format('d/m/Y H:i') }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">ID de Usuario</label>
                            <div class="mt-1 p-3 bg-gray-50 rounded-lg text-gray-800">
                                {{ $cuenta->user_id }}
                            </div>
                        </div>
                        
                        <div>
                            <label class="text-sm font-medium text-gray-500">Última Actualización</label>
                            <div class="mt-1 p-3 bg-gray-50 rounded-lg text-gray-800">
                                {{ $cuenta->updated_at->format('d/m/Y H:i') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Proyecto/Servicio -->
            <div class="glass-card p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-project-diagram text-purple-500 mr-3"></i>
                    Descripción del Proyecto/Servicio
                </h3>
                <div class="bg-purple-50 rounded-xl p-6 border-l-4 border-purple-400">
                    <p class="text-gray-800 leading-relaxed text-lg">
                        {{ $cuenta->proyecto_servicio }}
                    </p>
                </div>
            </div>

            <!-- Archivo adjunto -->
            @if($cuenta->ruta_archivo)
                <div class="glass-card p-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-paperclip text-green-500 mr-3"></i>
                        Archivo Adjunto
                    </h3>
                    <div class="bg-green-50 rounded-xl p-6 border border-green-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-file-pdf text-white text-xl"></i>
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-800">{{ $cuenta->archivo_nombre }}</div>
                                    <div class="text-sm text-gray-600">Documento adjunto</div>
                                </div>
                            </div>
                            @if($cuenta->archivo_url)
                                <a href="{{ $cuenta->archivo_url }}" target="_blank"
                                   class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-all font-medium">
                                    <i class="fas fa-download mr-2"></i>
                                    Descargar
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Observaciones -->
            @if($cuenta->observaciones)
                <div class="glass-card p-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-comment-alt text-yellow-500 mr-3"></i>
                        Observaciones
                    </h3>
                    <div class="bg-yellow-50 rounded-xl p-6 border-l-4 border-yellow-400">
                        <p class="text-gray-800 leading-relaxed">{{ $cuenta->observaciones }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Panel de acciones -->
        <div class="space-y-6">
            <!-- Acciones de revisión -->
            @if($cuenta->estado === 'pendiente_supervisor')
                <div class="glass-card p-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-clipboard-check text-indigo-500 mr-3"></i>
                        Acciones de Revisión
                    </h3>
                    
                    <!-- Aprobar -->
                    <form action="{{ route('cuentas-cobro.aprobar-supervisor', $cuenta->id) }}" method="POST" class="mb-4">
                        @csrf
                        <button type="submit" 
                                class="w-full bg-green-600 hover:bg-green-700 text-white py-3 px-4 rounded-xl font-medium transition-all flex items-center justify-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            Aprobar Cuenta
                        </button>
                    </form>
                    
                    <!-- Rechazar -->
                    <button onclick="showRejectModal()" 
                            class="w-full bg-red-600 hover:bg-red-700 text-white py-3 px-4 rounded-xl font-medium transition-all flex items-center justify-center mb-4">
                        <i class="fas fa-times-circle mr-2"></i>
                        Rechazar Cuenta
                    </button>
                    
                    <!-- Editar -->
                    <a href="{{ route('supervisor.cuentas-cobro.edit', $cuenta->id) }}" 
                       class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-xl font-medium transition-all flex items-center justify-center">
                        <i class="fas fa-edit mr-2"></i>
                        Editar Cuenta
                    </a>
                </div>
            @else
                <div class="glass-card p-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-info-circle text-blue-500 mr-3"></i>
                        Estado
                    </h3>
                    <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                        <p class="text-sm text-blue-800">
                            @if($cuenta->estado === 'borrador')
                                Esta cuenta está en borrador y debe ser enviada a revisión por el contratista.
                            @elseif($cuenta->estado === 'pendiente_contratacion')
                                Esta cuenta está pendiente de aprobación por el área de contratación.
                            @elseif($cuenta->estado === 'pendiente_tesoreria')
                                Esta cuenta está pendiente de aprobación por tesorería.
                            @elseif($cuenta->estado === 'pendiente_ordenador')
                                Esta cuenta está pendiente de aprobación por el ordenador del gasto.
                            @elseif($cuenta->estado === 'aprobada')
                                Esta cuenta ha sido completamente aprobada y está lista para pago.
                            @elseif($cuenta->estado === 'rechazada')
                                Esta cuenta ha sido rechazada.
                            @elseif($cuenta->estado === 'pagada')
                                Esta cuenta ha sido pagada.
                            @endif
                        </p>
                    </div>
                </div>
            @endif

            <!-- Información del historial -->
            <div class="glass-card p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-history text-gray-500 mr-3"></i>
                    Historial
                </h3>
                
                <div class="space-y-4">
                    <div class="flex items-start space-x-3">
                        <div class="w-3 h-3 bg-blue-500 rounded-full mt-2"></div>
                        <div>
                            <div class="font-medium text-gray-800">Cuenta creada</div>
                            <div class="text-sm text-gray-600">{{ $cuenta->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                    
                    @if($cuenta->created_at != $cuenta->updated_at)
                        <div class="flex items-start space-x-3">
                            <div class="w-3 h-3 bg-yellow-500 rounded-full mt-2"></div>
                            <div>
                                <div class="font-medium text-gray-800">Última actualización</div>
                                <div class="text-sm text-gray-600">{{ $cuenta->updated_at->format('d/m/Y H:i') }}</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

<!-- Modal de rechazo -->
<div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-semibold text-gray-800">Rechazar Cuenta</h3>
            <button onclick="hideRejectModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form action="{{ route('cuentas-cobro.rechazar', $cuenta->id) }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label for="comentarios" class="block text-sm font-medium text-gray-700 mb-2">
                    Motivo del rechazo (obligatorio)
                </label>
                <textarea name="comentarios" id="comentarios" rows="4" 
                          class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                          placeholder="Especifica el motivo del rechazo..." required></textarea>
            </div>
            
            <div class="flex space-x-3">
                <button type="button" onclick="hideRejectModal()" 
                        class="flex-1 py-2 px-4 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 font-medium">
                    Cancelar
                </button>
                <button type="submit" 
                        class="flex-1 py-2 px-4 bg-red-600 hover:bg-red-700 text-white rounded-xl font-medium">
                    Rechazar
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function showRejectModal() {
    document.getElementById('rejectModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function hideRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    document.getElementById('comentarios').value = '';
}

// Cerrar modal al hacer clic fuera
document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideRejectModal();
    }
});
</script>
@endpush
