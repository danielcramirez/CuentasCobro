@extends('layouts.dashboard')

@section('title', 'Cuentas de Cobro - Contratación')

@section('breadcrumb')
    @include('components.navigation.breadcrumb', [
        'items' => [
            ['label' => 'Dashboard Contratación', 'url' => route('contratacion.dashboard')],
            ['label' => 'Cuentas de Cobro']
        ]
    ])
@endsection

@section('dashboard-header')
    <div class="glass-card p-6 slide-up">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center space-x-4 mb-4 lg:mb-0">
                <div class="gradient-primary w-12 h-12 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-clipboard-check text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-800">Gestión de Cuentas</h1>
                    <p class="text-gray-600">Revisa, aprueba o rechaza las cuentas de cobro</p>
                </div>
            </div>
            
            <div class="flex items-center space-x-3">
                <a href="{{ route('contratacion.dashboard') }}" 
                   class="bg-gray-100 text-gray-700 px-4 py-2 rounded-xl hover:bg-gray-200 transition-colors font-medium flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Volver al Dashboard
                </a>
                <button onclick="window.location.reload()" 
                        class="gradient-primary text-white px-4 py-2 rounded-xl hover:shadow-lg transition-all font-medium flex items-center">
                    <i class="fas fa-sync-alt mr-2"></i>
                    Actualizar
                </button>
            </div>
        </div>
    </div>
@endsection

@section('dashboard-content')
    <!-- Estadísticas rápidas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total de cuentas -->
        <div class="glass-card p-6 hover-scale">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Cuentas</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $cuentas->total() }}</p>
                    <p class="text-sm text-blue-600 flex items-center mt-1">
                        <i class="fas fa-file-invoice mr-1"></i>
                        En el sistema
                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-file-alt text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <!-- Pendientes Contratación -->
        <div class="glass-card p-6 hover-scale">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Pendientes</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $cuentas->where('estado', 'pendiente_contratacion')->count() }}</p>
                    <p class="text-sm text-yellow-600 flex items-center mt-1">
                        <i class="fas fa-clock mr-1"></i>
                        Requieren aprobación
                    </p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-hourglass-half text-yellow-600"></i>
                </div>
            </div>
        </div>
        
        <!-- Aprobadas -->
        <div class="glass-card p-6 hover-scale">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Aprobadas</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $cuentas->where('estado', 'aprobada')->count() }}</p>
                    <p class="text-sm text-green-600 flex items-center mt-1">
                        <i class="fas fa-check-circle mr-1"></i>
                        En proceso
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check text-green-600"></i>
                </div>
            </div>
        </div>
        
        <!-- Rechazadas -->
        <div class="glass-card p-6 hover-scale">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Rechazadas</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $cuentas->where('estado', 'rechazada')->count() }}</p>
                    <p class="text-sm text-red-600 flex items-center mt-1">
                        <i class="fas fa-times-circle mr-1"></i>
                        No aprobadas
                    </p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-times text-red-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros y búsqueda -->
    <div class="glass-card p-6 mb-8">
        <form method="GET" action="{{ route('contratacion.cuentas-cobro.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Búsqueda -->
            <div class="md:col-span-2">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Búsqueda</label>
                <div class="relative">
                    <input type="text" 
                           id="search" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Buscar por proyecto, contratista o descripción..."
                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
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
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Todos los estados</option>
                    <option value="pendiente_contratacion" {{ request('estado') === 'pendiente_contratacion' ? 'selected' : '' }}>Pendiente Contratación</option>
                    <option value="pendiente_supervisor" {{ request('estado') === 'pendiente_supervisor' ? 'selected' : '' }}>Pendiente Supervisor</option>
                    <option value="pendiente_tesoreria" {{ request('estado') === 'pendiente_tesoreria' ? 'selected' : '' }}>Pendiente Tesorería</option>
                    <option value="pendiente_ordenador" {{ request('estado') === 'pendiente_ordenador' ? 'selected' : '' }}>Pendiente Ordenador</option>
                    <option value="aprobada" {{ request('estado') === 'aprobada' ? 'selected' : '' }}>Aprobada</option>
                    <option value="rechazada" {{ request('estado') === 'rechazada' ? 'selected' : '' }}>Rechazada</option>
                    <option value="pagada" {{ request('estado') === 'pagada' ? 'selected' : '' }}>Pagada</option>
                </select>
            </div>

            <!-- Filtro por mes -->
            <div>
                <label for="mes" class="block text-sm font-medium text-gray-700 mb-2">Mes</label>
                <select id="mes" 
                        name="mes" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
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
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl transition-all font-medium flex items-center">
                    <i class="fas fa-search mr-2"></i>
                    Filtrar
                </button>
                <a href="{{ route('contratacion.cuentas-cobro.index') }}" 
                   class="bg-gray-100 text-gray-700 px-6 py-3 rounded-xl hover:bg-gray-200 transition-colors font-medium flex items-center">
                    <i class="fas fa-times mr-2"></i>
                    Limpiar
                </a>
            </div>
        </form>
    </div>

    <!-- Tabla de cuentas -->
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
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                Fecha / Contratista
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                Proyecto/Servicio
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                Valor
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                Estado
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($cuentas as $cuenta)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $cuenta->fecha_emision ? $cuenta->fecha_emision->format('d/m/Y') : 'N/A' }}
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
                                        <p class="font-medium text-gray-900">{{ Str::limit($cuenta->proyecto_servicio, 50) }}</p>
                                        <p class="text-xs text-gray-400">{{ $cuenta->created_at->diffForHumans() }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                    ${{ number_format($cuenta->valor, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($cuenta->estado === 'pendiente_contratacion') bg-yellow-100 text-yellow-800
                                        @elseif($cuenta->estado === 'pendiente_supervisor') bg-orange-100 text-orange-800
                                        @elseif($cuenta->estado === 'pendiente_tesoreria') bg-blue-100 text-blue-800
                                        @elseif($cuenta->estado === 'pendiente_ordenador') bg-purple-100 text-purple-800
                                        @elseif($cuenta->estado === 'aprobada') bg-green-100 text-green-800
                                        @elseif($cuenta->estado === 'rechazada') bg-red-100 text-red-800
                                        @elseif($cuenta->estado === 'pagada') bg-emerald-100 text-emerald-800
                                        @else bg-gray-100 text-gray-800
                                        @endif
                                    ">
                                        <i class="fas fa-circle text-xs mr-1"></i>
                                        {{ ucfirst(str_replace('_', ' ', $cuenta->estado)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium">
                                    <a href="{{ route('contratacion.cuentas-cobro.show', $cuenta->id) }}" 
                                       class="inline-flex items-center px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                                        <i class="fas fa-eye mr-1"></i>
                                        Ver
                                    </a>
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
                    <a href="{{ route('contratacion.cuentas-cobro.index') }}" 
                       class="inline-flex items-center mt-4 text-blue-600 hover:text-blue-700">
                        <i class="fas fa-times mr-2"></i>
                        Limpiar filtros
                    </a>
                @endif
            </div>
        @endif
    </div>
@endsection
