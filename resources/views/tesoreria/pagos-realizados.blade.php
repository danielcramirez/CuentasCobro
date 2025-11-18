@extends('layouts.dashboard')

@section('title', 'Pagos Realizados - Tesorería')

@section('breadcrumb')
    @include('components.navigation.breadcrumb', [
        'items' => [
            ['label' => 'Dashboard Tesorería', 'url' => route('tesoreria.dashboard')],
            ['label' => 'Pagos Realizados']
        ]
    ])
@endsection

@section('dashboard-header')
    <div class="glass-card p-6 slide-up">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="gradient-secondary w-16 h-16 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-check-double text-white text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Pagos Realizados</h1>
                    <p class="text-gray-600">Historial completo de pagos procesados</p>
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
    <!-- Estadísticas de pagos -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="glass-card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Pagos</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $estadisticasPagos['total_pagos'] }}</p>
                    <p class="text-sm text-green-600 flex items-center mt-1">
                        <i class="fas fa-check-circle mr-1"></i>
                        Procesados
                    </p>
                </div>
                <div class="bg-gradient-to-br from-green-400 to-green-600 w-12 h-12 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-double text-white"></i>
                </div>
            </div>
        </div>
        
        <div class="glass-card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Valor Total</p>
                    <p class="text-2xl font-bold text-gray-800">${{ number_format($estadisticasPagos['valor_total'], 0, ',', '.') }}</p>
                    <p class="text-sm text-blue-600 flex items-center mt-1">
                        <i class="fas fa-dollar-sign mr-1"></i>
                        COP
                    </p>
                </div>
                <div class="bg-gradient-to-br from-blue-400 to-blue-600 w-12 h-12 rounded-xl flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-white"></i>
                </div>
            </div>
        </div>
        
        <div class="glass-card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Este Mes</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $estadisticasPagos['pagos_mes_actual'] }}</p>
                    <p class="text-sm text-purple-600 flex items-center mt-1">
                        <i class="fas fa-calendar mr-1"></i>
                        Pagos
                    </p>
                </div>
                <div class="bg-gradient-to-br from-purple-400 to-purple-600 w-12 h-12 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar text-white"></i>
                </div>
            </div>
        </div>
        
        <div class="glass-card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Valor Mes</p>
                    <p class="text-xl font-bold text-gray-800">${{ number_format($estadisticasPagos['valor_mes_actual'], 0, ',', '.') }}</p>
                    <p class="text-sm text-orange-600 flex items-center mt-1">
                        <i class="fas fa-trend-up mr-1"></i>
                        COP
                    </p>
                </div>
                <div class="bg-gradient-to-br from-orange-400 to-orange-600 w-12 h-12 rounded-xl flex items-center justify-center">
                    <i class="fas fa-trend-up text-white"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros de búsqueda -->
    <div class="glass-card p-6 mb-8">
        <form method="GET" action="{{ route('tesoreria.pagos-realizados') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Búsqueda general -->
                <div class="md:col-span-2">
                    <div class="relative">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Buscar por proyecto o contratista..."
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>
                
                <!-- Filtro por mes -->
                <div>
                    <select name="mes" 
                            class="w-full py-3 px-4 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="">Todos los meses</option>
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ request('mes') == $i ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($i)->format('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                
                <!-- Filtro por año -->
                <div>
                    <select name="año" 
                            class="w-full py-3 px-4 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="">Todos los años</option>
                        @for($año = date('Y'); $año >= date('Y') - 3; $año--)
                            <option value="{{ $año }}" {{ request('año') == $año ? 'selected' : '' }}>
                                {{ $año }}
                            </option>
                        @endfor
                    </select>
                </div>
            </div>
            
            <div class="flex gap-3">
                <button type="submit" 
                        class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl font-medium transition-all flex items-center">
                    <i class="fas fa-search mr-2"></i>
                    Buscar
                </button>
                
                @if(request()->hasAny(['search', 'mes', 'año']))                    <a href="{{ route('tesoreria.pagos-realizados') }}" 
                       class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition-all flex items-center">
                        <i class="fas fa-times mr-2"></i>
                        Limpiar
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Lista de pagos -->
    @if($pagos->isEmpty())
        <div class="glass-card p-12 text-center">
            <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-receipt text-green-500 text-3xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-800 mb-2">No hay pagos realizados</h3>
            <p class="text-gray-600">
                @if(request()->hasAny(['search', 'mes', 'año']))
                    No se encontraron pagos que coincidan con los filtros aplicados
                @else
                    Aún no se han procesado pagos en el sistema
                @endif
            </p>
        </div>
    @else
        <div class="glass-card overflow-hidden">
            <!-- Header de la tabla -->
            <div class="bg-gradient-to-r from-green-600 to-emerald-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-white">Historial de Pagos</h2>
                    <div class="text-green-100 text-sm">
                        {{ $pagos->total() }} pagos realizados
                    </div>
                </div>
            </div>

            <!-- Tabla responsive -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Pago
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Contratista
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Valor
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fecha Pago
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($pagos as $pago)
                            <tr class="hover:bg-green-50 transition-colors">
                                <!-- Información del pago -->
                                <td class="px-6 py-4">
                                    <div class="flex items-start space-x-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center">
                                            <i class="fas fa-check text-white"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-800">Cuenta #{{ $pago->id }}</h4>
                                            <p class="text-sm text-gray-600">{{ Str::limit($pago->proyecto_servicio, 50) }}</p>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 mt-1">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                PAGADO
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                
                                <!-- Contratista -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                            <span class="text-white font-bold text-xs">
                                                {{ strtoupper(substr($pago->user->name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-800">{{ $pago->user->name }}</h4>
                                            <p class="text-sm text-gray-500">{{ $pago->user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                
                                <!-- Valor -->
                                <td class="px-6 py-4">
                                    <div class="text-lg font-bold text-green-700">
                                        ${{ number_format($pago->valor, 0, ',', '.') }}
                                    </div>
                                    <div class="text-sm text-gray-500">COP</div>
                                </td>
                                
                                <!-- Fecha de pago -->
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-800">
                                        {{ $pago->updated_at->format('d/m/Y H:i') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $pago->updated_at->diffForHumans() }}
                                    </div>
                                </td>
                                
                                <!-- Acciones -->
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <!-- Ver detalles -->
                                        <a href="{{ route('cuentas-cobro.ver', $pago->id) }}" 
                                           class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-800 rounded-lg hover:bg-blue-200 transition-colors text-sm font-medium">
                                            <i class="fas fa-eye mr-1"></i>
                                            Ver
                                        </a>
                                        
                                        <!-- Descargar archivo -->
                                        @if($pago->archivo_url)
                                            <a href="{{ $pago->archivo_url }}" target="_blank"
                                               class="inline-flex items-center px-3 py-1 bg-purple-100 text-purple-800 rounded-lg hover:bg-purple-200 transition-colors text-sm font-medium">
                                                <i class="fas fa-download mr-1"></i>
                                                PDF
                                            </a>
                                        @endif
                                        
                                        <!-- Comprobante -->
                                        <button onclick="mostrarComprobante({{ $pago->id }})" 
                                                class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 rounded-lg hover:bg-green-200 transition-colors text-sm font-medium">
                                            <i class="fas fa-receipt mr-1"></i>
                                            Comprobante
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($pagos->hasPages())
                <div class="bg-gray-50 px-6 py-4">
                    {{ $pagos->links() }}
                </div>
            @endif
        </div>
    @endif
@endsection

<!-- Modal de comprobante de pago -->
<div id="comprobanteModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-semibold text-gray-800">Comprobante de Pago</h3>
            <button onclick="cerrarComprobanteModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div id="comprobanteContent" class="text-center py-8">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-receipt text-green-600 text-2xl"></i>
            </div>
            <h4 class="text-lg font-semibold text-gray-800 mb-2">Pago Procesado</h4>
            <p class="text-gray-600 mb-4">El pago ha sido procesado exitosamente</p>
            
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                <div class="text-sm text-gray-600">
                    <p><strong>Estado:</strong> Pagado</p>
                    <p><strong>Método:</strong> Transferencia Bancaria</p>
                    <p><strong>Procesado por:</strong> Tesorería Municipal</p>
                </div>
            </div>
            
            <button onclick="cerrarComprobanteModal()" 
                    class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-xl font-medium transition-colors">
                Cerrar
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function mostrarComprobante(pagoId) {
    const modal = document.getElementById('comprobanteModal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function cerrarComprobanteModal() {
    const modal = document.getElementById('comprobanteModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Cerrar modal al hacer clic fuera
document.getElementById('comprobanteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarComprobanteModal();
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
