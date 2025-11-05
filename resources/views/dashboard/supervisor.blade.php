@extends('dashboard.dashboard')

@section('dashboardRoles')
<!-- Panel específico para Supervisor -->
<div class="space-y-8">
    <!-- Estadísticas específicas del supervisor -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Cuentas por revisar -->
        <div class="glass-card p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Por Revisar</p>
                    <p class="text-3xl font-bold text-gray-800">8</p>
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
                    <p class="text-3xl font-bold text-gray-800">24</p>
                    <p class="text-sm text-green-600 flex items-center mt-1">
                        <i class="fas fa-check-circle mr-1"></i>
                        Este mes
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
                    <p class="text-3xl font-bold text-gray-800">3</p>
                    <p class="text-sm text-red-600 flex items-center mt-1">
                        <i class="fas fa-times-circle mr-1"></i>
                        Requieren corrección
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
                    <p class="text-3xl font-bold text-gray-800">2.5</p>
                    <p class="text-sm text-blue-600 flex items-center mt-1">
                        <i class="fas fa-stopwatch mr-1"></i>
                        Días de revisión
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
        
        <!-- Acciones principales de supervisión -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- Revisar cuentas pendientes -->
            <a href="{{ route('cuentas-cobro.mostrar') }}" class="group block">
                <div class="bg-gradient-to-br from-orange-50 to-red-100 p-6 rounded-xl border border-orange-200 hover:border-orange-300 transition-all duration-300 hover:shadow-lg transform hover:-translate-y-2">
                    <div class="text-center">
                        <div class="bg-gradient-to-br from-orange-500 to-red-600 w-16 h-16 rounded-xl mx-auto mb-4 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-search text-white text-xl"></i>
                        </div>
                        <h3 class="font-semibold text-gray-800 mb-2">Revisar Pendientes</h3>
                        <p class="text-sm text-gray-600">8 cuentas esperan revisión</p>
                    </div>
                </div>
            </a>
            
            <!-- Ver historial de aprobaciones -->
            <a href="{{ route('cuentas-cobro.mostrar') }}" class="group block">
                <div class="bg-gradient-to-br from-green-50 to-emerald-100 p-6 rounded-xl border border-green-200 hover:border-green-300 transition-all duration-300 hover:shadow-lg transform hover:-translate-y-2">
                    <div class="text-center">
                        <div class="bg-gradient-to-br from-green-500 to-emerald-600 w-16 h-16 rounded-xl mx-auto mb-4 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-history text-white text-xl"></i>
                        </div>
                        <h3 class="font-semibold text-gray-800 mb-2">Historial de Revisiones</h3>
                        <p class="text-sm text-gray-600">Consultar revisiones anteriores</p>
                    </div>
                </div>
            </a>
            
            <!-- Reportes de supervisión -->
            <a href="#" class="group block">
                <div class="bg-gradient-to-br from-blue-50 to-indigo-100 p-6 rounded-xl border border-blue-200 hover:border-blue-300 transition-all duration-300 hover:shadow-lg transform hover:-translate-y-2">
                    <div class="text-center">
                        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 w-16 h-16 rounded-xl mx-auto mb-4 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-chart-line text-white text-xl"></i>
                        </div>
                        <h3 class="font-semibold text-gray-800 mb-2">Reportes de Supervisión</h3>
                        <p class="text-sm text-gray-600">Estadísticas y métricas</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
    
    <!-- Lista de cuentas pendientes de revisión -->
    <div class="glass-card p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-clipboard-list text-orange-500 mr-2"></i>
                Cuentas Pendientes de Revisión
            </h3>
            <span class="gradient-secondary text-white px-3 py-1 rounded-full text-sm font-medium">8 pendientes</span>
        </div>
        
        <!-- Lista de cuentas por revisar -->
        <div class="space-y-4">
            <!-- Cuenta pendiente 1 -->
            <div class="flex items-center justify-between p-4 bg-white rounded-lg shadow-sm border border-gray-100 hover:border-orange-200 transition-all duration-300">
                <div class="flex items-center space-x-4">
                    <div class="w-3 h-3 bg-orange-400 rounded-full animate-pulse"></div>
                    <div>
                        <p class="font-medium text-gray-800">CC-2024-015</p>
                        <p class="text-sm text-gray-600">Servicios de desarrollo - Juan Pérez</p>
                        <p class="text-xs text-gray-500">Enviado hace 2 días</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="text-lg font-semibold text-gray-800">$12,000</span>
                    <div class="flex space-x-2">
                        <button class="bg-green-100 hover:bg-green-200 text-green-700 px-3 py-1 rounded-lg text-sm font-medium transition-all duration-200">
                            <i class="fas fa-check mr-1"></i>Aprobar
                        </button>
                        <button class="bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1 rounded-lg text-sm font-medium transition-all duration-200">
                            <i class="fas fa-times mr-1"></i>Rechazar
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Cuenta pendiente 2 -->
            <div class="flex items-center justify-between p-4 bg-white rounded-lg shadow-sm border border-gray-100 hover:border-orange-200 transition-all duration-300">
                <div class="flex items-center space-x-4">
                    <div class="w-3 h-3 bg-yellow-400 rounded-full animate-pulse"></div>
                    <div>
                        <p class="font-medium text-gray-800">CC-2024-016</p>
                        <p class="text-sm text-gray-600">Consultoría técnica - María González</p>
                        <p class="text-xs text-gray-500">Enviado hace 1 día</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="text-lg font-semibold text-gray-800">$8,500</span>
                    <div class="flex space-x-2">
                        <button class="bg-green-100 hover:bg-green-200 text-green-700 px-3 py-1 rounded-lg text-sm font-medium transition-all duration-200">
                            <i class="fas fa-check mr-1"></i>Aprobar
                        </button>
                        <button class="bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1 rounded-lg text-sm font-medium transition-all duration-200">
                            <i class="fas fa-times mr-1"></i>Rechazar
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Cuenta pendiente 3 -->
            <div class="flex items-center justify-between p-4 bg-white rounded-lg shadow-sm border border-gray-100 hover:border-orange-200 transition-all duration-300">
                <div class="flex items-center space-x-4">
                    <div class="w-3 h-3 bg-red-400 rounded-full animate-pulse"></div>
                    <div>
                        <p class="font-medium text-gray-800">CC-2024-017</p>
                        <p class="text-sm text-gray-600">Mantenimiento sistemas - Carlos López</p>
                        <p class="text-xs text-red-500">Enviado hace 5 días - Urgente</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="text-lg font-semibold text-gray-800">$15,000</span>
                    <div class="flex space-x-2">
                        <button class="bg-green-100 hover:bg-green-200 text-green-700 px-3 py-1 rounded-lg text-sm font-medium transition-all duration-200">
                            <i class="fas fa-check mr-1"></i>Aprobar
                        </button>
                        <button class="bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1 rounded-lg text-sm font-medium transition-all duration-200">
                            <i class="fas fa-times mr-1"></i>Rechazar
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Más cuentas... -->
            <div class="text-center pt-4">
                <a href="{{ route('cuentas-cobro.mostrar') }}" class="gradient-primary text-white px-6 py-2 rounded-lg font-medium hover:shadow-lg transition-all duration-300">
                    <i class="fas fa-list mr-2"></i>
                    Ver todas las cuentas pendientes
                </a>
            </div>
        </div>
    </div>
    
    <!-- Métricas de rendimiento -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Gráfico de rendimiento semanal -->
        <div class="glass-card p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-chart-bar text-blue-500 mr-2"></i>
                Rendimiento Semanal
            </h3>
            <div class="bg-gradient-to-r from-blue-50 to-indigo-100 p-6 rounded-lg">
                <div class="grid grid-cols-7 gap-2 mb-4">
                    <div class="text-center">
                        <div class="h-20 bg-blue-300 rounded-lg mb-2 flex items-end justify-center">
                            <div class="w-full h-3/4 bg-blue-500 rounded-lg"></div>
                        </div>
                        <span class="text-xs text-gray-600">Lun</span>
                    </div>
                    <div class="text-center">
                        <div class="h-20 bg-blue-300 rounded-lg mb-2 flex items-end justify-center">
                            <div class="w-full h-full bg-blue-500 rounded-lg"></div>
                        </div>
                        <span class="text-xs text-gray-600">Mar</span>
                    </div>
                    <div class="text-center">
                        <div class="h-20 bg-blue-300 rounded-lg mb-2 flex items-end justify-center">
                            <div class="w-full h-1/2 bg-blue-500 rounded-lg"></div>
                        </div>
                        <span class="text-xs text-gray-600">Mié</span>
                    </div>
                    <div class="text-center">
                        <div class="h-20 bg-blue-300 rounded-lg mb-2 flex items-end justify-center">
                            <div class="w-full h-4/5 bg-blue-500 rounded-lg"></div>
                        </div>
                        <span class="text-xs text-gray-600">Jue</span>
                    </div>
                    <div class="text-center">
                        <div class="h-20 bg-blue-300 rounded-lg mb-2 flex items-end justify-center">
                            <div class="w-full h-2/3 bg-blue-500 rounded-lg"></div>
                        </div>
                        <span class="text-xs text-gray-600">Vie</span>
                    </div>
                    <div class="text-center">
                        <div class="h-20 bg-blue-300 rounded-lg mb-2 flex items-end justify-center">
                            <div class="w-full h-1/4 bg-blue-500 rounded-lg"></div>
                        </div>
                        <span class="text-xs text-gray-600">Sáb</span>
                    </div>
                    <div class="text-center">
                        <div class="h-20 bg-blue-300 rounded-lg mb-2 flex items-end justify-center">
                            <div class="w-full h-1/3 bg-blue-500 rounded-lg"></div>
                        </div>
                        <span class="text-xs text-gray-600">Dom</span>
                    </div>
                </div>
                <p class="text-sm text-gray-600 text-center">Cuentas revisadas por día</p>
            </div>
        </div>
        
        <!-- Alertas y notificaciones -->
        <div class="glass-card p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-bell text-yellow-500 mr-2"></i>
                Alertas de Supervisión
            </h3>
            <div class="space-y-3">
                <div class="flex items-start space-x-3 p-3 bg-red-50 rounded-lg border border-red-200">
                    <i class="fas fa-exclamation-triangle text-red-500 mt-1"></i>
                    <div>
                        <p class="text-sm font-medium text-red-800">Cuenta urgente</p>
                        <p class="text-sm text-red-600">CC-2024-017 lleva 5 días sin revisar</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3 p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                    <i class="fas fa-clock text-yellow-500 mt-1"></i>
                    <div>
                        <p class="text-sm font-medium text-yellow-800">Meta de tiempo</p>
                        <p class="text-sm text-yellow-600">8 cuentas pendientes superan el tiempo promedio</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
                    <i class="fas fa-info-circle text-blue-500 mt-1"></i>
                    <div>
                        <p class="text-sm font-medium text-blue-800">Nuevo proceso</p>
                        <p class="text-sm text-blue-600">Nueva funcionalidad de aprobación rápida disponible</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection