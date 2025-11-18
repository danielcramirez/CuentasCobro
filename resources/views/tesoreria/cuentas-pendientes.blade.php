@extends('layouts.dashboard')

@section('title', 'Cuentas Pendientes - Tesorería')

@section('breadcrumb')
    @include('components.navigation.breadcrumb', [
        'items' => [
            ['label' => 'Dashboard Tesorería', 'url' => route('tesoreria.dashboard')],
            ['label' => 'Cuentas Pendientes']
        ]
    ])
@endsection

@section('dashboard-header')
    <div class="glass-card p-6 slide-up">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="gradient-secondary w-16 h-16 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-clock text-white text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Cuentas Pendientes</h1>
                    <p class="text-gray-600">Revisión y aprobación de cuentas</p>
                </div>
            </div>
            
            <div class="flex items-center space-x-3">
                <a href="{{ route('tesoreria.dashboard') }}" 
                   class="inline-flex items-center px-4 py-2 border-2 border-gray-300 text-gray-700 bg-white rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all font-medium">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Volver
                </a>
            </div>
        </div>
    </div>
@endsection

@section('dashboard-content')
    <!-- Estadísticas de pendientes -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="glass-card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Pendientes</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $estadisticasPendientes['total_pendientes'] }}</p>
                    <p class="text-sm text-orange-600 flex items-center mt-1">
                        <i class="fas fa-clock mr-1"></i>
                        Por revisar
                    </p>
                </div>
                <div class="bg-gradient-to-br from-orange-400 to-orange-600 w-12 h-12 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clock text-white"></i>
                </div>
            </div>
        </div>
        
        <div class="glass-card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Aprobadas</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $estadisticasPendientes['total_aprobadas'] }}</p>
                    <p class="text-sm text-blue-600 flex items-center mt-1">
                        <i class="fas fa-check mr-1"></i>
                        Listas para pago
                    </p>
                </div>
                <div class="bg-gradient-to-br from-blue-400 to-blue-600 w-12 h-12 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check text-white"></i>
                </div>
            </div>
        </div>
        
        <div class="glass-card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Valor Pendiente</p>
                    <p class="text-xl font-bold text-gray-800">${{ number_format($estadisticasPendientes['valor_pendiente'], 0, ',', '.') }}</p>
                    <p class="text-sm text-red-600 flex items-center mt-1">
                        <i class="fas fa-exclamation mr-1"></i>
                        COP
                    </p>
                </div>
                <div class="bg-gradient-to-br from-red-400 to-red-600 w-12 h-12 rounded-xl flex items-center justify-center">
                    <i class="fas fa-exclamation text-white"></i>
                </div>
            </div>
        </div>
        
        <div class="glass-card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Valor Aprobado</p>
                    <p class="text-xl font-bold text-gray-800">${{ number_format($estadisticasPendientes['valor_aprobado'], 0, ',', '.') }}</p>
                    <p class="text-sm text-green-600 flex items-center mt-1">
                        <i class="fas fa-dollar-sign mr-1"></i>
                        COP
                    </p>
                </div>
                <div class="bg-gradient-to-br from-green-400 to-green-600 w-12 h-12 rounded-xl flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-white"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros de búsqueda -->
    <div class="glass-card p-6 mb-8">
        <form method="GET" action="{{ route('tesoreria.pendientes') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Búsqueda general -->
                <div class="md:col-span-2">
                    <div class="relative">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Buscar por proyecto o contratista..."
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>
                
                <!-- Filtro por estado -->
                <div>
                    <select name="estado" 
                            class="w-full py-3 px-4 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        <option value="">Todos los estados</option>
                        <option value="pendiente" {{ request('estado') === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="aprobado" {{ request('estado') === 'aprobado' ? 'selected' : '' }}>Aprobado</option>
                    </select>
                </div>
            </div>
            
            <div class="flex gap-3">
                <button type="submit" 
                        class="px-6 py-3 bg-orange-600 hover:bg-orange-700 text-white rounded-xl font-medium transition-all flex items-center">
                    <i class="fas fa-search mr-2"></i>
                    Buscar
                </button>
                
                @if(request()->hasAny(['search', 'estado']))
                    <a href="{{ route('tesoreria.pendientes') }}" 
                       class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition-all flex items-center">
                        <i class="fas fa-times mr-2"></i>
                        Limpiar
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Lista de cuentas pendientes -->
    @if($pendientes->isEmpty())
        <div class="glass-card p-12 text-center">
            <div class="w-24 h-24 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-tasks text-orange-500 text-3xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-800 mb-2">No hay cuentas pendientes</h3>
            <p class="text-gray-600">
                @if(request()->hasAny(['search', 'estado']))
                    No se encontraron cuentas que coincidan con los filtros aplicados
                @else
                    Todas las cuentas han sido procesadas
                @endif
            </p>
        </div>
    @else
        <div class="glass-card overflow-hidden">
            <!-- Header de la tabla -->
            <div class="bg-gradient-to-r from-orange-600 to-red-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-white">Cuentas por Revisar</h2>
                    <div class="text-orange-100 text-sm">
                        {{ $pendientes->total() }} cuentas pendientes
                    </div>
                </div>
            </div>

            <!-- Tabla responsive -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Cuenta
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estado
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Valor
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fecha
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($pendientes as $cuenta)
                            <tr class="hover:bg-orange-50 transition-colors">
                                <!-- Información de la cuenta -->
                                <td class="px-6 py-4">
                                    <div class="flex items-start space-x-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-red-600 rounded-full flex items-center justify-center">
                                            <span class="text-white font-bold text-sm">
                                                {{ strtoupper(substr($cuenta->user->name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-800">{{ $cuenta->user->name }}</h4>
                                            <p class="text-sm text-gray-600">Cuenta #{{ $cuenta->id }}</p>
                                            <p class="text-sm text-gray-500">{{ Str::limit($cuenta->proyecto_servicio, 50) }}</p>
                                        </div>
                                    </div>
                                </td>
                                
                                <!-- Estado -->
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                        @if($cuenta->estado === 'aprobado') bg-blue-100 text-blue-800
                                        @elseif($cuenta->estado === 'pendiente') bg-orange-100 text-orange-800
                                        @else bg-gray-100 text-gray-800
                                        @endif
                                    ">
                                        <i class="fas fa-circle text-xs mr-2
                                            @if($cuenta->estado === 'aprobado') animate-pulse
                                            @endif
                                        "></i>
                                        {{ strtoupper($cuenta->estado) }}
                                    </span>
                                </td>
                                
                                <!-- Valor -->
                                <td class="px-6 py-4">
                                    <div class="text-lg font-bold text-gray-800">
                                        ${{ number_format($cuenta->valor, 0, ',', '.') }}
                                    </div>
                                    <div class="text-sm text-gray-500">COP</div>
                                </td>
                                
                                <!-- Fecha -->
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-800">
                                        {{ $cuenta->created_at->format('d/m/Y') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $cuenta->created_at->diffForHumans() }}
                                    </div>
                                </td>
                                
                                <!-- Acciones -->
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <!-- Ver cuenta -->
                                        <a href="{{ route('cuentas-cobro.ver', $cuenta->id) }}" 
                                           class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-800 rounded-lg hover:bg-blue-200 transition-colors text-sm font-medium">
                                            <i class="fas fa-eye mr-1"></i>
                                            Ver
                                        </a>
                                        
                                        @if($cuenta->estado === 'pendiente')
                                            <!-- Aprobar -->
                                            <button onclick="aprobarCuenta({{ $cuenta->id }})" 
                                                    class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 rounded-lg hover:bg-green-200 transition-colors text-sm font-medium">
                                                <i class="fas fa-check mr-1"></i>
                                                Aprobar
                                            </button>
                                            
                                            <!-- Rechazar -->
                                            <button onclick="rechazarCuenta({{ $cuenta->id }})" 
                                                    class="inline-flex items-center px-3 py-1 bg-red-100 text-red-800 rounded-lg hover:bg-red-200 transition-colors text-sm font-medium">
                                                <i class="fas fa-times mr-1"></i>
                                                Rechazar
                                            </button>
                                        @elseif($cuenta->estado === 'aprobado')
                                            <!-- Marcar como pagada -->
                                            <button onclick="marcarPagada({{ $cuenta->id }})" 
                                                    class="inline-flex items-center px-3 py-1 bg-purple-100 text-purple-800 rounded-lg hover:bg-purple-200 transition-colors text-sm font-medium">
                                                <i class="fas fa-credit-card mr-1"></i>
                                                Pagar
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($pendientes->hasPages())
                <div class="bg-gray-50 px-6 py-4">
                    {{ $pendientes->links() }}
                </div>
            @endif
        </div>
    @endif
@endsection

<!-- Modal para aprobar cuenta -->
<div id="aprobarModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-semibold text-gray-800">Aprobar Cuenta</h3>
            <button onclick="cerrarAprobarModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="aprobarForm" method="POST">
            @csrf
            <input type="hidden" name="estado" value="aprobado">
            
            <div class="mb-4">
                <p class="text-gray-600 mb-4">¿Estás seguro de que deseas aprobar esta cuenta para pago?</p>
                
                <label for="observaciones_aprobacion" class="block text-sm font-medium text-gray-700 mb-2">
                    Observaciones (opcional)
                </label>
                <textarea name="observaciones" id="observaciones_aprobacion" rows="3" 
                          class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                          placeholder="Observaciones sobre la aprobación..."></textarea>
            </div>
            
            <div class="flex space-x-3">
                <button type="button" onclick="cerrarAprobarModal()" 
                        class="flex-1 py-2 px-4 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 font-medium">
                    Cancelar
                </button>
                <button type="submit" 
                        class="flex-1 py-2 px-4 bg-green-600 hover:bg-green-700 text-white rounded-xl font-medium">
                    <i class="fas fa-check mr-2"></i>
                    Aprobar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal para rechazar cuenta -->
<div id="rechazarModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-semibold text-gray-800">Rechazar Cuenta</h3>
            <button onclick="cerrarRechazarModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="rechazarForm" method="POST">
            @csrf
            <input type="hidden" name="estado" value="rechazado">
            
            <div class="mb-4">
                <label for="observaciones_rechazo" class="block text-sm font-medium text-gray-700 mb-2">
                    Motivo del rechazo (obligatorio)
                </label>
                <textarea name="observaciones" id="observaciones_rechazo" rows="4" 
                          class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                          placeholder="Especifica el motivo del rechazo..." required></textarea>
            </div>
            
            <div class="flex space-x-3">
                <button type="button" onclick="cerrarRechazarModal()" 
                        class="flex-1 py-2 px-4 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 font-medium">
                    Cancelar
                </button>
                <button type="submit" 
                        class="flex-1 py-2 px-4 bg-red-600 hover:bg-red-700 text-white rounded-xl font-medium">
                    <i class="fas fa-times mr-2"></i>
                    Rechazar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal para marcar como pagada -->
<div id="pagoModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-semibold text-gray-800">Confirmar Pago</h3>
            <button onclick="cerrarPagoModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="pagoForm" method="POST">
            @csrf
            
            <div class="mb-4">
                <p class="text-gray-600 mb-4">¿Estás seguro de que deseas marcar esta cuenta como pagada?</p>
                
                <label for="observaciones_pago" class="block text-sm font-medium text-gray-700 mb-2">
                    Observaciones del pago (opcional)
                </label>
                <textarea name="observaciones_pago" id="observaciones_pago" rows="3" 
                          class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                          placeholder="Detalles del pago, método utilizado, etc..."></textarea>
            </div>
            
            <div class="flex space-x-3">
                <button type="button" onclick="cerrarPagoModal()" 
                        class="flex-1 py-2 px-4 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 font-medium">
                    Cancelar
                </button>
                <button type="submit" 
                        class="flex-1 py-2 px-4 bg-purple-600 hover:bg-purple-700 text-white rounded-xl font-medium">
                    <i class="fas fa-credit-card mr-2"></i>
                    Confirmar Pago
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function aprobarCuenta(cuentaId) {
    const modal = document.getElementById('aprobarModal');
    const form = document.getElementById('aprobarForm');
    
    form.action = `/tesoreria/estado/${cuentaId}`;
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function rechazarCuenta(cuentaId) {
    const modal = document.getElementById('rechazarModal');
    const form = document.getElementById('rechazarForm');
    
    form.action = `/tesoreria/estado/${cuentaId}`;
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function marcarPagada(cuentaId) {
    const modal = document.getElementById('pagoModal');
    const form = document.getElementById('pagoForm');
    
    form.action = `/tesoreria/marcar-pagada/${cuentaId}`;
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function cerrarAprobarModal() {
    const modal = document.getElementById('aprobarModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
    document.getElementById('observaciones_aprobacion').value = '';
}

function cerrarRechazarModal() {
    const modal = document.getElementById('rechazarModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
    document.getElementById('observaciones_rechazo').value = '';
}

function cerrarPagoModal() {
    const modal = document.getElementById('pagoModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
    document.getElementById('observaciones_pago').value = '';
}

// Cerrar modales al hacer clic fuera
['aprobarModal', 'rechazarModal', 'pagoModal'].forEach(modalId => {
    document.getElementById(modalId).addEventListener('click', function(e) {
        if (e.target === this) {
            if (modalId === 'aprobarModal') cerrarAprobarModal();
            else if (modalId === 'rechazarModal') cerrarRechazarModal();
            else if (modalId === 'pagoModal') cerrarPagoModal();
        }
    });
});

// Auto-submit search form on input change (with debounce)
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[name="search"]');
    let searchTimeout;
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if (this.value.length >= 3 || this.value.length === 0) {
                    this.form.submit();
                }
            }, 500);
        });
    }
});
</script>
@endpush
