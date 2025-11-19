@extends('layouts.dashboard')

@section('title', 'Dashboard Contratación')

@section('breadcrumb')
    @include('components.navigation.breadcrumb', [
        'items' => [
            ['label' => 'Dashboard Contratación']
        ]
    ])
@endsection

@section('dashboard-header')
    <div class="glass-card p-6 slide-up">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="gradient-primary w-16 h-16 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-file-contract text-white text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Dashboard Contratación</h1>
                    <p class="text-gray-600">Bienvenido, {{ $user->name }}</p>
                </div>
            </div>
            <div class="text-right">
                <div class="text-sm text-gray-500">Último acceso</div>
                <div class="text-gray-800 font-medium">{{ now()->format('d/m/Y H:i') }}</div>
            </div>
        </div>
    </div>
@endsection

@section('dashboard-content')
    <!-- Estadísticas principales -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Cuentas -->
        <div class="glass-card p-6 hover-scale">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-file-invoice text-blue-600 text-xl"></i>
                </div>
                <span class="text-xs font-medium text-gray-500 uppercase">Total</span>
            </div>
            <div class="text-3xl font-bold text-gray-800 mb-1">{{ $totalCuentas }}</div>
            <div class="text-sm text-gray-600">Cuentas de cobro</div>
        </div>

        <!-- Pendientes Contratación -->
        <div class="glass-card p-6 hover-scale">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
                <span class="text-xs font-medium text-gray-500 uppercase">Pendientes</span>
            </div>
            <div class="text-3xl font-bold text-yellow-600 mb-1">{{ $cuentasPendientesContratacion }}</div>
            <div class="text-sm text-gray-600">Requieren tu aprobación</div>
        </div>

        <!-- Aprobadas -->
        <div class="glass-card p-6 hover-scale">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <span class="text-xs font-medium text-gray-500 uppercase">Aprobadas</span>
            </div>
            <div class="text-3xl font-bold text-green-600 mb-1">{{ $cuentasAprobadas }}</div>
            <div class="text-sm text-gray-600">Cuentas aprobadas</div>
        </div>

        <!-- Valor Pendiente -->
        <div class="glass-card p-6 hover-scale">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-purple-600 text-xl"></i>
                </div>
                <span class="text-xs font-medium text-gray-500 uppercase">Valor</span>
            </div>
            <div class="text-2xl font-bold text-purple-600 mb-1">${{ number_format($valorTotalPendiente, 0, ',', '.') }}</div>
            <div class="text-sm text-gray-600">Pendiente</div>
        </div>
    </div>

    <!-- Notificaciones y acciones rápidas -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Notificaciones -->
        <div class="lg:col-span-2">
            <div class="glass-card p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-bell text-blue-500 mr-3"></i>
                    Notificaciones
                </h3>
                
                @if(count($notificaciones) > 0)
                    <div class="space-y-4">
                        @foreach($notificaciones as $notificacion)
                            <div class="border-l-4 rounded-lg p-4 transition-all hover:shadow-md
                                @if($notificacion['type'] === 'error') border-red-400 bg-red-50
                                @elseif($notificacion['type'] === 'warning') border-yellow-400 bg-yellow-50
                                @elseif($notificacion['type'] === 'success') border-green-400 bg-green-50
                                @else border-blue-400 bg-blue-50
                                @endif
                            ">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start space-x-3 flex-1">
                                        <div class="flex-shrink-0 mt-1">
                                            <i class="{{ $notificacion['icon'] }} 
                                                @if($notificacion['type'] === 'error') text-red-600
                                                @elseif($notificacion['type'] === 'warning') text-yellow-600
                                                @elseif($notificacion['type'] === 'success') text-green-600
                                                @else text-blue-600
                                                @endif
                                            "></i>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-gray-800 mb-1">{{ $notificacion['title'] }}</h4>
                                            <p class="text-sm text-gray-700">{{ $notificacion['message'] }}</p>
                                        </div>
                                    </div>
                                    @if(isset($notificacion['action']))
                                        <a href="{{ $notificacion['action'] }}" 
                                           class="ml-4 px-4 py-2 bg-white hover:bg-gray-50 border border-gray-300 rounded-lg text-sm font-medium transition-colors">
                                            {{ $notificacion['action_text'] ?? 'Ver' }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-check-circle text-green-500 text-4xl mb-3"></i>
                        <p class="text-gray-600">No hay notificaciones pendientes</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Acciones rápidas -->
        <div class="space-y-6">
            <!-- Enlaces rápidos -->
            <div class="glass-card p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-bolt text-yellow-500 mr-2"></i>
                    Acciones Rápidas
                </h3>
                <div class="space-y-3">
                    <a href="{{ route('contratacion.cuentas-cobro.index') }}" 
                       class="block w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-xl font-medium transition-all text-center">
                        <i class="fas fa-list mr-2"></i>
                        Ver Todas las Cuentas
                    </a>
                    <a href="{{ route('contratacion.cuentas-cobro.index', ['estado' => 'pendiente_contratacion']) }}" 
                       class="block w-full bg-yellow-600 hover:bg-yellow-700 text-white py-3 px-4 rounded-xl font-medium transition-all text-center">
                        <i class="fas fa-clock mr-2"></i>
                        Cuentas Pendientes
                    </a>
                </div>
            </div>

            <!-- Estadísticas del mes -->
            <div class="glass-card p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-calendar-alt text-purple-500 mr-2"></i>
                    Este Mes
                </h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Cuentas recibidas</span>
                        <span class="font-bold text-gray-800">{{ $estadisticasMes['cuentas_mes'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Aprobadas</span>
                        <span class="font-bold text-green-600">{{ $estadisticasMes['aprobadas_mes'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Rechazadas</span>
                        <span class="font-bold text-red-600">{{ $estadisticasMes['rechazadas_mes'] }}</span>
                    </div>
                    <div class="flex items-center justify-between pt-3 border-t">
                        <span class="text-gray-600">Valor total</span>
                        <span class="font-bold text-purple-600">${{ number_format($estadisticasMes['valor_mes'], 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cuentas recientes pendientes -->
    <div class="glass-card p-6">
        <h3 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
            <i class="fas fa-file-invoice text-purple-500 mr-3"></i>
            Cuentas Pendientes de Tu Aprobación
        </h3>
        
        @if($cuentasRecientes->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">ID</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Contratista</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Proyecto</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Valor</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Fecha</th>
                            <th class="text-center py-3 px-4 font-semibold text-gray-700">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cuentasRecientes as $cuenta)
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                <td class="py-3 px-4">
                                    <span class="font-medium text-gray-800">#{{ $cuenta->id }}</span>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-medium text-gray-800">{{ $cuenta->user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $cuenta->user->email }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="text-sm text-gray-800">{{ Str::limit($cuenta->proyecto_servicio, 40) }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="font-semibold text-green-600">${{ number_format($cuenta->valor, 0, ',', '.') }}</span>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="text-sm text-gray-600">{{ $cuenta->created_at->format('d/m/Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $cuenta->created_at->diffForHumans() }}</div>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <a href="{{ route('contratacion.cuentas-cobro.show', $cuenta->id) }}" 
                                       class="inline-flex items-center px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors">
                                        <i class="fas fa-eye mr-1"></i>
                                        Revisar
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8">
                <i class="fas fa-check-circle text-green-500 text-4xl mb-3"></i>
                <p class="text-gray-600">No hay cuentas pendientes de tu aprobación</p>
            </div>
        @endif
    </div>
@endsection
