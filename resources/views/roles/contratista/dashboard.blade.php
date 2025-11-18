@extends('layouts.app')

@section('title', 'Dashboard Contratista - CuentasCobro')

@section('content')
<!-- Contenedor principal del dashboard con padding superior para el navbar fijo -->
<div class="pt-24 pb-8 px-4 sm:px-6 lg:px-8 min-h-screen">
    <!-- Breadcrumb de navegación mejorado -->
    <div class="max-w-7xl mx-auto mb-4">
        <nav class="flex items-center space-x-2 text-sm text-gray-500" aria-label="Breadcrumb">
            <a href="{{ route('dashboard') }}" class="hover:text-gray-700 transition-colors flex items-center">
                <i class="fas fa-home mr-1"></i>
                Inicio
            </a>
            <i class="fas fa-chevron-right text-gray-300"></i>
            <span class="text-gray-700 font-medium">Dashboard Contratista</span>
        </nav>
    </div>

    <!-- Header del dashboard con animación -->
    <div class="max-w-7xl mx-auto mb-8">
        <div class="glass-card p-4 sm:p-6 slide-up">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <!-- Información del usuario -->
                <div class="flex items-center space-x-3 sm:space-x-4">
                    <div class="gradient-primary w-12 h-12 sm:w-16 sm:h-16 rounded-2xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-user-tie text-white text-lg sm:text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-800 font-poppins">
                            ¡Bienvenido, {{ $user->name }}!
                        </h1>
                        <div class="flex items-center space-x-2 mt-1">
                            <span class="text-gray-600 text-sm">Rol:</span>
                            <span class="inline-flex items-center px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm font-medium gradient-secondary text-white shadow-sm">
                                <i class="fas fa-user-tie mr-1"></i>
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
                        <a href="{{ route('contratista.cuentas.crear') }}" 
                           class="bg-green-600 hover:bg-green-700 text-white px-3 sm:px-4 py-2 rounded-xl transition-all duration-300 font-medium text-sm flex items-center">
                            <i class="fas fa-plus mr-2"></i>
                            <span class="hidden sm:inline">Nueva Cuenta</span>
                            <span class="sm:hidden">Nueva</span>
                        </a>
                    </div>
                    <div class="flex items-center space-x-2 text-xs sm:text-sm text-gray-600 bg-gray-50 px-3 py-2 rounded-lg">
                        <i class="fas fa-clock"></i>
                        <span>Última actualización:</span>
                        <span id="lastUpdate" class="font-medium">
                            @if($ultimaFechaEnvio)
                                {{ $ultimaFechaEnvio->format('H:i') }}
                            @else
                                {{ now()->format('H:i') }}
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto space-y-8">
        <!-- Tarjetas de estadísticas principales -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total de cuentas -->
            <div class="glass-card p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Mis Cuentas</p>
                        <p class="text-3xl font-bold text-gray-800" data-counter="{{ $totalCuentas }}">{{ $totalCuentas }}</p>
                        <p class="text-sm text-blue-600 flex items-center mt-1">
                            <i class="fas fa-file-invoice mr-1"></i>
                            Total creadas
                        </p>
                    </div>
                    <div class="bg-gradient-to-br from-blue-400 to-indigo-600 w-12 h-12 rounded-xl flex items-center justify-center">
                        <i class="fas fa-file-alt text-white"></i>
                    </div>
                </div>
            </div>
            
            <!-- Valor facturado -->
            <div class="glass-card p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Total Facturado</p>
                        <p class="text-3xl font-bold text-gray-800" data-currency="{{ $totalFacturado }}">
                            ${{ number_format($totalFacturado, 0, ',', '.') }}
                        </p>
                        <p class="text-sm text-green-600 flex items-center mt-1">
                            <i class="fas fa-chart-line mr-1"></i>
                            Histórico
                        </p>
                    </div>
                    <div class="bg-gradient-to-br from-green-400 to-emerald-600 w-12 h-12 rounded-xl flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-white"></i>
                    </div>
                </div>
            </div>
            
            <!-- Pagos recibidos -->
            <div class="glass-card p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Total Pagado</p>
                        <p class="text-3xl font-bold text-gray-800" data-currency="{{ $totalPagado }}">
                            ${{ number_format($totalPagado, 0, ',', '.') }}
                        </p>
                        <p class="text-sm text-emerald-600 flex items-center mt-1">
                            <i class="fas fa-check-circle mr-1"></i>
                            {{ $cuentasPagadas }} cuentas
                        </p>
                    </div>
                    <div class="bg-gradient-to-br from-emerald-400 to-teal-600 w-12 h-12 rounded-xl flex items-center justify-center">
                        <i class="fas fa-money-bill-wave text-white"></i>
                    </div>
                </div>
            </div>
            
            <!-- Pendientes -->
            <div class="glass-card p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Valor Pendiente</p>
                        <p class="text-3xl font-bold text-gray-800" data-currency="{{ $valorPendiente }}">
                            ${{ number_format($valorPendiente, 0, ',', '.') }}
                        </p>
                        <p class="text-sm text-yellow-600 flex items-center mt-1">
                            <i class="fas fa-clock mr-1"></i>
                            En proceso
                        </p>
                    </div>
                    <div class="bg-gradient-to-br from-yellow-400 to-orange-500 w-12 h-12 rounded-xl flex items-center justify-center">
                        <i class="fas fa-hourglass-half text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estado de cuentas y acciones rápidas -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Panel de estado de cuentas -->
            <div class="lg:col-span-2">
                <div class="glass-card p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                            <i class="fas fa-chart-pie text-blue-500 mr-2"></i>
                            Estado de mis Cuentas de Cobro
                        </h3>
                        @if($porcentajeExito > 0)
                        <div class="text-sm text-gray-600">
                            <span class="font-medium text-green-600">{{ $porcentajeExito }}%</span> de éxito
                        </div>
                        @endif
                    </div>

                    <!-- Estados con barras de progreso -->
                    <div class="space-y-4">
                        @if($cuentasPagadas > 0)
                        <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg border border-green-200">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                    <i class="fas fa-check text-white text-sm"></i>
                                </div>
                                <div>
                                    <span class="font-medium text-green-800">Pagadas</span>
                                    <p class="text-sm text-green-600">{{ $cuentasPagadas }} cuentas completadas</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-lg font-bold text-green-800">{{ $cuentasPagadas }}</span>
                            </div>
                        </div>
                        @endif

                        @if($cuentasPendientes > 0)
                        <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg border border-blue-200">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                    <i class="fas fa-clock text-white text-sm"></i>
                                </div>
                                <div>
                                    <span class="font-medium text-blue-800">Pendientes</span>
                                    <p class="text-sm text-blue-600">{{ $cuentasPendientes }} esperando revisión</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-lg font-bold text-blue-800">{{ $cuentasPendientes }}</span>
                            </div>
                        </div>
                        @endif

                        @if($cuentasRevision > 0)
                        <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                                    <i class="fas fa-search text-white text-sm"></i>
                                </div>
                                <div>
                                    <span class="font-medium text-yellow-800">En Revisión</span>
                                    <p class="text-sm text-yellow-600">{{ $cuentasRevision }} siendo evaluadas</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-lg font-bold text-yellow-800">{{ $cuentasRevision }}</span>
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
                                <i class="fas fa-file-plus text-gray-400 text-xl"></i>
                            </div>
                            <h4 class="text-lg font-medium text-gray-600 mb-2">No tienes cuentas de cobro</h4>
                            <p class="text-gray-500 mb-4">Comienza creando tu primera cuenta de cobro</p>
                            <a href="{{ route('contratista.cuentas.crear') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-plus mr-2"></i>
                                Crear primera cuenta
                            </a>
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
                        <!-- Crear nueva cuenta -->
                        <a href="{{ route('contratista.cuentas.crear') }}" class="group block">
                            <div class="bg-gradient-to-br from-blue-50 to-indigo-100 p-4 rounded-xl border border-blue-200 hover:border-blue-300 transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1">
                                <div class="flex items-center space-x-3">
                                    <div class="bg-gradient-to-br from-blue-500 to-indigo-600 w-10 h-10 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                        <i class="fas fa-plus text-white"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-800">Nueva Cuenta</h4>
                                        <p class="text-sm text-gray-600">Crear solicitud de pago</p>
                                    </div>
                                </div>
                            </div>
                        </a>
                        
                        <!-- Ver mis cuentas -->
                        <a href="{{ route('contratista.cuentas.index') }}" class="group block">
                            <div class="bg-gradient-to-br from-green-50 to-emerald-100 p-4 rounded-xl border border-green-200 hover:border-green-300 transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1">
                                <div class="flex items-center space-x-3">
                                    <div class="bg-gradient-to-br from-green-500 to-emerald-600 w-10 h-10 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                        <i class="fas fa-list-alt text-white"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-800">Mis Cuentas</h4>
                                        <p class="text-sm text-gray-600">{{ $totalCuentas }} registradas</p>
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
                            <span class="text-sm text-gray-600">Cuentas creadas</span>
                            <span class="font-semibold text-gray-800">{{ $estadisticasMes['cuentas_mes'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Valor facturado</span>
                            <span class="font-semibold text-gray-800">${{ number_format($estadisticasMes['valor_mes'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Pagos recibidos</span>
                            <span class="font-semibold text-green-600">{{ $estadisticasMes['aprobadas_mes'] }}</span>
                        </div>
                        @if($tiempoPromedioAprobacion)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Tiempo promedio</span>
                            <span class="font-semibold text-blue-600">{{ $tiempoPromedioAprobacion }} días</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico de evolución mensual -->
        @if(count($evolucionMensual) > 0)
        <div class="glass-card p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-chart-line text-green-500 mr-2"></i>
                    Evolución Mensual
                </h3>
                <div class="flex items-center space-x-4 text-sm text-gray-600">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                        <span>Facturado</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                        <span>Pagado</span>
                    </div>
                </div>
            </div>
            <div class="h-64">
                <canvas id="evolucionChart"></canvas>
            </div>
        </div>
        @endif

        <!-- Próximos pagos y cuentas recientes -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Próximos pagos programados -->
            @if($proximosPagos->count() > 0)
            <div class="glass-card p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-calendar-check text-blue-500 mr-2"></i>
                    Próximos Pagos
                </h3>
                <div class="space-y-4">
                    @foreach($proximosPagos as $pago)
                    <div class="flex items-center justify-between p-4 bg-white rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                        <div class="flex items-center space-x-4">
                            <div class="w-3 h-3 rounded-full
                                @if($pago->estado === 'aprobado') bg-green-400
                                @elseif($pago->estado === 'revision') bg-yellow-400
                                @else bg-blue-400
                                @endif
                            "></div>
                            <div>
                                <p class="font-medium text-gray-800">Cuenta #{{ $pago->id }}</p>
                                <p class="text-sm text-gray-600">{{ Str::limit($pago->proyecto_servicio, 40) }}</p>
                                <p class="text-xs text-gray-500">{{ $pago->fecha_emision->format('d/m/Y') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-gray-800">${{ number_format($pago->valor, 0, ',', '.') }}</p>
                            <p class="text-sm 
                                @if($pago->estado === 'aprobado') text-green-600
                                @elseif($pago->estado === 'revision') text-yellow-600
                                @else text-blue-600
                                @endif
                            ">{{ ucfirst($pago->estado) }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @if($proximosPagos->count() >= 5)
                <div class="mt-4 text-center">
                    <a href="{{ route('contratista.cuentas.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        Ver todas las cuentas →
                    </a>
                </div>
                @endif
            </div>
            @endif

            <!-- Cuentas recientes -->
            @if($cuentasRecientes->count() > 0)
            <div class="glass-card p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-history text-purple-500 mr-2"></i>
                    Actividad Reciente
                </h3>
                <div class="space-y-4">
                    @foreach($cuentasRecientes as $cuenta)
                    <div class="flex items-center justify-between p-4 bg-white rounded-lg shadow-sm border border-gray-100">
                        <div class="flex items-center space-x-4">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center
                                @if($cuenta->estado === 'pagado') bg-green-100 text-green-600
                                @elseif($cuenta->estado === 'rechazado') bg-red-100 text-red-600
                                @elseif($cuenta->estado === 'aprobado') bg-blue-100 text-blue-600
                                @elseif($cuenta->estado === 'revision') bg-yellow-100 text-yellow-600
                                @else bg-gray-100 text-gray-600
                                @endif
                            ">
                                @if($cuenta->estado === 'pagado')
                                    <i class="fas fa-check text-xs"></i>
                                @elseif($cuenta->estado === 'rechazado')
                                    <i class="fas fa-times text-xs"></i>
                                @elseif($cuenta->estado === 'aprobado')
                                    <i class="fas fa-thumbs-up text-xs"></i>
                                @elseif($cuenta->estado === 'revision')
                                    <i class="fas fa-search text-xs"></i>
                                @else
                                    <i class="fas fa-clock text-xs"></i>
                                @endif
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">Cuenta #{{ $cuenta->id }}</p>
                                <p class="text-sm text-gray-600">{{ Str::limit($cuenta->proyecto_servicio, 35) }}</p>
                                <p class="text-xs text-gray-500">{{ $cuenta->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-gray-800">${{ number_format($cuenta->valor, 0, ',', '.') }}</p>
                            <p class="text-sm capitalize
                                @if($cuenta->estado === 'pagado') text-green-600
                                @elseif($cuenta->estado === 'rechazado') text-red-600
                                @elseif($cuenta->estado === 'aprobado') text-blue-600
                                @elseif($cuenta->estado === 'revision') text-yellow-600
                                @else text-gray-600
                                @endif
                            ">{{ $cuenta->estado_formateado }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Notificaciones dinámicas -->
        @if(count($notificaciones) > 0)
        <div class="glass-card p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-bell text-yellow-500 mr-2"></i>
                Notificaciones Importantes
                <span class="ml-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full">{{ count($notificaciones) }}</span>
            </h3>
            <div class="space-y-3">
                @foreach($notificaciones as $notificacion)
                <div class="flex items-start space-x-3 p-4 rounded-lg border transition-all duration-200 hover:shadow-md
                    @if($notificacion['type'] === 'error') bg-red-50 border-red-200
                    @elseif($notificacion['type'] === 'warning') bg-yellow-50 border-yellow-200
                    @elseif($notificacion['type'] === 'success') bg-green-50 border-green-200
                    @else bg-blue-50 border-blue-200
                    @endif
                ">
                    <i class="{{ $notificacion['icon'] }} mt-1
                        @if($notificacion['type'] === 'error') text-red-500
                        @elseif($notificacion['type'] === 'warning') text-yellow-500
                        @elseif($notificacion['type'] === 'success') text-green-500
                        @else text-blue-500
                        @endif
                    "></i>
                    <div class="flex-1">
                        <p class="text-sm font-medium
                            @if($notificacion['type'] === 'error') text-red-800
                            @elseif($notificacion['type'] === 'warning') text-yellow-800
                            @elseif($notificacion['type'] === 'success') text-green-800
                            @else text-blue-800
                            @endif
                        ">{{ $notificacion['title'] }}</p>
                        <p class="text-sm
                            @if($notificacion['type'] === 'error') text-red-600
                            @elseif($notificacion['type'] === 'warning') text-yellow-600
                            @elseif($notificacion['type'] === 'success') text-green-600
                            @else text-blue-600
                            @endif
                        ">{{ $notificacion['message'] }}</p>
                        @if(isset($notificacion['action']))
                        <a href="{{ $notificacion['action'] }}" class="inline-flex items-center text-sm font-medium mt-2 hover:underline
                            @if($notificacion['type'] === 'error') text-red-700
                            @elseif($notificacion['type'] === 'warning') text-yellow-700
                            @elseif($notificacion['type'] === 'success') text-green-700
                            @else text-blue-700
                            @endif
                        ">
                            {{ $notificacion['action_text'] ?? 'Ver más' }} →
                        </a>
                        @endif
                    </div>
                    @if($notificacion['priority'] === 'high')
                    <div class="flex-shrink-0">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            Urgente
                        </span>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if(count($notificaciones) === 0 && $totalCuentas > 0)
        <!-- Estado todo al día -->
        <div class="glass-card p-6">
            <div class="text-center py-6">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                </div>
                <h4 class="text-lg font-medium text-gray-700 mb-2">¡Todo al día!</h4>
                <p class="text-gray-500">No tienes notificaciones pendientes en este momento.</p>
                @if($ultimaFechaEnvio)
                <p class="text-sm text-gray-400 mt-2">
                    Último envío: {{ $ultimaFechaEnvio->diffForHumans() }}
                </p>
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
    
    .gradient-secondary {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
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
    
    .notification-badge {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.7;
        }
    }
    
    /* Chart container */
    #evolucionChart {
        width: 100% !important;
        height: 100% !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar el dashboard
    initializeContractorDashboard();
    
    // Inicializar gráfico si hay datos
    @if(count($evolucionMensual) > 0)
    initializeEvolutionChart();
    @endif
    
    // Configurar actualizaciones automáticas
    setupAutoRefresh();
});

function initializeContractorDashboard() {
    // Animar contadores al cargar
    animateCounters();
    
    // Configurar tooltips y efectos hover
    setupInteractiveElements();
    
    console.log('✅ Contractor Dashboard initialized');
}

function animateCounters() {
    const counters = document.querySelectorAll('[data-counter]');
    
    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-counter'));
        const increment = target / 50;
        let current = 0;
        
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                counter.textContent = target.toLocaleString();
                clearInterval(timer);
            } else {
                counter.textContent = Math.floor(current).toLocaleString();
            }
        }, 30);
    });
    
    // Animar valores monetarios
    const currencyElements = document.querySelectorAll('[data-currency]');
    currencyElements.forEach(element => {
        const target = parseInt(element.getAttribute('data-currency'));
        if (target > 0) {
            element.style.transform = 'scale(1.05)';
            setTimeout(() => {
                element.style.transform = 'scale(1)';
            }, 200);
        }
    });
}

@if(count($evolucionMensual) > 0)
function initializeEvolutionChart() {
    const ctx = document.getElementById('evolucionChart').getContext('2d');
    
    const chartData = {
        labels: {!! json_encode(array_column($evolucionMensual, 'month_name')) !!},
        datasets: [
            {
                label: 'Valor Facturado',
                data: {!! json_encode(array_column($evolucionMensual, 'total_valor')) !!},
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: 'rgb(59, 130, 246)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8
            },
            {
                label: 'Valor Pagado',
                data: {!! json_encode(array_column($evolucionMensual, 'valor_pagado')) !!},
                borderColor: 'rgb(16, 185, 129)',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: 'rgb(16, 185, 129)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8
            }
        ]
    };
    
    const config = {
        type: 'line',
        data: chartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: {
                            size: 12,
                            weight: '500'
                        }
                    }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: true,
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': $' + 
                                   new Intl.NumberFormat('es-CO').format(context.parsed.y);
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 11
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        font: {
                            size: 11
                        },
                        callback: function(value) {
                            return '$' + new Intl.NumberFormat('es-CO', {
                                notation: 'compact',
                                compactDisplay: 'short'
                            }).format(value);
                        }
                    }
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            },
            elements: {
                point: {
                    hoverBorderWidth: 3
                }
            }
        }
    };
    
    window.evolutionChart = new Chart(ctx, config);
}
@endif

function setupAutoRefresh() {
    // Actualizar datos cada 2 minutos
    setInterval(refreshDashboardData, 120000);
    
    // Actualizar cuando la ventana recupera el foco
    window.addEventListener('focus', () => {
        refreshDashboardData();
    });
}

function refreshDashboard() {
    const button = document.getElementById('refresh-btn') || event.target;
    const originalText = button.innerHTML;
    
    // Mostrar estado de carga
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i><span class="hidden sm:inline">Actualizando...</span><span class="sm:hidden">...</span>';
    button.disabled = true;
    button.classList.add('opacity-75', 'cursor-not-allowed');
    
    refreshDashboardData().then(() => {
        // Restaurar botón
        button.innerHTML = originalText;
        button.disabled = false;
        button.classList.remove('opacity-75', 'cursor-not-allowed');
        
        // Mostrar feedback
        showToast('Dashboard actualizado correctamente', 'success');
        
        // Actualizar timestamp
        const lastUpdateElement = document.getElementById('lastUpdate');
        if (lastUpdateElement) {
            lastUpdateElement.textContent = new Date().toLocaleTimeString('es-ES', {
                hour: '2-digit',
                minute: '2-digit'
            });
        }
        
        // Animar indicador de actualización
        const updateElement = document.querySelector('#lastUpdate').parentElement;
        updateElement.classList.add('animate-pulse');
        setTimeout(() => {
            updateElement.classList.remove('animate-pulse');
        }, 1000);
        
    }).catch((error) => {
        button.innerHTML = originalText;
        button.disabled = false;
        button.classList.remove('opacity-75', 'cursor-not-allowed');
        
        console.error('Error refreshing dashboard:', error);
        showToast('Error al actualizar el dashboard. Intente nuevamente.', 'error');
    });
}

async function refreshDashboardData() {
    try {
        const response = await fetch('{{ route("api.contratista.dashboard") }}', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        const result = await response.json();
        
        if (result.success) {
            updateDashboardElements(result.data);
        }
        
    } catch (error) {
        console.error('❌ Error refreshing dashboard:', error);
        throw error;
    }
}

function updateDashboardElements(data) {
    // Actualizar contadores
    updateCounter('[data-counter]', data.totalCuentas);
    updateCurrency('[data-currency]', data.totalFacturado);
    
    // Actualizar gráfico si existe
    if (window.evolutionChart && data.evolucionMensual) {
        updateEvolutionChart(data.evolucionMensual);
    }
    
    // Disparar evento personalizado
    window.dispatchEvent(new CustomEvent('dashboardRefreshed', { detail: data }));
}

function updateCounter(selector, value) {
    const element = document.querySelector(selector);
    if (element) {
        animateValueChange(element, parseInt(element.textContent.replace(/[^\d]/g, '')), value);
    }
}

function updateCurrency(selector, value) {
    const elements = document.querySelectorAll(selector);
    elements.forEach(element => {
        const currentValue = parseInt(element.getAttribute('data-currency'));
        if (currentValue !== value) {
            element.setAttribute('data-currency', value);
            element.textContent = '$' + new Intl.NumberFormat('es-CO').format(value);
            
            // Efecto visual
            element.style.transform = 'scale(1.05)';
            element.style.color = '#10b981';
            setTimeout(() => {
                element.style.transform = 'scale(1)';
                element.style.color = '';
            }, 300);
        }
    });
}

function animateValueChange(element, from, to) {
    const duration = 1000;
    const steps = 30;
    const stepValue = (to - from) / steps;
    const stepDuration = duration / steps;
    
    let current = from;
    let step = 0;
    
    const animation = setInterval(() => {
        step++;
        current += stepValue;
        
        if (step >= steps) {
            current = to;
            clearInterval(animation);
        }
        
        element.textContent = Math.round(current).toLocaleString();
        
    }, stepDuration);
}

function setupInteractiveElements() {
    // Tooltips para elementos interactivos
    const tooltipElements = document.querySelectorAll('[title]');
    tooltipElements.forEach(element => {
        element.addEventListener('mouseenter', showTooltip);
        element.addEventListener('mouseleave', hideTooltip);
    });
}

function showToast(message, type = 'info', duration = 3000) {
    // Remover toasts existentes del mismo tipo para evitar acumulación
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
    
    // Trigger animation
    setTimeout(() => {
        toast.style.transform = 'translateX(0)';
    }, 100);
    
    // Auto remove
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

// Funciones globales para debugging
window.contractorDashboard = {
    refresh: refreshDashboard,
    getData: refreshDashboardData,
    animateCounters: animateCounters
};
</script>
@endpush
@endsection
