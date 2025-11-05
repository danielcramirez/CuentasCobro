@extends('dashboard.dashboard')

@section('dashboardRoles')
<!-- Panel específico para Alcalde -->
<div class="space-y-8">
    <!-- Estadísticas generales mejoradas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Usuarios -->
        <div class="glass-card p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Usuarios</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $totalUsers ?? 0 }}</p>
                    <p class="text-sm text-blue-600 flex items-center mt-1">
                        <i class="fas fa-users mr-1"></i>
                        Registrados
                    </p>
                </div>
                <div class="bg-gradient-to-br from-blue-400 to-blue-600 w-12 h-12 rounded-xl flex items-center justify-center">
                    <i class="fas fa-users text-white"></i>
                </div>
            </div>
        </div>
        
        <!-- Usuarios con Rol -->
        <div class="glass-card p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Con Rol Asignado</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $usersWithRoles ?? 0 }}</p>
                    <p class="text-sm text-green-600 flex items-center mt-1">
                        <i class="fas fa-user-check mr-1"></i>
                        Activos
                    </p>
                </div>
                <div class="bg-gradient-to-br from-green-400 to-emerald-600 w-12 h-12 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-check text-white"></i>
                </div>
            </div>
        </div>
        
        <!-- Total Roles -->
        <div class="glass-card p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Roles Disponibles</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $totalRoles ?? 0 }}</p>
                    <p class="text-sm text-purple-600 flex items-center mt-1">
                        <i class="fas fa-users-cog mr-1"></i>
                        Configurados
                    </p>
                </div>
                <div class="bg-gradient-to-br from-purple-400 to-violet-600 w-12 h-12 rounded-xl flex items-center justify-center">
                    <i class="fas fa-users-cog text-white"></i>
                </div>
            </div>
        </div>
        
        <!-- Sin Rol Asignado -->
        <div class="glass-card p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Sin Rol</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $usersWithoutRoles ?? 0 }}</p>
                    <p class="text-sm text-orange-600 flex items-center mt-1">
                        <i class="fas fa-user-times mr-1"></i>
                        Pendientes
                    </p>
                </div>
                <div class="bg-gradient-to-br from-orange-400 to-red-500 w-12 h-12 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-times text-white"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Panel principal del alcalde -->
    <div class="glass-card p-8">
        <div class="text-center mb-8">
            <div class="gradient-primary w-20 h-20 rounded-2xl mx-auto mb-6 flex items-center justify-center shadow-xl">
                <i class="fas fa-crown text-white text-3xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2 font-poppins">Panel de Administración</h2>
            <p class="text-gray-600">Control total del sistema de cuentas de cobro</p>
        </div>
        
        <!-- Acciones administrativas -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- Gestión de roles -->
            <a href="{{ route('roles.index') }}" class="group block">
                <div class="bg-gradient-to-br from-indigo-50 to-purple-100 p-6 rounded-xl border border-indigo-200 hover:border-indigo-300 transition-all duration-300 hover:shadow-lg transform hover:-translate-y-2">
                    <div class="text-center">
                        <div class="bg-gradient-to-br from-indigo-500 to-purple-600 w-16 h-16 rounded-xl mx-auto mb-4 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-users-cog text-white text-xl"></i>
                        </div>
                        <h3 class="font-semibold text-gray-800 mb-2">Gestión de Roles</h3>
                        <p class="text-sm text-gray-600">Administrar roles y permisos</p>
                    </div>
                </div>
            </a>
            
            <!-- Crear nuevo rol -->
            <a href="{{ route('roles.create') }}" class="group block">
                <div class="bg-gradient-to-br from-green-50 to-emerald-100 p-6 rounded-xl border border-green-200 hover:border-green-300 transition-all duration-300 hover:shadow-lg transform hover:-translate-y-2">
                    <div class="text-center">
                        <div class="bg-gradient-to-br from-green-500 to-emerald-600 w-16 h-16 rounded-xl mx-auto mb-4 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-plus-circle text-white text-xl"></i>
                        </div>
                        <h3 class="font-semibold text-gray-800 mb-2">Crear Nuevo Rol</h3>
                        <p class="text-sm text-gray-600">Definir nuevos roles del sistema</p>
                    </div>
                </div>
            </a>
            
            <!-- Reportes generales -->
            <a href="#" class="group block">
                <div class="bg-gradient-to-br from-orange-50 to-red-100 p-6 rounded-xl border border-orange-200 hover:border-orange-300 transition-all duration-300 hover:shadow-lg transform hover:-translate-y-2">
                    <div class="text-center">
                        <div class="bg-gradient-to-br from-orange-500 to-red-600 w-16 h-16 rounded-xl mx-auto mb-4 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-chart-bar text-white text-xl"></i>
                        </div>
                        <h3 class="font-semibold text-gray-800 mb-2">Reportes Generales</h3>
                        <p class="text-sm text-gray-600">Análisis y estadísticas del sistema</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
    
    <!-- Sección de gestión de roles y usuarios -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Gestión de Roles -->
        <div class="glass-card p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-users-cog text-indigo-500 mr-2"></i>
                    Gestión de Roles
                </h3>
                <a href="{{ route('roles.index') }}" class="gradient-primary text-white px-4 py-2 rounded-lg text-sm font-medium hover:shadow-lg transition-all duration-300">
                    <i class="fas fa-eye mr-1"></i>
                    Ver Todos
                </a>
            </div>
            
            <!-- Acciones rápidas de roles -->
            <div class="grid grid-cols-2 gap-4 mb-6">
                <a href="{{ route('roles.index') }}" class="bg-blue-50 hover:bg-blue-100 p-4 rounded-lg text-center transition-all duration-300 border border-blue-200 hover:border-blue-300">
                    <i class="fas fa-list text-blue-500 text-xl mb-2"></i>
                    <p class="text-sm font-medium text-blue-700">Ver Roles</p>
                </a>
                <a href="{{ route('roles.create') }}" class="bg-green-50 hover:bg-green-100 p-4 rounded-lg text-center transition-all duration-300 border border-green-200 hover:border-green-300">
                    <i class="fas fa-plus text-green-500 text-xl mb-2"></i>
                    <p class="text-sm font-medium text-green-700">Crear Rol</p>
                </a>
            </div>
            
            <!-- Estadísticas de roles -->
            @if(isset($systemRoles) && isset($rolesStats))
            <div class="bg-gray-50 p-4 rounded-lg">
                <h4 class="text-sm font-semibold text-gray-700 mb-3">Distribución de Roles:</h4>
                <div class="space-y-2">
                    @foreach($systemRoles as $role)
                        @php
                            $roleData = $rolesStats->where('name', $role)->first();
                            $userCount = $roleData ? $roleData->users_count : 0;
                            $percentage = $totalUsers > 0 ? ($userCount / $totalUsers) * 100 : 0;
                        @endphp
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600 capitalize">{{ str_replace('_', ' ', $role) }}</span>
                            <div class="flex items-center space-x-2">
                                <div class="w-20 h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-blue-400 to-purple-500 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                                <span class="text-sm font-medium text-gray-800">{{ $userCount }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        
        <!-- Usuarios Recientes -->
        <div class="glass-card p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-6 flex items-center">
                <i class="fas fa-user-clock text-green-500 mr-2"></i>
                Usuarios Recientes
            </h3>
            
            @if(isset($recentUsers) && $recentUsers->count() > 0)
                <div class="space-y-4">
                    @foreach($recentUsers as $recentUser)
                        <div class="flex items-center justify-between p-4 bg-white rounded-lg border border-gray-100 hover:border-gray-200 transition-all duration-300">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 gradient-primary rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-white text-sm"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">{{ $recentUser->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $recentUser->email }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                @if($recentUser->role)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium gradient-secondary text-white">
                                        {{ ucfirst(str_replace('_', ' ', $recentUser->role->name)) }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                        Sin rol
                                    </span>
                                @endif
                                <p class="text-xs text-gray-500 mt-1">{{ $recentUser->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-users text-gray-300 text-4xl mb-4"></i>
                    <p class="text-gray-500">No hay usuarios registrados recientemente</p>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Panel de notificaciones administrativas -->
    <div class="glass-card p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-bell text-yellow-500 mr-2"></i>
            Alertas Administrativas
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @if(($usersWithoutRoles ?? 0) > 0)
            <div class="flex items-start space-x-3 p-4 bg-orange-50 rounded-lg border border-orange-200">
                <i class="fas fa-exclamation-triangle text-orange-500 mt-1"></i>
                <div>
                    <p class="text-sm font-medium text-orange-800">Usuarios sin rol asignado</p>
                    <p class="text-sm text-orange-600">{{ $usersWithoutRoles }} usuarios necesitan rol</p>
                </div>
            </div>
            @endif
            
            <div class="flex items-start space-x-3 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <i class="fas fa-info-circle text-blue-500 mt-1"></i>
                <div>
                    <p class="text-sm font-medium text-blue-800">Sistema actualizado</p>
                    <p class="text-sm text-blue-600">Nuevas funcionalidades disponibles</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection