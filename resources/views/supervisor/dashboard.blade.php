@extends('layouts.app')

@section('title', 'Dashboard Supervisor - CuentasCobro')

@section('content')
<!-- Contenedor principal del dashboard con padding superior para el navbar fijo -->
<div class="pt-24 pb-8 px-4 sm:px-6 lg:px-8 min-h-screen">
    <!-- Breadcrumb de navegación -->
    <div class="max-w-7xl mx-auto mb-4">
        <nav class="flex items-center space-x-2 text-sm text-gray-500" aria-label="Breadcrumb">
            <a href="{{ route('dashboard') }}" class="hover:text-gray-700 transition-colors flex items-center">
                <i class="fas fa-home mr-1"></i>
                Inicio
            </a>
            <i class="fas fa-chevron-right text-gray-300"></i>
            <span class="text-gray-700 font-medium">Dashboard Supervisor</span>
        </nav>
    </div>

    <!-- Header del dashboard -->
    <div class="max-w-7xl mx-auto mb-8">
        <div class="glass-card p-4 sm:p-6 slide-up">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <!-- Información del usuario -->
                <div class="flex items-center space-x-3 sm:space-x-4">
                    <div class="gradient-primary w-12 h-12 sm:w-16 sm:h-16 rounded-2xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-user-check text-white text-lg sm:text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-800 font-poppins">
                            ¡Bienvenido, {{ $user->name }}!
                        </h1>
                        <div class="flex items-center space-x-2 mt-1">
                            <span class="text-gray-600 text-sm">Rol:</span>
                            <span class="inline-flex items-center px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm font-medium gradient-secondary text-white shadow-sm">
                                <i class="fas fa-user-check mr-1"></i>
                                {{ $user->role->name ? ucfirst($user->role->name) : 'Sin rol' }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Acciones rápidas -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                    <div class="flex items-center gap-3">
                        <button 
                            class="gradient-primary text-white px-3 sm:px-4 py-2 rounded-xl hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300 font-medium text-sm flex items-center"
                            onclick="refreshDashboard()"
                            id="refresh-btn">
                            <i class="fas fa-sync-alt mr-2"></i>
                            <span class="hidden sm:inline">Actualizar</span>
                            <span class="sm:hidden">Sync</span>
                        </button>
                        <a href="{{ route('supervisor.cuentas-cobro.index') }}" 
                           class="bg-orange-600 hover:bg-orange-700 text-white px-3 sm:px-4 py-2 rounded-xl transition-all duration-300 font-medium text-sm flex items-center">
                            <i class="fas fa-search mr-2"></i>
                            <span class="hidden sm:inline">Revisar Cuentas</span>
                            <span class="sm:hidden">Revisar</span>
                        </a>
                    </div>
                    <div class="flex items-center space-x-2 text-xs sm:text-sm text-gray-600 bg-gray-50 px-3 py-2 rounded-lg">
                        <i class="fas fa-clock"></i>
                        <span>Actualizado:</span>
                        <span id="lastUpdate" class="font-medium">{{ now()->format('H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto space-y-8">
        <!-- Tarjetas de estadísticas principales -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Cuentas por revisar -->
            <div class="glass-card p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Por Revisar</p>
                        <p class="text-3xl font-bold text-gray-800" data-counter="{{ $cuentasPendientes }}">{{ $cuentasPendientes }}</p>
                        <p class="text-sm text-orange-600 flex items-center mt-1">
                            <i class="fas fa-clock mr-1"></i>
                            Pendientes
                        </p>
                    </div>
                    <div class="bg-gradient-to-br from-orange-400 to-red-500 w-12 h-12 rounded-xl flex items-center justify-center">
                        <i class="fas fa-hourglass-half text-white"></i>
                    </div>
                </div>
            </div>
            
            <!-- Cuentas aprobadas -->
            <div class="glass-card p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Aprobadas</p>
                        <p class="text-3xl font-bold text-gray-800" data-counter="{{ $cuentasAprobadas }}">{{ $cuentasAprobadas }}</p>
                        <p class="text-sm text-green-600 flex items-center mt-1">
                            <i class="fas fa-check-circle mr-1"></i>
                            Total
                        </p>
                    </div>
                    <div class="bg-gradient-to-br from-green-400 to-emerald-600 w-12 h-12 rounded-xl flex items-center justify-center">
                        <i class="fas fa-check-double text-white"></i>
                    </div>
                </div>
            </div>
            
            <!-- Cuentas rechazadas -->
            <div class="glass-card p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Rechazadas</p>
                        <p class="text-3xl font-bold text-gray-800" data-counter="{{ $cuentasRechazadas }}">{{ $cuentasRechazadas }}</p>
                        <p class="text-sm text-red-600 flex items-center mt-1">
                            <i class="fas fa-times-circle mr-1"></i>
                            Total
                        </p>
                    </div>
                    <div class="bg-gradient-to-br from-red-400 to-pink-500 w-12 h-12 rounded-xl flex items-center justify-center">
                        <i class="fas fa-times text-white"></i>
                    </div>
                </div>
            </div>
            
            <!-- Tiempo promedio de revisión -->
            <div class="glass-card p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Tiempo Promedio</p>
                        <p class="text-3xl font-bold text-gray-800">
                            @if($tiempoPromedioRevision)
                                {{ $tiempoPromedioRevision }}
                            @else
                                --
                            @endif
                        </p>
                        <p class="text-sm text-blue-600 flex items-center mt-1">
                            <i class="fas fa-stopwatch mr-1"></i>
                            @if($tiempoPromedioRevision) Horas @else Sin datos @endif
                        </p>
                    </div>
                    <div class="bg-gradient-to-br from-blue-400 to-indigo-600 w-12 h-12 rounded-xl flex items-center justify-center">
                        <i class="fas fa-tachometer-alt text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    
    <!-- Panel principal del supervisor -->
    <div class="glass-card p-8">
        <div class="text-center mb-8">
            <div class="gradient-secondary w-20 h-20 rounded-2xl mx-auto mb-6 flex items-center justify-center shadow-xl">
                <i class="fas fa-user-check text-white text-3xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2 font-poppins">Panel de Supervisión</h2>
            <p class="text-gray-600">Revisa y aprueba las cuentas de cobro del sistema</p>
        </div>
        
        <!-- Estado de cuentas y acciones rápidas -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Panel de estado de cuentas -->
            <div class="lg:col-span-2">
                <div class="glass-card p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                            <i class="fas fa-chart-pie text-blue-500 mr-2"></i>
                            Estado de las Cuentas de Cobro
                        </h3>
                        @if($porcentajeEficiencia > 0)
                        <div class="text-sm text-gray-600">
                            <span class="font-medium text-green-600">{{ $porcentajeEficiencia }}%</span> de eficiencia
                        </div>
                        @endif
                    </div>

                    <!-- Estados con barras de progreso -->
                    <div class="space-y-4">
                        @if($cuentasPendientes > 0)
                        <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg border border-orange-200">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center">
                                    <i class="fas fa-clock text-white text-sm"></i>
                                </div>
                                <div>
                                    <span class="font-medium text-orange-800">Pendientes de Revisión</span>
                                    <p class="text-sm text-orange-600">{{ $cuentasPendientes }} requieren atención</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-lg font-bold text-orange-800">{{ $cuentasPendientes }}</span>
                            </div>
                        </div>
                        @endif

                        @if($cuentasRevision > 0)
                        <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg border border-blue-200">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                    <i class="fas fa-search text-white text-sm"></i>
                                </div>
                                <div>
                                    <span class="font-medium text-blue-800">En Revisión</span>
                                    <p class="text-sm text-blue-600">{{ $cuentasRevision }} siendo evaluadas</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-lg font-bold text-blue-800">{{ $cuentasRevision }}</span>
                            </div>
                        </div>
                        @endif

                        @if($cuentasAprobadas > 0)
                        <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg border border-green-200">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                    <i class="fas fa-check text-white text-sm"></i>
                                </div>
                                <div>
                                    <span class="font-medium text-green-800">Aprobadas</span>
                                    <p class="text-sm text-green-600">{{ $cuentasAprobadas }} completadas</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-lg font-bold text-green-800">{{ $cuentasAprobadas }}</span>
                            </div>
                        </div>
                        @endif

                        @if($cuentasRechazadas > 0)
                        <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg border border-red-200">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
                                    <i class="fas fa-times text-white text-sm"></i>
                                </div>
                                <div>
                                    <span class="font-medium text-red-800">Rechazadas</span>
                                    <p class="text-sm text-red-600">{{ $cuentasRechazadas }} requieren corrección</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-lg font-bold text-red-800">{{ $cuentasRechazadas }}</span>
                            </div>
                        </div>
                        @endif

                        @if($totalCuentas == 0)
                        <div class="text-center py-8">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-inbox text-gray-400 text-xl"></i>
                            </div>
                            <h4 class="text-lg font-medium text-gray-600 mb-2">No hay cuentas de cobro</h4>
                            <p class="text-gray-500">No hay cuentas de cobro en el sistema actualmente</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Panel de acciones rápidas -->
            <div class="space-y-6">
                <!-- Acciones principales -->
                <div class="glass-card p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-bolt text-yellow-500 mr-2"></i>
                        Acciones Rápidas
                    </h3>
                    <div class="space-y-3">
                        <!-- Revisar pendientes -->
                        <a href="{{ route('supervisor.cuentas-cobro.index', ['estado' => 'pendiente']) }}" class="group block">
                            <div class="bg-gradient-to-br from-orange-50 to-red-100 p-4 rounded-xl border border-orange-200 hover:border-orange-300 transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1">
                                <div class="flex items-center space-x-3">
                                    <div class="bg-gradient-to-br from-orange-500 to-red-600 w-10 h-10 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                        <i class="fas fa-search text-white"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-800">Revisar Pendientes</h4>
                                        <p class="text-sm text-gray-600">{{ $cuentasPendientes }} esperando</p>
                                    </div>
                                </div>
                            </div>
                        </a>
                        
                        <!-- Ver todas las cuentas -->
                        <a href="{{ route('supervisor.cuentas-cobro.index') }}" class="group block">
                            <div class="bg-gradient-to-br from-blue-50 to-indigo-100 p-4 rounded-xl border border-blue-200 hover:border-blue-300 transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1">
                                <div class="flex items-center space-x-3">
                                    <div class="bg-gradient-to-br from-blue-500 to-indigo-600 w-10 h-10 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                        <i class="fas fa-list-alt text-white"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-800">Todas las Cuentas</h4>
                                        <p class="text-sm text-gray-600">{{ $totalCuentas }} registradas</p>
                                    </div>
                                </div>
                            </div>
                        </a>

                        <!-- Ver contratistas -->
                        <a href="{{ route('supervisor.contratistas.index') }}" class="group block">
                            <div class="bg-gradient-to-br from-green-50 to-emerald-100 p-4 rounded-xl border border-green-200 hover:border-green-300 transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1">
                                <div class="flex items-center space-x-3">
                                    <div class="bg-gradient-to-br from-green-500 to-emerald-600 w-10 h-10 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                        <i class="fas fa-users text-white"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-800">Contratistas</h4>
                                        <p class="text-sm text-gray-600">{{ $contratistasActivos }} activos</p>
                                    </div>
                                </div>
                            </div>
                        </a>

                        <!-- Volver al dashboard principal -->
                        <a href="{{ route('dashboard') }}" class="group block">
                            <div class="bg-gradient-to-br from-purple-50 to-pink-100 p-4 rounded-xl border border-purple-200 hover:border-purple-300 transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1">
                                <div class="flex items-center space-x-3">
                                    <div class="bg-gradient-to-br from-purple-500 to-pink-600 w-10 h-10 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                        <i class="fas fa-home text-white"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-800">Dashboard Principal</h4>
                                        <p class="text-sm text-gray-600">Ir al menú general</p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Estadísticas del mes -->
                <div class="glass-card p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>
                        Este Mes
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Cuentas recibidas</span>
                            <span class="font-semibold text-gray-800">{{ $estadisticasMes['cuentas_mes'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Valor total</span>
                            <span class="font-semibold text-gray-800">${{ number_format($estadisticasMes['valor_mes'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Aprobadas</span>
                            <span class="font-semibold text-green-600">{{ $estadisticasMes['aprobadas_mes'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Rechazadas</span>
                            <span class="font-semibold text-red-600">{{ $estadisticasMes['rechazadas_mes'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico de evolución semanal -->
        @if(count($evolucionSemanal) > 0)
        <div class="glass-card p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-chart-line text-green-500 mr-2"></i>
                    Evolución Semanal
                </h3>
                <div class="flex items-center space-x-4 text-sm text-gray-600">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                        <span>Recibidas</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                        <span>Aprobadas</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-red-500 rounded-full mr-2"></div>
                        <span>Rechazadas</span>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                @foreach($evolucionSemanal as $semana)
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">{{ $semana['week'] }}</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Total:</span>
                            <span class="font-medium">{{ $semana['total_cuentas'] }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-green-600">Aprobadas:</span>
                            <span class="font-medium text-green-600">{{ $semana['aprobadas'] }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-red-600">Rechazadas:</span>
                            <span class="font-medium text-red-600">{{ $semana['rechazadas'] }}</span>
                        </div>
                        <div class="text-xs text-gray-500 pt-2 border-t">
                            ${{ number_format($semana['total_valor'], 0, ',', '.') }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Notificaciones y cuentas recientes -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Notificaciones -->
            @if(count($notificaciones) > 0)
            <div class="glass-card p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-bell text-yellow-500 mr-2"></i>
                    Notificaciones
                </h3>
                <div class="space-y-4">
                    @foreach($notificaciones as $notificacion)
                    <div class="flex items-start space-x-4 p-4 rounded-lg border
                        @if($notificacion['type'] === 'error') bg-red-50 border-red-200
                        @elseif($notificacion['type'] === 'warning') bg-yellow-50 border-yellow-200
                        @elseif($notificacion['type'] === 'info') bg-blue-50 border-blue-200
                        @else bg-green-50 border-green-200
                        @endif
                    ">
                        <div class="
                            @if($notificacion['type'] === 'error') text-red-500
                            @elseif($notificacion['type'] === 'warning') text-yellow-500
                            @elseif($notificacion['type'] === 'info') text-blue-500
                            @else text-green-500
                            @endif
                        ">
                            <i class="{{ $notificacion['icon'] }}"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-800">{{ $notificacion['title'] }}</h4>
                            <p class="text-sm text-gray-600 mt-1">{{ $notificacion['message'] }}</p>
                            @if(isset($notificacion['action']))
                            <a href="{{ $notificacion['action'] }}" 
                               class="inline-flex items-center text-sm text-blue-600 hover:text-blue-700 mt-2">
                                {{ $notificacion['action_text'] }} 
                                <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
            
            <!-- Cuentas recientes para revisión -->
            <div class="glass-card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-clipboard-list text-orange-500 mr-2"></i>
                        Cuentas Recientes
                    </h3>
                    @if($cuentasRecientes->count() > 0)
                    <a href="{{ route('supervisor.cuentas-cobro.index') }}" 
                       class="text-sm text-blue-600 hover:text-blue-700">Ver todas</a>
                    @endif
                </div>
                
                @if($cuentasRecientes->count() > 0)
                <div class="space-y-3">
                    @foreach($cuentasRecientes as $cuenta)
                    <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-gray-100 hover:border-orange-200 transition-all duration-200 hover:shadow-sm">
                        <div class="flex items-center space-x-3">
                            <div class="w-3 h-3 rounded-full
                                @if($cuenta->estado === 'pendiente') bg-orange-400 animate-pulse
                                @elseif($cuenta->estado === 'revision') bg-blue-400
                                @elseif($cuenta->estado === 'aprobado') bg-green-400
                                @elseif($cuenta->estado === 'rechazado') bg-red-400
                                @else bg-gray-400
                                @endif
                            "></div>
                            <div>
                                <p class="font-medium text-gray-800 text-sm">Cuenta #{{ str_pad($cuenta->id, 6, '0', STR_PAD_LEFT) }}</p>
                                <p class="text-xs text-gray-600">{{ Str::limit($cuenta->proyecto_servicio, 30) }} - {{ $cuenta->user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $cuenta->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-semibold text-gray-800">${{ number_format($cuenta->valor, 0, ',', '.') }}</span>
                                <a href="{{ route('supervisor.cuentas-cobro.show', $cuenta->id) }}" 
                                   class="text-xs text-blue-600 hover:text-blue-700 flex items-center">
                                    <i class="fas fa-eye mr-1"></i>
                                    Ver
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-inbox text-gray-400 text-xl"></i>
                    </div>
                    <h4 class="text-lg font-medium text-gray-600 mb-2">No hay cuentas pendientes</h4>
                    <p class="text-gray-500">Todas las cuentas han sido procesadas</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Valores monetarios resumen -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
        <div class="glass-card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Valor Pendiente</p>
                    <p class="text-2xl font-bold text-orange-600">${{ number_format($valorTotalPendiente, 0, ',', '.') }}</p>
                </div>
                <div class="bg-orange-100 p-3 rounded-xl">
                    <i class="fas fa-hourglass-half text-orange-600"></i>
                </div>
            </div>
        </div>

        <div class="glass-card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Valor Aprobado</p>
                    <p class="text-2xl font-bold text-green-600">${{ number_format($valorTotalAprobado, 0, ',', '.') }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-xl">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="glass-card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Valor Pagado</p>
                    <p class="text-2xl font-bold text-emerald-600">${{ number_format($valorTotalPagado, 0, ',', '.') }}</p>
                </div>
                <div class="bg-emerald-100 p-3 rounded-xl">
                    <i class="fas fa-money-bill-wave text-emerald-600"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript para funcionalidad del dashboard -->
<script>
function refreshDashboard() {
    const refreshBtn = document.getElementById('refresh-btn');
    const lastUpdate = document.getElementById('lastUpdate');
    
    // Animación de loading
    refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Actualizando...';
    refreshBtn.disabled = true;
    
    // Simular carga (en producción, esto sería una llamada AJAX)
    setTimeout(() => {
        refreshBtn.innerHTML = '<i class="fas fa-sync-alt mr-2"></i><span class="hidden sm:inline">Actualizar</span><span class="sm:hidden">Sync</span>';
        refreshBtn.disabled = false;
        lastUpdate.textContent = new Date().toLocaleTimeString('es-ES', {hour: '2-digit', minute: '2-digit'});
        
        // Mostrar notificación de éxito
        showNotification('Dashboard actualizado correctamente', 'success');
    }, 1500);
}

function showNotification(message, type = 'info') {
    // Crear elemento de notificación
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transform translate-x-full transition-transform duration-300 ${
        type === 'success' ? 'bg-green-500 text-white' : 
        type === 'error' ? 'bg-red-500 text-white' : 'bg-blue-500 text-white'
    }`;
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'exclamation-triangle' : 'info'} mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animar entrada
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Remover después de 3 segundos
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Auto-actualización cada 5 minutos
setInterval(() => {
    fetch('{{ route("api.supervisor.dashboard") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Actualizar contadores
                document.querySelectorAll('[data-counter]').forEach(counter => {
                    const value = data.data[counter.dataset.counter];
                    if (value !== undefined) {
                        animateCounter(counter, parseInt(counter.textContent), value);
                    }
                });
                
                document.getElementById('lastUpdate').textContent = new Date().toLocaleTimeString('es-ES', {hour: '2-digit', minute: '2-digit'});
            }
        })
        .catch(error => console.error('Error actualizando dashboard:', error));
}, 300000); // 5 minutos

function animateCounter(element, start, end) {
    const duration = 1000;
    const increment = (end - start) / (duration / 16);
    let current = start;
    
    const timer = setInterval(() => {
        current += increment;
        if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
            current = end;
            clearInterval(timer);
        }
        element.textContent = Math.round(current);
    }, 16);
}
</script>

<style>
.gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.gradient-secondary {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.glass-card {
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    background: rgba(255, 255, 255, 0.7);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.glass-card:hover {
    background: rgba(255, 255, 255, 0.8);
    transform: translateY(-2px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
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