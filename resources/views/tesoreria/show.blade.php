@extends('layouts.dashboard')

@section('title', 'Detalle Cuenta de Cobro')

@section('breadcrumb')
    @include('components.navigation.breadcrumb', [
        'items' => [
            ['label' => 'Dashboard Tesorería', 'url' => route('tesoreria.dashboard')],
            ['label' => 'Cuentas', 'url' => route('tesoreria.cuentas')],
            ['label' => 'Detalle #' . $cuenta->id]
        ]
    ])
@endsection

@section('dashboard-header')
    <div class="glass-card p-6 slide-up">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="gradient-secondary w-16 h-16 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-file-invoice text-white text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Cuenta de Cobro #{{ $cuenta->id }}</h1>
                    <p class="text-gray-600">Información completa y gestión de la cuenta</p>
                </div>
            </div>
            
            <a href="{{ route('tesoreria.cuentas') }}" 
               class="inline-flex items-center px-4 py-2 border-2 border-gray-300 text-gray-700 bg-white rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all font-medium">
                <i class="fas fa-arrow-left mr-2"></i>
                Volver a Cuentas
            </a>
        </div>
    </div>
@endsection

@section('dashboard-content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Información Principal -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Estado Actual -->
            <div class="glass-card p-6">
                <h2 class="text-xl font-semibold mb-4 flex items-center">
                    <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                    Estado Actual
                </h2>
                @php
                    $badges = [
                        'borrador' => ['bg-gray-100 text-gray-800', 'Borrador'],
                        'pendiente_supervisor' => ['bg-yellow-100 text-yellow-800', 'Pendiente Supervisor'],
                        'pendiente_contratacion' => ['bg-blue-100 text-blue-800', 'Pendiente Contratación'],
                        'pendiente_tesoreria' => ['bg-orange-100 text-orange-800', 'Pendiente Tesorería'],
                        'pendiente_ordenador' => ['bg-purple-100 text-purple-800', 'Pendiente Ordenador'],
                        'aprobada' => ['bg-green-100 text-green-800', 'Aprobada'],
                        'rechazada' => ['bg-red-100 text-red-800', 'Rechazada'],
                        'pagada' => ['bg-indigo-100 text-indigo-800', 'Pagada'],
                    ];
                    $badgeInfo = $badges[$cuenta->estado] ?? ['bg-gray-100 text-gray-800', ucfirst($cuenta->estado)];
                @endphp
                <span class="px-4 py-2 inline-flex text-lg font-semibold rounded-full {{ $badgeInfo[0] }}">
                    <i class="fas fa-circle text-xs mr-2"></i>
                    {{ $badgeInfo[1] }}
                </span>
            </div>

            <!-- Detalles de la Cuenta -->
            <div class="glass-card p-6">
                <h2 class="text-xl font-semibold mb-4 flex items-center">
                    <i class="fas fa-clipboard-list mr-2 text-blue-600"></i>
                    Detalles de la Cuenta
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 p-4 rounded-xl border border-green-200">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Valor</label>
                        <p class="text-3xl font-bold text-green-600">
                            ${{ number_format($cuenta->valor, 0, ',', '.') }}
                        </p>
                        <p class="text-sm text-gray-500 mt-1">COP</p>
                    </div>
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-4 rounded-xl border border-blue-200">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Fecha de Emisión</label>
                        <p class="text-xl font-semibold text-blue-800">
                            {{ \Carbon\Carbon::parse($cuenta->fecha_emision)->format('d/m/Y') }}
                        </p>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ \Carbon\Carbon::parse($cuenta->fecha_emision)->diffForHumans() }}
                        </p>
                    </div>
                    <div class="md:col-span-2 bg-gray-50 p-4 rounded-xl">
                        <label class="block text-sm font-medium text-gray-600 mb-2">
                            <i class="fas fa-user mr-1"></i>
                            Contratista
                        </label>
                        <p class="text-lg font-semibold text-gray-800">{{ $cuenta->user->name }}</p>
                        <p class="text-sm text-gray-500">{{ $cuenta->user->email }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-600 mb-2">
                            <i class="fas fa-project-diagram mr-1"></i>
                            Proyecto / Servicio
                        </label>
                        <p class="text-base text-gray-800 bg-gray-50 p-3 rounded-lg">{{ $cuenta->proyecto_servicio }}</p>
                    </div>
                    @if($cuenta->observaciones)
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-600 mb-2">
                                <i class="fas fa-comment-alt mr-1"></i>
                                Observaciones
                            </label>
                            <p class="text-gray-700 bg-yellow-50 p-4 rounded-lg border border-yellow-200">{{ $cuenta->observaciones }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Archivo Adjunto -->
            @if($cuenta->archivo_adjunto)
            <div class="glass-card p-6">
                <h2 class="text-xl font-semibold mb-4 flex items-center">
                    <i class="fas fa-paperclip mr-2 text-blue-600"></i>
                    Archivo Adjunto
                </h2>
                <a href="{{ Storage::url($cuenta->archivo_adjunto) }}" 
                   target="_blank"
                   class="inline-flex items-center bg-gradient-to-r from-blue-500 to-indigo-600 text-white px-6 py-3 rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all shadow-lg hover:shadow-xl">
                    <i class="fas fa-file-pdf mr-2 text-xl"></i>
                    <span class="font-medium">Ver documento adjunto</span>
                </a>
            </div>
            @endif

            <!-- Botones de Acción -->
            @if($cuenta->estado === 'pendiente_tesoreria')
            <div class="glass-card p-6 bg-gradient-to-br from-orange-50 to-yellow-50 border-2 border-orange-200">
                <h2 class="text-xl font-semibold mb-4 flex items-center text-orange-800">
                    <i class="fas fa-tasks mr-2"></i>
                    Acciones Disponibles
                </h2>
                <p class="text-gray-700 mb-4">Esta cuenta requiere tu revisión y aprobación.</p>
                <div class="flex flex-wrap gap-3">
                    <form action="{{ route('cuentas-cobro.aprobar-tesoreria', $cuenta->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" 
                                onclick="return confirm('¿Estás seguro de aprobar esta cuenta? Pasará a pendiente de ordenador de gasto.')"
                                class="bg-gradient-to-r from-green-500 to-emerald-600 text-white px-6 py-3 rounded-xl hover:from-green-600 hover:to-emerald-700 transition-all shadow-lg hover:shadow-xl flex items-center font-medium">
                            <i class="fas fa-check-circle mr-2"></i>
                            Aprobar Cuenta
                        </button>
                    </form>

                    <button onclick="document.getElementById('modal-rechazar').classList.remove('hidden')"
                            class="bg-gradient-to-r from-red-500 to-pink-600 text-white px-6 py-3 rounded-xl hover:from-red-600 hover:to-pink-700 transition-all shadow-lg hover:shadow-xl flex items-center font-medium">
                        <i class="fas fa-times-circle mr-2"></i>
                        Rechazar Cuenta
                    </button>

                    <a href="{{ route('tesoreria.edit', $cuenta->id) }}" 
                       class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white px-6 py-3 rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all shadow-lg hover:shadow-xl flex items-center font-medium">
                        <i class="fas fa-edit mr-2"></i>
                        Editar Datos
                    </a>
                </div>
            </div>
            @else
            <div class="glass-card p-4 bg-blue-50 border-l-4 border-blue-500">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-500 text-xl mt-1 mr-3"></i>
                    <div>
                        <p class="font-medium text-blue-800 mb-1">Información</p>
                        <p class="text-blue-700">
                            @if($cuenta->estado === 'aprobada')
                                Esta cuenta ha sido aprobada por el ordenador de gasto y está lista para procesar el pago.
                            @elseif($cuenta->estado === 'pagada')
                                Esta cuenta ya ha sido pagada.
                            @elseif($cuenta->estado === 'rechazada')
                                Esta cuenta ha sido rechazada.
                            @else
                                Esta cuenta está en proceso de revisión en otra etapa del flujo.
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Historial -->
            <div class="glass-card p-6">
                <h2 class="text-xl font-semibold mb-4 flex items-center">
                    <i class="fas fa-history mr-2 text-blue-600"></i>
                    Historial
                </h2>
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="bg-gray-200 rounded-full p-2 mr-3 flex-shrink-0">
                            <i class="fas fa-plus text-gray-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Creada</p>
                            <p class="text-xs text-gray-500">{{ $cuenta->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    @if($cuenta->aprobado_supervisor_at)
                    <div class="flex items-start">
                        <div class="bg-green-200 rounded-full p-2 mr-3 flex-shrink-0">
                            <i class="fas fa-check text-green-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Aprobada por Supervisor</p>
                            <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($cuenta->aprobado_supervisor_at)->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    @endif

                    @if($cuenta->aprobado_contratacion_at)
                    <div class="flex items-start">
                        <div class="bg-blue-200 rounded-full p-2 mr-3 flex-shrink-0">
                            <i class="fas fa-check text-blue-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Aprobada por Contratación</p>
                            <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($cuenta->aprobado_contratacion_at)->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    @endif

                    @if($cuenta->aprobado_tesoreria_at)
                    <div class="flex items-start">
                        <div class="bg-orange-200 rounded-full p-2 mr-3 flex-shrink-0">
                            <i class="fas fa-check text-orange-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Aprobada por Tesorería</p>
                            <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($cuenta->aprobado_tesoreria_at)->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Estadísticas -->
            <div class="glass-card p-6 bg-gradient-to-br from-purple-50 to-pink-50">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center">
                    <i class="fas fa-chart-line mr-2 text-purple-600"></i>
                    Estadísticas
                </h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Días desde creación:</span>
                        <span class="font-semibold text-gray-800">{{ $cuenta->created_at->diffInDays(now()) }}</span>
                    </div>
                    @if($cuenta->updated_at->ne($cuenta->created_at))
                    <div class="flex justify-between">
                        <span class="text-gray-600">Última actualización:</span>
                        <span class="font-semibold text-gray-800">{{ $cuenta->updated_at->diffForHumans() }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

<!-- Modal Rechazar -->
<div id="modal-rechazar" class="hidden fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-times-circle text-red-600 mr-2"></i>
                    Rechazar Cuenta de Cobro
                </h3>
                <button type="button" 
                        onclick="document.getElementById('modal-rechazar').classList.add('hidden')"
                        class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form action="{{ route('cuentas-cobro.rechazar', $cuenta->id) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Comentarios (obligatorio) <span class="text-red-500">*</span>
                    </label>
                    <textarea name="comentarios" 
                              required
                              rows="4"
                              class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent"
                              placeholder="Explica el motivo del rechazo..."></textarea>
                </div>
                
                <div class="flex justify-end gap-3">
                    <button type="button" 
                            onclick="document.getElementById('modal-rechazar').classList.add('hidden')"
                            class="px-6 py-2 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all font-medium">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-6 py-2 bg-gradient-to-r from-red-500 to-pink-600 text-white rounded-xl hover:from-red-600 hover:to-pink-700 transition-all font-medium">
                        <i class="fas fa-times mr-2"></i>
                        Rechazar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Cerrar modal al hacer clic fuera
document.getElementById('modal-rechazar')?.addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.add('hidden');
    }
});
</script>
@endpush
