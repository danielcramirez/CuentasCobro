@extends('layouts.app')

@section('title', 'Cuentas de Cobro - Supervisor')

@section('content')
<!-- Contenedor principal con padding superior para el navbar fijo -->
<div class="pt-24 pb-8 px-4 sm:px-6 lg:px-8 min-h-screen">
    
    <!-- Breadcrumb de navegación -->
    <div class="max-w-7xl mx-auto mb-4">
        <nav class="flex items-center space-x-2 text-sm text-gray-500" aria-label="Breadcrumb">
            <a href="{{ route('dashboard') }}" class="hover:text-gray-700 transition-colors flex items-center">
                <i class="fas fa-home mr-1"></i>
                Inicio
            </a>
            <i class="fas fa-chevron-right text-gray-300"></i>
            <a href="{{ route('supervisor.dashboard') }}" class="hover:text-gray-700 transition-colors">
                Dashboard Supervisor
            </a>
            <i class="fas fa-chevron-right text-gray-300"></i>
            <span class="text-gray-700 font-medium">Cuentas de Cobro</span>
        </nav>
    </div>

    <!-- Header de la página -->
    <div class="max-w-7xl mx-auto mb-8">
        <div class="glass-card p-6 slide-up">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center space-x-4 mb-4 lg:mb-0">
                    <div class="gradient-primary w-12 h-12 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-clipboard-check text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl lg:text-3xl font-bold text-gray-800">Supervisión de Cuentas</h1>
                        <p class="text-gray-600">Revisa, aprueba o rechaza las cuentas de cobro</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-3">
                    <a href="{{ route('supervisor.dashboard') }}" 
                       class="bg-gray-100 text-gray-700 px-4 py-2 rounded-xl hover:bg-gray-200 transition-colors font-medium flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Volver al Dashboard
                    </a>
                    <button onclick="window.location.reload()" 
                            class="gradient-primary text-white px-4 py-2 rounded-xl hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300 font-medium flex items-center">
                        <i class="fas fa-sync-alt mr-2"></i>
                        Actualizar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas rápidas -->
    <div class="max-w-7xl mx-auto mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total de cuentas -->
            <div class="glass-card p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Total Cuentas</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $cuentas->total() }}</p>
                        <p class="text-sm text-blue-600 flex items-center mt-1">
                            <i class="fas fa-file-invoice mr-1"></i>
                            En el sistema
                        </p>
                    </div>
                    <div class="bg-gradient-to-br from-blue-400 to-indigo-600 w-12 h-12 rounded-xl flex items-center justify-center">
                        <i class="fas fa-file-alt text-white"></i>
                    </div>
                </div>
            </div>
            
            <!-- Pendientes -->
            <div class="glass-card p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Pendientes</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $cuentas->where('estado', 'pendiente')->count() }}</p>
                        <p class="text-sm text-orange-600 flex items-center mt-1">
                            <i class="fas fa-clock mr-1"></i>
                            Esperando revisión
                        </p>
                    </div>
                    <div class="bg-gradient-to-br from-orange-400 to-red-500 w-12 h-12 rounded-xl flex items-center justify-center">
                        <i class="fas fa-hourglass-half text-white"></i>
                    </div>
                </div>
            </div>
            
            <!-- En revisión -->
            <div class="glass-card p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">En Revisión</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $cuentas->where('estado', 'revision')->count() }}</p>
                        <p class="text-sm text-blue-600 flex items-center mt-1">
                            <i class="fas fa-search mr-1"></i>
                            Siendo evaluadas
                        </p>
                    </div>
                    <div class="bg-gradient-to-br from-blue-400 to-indigo-600 w-12 h-12 rounded-xl flex items-center justify-center">
                        <i class="fas fa-search text-white"></i>
                    </div>
                </div>
            </div>
            
            <!-- Aprobadas -->
            <div class="glass-card p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Aprobadas</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $cuentas->where('estado', 'aprobado')->count() }}</p>
                        <p class="text-sm text-green-600 flex items-center mt-1">
                            <i class="fas fa-check-circle mr-1"></i>
                            Listas para pago
                        </p>
                    </div>
                    <div class="bg-gradient-to-br from-green-400 to-emerald-600 w-12 h-12 rounded-xl flex items-center justify-center">
                        <i class="fas fa-check text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros y búsqueda -->
    <div class="max-w-7xl mx-auto mb-8">
        <div class="glass-card p-6">
            <form method="GET" action="{{ route('supervisor.cuentas-cobro.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Búsqueda -->
                <div class="md:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Búsqueda</label>
                    <div class="relative">
                        <input type="text" 
                               id="search" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Buscar por proyecto, contratista o descripción..."
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <!-- Filtro por estado -->
                <div>
                    <label for="estado" class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                    <select id="estado" 
                            name="estado" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <option value="">Todos los estados</option>
                        <option value="pendiente" {{ request('estado') === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="revision" {{ request('estado') === 'revision' ? 'selected' : '' }}>En Revisión</option>
                        <option value="aprobado" {{ request('estado') === 'aprobado' ? 'selected' : '' }}>Aprobado</option>
                        <option value="rechazado" {{ request('estado') === 'rechazado' ? 'selected' : '' }}>Rechazado</option>
                        <option value="pagado" {{ request('estado') === 'pagado' ? 'selected' : '' }}>Pagado</option>
                    </select>
                </div>

                <!-- Filtro por mes -->
                <div>
                    <label for="mes" class="block text-sm font-medium text-gray-700 mb-2">Mes</label>
                    <select id="mes" 
                            name="mes" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <option value="">Todos los meses</option>
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ request('mes') == $i ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>

                <!-- Botones -->
                <div class="md:col-span-4 flex space-x-3">
                    <button type="submit" 
                            class="gradient-primary text-white px-6 py-3 rounded-xl hover:shadow-lg transition-all duration-300 font-medium flex items-center">
                        <i class="fas fa-search mr-2"></i>
                        Filtrar
                    </button>
                    <a href="{{ route('supervisor.cuentas-cobro.index') }}" 
                       class="bg-gray-100 text-gray-700 px-6 py-3 rounded-xl hover:bg-gray-200 transition-colors font-medium flex items-center">
                        <i class="fas fa-times mr-2"></i>
                        Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de cuentas -->
    <div class="max-w-7xl mx-auto">
        <div class="glass-card overflow-hidden">
            @if($cuentas->count() > 0)
                <!-- Header de la tabla -->
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-800">
                            Cuentas de Cobro ({{ $cuentas->total() }} total{{ $cuentas->total() !== 1 ? 'es' : '' }})
                        </h3>
                        <div class="text-sm text-gray-600">
                            Mostrando {{ $cuentas->firstItem() }} a {{ $cuentas->lastItem() }} de {{ $cuentas->total() }}
                        </div>
                    </div>
                </div>

                <!-- Tabla responsive -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Fecha / Contratista
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Proyecto/Servicio
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Valor
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Estado
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Archivo
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($cuentas as $cuenta)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $cuenta->fecha_emision->format('d/m/Y') }}
                                            </div>
                                            <div class="text-sm text-gray-600">
                                                {{ $cuenta->user->name }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                #{{ str_pad($cuenta->id, 6, '0', STR_PAD_LEFT) }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="max-w-xs">
                                            <p class="font-medium text-gray-900 truncate">{{ $cuenta->proyecto_servicio }}</p>
                                            @if($cuenta->descripcion)
                                                <p class="text-sm text-gray-500 truncate">{{ Str::limit($cuenta->descripcion, 50) }}</p>
                                            @endif
                                            <p class="text-xs text-gray-400">{{ $cuenta->created_at->diffForHumans() }}</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        ${{ number_format($cuenta->valor, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @switch($cuenta->estado)
                                                @case('borrador')
                                                    bg-gray-100 text-gray-800
                                                    @break
                                                @case('pendiente')
                                                    bg-orange-100 text-orange-800
                                                    @break
                                                @case('revision')
                                                    bg-blue-100 text-blue-800
                                                    @break
                                                @case('aprobado')
                                                    bg-green-100 text-green-800
                                                    @break
                                                @case('pagado')
                                                    bg-emerald-100 text-emerald-800
                                                    @break
                                                @case('rechazado')
                                                    bg-red-100 text-red-800
                                                    @break
                                                @default
                                                    bg-gray-100 text-gray-800
                                            @endswitch
                                        ">
                                            @switch($cuenta->estado)
                                                @case('borrador')
                                                    <i class="fas fa-edit mr-1"></i>
                                                    @break
                                                @case('pendiente')
                                                    <i class="fas fa-clock mr-1"></i>
                                                    @break
                                                @case('revision')
                                                    <i class="fas fa-search mr-1"></i>
                                                    @break
                                                @case('aprobado')
                                                    <i class="fas fa-check mr-1"></i>
                                                    @break
                                                @case('pagado')
                                                    <i class="fas fa-check-circle mr-1"></i>
                                                    @break
                                                @case('rechazado')
                                                    <i class="fas fa-times mr-1"></i>
                                                    @break
                                            @endswitch
                                            {{ ucfirst($cuenta->estado) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($cuenta->archivo_url)
                                            <a href="{{ $cuenta->archivo_url }}" 
                                               target="_blank" 
                                               class="text-blue-600 hover:text-blue-900 flex items-center">
                                                <i class="fas fa-file-pdf mr-1"></i>
                                                Descargar
                                            </a>
                                        @else
                                            <span class="text-gray-400">Sin archivo</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            <!-- Ver detalles -->
                                            <a href="{{ route('supervisor.cuentas-cobro.show', $cuenta->id) }}" 
                                               class="text-blue-600 hover:text-blue-900 p-2 rounded-lg hover:bg-blue-50 transition-colors"
                                               title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            <!-- Acciones rápidas según estado -->
                                            @if(in_array($cuenta->estado, ['pendiente', 'revision']))
                                                <form method="POST" 
                                                      action="{{ route('supervisor.cuentas-cobro.update', $cuenta->id) }}" 
                                                      class="inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="estado" value="aprobado">
                                                    <button type="submit" 
                                                            class="text-green-600 hover:text-green-900 p-2 rounded-lg hover:bg-green-50 transition-colors"
                                                            title="Aprobar"
                                                            onclick="return confirm('¿Estás seguro de aprobar esta cuenta?')">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                
                                                <button onclick="openRejectModal({{ $cuenta->id }})" 
                                                        class="text-red-600 hover:text-red-900 p-2 rounded-lg hover:bg-red-50 transition-colors"
                                                        title="Rechazar">
                                                    <i class="fas fa-times"></i>
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
                @if($cuentas->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $cuentas->links() }}
                </div>
                @endif
            @else
                <!-- Estado vacío -->
                <div class="text-center py-12">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-inbox text-gray-400 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-600 mb-2">No hay cuentas de cobro</h3>
                    <p class="text-gray-500">
                        @if(request()->has('search') || request()->has('estado') || request()->has('mes'))
                            No se encontraron cuentas con los filtros aplicados.
                        @else
                            No hay cuentas de cobro en el sistema actualmente.
                        @endif
                    </p>
                    @if(request()->has('search') || request()->has('estado') || request()->has('mes'))
                        <a href="{{ route('supervisor.cuentas-cobro.index') }}" 
                           class="inline-flex items-center mt-4 text-blue-600 hover:text-blue-700">
                            <i class="fas fa-times mr-2"></i>
                            Limpiar filtros
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal para rechazar cuenta -->
<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Rechazar Cuenta de Cobro</h3>
                <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="rejectForm" method="POST" action="">
                @csrf
                @method('PUT')
                <input type="hidden" name="estado" value="rechazado">
                
                <div class="mb-4">
                    <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-2">
                        Observaciones del rechazo *
                    </label>
                    <textarea id="observaciones" 
                              name="observaciones" 
                              rows="4"
                              required
                              placeholder="Explica las razones del rechazo..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
                
                <div class="flex space-x-3">
                    <button type="submit" 
                            class="flex-1 bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                        <i class="fas fa-times mr-2"></i>
                        Rechazar
                    </button>
                    <button type="button" 
                            onclick="closeRejectModal()"
                            class="flex-1 bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openRejectModal(cuentaId) {
    document.getElementById('rejectForm').action = `/supervisor/cuentas-cobro/${cuentaId}`;
    document.getElementById('rejectModal').classList.remove('hidden');
    document.getElementById('observaciones').focus();
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('observaciones').value = '';
}

// Cerrar modal con ESC
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeRejectModal();
    }
});
</script>

<style>
.gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.glass-card {
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    background: rgba(255, 255, 255, 0.7);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.glass-card:hover {
    background: rgba(255, 255, 255, 0.8);
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
</style>
@endsection
