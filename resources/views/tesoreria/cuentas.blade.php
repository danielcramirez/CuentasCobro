@extends('layouts.dashboard')

@section('title', 'Gestión de Cuentas - Tesorería')

@section('breadcrumb')
    @include('components.navigation.breadcrumb', [
        'items' => [
            ['label' => 'Dashboard Tesorería', 'url' => route('tesoreria.dashboard')],
            ['label' => 'Gestión de Cuentas']
        ]
    ])
@endsection

@section('dashboard-header')
    <div class="glass-card p-6 slide-up">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="gradient-secondary w-16 h-16 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-list-alt text-white text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Gestión de Cuentas</h1>
                    <p class="text-gray-600">Administración y control de todas las cuentas de cobro</p>
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
    <!-- Filtros de búsqueda -->
    <div class="glass-card p-6 mb-8">
        <form method="GET" action="{{ route('tesoreria.cuentas') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Búsqueda general -->
                <div class="md:col-span-2">
                    <div class="relative">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Buscar por proyecto, contratista..."
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>
                
                <!-- Filtro por estado -->
                <div>
                    <select name="estado" 
                            class="w-full py-3 px-4 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Todos los estados</option>
                        <option value="borrador" {{ request('estado') === 'borrador' ? 'selected' : '' }}>Borrador</option>
                        <option value="pendiente_supervisor" {{ request('estado') === 'pendiente_supervisor' ? 'selected' : '' }}>Pendiente Supervisor</option>
                        <option value="pendiente_contratacion" {{ request('estado') === 'pendiente_contratacion' ? 'selected' : '' }}>Pendiente Contratación</option>
                        <option value="pendiente_tesoreria" {{ request('estado') === 'pendiente_tesoreria' ? 'selected' : '' }}>Pendiente Tesorería</option>
                        <option value="pendiente_ordenador" {{ request('estado') === 'pendiente_ordenador' ? 'selected' : '' }}>Pendiente Ordenador</option>
                        <option value="aprobada" {{ request('estado') === 'aprobada' ? 'selected' : '' }}>Aprobada</option>
                        <option value="rechazada" {{ request('estado') === 'rechazada' ? 'selected' : '' }}>Rechazada</option>
                        <option value="pagada" {{ request('estado') === 'pagada' ? 'selected' : '' }}>Pagada</option>
                    </select>
                </div>
                
                <!-- Filtro por mes -->
                <div>
                    <select name="mes" 
                            class="w-full py-3 px-4 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Todos los meses</option>
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ request('mes') == $i ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($i)->format('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
            </div>
            
            <div class="flex gap-3">
                <button type="submit" 
                        class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium transition-all flex items-center">
                    <i class="fas fa-search mr-2"></i>
                    Buscar
                </button>
                
                @if(request()->hasAny(['search', 'estado', 'mes']))
                    <a href="{{ route('tesoreria.cuentas') }}" 
                       class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition-all flex items-center">
                        <i class="fas fa-times mr-2"></i>
                        Limpiar
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Lista de cuentas -->
    @if($cuentas->isEmpty())
        <div class="glass-card p-12 text-center">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-file-invoice text-gray-400 text-3xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-800 mb-2">No hay cuentas</h3>
            <p class="text-gray-600">
                @if(request()->hasAny(['search', 'estado', 'mes']))
                    No se encontraron cuentas que coincidan con los filtros aplicados
                @else
                    No hay cuentas de cobro registradas en el sistema
                @endif
            </p>
        </div>
    @else
        <div class="glass-card overflow-hidden">
            <!-- Header de la tabla -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-white">Lista de Cuentas de Cobro</h2>
                    <div class="text-blue-100 text-sm">
                        {{ $cuentas->total() }} cuentas encontradas
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
                        @foreach($cuentas as $cuenta)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <!-- Información de la cuenta -->
                                <td class="px-6 py-4">
                                    <div class="flex items-start space-x-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                            <span class="text-white font-bold text-sm">
                                                {{ strtoupper(substr($cuenta->user->name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-800">{{ $cuenta->user->name }}</h4>
                                            <p class="text-sm text-gray-600">ID: #{{ $cuenta->id }}</p>
                                            <p class="text-sm text-gray-500">{{ Str::limit($cuenta->proyecto_servicio, 50) }}</p>
                                        </div>
                                    </div>
                                </td>
                                
                                <!-- Estado -->
                                <td class="px-6 py-4">
                                    @php
                                        $badges = [
                                            'borrador' => 'bg-gray-100 text-gray-800',
                                            'pendiente_supervisor' => 'bg-yellow-100 text-yellow-800',
                                            'pendiente_contratacion' => 'bg-blue-100 text-blue-800',
                                            'pendiente_tesoreria' => 'bg-orange-100 text-orange-800',
                                            'pendiente_ordenador' => 'bg-purple-100 text-purple-800',
                                            'aprobada' => 'bg-green-100 text-green-800',
                                            'rechazada' => 'bg-red-100 text-red-800',
                                            'pagada' => 'bg-indigo-100 text-indigo-800',
                                        ];
                                        $badgeClass = $badges[$cuenta->estado] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $badgeClass }}">
                                        <i class="fas fa-circle text-xs mr-2"></i>
                                        {{ ucfirst(str_replace('_', ' ', $cuenta->estado)) }}
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
                                        <a href="{{ route('tesoreria.show', $cuenta->id) }}" 
                                           class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-800 rounded-lg hover:bg-blue-200 transition-colors text-sm font-medium">
                                            <i class="fas fa-eye mr-1"></i>
                                            Ver
                                        </a>
                                        
                                        <!-- Marcar como pagada (solo si está aprobada) -->
                                        @if($cuenta->estado === 'aprobada')
                                            <button onclick="marcarPagada({{ $cuenta->id }})" 
                                                    class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 rounded-lg hover:bg-green-200 transition-colors text-sm font-medium">
                                                <i class="fas fa-check mr-1"></i>
                                                Pagar
                                            </button>
                                        @endif
                                        
                                        <!-- Descargar archivo -->
                                        @if($cuenta->archivo_adjunto)
                                            <a href="{{ Storage::url($cuenta->archivo_adjunto) }}" target="_blank"
                                               class="inline-flex items-center px-3 py-1 bg-purple-100 text-purple-800 rounded-lg hover:bg-purple-200 transition-colors text-sm font-medium">
                                                <i class="fas fa-download mr-1"></i>
                                                PDF
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($cuentas->hasPages())
                <div class="bg-gray-50 px-6 py-4">
                    {{ $cuentas->links() }}
                </div>
            @endif
        </div>
    @endif
@endsection

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
                          class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                          placeholder="Detalles del pago, método utilizado, etc..."></textarea>
            </div>
            
            <div class="flex space-x-3">
                <button type="button" onclick="cerrarPagoModal()" 
                        class="flex-1 py-2 px-4 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 font-medium">
                    Cancelar
                </button>
                <button type="submit" 
                        class="flex-1 py-2 px-4 bg-green-600 hover:bg-green-700 text-white rounded-xl font-medium">
                    <i class="fas fa-check mr-2"></i>
                    Confirmar Pago
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function marcarPagada(cuentaId) {
    const modal = document.getElementById('pagoModal');
    const form = document.getElementById('pagoForm');
    
    form.action = `/tesoreria/marcar-pagada/${cuentaId}`;
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function cerrarPagoModal() {
    const modal = document.getElementById('pagoModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
    document.getElementById('observaciones_pago').value = '';
}

// Cerrar modal al hacer clic fuera
document.getElementById('pagoModal').addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarPagoModal();
    }
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
