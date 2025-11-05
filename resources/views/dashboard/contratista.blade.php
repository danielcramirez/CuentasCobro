@extends('dashboard.dashboard')

@section('dashboardRoles')
<!-- Panel específico para Contratista -->
<div class="space-y-8">
    <!-- Estadísticas específicas del contratista -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Mis cuentas de cobro -->
        <div class="glass-card p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Mis Cuentas</p>
                    <p class="text-3xl font-bold text-gray-800">12</p>
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
        
        <!-- Contratos activos -->
        <div class="glass-card p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Contratos Activos</p>
                    <p class="text-3xl font-bold text-gray-800">3</p>
                    <p class="text-sm text-green-600 flex items-center mt-1">
                        <i class="fas fa-handshake mr-1"></i>
                        En ejecución
                    </p>
                </div>
                <div class="bg-gradient-to-br from-green-400 to-emerald-600 w-12 h-12 rounded-xl flex items-center justify-center">
                    <i class="fas fa-contract text-white"></i>
                </div>
            </div>
        </div>
        
        <!-- Pagos pendientes -->
        <div class="glass-card p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Pagos Pendientes</p>
                    <p class="text-3xl font-bold text-gray-800">$45K</p>
                    <p class="text-sm text-yellow-600 flex items-center mt-1">
                        <i class="fas fa-clock mr-1"></i>
                        En proceso
                    </p>
                </div>
                <div class="bg-gradient-to-br from-yellow-400 to-orange-500 w-12 h-12 rounded-xl flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-white"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Panel principal del contratista -->
    <div class="glass-card p-8">
        <div class="text-center mb-8">
            <div class="gradient-primary w-20 h-20 rounded-2xl mx-auto mb-6 flex items-center justify-center shadow-xl">
                <i class="fas fa-user-tie text-white text-3xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2 font-poppins">Panel de Contratista</h2>
            <p class="text-gray-600">Gestiona tus cuentas de cobro y contratos de manera eficiente</p>
        </div>
        
        <!-- Acciones principales -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- Crear nueva cuenta -->
            <a href="{{ route('cuentas-cobro.crear') }}" class="group block">
                <div class="bg-gradient-to-br from-blue-50 to-indigo-100 p-6 rounded-xl border border-blue-200 hover:border-blue-300 transition-all duration-300 hover:shadow-lg transform hover:-translate-y-2">
                    <div class="text-center">
                        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 w-16 h-16 rounded-xl mx-auto mb-4 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-plus text-white text-xl"></i>
                        </div>
                        <h3 class="font-semibold text-gray-800 mb-2">Nueva Cuenta de Cobro</h3>
                        <p class="text-sm text-gray-600">Crear una nueva solicitud de pago</p>
                    </div>
                </div>
            </a>
            
            <!-- Ver mis cuentas -->
            <a href="{{ route('cuentas-cobro.mostrar') }}" class="group block">
                <div class="bg-gradient-to-br from-green-50 to-emerald-100 p-6 rounded-xl border border-green-200 hover:border-green-300 transition-all duration-300 hover:shadow-lg transform hover:-translate-y-2">
                    <div class="text-center">
                        <div class="bg-gradient-to-br from-green-500 to-emerald-600 w-16 h-16 rounded-xl mx-auto mb-4 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-list-alt text-white text-xl"></i>
                        </div>
                        <h3 class="font-semibold text-gray-800 mb-2">Mis Cuentas de Cobro</h3>
                        <p class="text-sm text-gray-600">Ver y gestionar mis solicitudes</p>
                    </div>
                </div>
            </a>
            
            <!-- Estado de contratos -->
            <a href="#" class="group block">
                <div class="bg-gradient-to-br from-purple-50 to-violet-100 p-6 rounded-xl border border-purple-200 hover:border-purple-300 transition-all duration-300 hover:shadow-lg transform hover:-translate-y-2">
                    <div class="text-center">
                        <div class="bg-gradient-to-br from-purple-500 to-violet-600 w-16 h-16 rounded-xl mx-auto mb-4 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-file-contract text-white text-xl"></i>
                        </div>
                        <h3 class="font-semibold text-gray-800 mb-2">Estado de Contratos</h3>
                        <p class="text-sm text-gray-600">Consultar contratos vigentes</p>
                    </div>
                </div>
            </a>
        </div>
        
        <!-- Cronograma de pagos -->
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 p-6 rounded-xl border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>
                Próximos Pagos Programados
            </h3>
            <div class="space-y-4">
                <!-- Pago 1 -->
                <div class="flex items-center justify-between p-4 bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="flex items-center space-x-4">
                        <div class="w-3 h-3 bg-green-400 rounded-full"></div>
                        <div>
                            <p class="font-medium text-gray-800">Cuenta #CC-2024-001</p>
                            <p class="text-sm text-gray-600">Servicios de consultoría - Octubre</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-gray-800">$15,000</p>
                        <p class="text-sm text-green-600">Aprobado</p>
                    </div>
                </div>
                
                <!-- Pago 2 -->
                <div class="flex items-center justify-between p-4 bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="flex items-center space-x-4">
                        <div class="w-3 h-3 bg-yellow-400 rounded-full"></div>
                        <div>
                            <p class="font-medium text-gray-800">Cuenta #CC-2024-002</p>
                            <p class="text-sm text-gray-600">Desarrollo de software - Noviembre</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-gray-800">$25,000</p>
                        <p class="text-sm text-yellow-600">En revisión</p>
                    </div>
                </div>
                
                <!-- Pago 3 -->
                <div class="flex items-center justify-between p-4 bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="flex items-center space-x-4">
                        <div class="w-3 h-3 bg-blue-400 rounded-full"></div>
                        <div>
                            <p class="font-medium text-gray-800">Cuenta #CC-2024-003</p>
                            <p class="text-sm text-gray-600">Mantenimiento sistema - Diciembre</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-gray-800">$8,500</p>
                        <p class="text-sm text-blue-600">Programado</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Notificaciones y alertas -->
    <div class="glass-card p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-bell text-yellow-500 mr-2"></i>
            Notificaciones Importantes
        </h3>
        <div class="space-y-3">
            <div class="flex items-start space-x-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
                <i class="fas fa-info-circle text-blue-500 mt-1"></i>
                <div>
                    <p class="text-sm font-medium text-blue-800">Nueva funcionalidad disponible</p>
                    <p class="text-sm text-blue-600">Ahora puedes subir documentos adjuntos a tus cuentas de cobro</p>
                </div>
            </div>
            <div class="flex items-start space-x-3 p-3 bg-green-50 rounded-lg border border-green-200">
                <i class="fas fa-check-circle text-green-500 mt-1"></i>
                <div>
                    <p class="text-sm font-medium text-green-800">Pago procesado exitosamente</p>
                    <p class="text-sm text-green-600">La cuenta #CC-2024-001 ha sido pagada y depositada</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
