@extends('layouts.dashboard')

@section('title', 'Dashboard Tesorería')

@section('breadcrumb')
    @include('components.navigation.breadcrumb', [
        'items' => [
            ['label' => 'Dashboard Tesorería']
        ]
    ])
@endsection

@section('dashboard-header')
    <div class="glass-card p-6 slide-up">
        <div class="flex items-center space-x-4">
            <div class="gradient-secondary w-16 h-16 rounded-2xl flex items-center justify-center">
                <i class="fas fa-university text-white text-2xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Dashboard Tesorería</h1>
                <p class="text-gray-600">Gestión de pagos y finanzas</p>
            </div>
        </div>
    </div>
@endsection

@section('dashboard-content')
    <!-- Estadísticas principales -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Cuentas pendientes de pago -->
        <div class="glass-card p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Pendientes de Pago</p>
                    <p class="text-3xl font-bold text-gray-800" data-counter="{{ $cuentasAprobadas }}">{{ $cuentasAprobadas }}</p>
                    <p class="text-sm text-blue-600 flex items-center mt-1">
                        <i class="fas fa-credit-card mr-1"></i>
                        Listas para procesar
                    </p>
                </div>
                <div class="bg-gradient-to-br from-blue-400 to-blue-600 w-12 h-12 rounded-xl flex items-center justify-center">
                    <i class="fas fa-credit-card text-white"></i>
                </div>
            </div>
        </div>
        
        <!-- Pagos realizados hoy -->
        <div class="glass-card p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Pagos Hoy</p>
                    <p class="text-3xl font-bold text-gray-800" data-counter="{{ $pagosHoy }}">{{ $pagosHoy }}</p>
                    <p class="text-sm text-green-600 flex items-center mt-1">
                        <i class="fas fa-check-circle mr-1"></i>
                        ${{ number_format($valorPagadoHoy, 0, ',', '.') }}
                    </p>
                </div>
                <div class="bg-gradient-to-br from-green-400 to-green-600 w-12 h-12 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-white"></i>
                </div>
            </div>
        </div>
        
        <!-- Total pagado -->
        <div class="glass-card p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Pagado</p>
                    <p class="text-2xl font-bold text-gray-800">${{ number_format($valorTotalPagado, 0, ',', '.') }}</p>
                    <p class="text-sm text-purple-600 flex items-center mt-1">
                        <i class="fas fa-dollar-sign mr-1"></i>
                        {{ $cuentasPagadas }} cuentas
                    </p>
                </div>
                <div class="bg-gradient-to-br from-purple-400 to-purple-600 w-12 h-12 rounded-xl flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-white"></i>
                </div>
            </div>
        </div>
        
        <!-- Eficiencia de pagos -->
        <div class="glass-card p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">                <div>
                    <p class="text-sm text-gray-600 mb-1">Eficiencia</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $eficienciaPagos }}%</p>
                    <p class="text-sm text-orange-600 flex items-center mt-1">
                        <i class="fas fa-chart-line mr-1"></i>
                        Pagos procesados
                    </p>
                </div>
                <div class="bg-gradient-to-br from-orange-400 to-orange-600 w-12 h-12 rounded-xl flex items-center justify-center">
                    <i class="fas fa-chart-line text-white"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Accesos rápidos -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <a href="{{ route('tesoreria.cuentas') }}" class="glass-card p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 group">
            <div class="flex items-center space-x-4">
                <div class="bg-gradient-to-br from-blue-500 to-indigo-600 w-12 h-12 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fas fa-list text-white"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800 group-hover:text-blue-600 transition-colors">Todas las Cuentas</h3>
                    <p class="text-sm text-gray-600">Gestionar cuentas</p>
                </div>
            </div>
        </a>
        
        <a href="{{ route('tesoreria.pagos-realizados') }}" class="glass-card p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 group">
            <div class="flex items-center space-x-4">
                <div class="bg-gradient-to-br from-green-500 to-emerald-600 w-12 h-12 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fas fa-check-double text-white"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800 group-hover:text-green-600 transition-colors">Pagos Realizados</h3>
                    <p class="text-sm text-gray-600">Historial de pagos</p>
                </div>
            </div>
        </a>
        
        <a href="{{ route('tesoreria.pendientes') }}" class="glass-card p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 group">
            <div class="flex items-center space-x-4">
                <div class="bg-gradient-to-br from-orange-500 to-red-600 w-12 h-12 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fas fa-clock text-white"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800 group-hover:text-orange-600 transition-colors">Cuentas Pendientes</h3>
                    <p class="text-sm text-gray-600">Revisar y aprobar</p>
                </div>
            </div>
        </a>
        
        <a href="{{ route('dashboard') }}" class="glass-card p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 group">
            <div class="flex items-center space-x-4">
                <div class="bg-gradient-to-br from-gray-500 to-gray-700 w-12 h-12 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fas fa-home text-white"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800 group-hover:text-gray-600 transition-colors">Dashboard General</h3>
                    <p class="text-sm text-gray-600">Volver al inicio</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Gráfico de evolución de pagos y notificaciones -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 mb-8">
        <!-- Gráfico de pagos últimos 7 días -->
        <div class="xl:col-span-2 glass-card p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-gray-800">Evolución de Pagos (Últimos 7 días)</h2>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                        <span class="text-sm text-gray-600">Pagos</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                        <span class="text-sm text-gray-600">Valor</span>
                    </div>
                </div>
            </div>
            
            <div class="relative h-64">
                <canvas id="pagosChart" class="w-full h-full"></canvas>
            </div>
              <!-- Datos del gráfico para JavaScript -->
            <script>
                window.pagosData = @json($pagosPorDia);
            </script>
        </div>

        <!-- Panel de notificaciones -->
        <div class="glass-card p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                <i class="fas fa-bell text-yellow-500 mr-3"></i>
                Notificaciones
            </h3>
            
            @if(count($notificaciones) > 0)
                <div class="space-y-4">
                    @foreach($notificaciones as $notificacion)
                        <div class="p-4 rounded-xl border-l-4 
                            @if($notificacion['type'] === 'error') bg-red-50 border-red-400
                            @elseif($notificacion['type'] === 'warning') bg-yellow-50 border-yellow-400
                            @elseif($notificacion['type'] === 'info') bg-blue-50 border-blue-400
                            @elseif($notificacion['type'] === 'success') bg-green-50 border-green-400
                            @else bg-gray-50 border-gray-400
                            @endif
                        ">
                            <div class="flex items-start space-x-3">
                                <i class="{{ $notificacion['icon'] }} 
                                    @if($notificacion['type'] === 'error') text-red-500
                                    @elseif($notificacion['type'] === 'warning') text-yellow-500
                                    @elseif($notificacion['type'] === 'info') text-blue-500
                                    @elseif($notificacion['type'] === 'success') text-green-500
                                    @else text-gray-500
                                    @endif
                                    text-lg mt-0.5"></i>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-800 text-sm">{{ $notificacion['title'] }}</h4>
                                    <p class="text-gray-600 text-sm mt-1">{{ $notificacion['message'] }}</p>
                                    @if(isset($notificacion['action']))
                                        <a href="{{ $notificacion['action'] }}" 
                                           class="inline-flex items-center mt-2 text-sm font-medium 
                                                @if($notificacion['type'] === 'error') text-red-600 hover:text-red-800
                                                @elseif($notificacion['type'] === 'warning') text-yellow-600 hover:text-yellow-800
                                                @elseif($notificacion['type'] === 'info') text-blue-600 hover:text-blue-800
                                                @elseif($notificacion['type'] === 'success') text-green-600 hover:text-green-800
                                                @else text-gray-600 hover:text-gray-800
                                                @endif
                                                transition-colors">
                                            {{ $notificacion['action_text'] }}
                                            <i class="fas fa-arrow-right ml-1 text-xs"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-bell-slash text-gray-400 text-3xl mb-4"></i>
                    <p class="text-gray-500">No hay notificaciones pendientes</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Últimos movimientos -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Últimos pagos realizados -->
        <div class="glass-card p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-3"></i>
                    Últimos Pagos Realizados
                </h3>
                <a href="{{ route('tesoreria.pagos-realizados') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                    Ver todos <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
              @if($ultimasCuentasPagadas->count() > 0)
                <div class="space-y-4">
                    @foreach($ultimasCuentasPagadas as $pago)
                        <div class="flex items-center space-x-4 p-3 bg-green-50 rounded-xl hover:bg-green-100 transition-colors">
                            <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                                <span class="text-white font-bold text-sm">
                                    {{ strtoupper(substr($pago->user->name, 0, 1)) }}
                                </span>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-800 text-sm">{{ $pago->user->name }}</h4>
                                <p class="text-gray-600 text-xs">{{ Str::limit($pago->proyecto_servicio, 40) }}</p>
                                <p class="text-green-600 text-xs font-medium">
                                    Pagado: {{ $pago->updated_at->diffForHumans() }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-green-700">${{ number_format($pago->valor, 0, ',', '.') }}</p>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check mr-1"></i>
                                    Pagado
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-receipt text-gray-400 text-3xl mb-4"></i>
                    <p class="text-gray-500">No hay pagos realizados recientemente</p>
                </div>
            @endif
        </div>

        <!-- Últimas cuentas pendientes -->
        <div class="glass-card p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-clock text-orange-500 mr-3"></i>
                    Cuentas Pendientes
                </h3>
                <a href="{{ route('tesoreria.pendientes') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                    Ver todas <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
              @if($cuentasPendientesPago->count() > 0)
                <div class="space-y-4">
                    @foreach($cuentasPendientesPago as $pendiente)
                        <div class="flex items-center space-x-4 p-3 bg-orange-50 rounded-xl hover:bg-orange-100 transition-colors">
                            <div class="w-10 h-10 bg-orange-500 rounded-full flex items-center justify-center">
                                <span class="text-white font-bold text-sm">
                                    {{ strtoupper(substr($pendiente->user->name, 0, 1)) }}
                                </span>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-800 text-sm">{{ $pendiente->user->name }}</h4>
                                <p class="text-gray-600 text-xs">{{ Str::limit($pendiente->proyecto_servicio, 40) }}</p>
                                <p class="text-orange-600 text-xs font-medium">
                                    Creada: {{ $pendiente->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-orange-700">${{ number_format($pendiente->valor, 0, ',', '.') }}</p>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                    <i class="fas fa-clock mr-1"></i>
                                    Pendiente
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-tasks text-gray-400 text-3xl mb-4"></i>
                    <p class="text-gray-500">No hay cuentas pendientes</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gráfico de evolución de pagos
    const ctx = document.getElementById('pagosChart').getContext('2d');
    const pagosData = window.pagosData || [];
    
    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: pagosData.map(item => item.day),
            datasets: [
                {
                    label: 'Número de Pagos',
                    data: pagosData.map(item => item.total_pagos),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    yAxisID: 'y'
                },
                {
                    label: 'Valor (Miles)',
                    data: pagosData.map(item => Math.round(item.total_valor / 1000)),
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Número de Pagos'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Valor (Miles COP)'
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            if (context.datasetIndex === 0) {
                                return 'Pagos: ' + context.parsed.y;
                            } else {
                                return 'Valor: $' + (context.parsed.y * 1000).toLocaleString();
                            }
                        }
                    }
                }
            }
        }
    });
    
    // Animación de contadores
    const counters = document.querySelectorAll('[data-counter]');
    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-counter'));
        const duration = 2000;
        const start = performance.now();
        
        const updateCounter = (currentTime) => {
            const elapsed = currentTime - start;
            const progress = Math.min(elapsed / duration, 1);
            const current = Math.floor(progress * target);
            
            counter.textContent = current.toLocaleString();
            
            if (progress < 1) {
                requestAnimationFrame(updateCounter);
            }
        };
        
        requestAnimationFrame(updateCounter);
    });
});
</script>
@endpush
