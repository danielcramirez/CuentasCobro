@extends('layouts.dashboard')

@section('title', 'Dashboard Ordenador de Gasto')

@section('breadcrumb')
    @include('components.navigation.breadcrumb', [
        'items' => [
            ['label' => 'Dashboard Ordenador de Gasto']
        ]
    ])
@endsection

@section('dashboard-header')
    <div class="glass-card p-6 slide-up">
        <div class="flex items-center space-x-4">
            <div class="gradient-secondary w-16 h-16 rounded-2xl flex items-center justify-center">
                <i class="fas fa-file-signature text-white text-2xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Dashboard Ordenador de Gasto</h1>
                <p class="text-gray-600">Aprobación final de cuentas de cobro</p>
            </div>
        </div>
    </div>
@endsection

@section('dashboard-content')
    <!-- Estadísticas principales -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="glass-card p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Pendientes Aprobación</p>
                    <p class="text-3xl font-bold text-orange-600">{{ $cuentasPendientes }}</p>
                    <p class="text-sm text-orange-500 flex items-center mt-1">
                        <i class="fas fa-clock mr-1"></i>
                        Requieren revisión
                    </p>
                </div>
                <div class="bg-gradient-to-br from-orange-400 to-orange-600 w-12 h-12 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clock text-white"></i>
                </div>
            </div>
        </div>

        <div class="glass-card p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Aprobadas</p>
                    <p class="text-3xl font-bold text-green-600">{{ $cuentasAprobadas }}</p>
                    <p class="text-sm text-green-500 flex items-center mt-1">
                        <i class="fas fa-check-circle mr-1"></i>
                        Listas para pago
                    </p>
                </div>
                <div class="bg-gradient-to-br from-green-400 to-green-600 w-12 h-12 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-white"></i>
                </div>
            </div>
        </div>

        <div class="glass-card p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Valor Pendiente</p>
                    <p class="text-2xl font-bold text-gray-800">${{ number_format($valorPendiente, 0, ',', '.') }}</p>
                    <p class="text-sm text-gray-500 mt-1">COP</p>
                </div>
                <div class="bg-gradient-to-br from-blue-400 to-blue-600 w-12 h-12 rounded-xl flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-white"></i>
                </div>
            </div>
        </div>

        <div class="glass-card p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Pagadas</p>
                    <p class="text-3xl font-bold text-indigo-600">{{ $cuentasPagadas }}</p>
                    <p class="text-sm text-indigo-500 flex items-center mt-1">
                        <i class="fas fa-money-bill-wave mr-1"></i>
                        Completadas
                    </p>
                </div>
                <div class="bg-gradient-to-br from-indigo-400 to-indigo-600 w-12 h-12 rounded-xl flex items-center justify-center">
                    <i class="fas fa-money-bill-wave text-white"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Accesos rápidos -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <a href="{{ route('ordenador-gasto.cuentas') }}" class="glass-card p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 group">
            <div class="flex items-center space-x-4">
                <div class="bg-gradient-to-br from-blue-500 to-indigo-600 w-12 h-12 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fas fa-list text-white"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800 group-hover:text-blue-600 transition-colors">Todas las Cuentas</h3>
                    <p class="text-sm text-gray-600">Ver listado completo</p>
                </div>
            </div>
        </a>
        
        <a href="{{ route('ordenador-gasto.cuentas', ['estado' => 'pendiente_ordenador']) }}" class="glass-card p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 group">
            <div class="flex items-center space-x-4">
                <div class="bg-gradient-to-br from-orange-500 to-red-600 w-12 h-12 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fas fa-exclamation-circle text-white"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800 group-hover:text-orange-600 transition-colors">Pendientes</h3>
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

    <!-- Notificaciones -->
    @if(!empty($notificaciones))
    <div class="glass-card p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4 flex items-center">
            <i class="fas fa-bell mr-2 text-blue-600"></i>
            Notificaciones
        </h2>
        <div class="space-y-3">
            @foreach($notificaciones as $notificacion)
                <div class="flex items-start p-4 rounded-xl border-l-4 
                    @if($notificacion['type'] === 'danger') bg-red-50 border-red-500
                    @elseif($notificacion['type'] === 'warning') bg-yellow-50 border-yellow-500
                    @elseif($notificacion['type'] === 'success') bg-green-50 border-green-500
                    @else bg-blue-50 border-blue-500 @endif">
                    <i class="{{ $notificacion['icon'] }} 
                        @if($notificacion['type'] === 'danger') text-red-500
                        @elseif($notificacion['type'] === 'warning') text-yellow-500
                        @elseif($notificacion['type'] === 'success') text-green-500
                        @else text-blue-500 @endif text-xl mr-3 mt-1"></i>
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-800">{{ $notificacion['title'] }}</h3>
                        <p class="text-sm text-gray-600">{{ $notificacion['message'] }}</p>
                        @if(isset($notificacion['action']))
                            <a href="{{ $notificacion['action'] }}" 
                               class="text-sm font-medium mt-2 inline-block
                               @if($notificacion['type'] === 'danger') text-red-600 hover:text-red-800
                               @elseif($notificacion['type'] === 'warning') text-yellow-600 hover:text-yellow-800
                               @else text-blue-600 hover:text-blue-800 @endif">
                                {{ $notificacion['action_text'] }} <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Cuentas recientes pendientes -->
    @if($cuentasRecientes->isNotEmpty())
    <div class="glass-card overflow-hidden">
        <div class="bg-gradient-to-r from-orange-600 to-red-700 px-6 py-4">
            <h2 class="text-xl font-bold text-white">Cuentas Pendientes de Aprobación</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contratista</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Proyecto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($cuentasRecientes as $cuenta)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-800">{{ $cuenta->user->name }}</div>
                                <div class="text-sm text-gray-500">ID: #{{ $cuenta->id }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ Str::limit($cuenta->proyecto_servicio, 40) }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-800">${{ number_format($cuenta->valor, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $cuenta->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('ordenador-gasto.show', $cuenta->id) }}" 
                                   class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-800 rounded-lg hover:bg-blue-200 transition-colors text-sm font-medium">
                                    <i class="fas fa-eye mr-1"></i>
                                    Revisar
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
@endsection
