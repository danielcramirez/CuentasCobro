@extends('layouts.dashboard')

@section('title', 'Detalles del Contratista - Supervisor')

@section('breadcrumb')
    @include('components.navigation.breadcrumb', [
        'items' => [
            ['label' => 'Dashboard Supervisor', 'url' => route('supervisor.dashboard')],
            ['label' => 'Contratistas', 'url' => route('supervisor.contratistas.index')],
            ['label' => $contratista->name]
        ]
    ])
@endsection

@section('dashboard-header')
    <div class="glass-card p-6 slide-up">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="gradient-secondary w-16 h-16 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-user-tie text-white text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">{{ $contratista->name }}</h1>
                    <p class="text-gray-600">{{ $contratista->email }}</p>
                </div>
            </div>
            
            <div class="flex items-center space-x-3">
                <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-medium bg-green-100 text-green-800">
                    <i class="fas fa-circle text-green-500 text-xs mr-2"></i>
                    Contratista Activo
                </span>
                <a href="{{ route('supervisor.contratistas.index') }}" 
                   class="inline-flex items-center px-4 py-2 border-2 border-gray-300 text-gray-700 bg-white rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all font-medium">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Volver a Lista
                </a>
            </div>
        </div>
    </div>
@endsection

@section('dashboard-content')
    <!-- Estadísticas principales -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @include('components.cards.stats-card', [
            'title' => 'Total Cuentas',
            'value' => $estadisticas['total_cuentas'],
            'subtitle' => 'Generadas',
            'subtitleColor' => 'text-blue-600',
            'icon' => 'file-invoice-dollar',
            'cardIcon' => 'file-invoice-dollar',
            'gradientFrom' => 'from-blue-400',
            'gradientTo' => 'to-blue-600'
        ])
        
        @include('components.cards.stats-card', [
            'title' => 'Pendientes',
            'value' => $estadisticas['pendientes'],
            'subtitle' => 'Por revisar',
            'subtitleColor' => 'text-orange-600',
            'icon' => 'clock',
            'cardIcon' => 'hourglass-half',
            'gradientFrom' => 'from-orange-400',
            'gradientTo' => 'to-red-500'
        ])
        
        @include('components.cards.stats-card', [
            'title' => 'Aprobadas',
            'value' => $estadisticas['aprobadas'],
            'subtitle' => 'Procesadas',
            'subtitleColor' => 'text-green-600',
            'icon' => 'check-circle',
            'cardIcon' => 'check-circle',
            'gradientFrom' => 'from-green-400',
            'gradientTo' => 'to-green-600'
        ])
        
        @include('components.cards.stats-card', [
            'title' => 'Rechazadas',
            'value' => $estadisticas['rechazadas'],
            'subtitle' => 'Devueltas',
            'subtitleColor' => 'text-red-600',
            'icon' => 'times-circle',
            'cardIcon' => 'times-circle',
            'gradientFrom' => 'from-red-400',
            'gradientTo' => 'to-red-600'
        ])
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        <!-- Información principal -->
        <div class="xl:col-span-2 space-y-6">
            <!-- Información del contratista -->
            <div class="glass-card p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-user text-blue-500 mr-3"></i>
                    Información del Contratista
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Nombre Completo</label>
                            <div class="mt-1 p-3 bg-gray-50 rounded-lg text-gray-800 font-medium">
                                {{ $contratista->name }}
                            </div>
                        </div>
                        
                        <div>
                            <label class="text-sm font-medium text-gray-500">Correo Electrónico</label>
                            <div class="mt-1 p-3 bg-gray-50 rounded-lg text-gray-800">
                                {{ $contratista->email }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">ID de Usuario</label>
                            <div class="mt-1 p-3 bg-gray-50 rounded-lg text-gray-800">
                                {{ $contratista->id }}
                            </div>
                        </div>
                        
                        <div>
                            <label class="text-sm font-medium text-gray-500">Fecha de Registro</label>
                            <div class="mt-1 p-3 bg-gray-50 rounded-lg text-gray-800">
                                {{ $contratista->created_at->format('d/m/Y H:i') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cuentas recientes -->
            <div class="glass-card p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-history text-purple-500 mr-3"></i>
                        Cuentas Recientes
                    </h3>
                    @if($estadisticas['total_cuentas'] > 0)
                        <span class="text-sm text-gray-600">Últimas 10 cuentas</span>
                    @endif
                </div>

                @if(count($cuentasRecientes) > 0)
                    <div class="space-y-4">
                        @foreach($cuentasRecientes as $cuenta)
                            <div class="border border-gray-200 rounded-xl p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-2">
                                            <h4 class="font-semibold text-gray-800">Cuenta #{{ $cuenta->id }}</h4>
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                                @if($cuenta->estado === 'pagado') bg-green-100 text-green-800
                                                @elseif($cuenta->estado === 'aprobado') bg-blue-100 text-blue-800
                                                @elseif($cuenta->estado === 'rechazado') bg-red-100 text-red-800
                                                @elseif($cuenta->estado === 'revision') bg-yellow-100 text-yellow-800
                                                @elseif($cuenta->estado === 'pendiente') bg-orange-100 text-orange-800
                                                @else bg-gray-100 text-gray-800
                                                @endif
                                            ">
                                                {{ strtoupper($cuenta->estado) }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600 mb-2">{{ Str::limit($cuenta->proyecto_servicio, 80) }}</p>
                                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                                            <span>
                                                <i class="fas fa-calendar-alt mr-1"></i>
                                                {{ $cuenta->created_at->format('d/m/Y') }}
                                            </span>
                                            <span>
                                                <i class="fas fa-dollar-sign mr-1"></i>
                                                ${{ number_format($cuenta->valor, 0, ',', '.') }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="flex space-x-2">
                                        <a href="{{ route('supervisor.cuentas-cobro.show', $cuenta->id) }}" 
                                           class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-all">
                                            <i class="fas fa-eye mr-2"></i>
                                            Ver
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($estadisticas['total_cuentas'] > 10)
                        <div class="mt-6 text-center">
                            <p class="text-sm text-gray-600 mb-3">
                                Mostrando 10 de {{ $estadisticas['total_cuentas'] }} cuentas totales
                            </p>
                            <a href="{{ route('supervisor.cuentas-cobro.index', ['search' => $contratista->email]) }}" 
                               class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl font-medium transition-all">
                                <i class="fas fa-list mr-2"></i>
                                Ver todas las cuentas
                            </a>
                        </div>
                    @endif
                @else
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-file-invoice text-gray-400 text-2xl"></i>
                        </div>
                        <p class="text-gray-600">Este contratista aún no ha generado cuentas de cobro</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Panel lateral -->
        <div class="space-y-6">
            <!-- Resumen financiero -->
            <div class="glass-card p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-chart-pie text-green-500 mr-3"></i>
                    Resumen Financiero
                </h3>
                
                <div class="space-y-4">
                    <div class="bg-green-50 rounded-xl p-4 border-l-4 border-green-400">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-green-700">Valor Total Generado</span>
                            <i class="fas fa-coins text-green-600"></i>
                        </div>
                        <div class="text-2xl font-bold text-green-800 mt-2">
                            ${{ number_format($estadisticas['valor_total'], 0, ',', '.') }}
                        </div>
                    </div>
                    
                    <div class="bg-blue-50 rounded-xl p-4 border-l-4 border-blue-400">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-blue-700">Valor Aprobado</span>
                            <i class="fas fa-check-circle text-blue-600"></i>
                        </div>
                        <div class="text-2xl font-bold text-blue-800 mt-2">
                            ${{ number_format($estadisticas['valor_aprobado'], 0, ',', '.') }}
                        </div>
                    </div>

                    @if($estadisticas['valor_total'] > 0)
                        <div class="bg-gray-50 rounded-xl p-4">
                            <div class="text-sm text-gray-600 mb-2">Porcentaje de Aprobación</div>
                            @php
                                $porcentaje = $estadisticas['valor_total'] > 0 ? ($estadisticas['valor_aprobado'] / $estadisticas['valor_total']) * 100 : 0;
                            @endphp
                            <div class="flex items-center space-x-3">
                                <div class="flex-1 bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full transition-all duration-500" 
                                         style="width: {{ $porcentaje }}%"></div>
                                </div>
                                <span class="text-sm font-semibold text-gray-800">{{ number_format($porcentaje, 1) }}%</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Distribución de estados -->
            <div class="glass-card p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-chart-bar text-indigo-500 mr-3"></i>
                    Estados de Cuentas
                </h3>
                
                @if($estadisticas['total_cuentas'] > 0)
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-orange-500 rounded-full"></div>
                                <span class="text-sm text-gray-600">Pendientes</span>
                            </div>
                            <span class="font-semibold text-gray-800">{{ $estadisticas['pendientes'] }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                <span class="text-sm text-gray-600">Aprobadas</span>
                            </div>
                            <span class="font-semibold text-gray-800">{{ $estadisticas['aprobadas'] }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                                <span class="text-sm text-gray-600">Rechazadas</span>
                            </div>
                            <span class="font-semibold text-gray-800">{{ $estadisticas['rechazadas'] }}</span>
                        </div>
                    </div>
                @else
                    <div class="text-center py-4 text-gray-500">
                        <i class="fas fa-chart-bar text-2xl mb-2"></i>
                        <p class="text-sm">Sin datos disponibles</p>
                    </div>
                @endif
            </div>

            <!-- Acciones rápidas -->
            <div class="glass-card p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-bolt text-yellow-500 mr-3"></i>
                    Acciones Rápidas
                </h3>
                
                <div class="space-y-3">
                    @if($estadisticas['pendientes'] > 0)
                        <a href="{{ route('supervisor.cuentas-cobro.index', ['search' => $contratista->email, 'estado' => 'pendiente']) }}" 
                           class="w-full bg-orange-600 hover:bg-orange-700 text-white py-3 px-4 rounded-xl font-medium transition-all flex items-center justify-center">
                            <i class="fas fa-eye mr-2"></i>
                            Ver Cuentas Pendientes
                        </a>
                    @endif
                    
                    <a href="{{ route('supervisor.cuentas-cobro.index', ['search' => $contratista->email]) }}" 
                       class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-xl font-medium transition-all flex items-center justify-center">
                        <i class="fas fa-list mr-2"></i>
                        Ver Todas las Cuentas
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
