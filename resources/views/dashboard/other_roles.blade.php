@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @switch($userRole)
            @case('ordenador_gasto')
                <!-- Dashboard Ordenador del Gasto -->
                <div class="mb-8">
                    <div class="text-center mb-8">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full mb-4 shadow-lg animate-pulse">
                            <i class="fas fa-money-check-alt text-white text-3xl"></i>
                        </div>
                        <h1 class="text-4xl font-bold text-gray-900 mb-2">Dashboard Ordenador del Gasto</h1>
                        <p class="text-xl text-gray-600">Gestión de presupuestos y autorizaciones de pago</p>
                    </div>

                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <div class="glass-card p-6 text-center transform hover:scale-105 transition-all duration-300">
                            <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-check-circle text-white text-xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-1">45</h3>
                            <p class="text-gray-600 text-sm">Pagos Autorizados</p>
                        </div>
                        
                        <div class="glass-card p-6 text-center transform hover:scale-105 transition-all duration-300">
                            <div class="w-12 h-12 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-clock text-white text-xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-1">12</h3>
                            <p class="text-gray-600 text-sm">Pendientes</p>
                        </div>
                        
                        <div class="glass-card p-6 text-center transform hover:scale-105 transition-all duration-300">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-chart-line text-white text-xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-1">$2.5M</h3>
                            <p class="text-gray-600 text-sm">Presupuesto Total</p>
                        </div>
                        
                        <div class="glass-card p-6 text-center transform hover:scale-105 transition-all duration-300">
                            <div class="w-12 h-12 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-percentage text-white text-xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-1">78%</h3>
                            <p class="text-gray-600 text-sm">Ejecutado</p>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="glass-card p-6 mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Acciones Rápidas</h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <button class="p-4 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all duration-300 transform hover:scale-105">
                                <i class="fas fa-check-double text-2xl mb-2"></i>
                                <p class="font-semibold">Autorizar Pagos</p>
                            </button>
                            <button class="p-4 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl hover:from-green-600 hover:to-emerald-700 transition-all duration-300 transform hover:scale-105">
                                <i class="fas fa-chart-pie text-2xl mb-2"></i>
                                <p class="font-semibold">Ver Presupuesto</p>
                            </button>
                            <button class="p-4 bg-gradient-to-r from-purple-500 to-pink-600 text-white rounded-xl hover:from-purple-600 hover:to-pink-700 transition-all duration-300 transform hover:scale-105">
                                <i class="fas fa-file-alt text-2xl mb-2"></i>
                                <p class="font-semibold">Generar Reporte</p>
                            </button>
                        </div>
                    </div>
                </div>
                @break

            @case('tesoreria')
                <!-- Dashboard Tesorería -->
                <div class="mb-8">
                    <div class="text-center mb-8">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full mb-4 shadow-lg animate-pulse">
                            <i class="fas fa-coins text-white text-3xl"></i>
                        </div>
                        <h1 class="text-4xl font-bold text-gray-900 mb-2">Dashboard Tesorería</h1>
                        <p class="text-xl text-gray-600">Gestión financiera y procesamiento de pagos</p>
                    </div>

                    <!-- Financial Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <div class="glass-card p-6 text-center transform hover:scale-105 transition-all duration-300">
                            <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-dollar-sign text-white text-xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-1">$1.8M</h3>
                            <p class="text-gray-600 text-sm">Pagos Procesados</p>
                        </div>
                        
                        <div class="glass-card p-6 text-center transform hover:scale-105 transition-all duration-300">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-university text-white text-xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-1">$750K</h3>
                            <p class="text-gray-600 text-sm">Saldo Disponible</p>
                        </div>
                        
                        <div class="glass-card p-6 text-center transform hover:scale-105 transition-all duration-300">
                            <div class="w-12 h-12 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-hourglass-half text-white text-xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-1">8</h3>
                            <p class="text-gray-600 text-sm">Pagos Pendientes</p>
                        </div>
                        
                        <div class="glass-card p-6 text-center transform hover:scale-105 transition-all duration-300">
                            <div class="w-12 h-12 bg-gradient-to-br from-indigo-400 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-calendar-check text-white text-xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-1">95%</h3>
                            <p class="text-gray-600 text-sm">Eficiencia</p>
                        </div>
                    </div>

                    <!-- Treasury Actions -->
                    <div class="glass-card p-6 mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Operaciones de Tesorería</h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <button class="p-4 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl hover:from-green-600 hover:to-emerald-700 transition-all duration-300 transform hover:scale-105">
                                <i class="fas fa-credit-card text-2xl mb-2"></i>
                                <p class="font-semibold">Procesar Pagos</p>
                            </button>
                            <button class="p-4 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all duration-300 transform hover:scale-105">
                                <i class="fas fa-chart-bar text-2xl mb-2"></i>
                                <p class="font-semibold">Reportes Financieros</p>
                            </button>
                            <button class="p-4 bg-gradient-to-r from-purple-500 to-pink-600 text-white rounded-xl hover:from-purple-600 hover:to-pink-700 transition-all duration-300 transform hover:scale-105">
                                <i class="fas fa-sync text-2xl mb-2"></i>
                                <p class="font-semibold">Conciliaciones</p>
                            </button>
                        </div>
                    </div>
                </div>
                @break

            @case('contratacion')
                <!-- Dashboard Contratación -->
                <div class="mb-8">
                    <div class="text-center mb-8">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-purple-500 to-pink-600 rounded-full mb-4 shadow-lg animate-pulse">
                            <i class="fas fa-handshake text-white text-3xl"></i>
                        </div>
                        <h1 class="text-4xl font-bold text-gray-900 mb-2">Dashboard Contratación</h1>
                        <p class="text-xl text-gray-600">Gestión de contratos y contratistas</p>
                    </div>

                    <!-- Contract Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <div class="glass-card p-6 text-center transform hover:scale-105 transition-all duration-300">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-file-contract text-white text-xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-1">156</h3>
                            <p class="text-gray-600 text-sm">Contratos Activos</p>
                        </div>
                        
                        <div class="glass-card p-6 text-center transform hover:scale-105 transition-all duration-300">
                            <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-users text-white text-xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-1">89</h3>
                            <p class="text-gray-600 text-sm">Contratistas</p>
                        </div>
                        
                        <div class="glass-card p-6 text-center transform hover:scale-105 transition-all duration-300">
                            <div class="w-12 h-12 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-clock text-white text-xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-1">23</h3>
                            <p class="text-gray-600 text-sm">Por Vencer</p>
                        </div>
                        
                        <div class="glass-card p-6 text-center transform hover:scale-105 transition-all duration-300">
                            <div class="w-12 h-12 bg-gradient-to-br from-red-400 to-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-1">5</h3>
                            <p class="text-gray-600 text-sm">Vencidos</p>
                        </div>
                    </div>

                    <!-- Contract Management Actions -->
                    <div class="glass-card p-6 mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Gestión de Contratos</h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <button class="p-4 bg-gradient-to-r from-purple-500 to-pink-600 text-white rounded-xl hover:from-purple-600 hover:to-pink-700 transition-all duration-300 transform hover:scale-105">
                                <i class="fas fa-plus-circle text-2xl mb-2"></i>
                                <p class="font-semibold">Nuevo Contrato</p>
                            </button>
                            <button class="p-4 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all duration-300 transform hover:scale-105">
                                <i class="fas fa-search text-2xl mb-2"></i>
                                <p class="font-semibold">Buscar Contratistas</p>
                            </button>
                            <button class="p-4 bg-gradient-to-r from-orange-500 to-red-600 text-white rounded-xl hover:from-orange-600 hover:to-red-700 transition-all duration-300 transform hover:scale-105">
                                <i class="fas fa-calendar-times text-2xl mb-2"></i>
                                <p class="font-semibold">Revisar Vencimientos</p>
                            </button>
                        </div>
                    </div>
                </div>
                @break
        @endswitch

        <!-- Coming Soon Notice -->
        <div class="glass-card p-8 text-center">
            <div class="w-16 h-16 bg-gradient-to-br from-blue-400 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-cog text-white text-2xl animate-spin"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">Funcionalidades en Desarrollo</h3>
            <p class="text-gray-600 mb-4">Estamos trabajando para implementar todas las características específicas de este rol.</p>
            <div class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-100 to-purple-100 text-blue-800 rounded-full text-sm font-medium">
                <i class="fas fa-rocket mr-2"></i>
                Próximamente disponible
            </div>
        </div>
    </div>
</div>

<style>
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.glass-card {
    animation: fadeInUp 0.6s ease-out;
}

.glass-card:nth-child(1) { animation-delay: 0.1s; }
.glass-card:nth-child(2) { animation-delay: 0.2s; }
.glass-card:nth-child(3) { animation-delay: 0.3s; }
.glass-card:nth-child(4) { animation-delay: 0.4s; }
</style>
@endsection
