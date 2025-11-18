@extends('layouts.app')

@section('title', 'Cuentas de Cobro - CuentasCobro')

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
            @if(auth()->user()->hasRole('contratista'))
                <a href="{{ route('contratista.dashboard') }}" class="hover:text-gray-700 transition-colors">
                    Dashboard Contratista
                </a>
                <i class="fas fa-chevron-right text-gray-300"></i>
            @endif
            <span class="text-gray-700 font-medium">Cuentas de Cobro</span>
        </nav>
    </div>

    @if(session('error'))
        <div id="error-popup" class="fixed inset-x-0 top-24 flex justify-center z-50 pointer-events-auto">
            <div class="w-full max-w-2xl mx-4 glass-card p-4 flex items-start space-x-4 shadow-lg transition transform duration-300 opacity-100" role="alert">
                <div class="text-red-600 mt-1">
                    <i class="fas fa-exclamation-circle text-2xl"></i>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-gray-800">Atención</p>
                    <p class="text-sm text-gray-600 mt-1">{{ session('error') }}</p>
                </div>
                <button type="button" onclick="closeErrorPopup()" class="text-gray-400 hover:text-gray-600 ml-2 p-1">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    @if(session('success'))
        <div id="success-popup" class="fixed inset-x-0 top-24 flex justify-center z-50 pointer-events-auto">
            <div class="w-full max-w-2xl mx-4 glass-card p-4 flex items-start space-x-4 shadow-lg transition transform duration-300 opacity-100 bg-green-50 border border-green-200" role="alert">
                <div class="text-green-600 mt-1">
                    <i class="fas fa-check-circle text-2xl"></i>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-gray-800">¡Éxito!</p>
                    <p class="text-sm text-gray-600 mt-1">{{ session('success') }}</p>
                </div>
                <button type="button" onclick="closeSuccessPopup()" class="text-gray-400 hover:text-gray-600 ml-2 p-1">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    <!-- Header de la página -->
    <div class="max-w-7xl mx-auto mb-8">
        <div class="glass-card p-4 sm:p-6 slide-up">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="flex items-center space-x-3 sm:space-x-4">
                    <div class="gradient-primary w-12 h-12 sm:w-16 sm:h-16 rounded-2xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-file-invoice text-white text-lg sm:text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-800 font-poppins">Cuentas de Cobro</h1>
                        <p class="text-sm sm:text-base text-gray-600">Gestiona y consulta todas las cuentas de cobro</p>
                    </div>
                </div>
                
                <!-- Acciones principales -->
                <div class="flex flex-col sm:flex-row gap-3">
                    @if(auth()->user()->hasRole('contratista'))
                        <a href="{{ route('contratista.dashboard') }}" 
                           class="bg-purple-600 hover:bg-purple-700 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-xl font-medium shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 flex items-center justify-center text-sm sm:text-base">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Volver al Dashboard
                        </a>
                    @endif
                    <a href="{{ route('cuentas-cobro.crear') }}" 
                       class="gradient-primary text-white px-4 sm:px-6 py-2 sm:py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 flex items-center justify-center text-sm sm:text-base">
                        <i class="fas fa-plus mr-2"></i>
                        Nueva Cuenta
                    </a>
                    <button onclick="exportData()" class="bg-white/70 text-gray-700 px-4 sm:px-6 py-2 sm:py-3 rounded-xl border border-gray-200 hover:bg-white hover:shadow-md transition-all duration-300 font-medium flex items-center justify-center text-sm sm:text-base">
                        <i class="fas fa-download mr-2"></i>
                        Exportar
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Estadísticas rápidas -->
    <div class="max-w-7xl mx-auto mb-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="glass-card p-6 text-center">
                <div class="text-2xl font-bold text-gray-800">{{ $cuentas->count() }}</div>
                <div class="text-sm text-gray-600">Total Cuentas</div>
            </div>
            <div class="glass-card p-6 text-center">
                <div class="text-2xl font-bold text-yellow-600">{{ $cuentas->where('estado', 'pendiente')->count() }}</div>
                <div class="text-sm text-gray-600">Pendientes</div>
            </div>
            <div class="glass-card p-6 text-center">
                <div class="text-2xl font-bold text-green-600">{{ $cuentas->where('estado', 'aprobada')->count() }}</div>
                <div class="text-sm text-gray-600">Aprobadas</div>
            </div>
            <div class="glass-card p-6 text-center">
                <div class="text-2xl font-bold text-blue-600">${{ number_format($cuentas->sum('valor'), 0, ',', '.') }}</div>
                <div class="text-sm text-gray-600">Valor Total</div>
            </div>
        </div>
    </div>
    
    <!-- Filtros y búsqueda -->
    <div class="max-w-7xl mx-auto mb-8">
        <div class="glass-card p-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                <!-- Búsqueda -->
                <div class="flex-1 max-w-md">
                    <div class="relative">
                        <input type="text" 
                               placeholder="Buscar por proyecto, usuario o número..."
                               class="w-full pl-10 pr-4 py-3 bg-white/70 border-2 border-transparent rounded-xl focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-100 transition-all duration-300"
                               id="search-input">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Filtros -->
                <div class="flex flex-wrap gap-3">
                    <select class="px-4 py-2 bg-white/70 border-2 border-transparent rounded-lg focus:border-blue-500 focus:outline-none transition-all duration-300" id="filter-estado">
                        <option value="">Todos los estados</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="aprobada">Aprobada</option>
                        <option value="rechazada">Rechazada</option>
                        <option value="pagada">Pagada</option>
                    </select>
                    
                    <select class="px-4 py-2 bg-white/70 border-2 border-transparent rounded-lg focus:border-blue-500 focus:outline-none transition-all duration-300" id="filter-fecha">
                        <option value="">Todas las fechas</option>
                        <option value="hoy">Hoy</option>
                        <option value="semana">Esta semana</option>
                        <option value="mes">Este mes</option>
                        <option value="año">Este año</option>
                    </select>
                    
                    <button class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-all duration-300" onclick="clearFilters()">
                        <i class="fas fa-times mr-1"></i>
                        Limpiar
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Contenido principal -->
    <div class="max-w-7xl mx-auto">
        @if($cuentas->isEmpty())
            <!-- Estado vacío -->
            <div class="glass-card p-12 text-center">
                <div class="w-24 h-24 bg-gray-100 rounded-full mx-auto mb-6 flex items-center justify-center">
                    <i class="fas fa-file-invoice text-gray-400 text-3xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">No hay cuentas de cobro</h3>
                <p class="text-gray-600 mb-6">Comienza creando tu primera cuenta de cobro</p>
                <a href="{{ route('cuentas-cobro.crear') }}" 
                   class="gradient-primary text-white px-8 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 inline-flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Crear Primera Cuenta
                </a>
            </div>
        @else
            <!-- Lista de cuentas (vista de escritorio) -->
            <div class="hidden lg:block glass-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <button class="flex items-center space-x-1 hover:text-gray-700 transition-colors duration-200" onclick="sortBy('numero')">
                                        <span>Número</span>
                                        <i class="fas fa-sort text-xs"></i>
                                    </button>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <button class="flex items-center space-x-1 hover:text-gray-700 transition-colors duration-200" onclick="sortBy('usuario')">
                                        <span>Usuario</span>
                                        <i class="fas fa-sort text-xs"></i>
                                    </button>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <button class="flex items-center space-x-1 hover:text-gray-700 transition-colors duration-200" onclick="sortBy('proyecto')">
                                        <span>Proyecto/Servicio</span>
                                        <i class="fas fa-sort text-xs"></i>
                                    </button>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <button class="flex items-center space-x-1 hover:text-gray-700 transition-colors duration-200" onclick="sortBy('valor')">
                                        <span>Valor</span>
                                        <i class="fas fa-sort text-xs"></i>
                                    </button>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <button class="flex items-center space-x-1 hover:text-gray-700 transition-colors duration-200" onclick="sortBy('estado')">
                                        <span>Estado</span>
                                        <i class="fas fa-sort text-xs"></i>
                                    </button>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <button class="flex items-center space-x-1 hover:text-gray-700 transition-colors duration-200" onclick="sortBy('fecha')">
                                        <span>Fecha</span>
                                        <i class="fas fa-sort text-xs"></i>
                                    </button>
                                </th>
                                <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="cuentas-table-body">
                            @foreach($cuentas as $cuenta)
                                <tr class="hover:bg-gray-50 transition-colors duration-200 cuenta-row" 
                                    data-id="{{ $cuenta->id }}"
                                    data-estado="{{ $cuenta->estado }}" 
                                    data-fecha="{{ $cuenta->fecha_emision }}"
                                    data-proyecto="{{ $cuenta->proyecto_servicio }}"
                                    data-usuario="{{ strtolower($cuenta->user->name) }}"
                                    data-descripcion="{{ e($cuenta->descripcion ?? 'Sin descripción') }}"
                                    data-filename="{{ e($cuenta->archivo_nombre ?? 'Sin archivo') }}"
                                    data-filepath="{{ e($cuenta->archivo_url ?? '') }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 rounded-full mr-3 {{ 
                                                $cuenta->estado === 'pendiente' ? 'bg-yellow-400' : 
                                                ($cuenta->estado === 'aprobada' ? 'bg-green-400' : 
                                                ($cuenta->estado === 'rechazada' ? 'bg-red-400' : 'bg-blue-400')) 
                                            }}"></div>
                                            <div class="text-sm font-medium text-gray-900">
                                                CC-{{ date('Y', strtotime($cuenta->fecha_emision)) }}-{{ str_pad($cuenta->id, 3, '0', STR_PAD_LEFT) }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full gradient-primary flex items-center justify-center">
                                                    <span class="text-white font-medium text-sm">{{ substr($cuenta->user->name, 0, 2) }}</span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $cuenta->user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ ucfirst(str_replace('_', ' ', $cuenta->user->role->name ?? 'Sin rol')) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 font-medium">{{ $cuenta->proyecto_servicio }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900">${{ number_format($cuenta->valor, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ 
                                            $cuenta->estado === 'pendiente' ? 'bg-yellow-100 text-yellow-800' : 
                                            ($cuenta->estado === 'aprobada' ? 'bg-green-100 text-green-800' : 
                                            ($cuenta->estado === 'rechazada' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800')) 
                                        }}">
                                            <i class="fas {{ 
                                                $cuenta->estado === 'pendiente' ? 'fa-clock' : 
                                                ($cuenta->estado === 'aprobada' ? 'fa-check-circle' : 
                                                ($cuenta->estado === 'rechazada' ? 'fa-times-circle' : 'fa-credit-card')) 
                                            }} mr-1"></i>
                                            {{ ucfirst($cuenta->estado) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($cuenta->fecha_emision)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <div class="flex items-center justify-center space-x-2">
                                            <button onclick="viewCuenta({{ $cuenta->id }})" 
                                                    class="text-blue-600 hover:text-blue-900 hover:bg-blue-50 p-2 rounded-lg transition-all duration-200" 
                                                    title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <a href="{{ route('cuentas-cobro.edit', $cuenta->id) }}" 
                                               class="text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50 p-2 rounded-lg transition-all duration-200" 
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button onclick="deleteCuenta({{ $cuenta->id }})" 
                                                    class="text-red-600 hover:text-red-900 hover:bg-red-50 p-2 rounded-lg transition-all duration-200" 
                                                    title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Vista móvil -->
            <div class="lg:hidden space-y-4" id="cuentas-mobile">
                @foreach($cuentas as $cuenta)
                    <div class="glass-card p-4 cuenta-card" 
                         data-id="{{ $cuenta->id }}"
                         data-estado="{{ $cuenta->estado }}" 
                         data-fecha="{{ $cuenta->fecha_emision }}"
                         data-proyecto="{{ $cuenta->proyecto_servicio }}"
                         data-usuario="{{ strtolower($cuenta->user->name) }}"
                         data-descripcion="{{ e($cuenta->descripcion ?? 'Sin descripción') }}"
                         data-filename="{{ e($cuenta->archivo_nombre ?? 'Sin archivo') }}"
                         data-filepath="{{ e($cuenta->archivo_url ?? '') }}">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-3 h-3 rounded-full {{ 
                                    $cuenta->estado === 'pendiente' ? 'bg-yellow-400' : 
                                    ($cuenta->estado === 'aprobada' ? 'bg-green-400' : 
                                    ($cuenta->estado === 'rechazada' ? 'bg-red-400' : 'bg-blue-400')) 
                                }}"></div>
                                <div>
                                    <p class="font-semibold text-gray-800">CC-{{ date('Y', strtotime($cuenta->fecha_emision)) }}-{{ str_pad($cuenta->id, 3, '0', STR_PAD_LEFT) }}</p>
                                    <p class="text-sm text-gray-600">{{ $cuenta->user->name }}</p>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ 
                                $cuenta->estado === 'pendiente' ? 'bg-yellow-100 text-yellow-800' : 
                                ($cuenta->estado === 'aprobada' ? 'bg-green-100 text-green-800' : 
                                ($cuenta->estado === 'rechazada' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800')) 
                            }}">
                                {{ ucfirst($cuenta->estado) }}
                            </span>
                        </div>
                        
                        <div class="mb-3">
                            <p class="font-medium text-gray-900">{{ $cuenta->proyecto_servicio }}</p>
                            <p class="text-sm text-gray-600">${{ number_format($cuenta->valor, 0, ',', '.') }}</p>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($cuenta->fecha_emision)->format('d/m/Y') }}</span>
                            <div class="flex space-x-2">
                                <button onclick="viewCuenta({{ $cuenta->id }})" class="text-blue-600 hover:text-blue-800 p-1">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="{{ route('cuentas-cobro.edit', $cuenta->id) }}" class="text-indigo-600 hover:text-indigo-800 p-1">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="deleteCuenta({{ $cuenta->id }})" class="text-red-600 hover:text-red-800 p-1">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

@endsection

@push('styles')
<style>
    /* Estilos adicionales para la tabla y filtros */
    .cuenta-row, .cuenta-card {
        transition: all 0.3s ease;
    }
    .cuenta-row:hover, .cuenta-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    /* Animaciones de filtrado */
    .hidden-row {
        opacity: 0;
        transform: scale(0.95);
        pointer-events: none;
    }
    /* Ajuste extra para evitar solapamiento con navbar fijo en pantallas pequeñas */
    @media (max-width: 768px) {
        .pt-32 {
            padding-top: 7rem !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
let sortDirection = {};

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar filtros
    setupFilters();
});

// Configurar filtros y búsqueda
function setupFilters() {
    const searchInput = document.getElementById('search-input');
    const estadoFilter = document.getElementById('filter-estado');
    const fechaFilter = document.getElementById('filter-fecha');
    
    [searchInput, estadoFilter, fechaFilter].forEach(element => {
        element.addEventListener('input', filterCuentas);
    });
}

// Filtrar cuentas
function filterCuentas() {
    const searchTerm = document.getElementById('search-input').value.toLowerCase();
    const estadoFilter = document.getElementById('filter-estado').value;
    const fechaFilter = document.getElementById('filter-fecha').value;
    
    const rows = document.querySelectorAll('.cuenta-row, .cuenta-card');
    
    rows.forEach(row => {
        const estado = row.dataset.estado;
        const fecha = row.dataset.fecha;
        const proyecto = row.dataset.proyecto;
        const usuario = row.dataset.usuario;
        
        let show = true;
        
        // Filtro de búsqueda
        if (searchTerm && !proyecto.includes(searchTerm) && !usuario.includes(searchTerm)) {
            show = false;
        }
        
        // Filtro de estado
        if (estadoFilter && estado !== estadoFilter) {
            show = false;
        }
        
        // Filtro de fecha
        if (fechaFilter) {
            const fechaCuenta = new Date(fecha);
            const hoy = new Date();
            
            switch(fechaFilter) {
                case 'hoy':
                    if (fechaCuenta.toDateString() !== hoy.toDateString()) show = false;
                    break;
                case 'semana':
                    const semanaAtras = new Date(hoy.getTime() - 7 * 24 * 60 * 60 * 1000);
                    if (fechaCuenta < semanaAtras) show = false;
                    break;
                case 'mes':
                    if (fechaCuenta.getMonth() !== hoy.getMonth() || fechaCuenta.getFullYear() !== hoy.getFullYear()) show = false;
                    break;
                case 'año':
                    if (fechaCuenta.getFullYear() !== hoy.getFullYear()) show = false;
                    break;
            }
        }
        
        // Aplicar filtro
        if (show) {
            row.style.display = '';
            row.classList.remove('hidden-row');
        } else {
            row.classList.add('hidden-row');
            setTimeout(() => {
                if (row.classList.contains('hidden-row')) {
                    row.style.display = 'none';
                }
            }, 300);
        }
    });
}

// Limpiar filtros
function clearFilters() {
    document.getElementById('search-input').value = '';
    document.getElementById('filter-estado').value = '';
    document.getElementById('filter-fecha').value = '';
    filterCuentas();
}

// Ordenar tabla
function sortBy(column) {
    const tableBody = document.getElementById('cuentas-table-body');
    const rows = Array.from(tableBody.querySelectorAll('tr'));
    
    const direction = sortDirection[column] === 'asc' ? 'desc' : 'asc';
    sortDirection[column] = direction;
    
    rows.sort((a, b) => {
        let aVal, bVal;
        
        switch(column) {
            case 'numero':
                aVal = a.querySelector('td:nth-child(1)').textContent.trim();
                bVal = b.querySelector('td:nth-child(1)').textContent.trim();
                break;
            case 'usuario':
                aVal = a.querySelector('td:nth-child(2) .text-sm.font-medium').textContent.trim();
                bVal = b.querySelector('td:nth-child(2) .text-sm.font-medium').textContent.trim();
                break;
            case 'proyecto':
                aVal = a.querySelector('td:nth-child(3) .text-sm.font-medium').textContent.trim();
                bVal = b.querySelector('td:nth-child(3) .text-sm.font-medium').textContent.trim();
                break;
            case 'valor':
                aVal = parseFloat(a.querySelector('td:nth-child(4)').textContent.replace(/[$,]/g, ''));
                bVal = parseFloat(b.querySelector('td:nth-child(4)').textContent.replace(/[$,]/g, ''));
                break;
            case 'estado':
                aVal = a.dataset.estado;
                bVal = b.dataset.estado;
                break;
            case 'fecha':
                aVal = new Date(a.dataset.fecha);
                bVal = new Date(b.dataset.fecha);
                break;
        }
        
        if (typeof aVal === 'string') {
            return direction === 'asc' ? aVal.localeCompare(bVal) : bVal.localeCompare(aVal);
        } else {
            return direction === 'asc' ? aVal - bVal : bVal - aVal;
        }
    });
    
    // Reorganizar filas
    rows.forEach(row => tableBody.appendChild(row));
}

// Ver detalles de cuenta
function viewCuenta(id) {
    // Redirigir a la vista de detalle de la cuenta
    window.location.href = `/cuentas-cobro/${id}`;
}



// Eliminar cuenta
function deleteCuenta(id) {
    // Redirigir a la página de confirmación de eliminación
    window.location.href = `/cuentas-cobro/${id}/eliminar`;
}



// Cerrar popup de error y eliminar del DOM con animación
function closeErrorPopup() {
    const el = document.getElementById('error-popup');
    if (!el) return;
    const card = el.querySelector('.glass-card') || el.firstElementChild;
    if (card) {
        card.classList.add('opacity-0', 'scale-95');
    }
    setTimeout(() => {
        if (el && el.parentNode) el.parentNode.removeChild(el);
    }, 260);
}

// Función para cerrar popups de éxito
function closeSuccessPopup() {
    const popup = document.getElementById('success-popup');
    if (popup) {
        popup.style.opacity = '0';
        popup.style.transform = 'translateY(-20px)';
        setTimeout(() => popup.remove(), 300);
    }
}

// Función para exportar datos
function exportData() {
    const button = event.target;
    const originalText = button.innerHTML;
    
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Exportando...';
    button.disabled = true;
    
    // Simular exportación (aquí iría la lógica real)
    setTimeout(() => {
        button.innerHTML = originalText;
        button.disabled = false;
        showToast('Datos exportados correctamente', 'success');
    }, 2000);
}

// Auto-cerrar notificaciones después de 5 segundos
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        closeErrorPopup();
        closeSuccessPopup();
    }, 5000);
});

function showToast(message, type = 'info', duration = 3000) {
    const existingToasts = document.querySelectorAll(`[data-toast-type="${type}"]`);
    existingToasts.forEach(toast => toast.remove());
    
    const toast = document.createElement('div');
    const bgColor = {
        'success': 'bg-green-500',
        'error': 'bg-red-500',
        'warning': 'bg-yellow-500',
        'info': 'bg-blue-500'
    }[type] || 'bg-gray-500';
    
    const icon = {
        'success': 'fas fa-check-circle',
        'error': 'fas fa-exclamation-circle',
        'warning': 'fas fa-exclamation-triangle',
        'info': 'fas fa-info-circle'
    }[type] || 'fas fa-info-circle';
    
    toast.setAttribute('data-toast-type', type);
    toast.className = `fixed top-4 right-4 ${bgColor} text-white px-4 sm:px-6 py-3 rounded-lg shadow-lg transform translate-x-full transition-all duration-300 z-50 max-w-sm`;
    toast.innerHTML = `
        <div class="flex items-center space-x-3">
            <i class="${icon} text-lg"></i>
            <div class="flex-1 text-sm sm:text-base font-medium">${message}</div>
            <button 
                onclick="removeToast(this.parentElement.parentElement)" 
                class="ml-2 text-white hover:text-gray-200 transition-colors focus:outline-none focus:ring-2 focus:ring-white focus:ring-opacity-50 rounded p-1"
                aria-label="Cerrar notificación">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.transform = 'translateX(0)';
    }, 100);
    
    setTimeout(() => {
        removeToast(toast);
    }, duration);
}

function removeToast(toastElement) {
    if (toastElement && toastElement.parentNode) {
        toastElement.style.transform = 'translateX(100%)';
        toastElement.style.opacity = '0';
        setTimeout(() => {
            if (toastElement.parentNode) {
                toastElement.remove();
            }
        }, 300);
    }
}
</script>
@endpush