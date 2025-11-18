@extends('layouts.app')

@section('title', 'Mis Cuentas de Cobro - Contratista')

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
            <a href="{{ route('contratista.dashboard') }}" class="hover:text-gray-700 transition-colors">
                Dashboard Contratista
            </a>
            <i class="fas fa-chevron-right text-gray-300"></i>
            <span class="text-gray-700 font-medium">Mis Cuentas de Cobro</span>
        </nav>
    </div>

    <!-- Header de la página -->
    <div class="max-w-7xl mx-auto mb-8">
        <div class="glass-card p-6 slide-up">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center space-x-4 mb-4 lg:mb-0">
                    <div class="gradient-primary w-12 h-12 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-file-invoice-dollar text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl lg:text-3xl font-bold text-gray-800">Mis Cuentas de Cobro</h1>
                        <p class="text-gray-600">Gestiona y revisa todas tus cuentas de cobro</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-3">
                    <a href="{{ route('contratista.cuentas.crear') }}" 
                       class="gradient-primary text-white px-4 py-2 rounded-xl hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300 font-medium flex items-center">
                        <i class="fas fa-plus mr-2"></i>
                        Nueva Cuenta
                    </a>
                    <button onclick="window.location.reload()" 
                            class="bg-gray-100 text-gray-700 px-4 py-2 rounded-xl hover:bg-gray-200 transition-colors font-medium flex items-center">
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
                            Registradas
                        </p>
                    </div>
                    <div class="bg-gradient-to-br from-blue-400 to-indigo-600 w-12 h-12 rounded-xl flex items-center justify-center">
                        <i class="fas fa-file-alt text-white"></i>
                    </div>
                </div>
            </div>
            
            <!-- Borradores -->
            <div class="glass-card p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Borradores</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $cuentas->where('estado', 'borrador')->count() }}</p>
                        <p class="text-sm text-gray-600 flex items-center mt-1">
                            <i class="fas fa-edit mr-1"></i>
                            Sin enviar
                        </p>
                    </div>
                    <div class="bg-gradient-to-br from-gray-400 to-gray-600 w-12 h-12 rounded-xl flex items-center justify-center">
                        <i class="fas fa-edit text-white"></i>
                    </div>
                </div>
            </div>
            
            <!-- Pendientes -->
            <div class="glass-card p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Pendientes</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $cuentas->where('estado', 'pendiente')->count() }}</p>
                        <p class="text-sm text-yellow-600 flex items-center mt-1">
                            <i class="fas fa-clock mr-1"></i>
                            En revisión
                        </p>
                    </div>
                    <div class="bg-gradient-to-br from-yellow-400 to-orange-500 w-12 h-12 rounded-xl flex items-center justify-center">
                        <i class="fas fa-clock text-white"></i>
                    </div>
                </div>
            </div>
            
            <!-- Pagadas -->
            <div class="glass-card p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Pagadas</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $cuentas->where('estado', 'pagado')->count() }}</p>
                        <p class="text-sm text-green-600 flex items-center mt-1">
                            <i class="fas fa-check-circle mr-1"></i>
                            Completadas
                        </p>
                    </div>
                    <div class="bg-gradient-to-br from-green-400 to-green-600 w-12 h-12 rounded-xl flex items-center justify-center">
                        <i class="fas fa-money-check-alt text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros y búsqueda -->
    <div class="max-w-7xl mx-auto mb-6">
        <div class="glass-card p-4">
            <form method="GET" action="{{ route('contratista.cuentas.index') }}" class="flex flex-col sm:flex-row items-start sm:items-center space-y-3 sm:space-y-0 sm:space-x-4">
                <!-- Búsqueda -->
                <div class="flex-1">
                    <div class="relative">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Buscar por proyecto o descripción..."
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Filtro por estado -->
                <div class="w-full sm:w-auto">
                    <select name="estado" class="w-full sm:w-auto px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Todos los estados</option>
                        <option value="borrador" {{ request('estado') === 'borrador' ? 'selected' : '' }}>Borrador</option>
                        <option value="pendiente" {{ request('estado') === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="revision" {{ request('estado') === 'revision' ? 'selected' : '' }}>En Revisión</option>
                        <option value="aprobado" {{ request('estado') === 'aprobado' ? 'selected' : '' }}>Aprobado</option>
                        <option value="pagado" {{ request('estado') === 'pagado' ? 'selected' : '' }}>Pagado</option>
                        <option value="rechazado" {{ request('estado') === 'rechazado' ? 'selected' : '' }}>Rechazado</option>
                    </select>
                </div>
                
                <!-- Botones -->
                <div class="flex space-x-2">
                    <button type="submit" class="gradient-primary text-white px-4 py-2 rounded-lg hover:shadow-lg transition-all duration-300 font-medium">
                        <i class="fas fa-filter mr-2"></i>
                        Filtrar
                    </button>
                    <a href="{{ route('contratista.cuentas.index') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                        <i class="fas fa-times mr-2"></i>
                        Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de cuentas de cobro -->
    <div class="max-w-7xl mx-auto">
        @if($cuentas->count() > 0)
            <div class="glass-card overflow-hidden">
                <!-- Header de la tabla -->
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-800">Tus Cuentas de Cobro</h3>
                </div>

                <!-- Tabla responsive -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Fecha
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
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $cuenta->fecha_emision->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <div class="max-w-xs">
                                            <p class="font-medium truncate">{{ $cuenta->proyecto_servicio }}</p>
                                            @if($cuenta->descripcion)
                                                <p class="text-gray-500 text-xs truncate">{{ Str::limit($cuenta->descripcion, 50) }}</p>
                                            @endif
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
                                                    bg-yellow-100 text-yellow-800
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
                                                    <i class="fas fa-thumbs-up mr-1"></i>
                                                    @break
                                                @case('pagado')
                                                    <i class="fas fa-check-circle mr-1"></i>
                                                    @break
                                                @case('rechazado')
                                                    <i class="fas fa-times-circle mr-1"></i>
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
                                                {{ $cuenta->archivo_nombre }}
                                            </a>
                                        @else
                                            <span class="text-gray-400">Sin archivo</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            <!-- Ver -->
                                            <a href="{{ route('contratista.cuentas.ver', $cuenta->id) }}" 
                                               class="text-blue-600 hover:text-blue-900 p-2 rounded-lg hover:bg-blue-50 transition-colors"
                                               title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            <!-- Editar (solo borradores y rechazadas) -->
                                            @if(in_array($cuenta->estado, ['borrador', 'rechazado']))
                                                <a href="{{ route('contratista.cuentas.editar', $cuenta->id) }}" 
                                                   class="text-green-600 hover:text-green-900 p-2 rounded-lg hover:bg-green-50 transition-colors"
                                                   title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif
                                            
                                            <!-- Eliminar (solo borradores) -->
                                            @if($cuenta->estado === 'borrador')
                                                <a href="{{ route('contratista.cuentas.eliminar', $cuenta->id) }}" 
                                                   class="text-red-600 hover:text-red-900 p-2 rounded-lg hover:bg-red-50 transition-colors"
                                                   title="Eliminar">
                                                    <i class="fas fa-trash"></i>
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
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                        {{ $cuentas->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        @else
            <!-- Estado vacío -->
            <div class="glass-card p-12 text-center">
                <div class="w-20 h-20 mx-auto mb-6 gradient-primary rounded-2xl flex items-center justify-center">
                    <i class="fas fa-file-invoice text-white text-3xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">
                    @if(request()->filled('search') || request()->filled('estado'))
                        No se encontraron cuentas
                    @else
                        No tienes cuentas de cobro
                    @endif
                </h3>
                <p class="text-gray-600 mb-6">
                    @if(request()->filled('search') || request()->filled('estado'))
                        Intenta ajustar los filtros de búsqueda o crear una nueva cuenta de cobro.
                    @else
                        Comienza creando tu primera cuenta de cobro para gestionar tus cobros.
                    @endif
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center space-y-3 sm:space-y-0 sm:space-x-3">
                    <a href="{{ route('contratista.cuentas.crear') }}" 
                       class="gradient-primary text-white px-6 py-3 rounded-xl hover:shadow-lg transition-all duration-300 font-medium flex items-center">
                        <i class="fas fa-plus mr-2"></i>
                        @if(request()->filled('search') || request()->filled('estado'))
                            Nueva Cuenta
                        @else
                            Crear Primera Cuenta
                        @endif
                    </a>
                    @if(request()->filled('search') || request()->filled('estado'))
                        <a href="{{ route('contratista.cuentas.index') }}" 
                           class="bg-gray-100 text-gray-700 px-6 py-3 rounded-xl hover:bg-gray-200 transition-colors font-medium flex items-center">
                            <i class="fas fa-times mr-2"></i>
                            Limpiar Filtros
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

@push('styles')
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 4px 15px 0 rgba(31, 38, 135, 0.07);
        border-radius: 16px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .glass-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px 0 rgba(31, 38, 135, 0.12);
    }
    
    .gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
@endpush
@endsection
