@extends('layouts.app')

@section('title', 'Ver Cuenta de Cobro - CuentasCobro')

@section('content')
<!-- Contenedor principal con padding superior para navbar -->
<div class="min-h-screen pt-24 pb-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto">
        
        <!-- Breadcrumb de navegación -->
        <div class="mb-6">
            <nav class="flex items-center space-x-2 text-sm text-gray-500" aria-label="Breadcrumb">
                <a href="{{ route('dashboard') }}" class="hover:text-gray-700 transition-colors flex items-center">
                    <i class="fas fa-home mr-1"></i>
                    Inicio
                </a>
                <i class="fas fa-chevron-right text-gray-300"></i>
                @if(auth()->user()->hasRole('contratista'))
                    <a href="{{ route('contratista.dashboard') }}" class="hover:text-gray-700 transition-colors">
                        Dashboard Contratista
                    </a>
                    <i class="fas fa-chevron-right text-gray-300"></i>
                @endif
                <a href="{{ route('cuentas-cobro.mostrar') }}" class="hover:text-gray-700 transition-colors">
                    Cuentas de Cobro
                </a>
                <i class="fas fa-chevron-right text-gray-300"></i>
                <span class="text-gray-700 font-medium">Ver #{{ $cuenta->id }}</span>
            </nav>
        </div>
        
        <!-- Header mejorado -->
        <div class="glass-card p-6 mb-8 slide-up">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="flex items-center space-x-4">
                    <div class="gradient-primary w-12 h-12 sm:w-16 sm:h-16 rounded-2xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-file-invoice-dollar text-white text-lg sm:text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-xl sm:text-2xl lg:text-4xl font-bold text-gray-800 font-poppins">Cuenta de Cobro #{{ $cuenta->id }}</h1>
                        <p class="text-sm sm:text-base lg:text-lg text-gray-600 mt-1">{{ $cuenta->proyecto_servicio }}</p>
                    </div>
                </div>
                
                <!-- Botones de acción -->
                <div class="flex flex-col sm:flex-row gap-3">
                    @if(auth()->user()->hasRole('contratista'))
                        <a href="{{ route('contratista.dashboard') }}" 
                           class="inline-flex items-center justify-center px-4 sm:px-6 py-3 border-2 border-purple-300 text-purple-700 bg-purple-50 rounded-xl hover:bg-purple-100 hover:border-purple-400 transition-all duration-300 font-medium text-sm sm:text-base">
                            <i class="fas fa-home mr-2"></i>
                            <span class="hidden sm:inline">Dashboard</span>
                            <span class="sm:hidden">Inicio</span>
                        </a>
                    @endif
                    <a href="{{ route('cuentas-cobro.edit', $cuenta->id) }}" 
                       class="inline-flex items-center justify-center px-4 sm:px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition-all duration-300 font-medium text-sm sm:text-base">
                        <i class="fas fa-edit mr-2"></i>
                        <span class="hidden sm:inline">Editar Cuenta</span>
                        <span class="sm:hidden">Editar</span>
                    </a>
                    <a href="{{ route('cuentas-cobro.mostrar') }}" 
                       class="inline-flex items-center justify-center px-4 sm:px-6 py-3 border-2 border-gray-300 text-gray-700 bg-white rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all duration-300 font-medium text-sm sm:text-base">
                        <i class="fas fa-arrow-left mr-2"></i>
                        <span class="hidden sm:inline">Volver a Lista</span>
                        <span class="sm:hidden">Volver</span>
                    </a>
                </div>
            </div>
        </div><!-- Tarjeta de información principal con estado -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-8 py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-file-invoice text-white text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-white">Detalles Completos</h2>
                            <p class="text-blue-100 text-sm">Visualización de la cuenta de cobro</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <!-- Badge de estado grande y visible -->
                        <span class="inline-flex items-center px-5 py-2.5 rounded-full text-base font-bold shadow-lg
                            @if($cuenta->estado === 'pagado') bg-green-500 text-white
                            @elseif($cuenta->estado === 'aprobado') bg-blue-500 text-white
                            @elseif($cuenta->estado === 'rechazado') bg-red-500 text-white
                            @elseif($cuenta->estado === 'revision') bg-yellow-500 text-white
                            @elseif($cuenta->estado === 'pendiente') bg-orange-500 text-white
                            @else bg-gray-500 text-white
                            @endif
                        ">
                            <i class="fas fa-circle text-xs mr-2 animate-pulse"></i>
                            {{ strtoupper($cuenta->estado) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Contenido principal -->
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    
                    <!-- Información del contratista -->
                    <div class="space-y-6">
                        <div class="border-l-4 border-blue-500 pl-4">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-user-tie text-blue-500 mr-2"></i>
                                Información del Contratista
                            </h3>
                            <div class="space-y-3">
                                <div class="flex items-start">
                                    <div class="w-24 text-sm font-medium text-gray-500">Nombre:</div>
                                    <div class="flex-1 text-gray-800 font-medium">{{ $cuenta->user->name }}</div>
                                </div>
                                <div class="flex items-start">
                                    <div class="w-24 text-sm font-medium text-gray-500">Email:</div>
                                    <div class="flex-1 text-gray-800">{{ $cuenta->user->email }}</div>
                                </div>
                                <div class="flex items-start">
                                    <div class="w-24 text-sm font-medium text-gray-500">Usuario ID:</div>
                                    <div class="flex-1 text-gray-800">{{ $cuenta->user_id }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Información de fechas -->
                        <div class="border-l-4 border-green-500 pl-4">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-calendar-alt text-green-500 mr-2"></i>
                                Fechas
                            </h3>
                            <div class="space-y-3">
                                <div class="flex items-start">
                                    <div class="w-32 text-sm font-medium text-gray-500">Fecha emisión:</div>
                                    <div class="flex-1 text-gray-800 font-medium">
                                        {{ \Carbon\Carbon::parse($cuenta->fecha_emision)->format('d/m/Y') }}
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <div class="w-32 text-sm font-medium text-gray-500">Fecha creación:</div>
                                    <div class="flex-1 text-gray-800">
                                        {{ $cuenta->created_at->format('d/m/Y H:i') }}
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <div class="w-32 text-sm font-medium text-gray-500">Última actualización:</div>
                                    <div class="flex-1 text-gray-800">
                                        {{ $cuenta->updated_at->format('d/m/Y H:i') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información financiera y proyecto -->
                    <div class="space-y-6">
                        <!-- Valor -->
                        <div class="bg-gradient-to-br from-green-50 to-emerald-100 rounded-xl p-6 border border-green-200">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-green-700">Valor Total</span>
                                <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
                            </div>
                            <div class="text-3xl font-bold text-green-800">
                                ${{ number_format($cuenta->valor, 0, ',', '.') }}
                            </div>
                            <div class="text-sm text-green-600 mt-2">
                                {{--<!--{{ \NumberFormatter::create('es_CO', \NumberFormatter::SPELLOUT)->format($cuenta->valor) }} pesos -->--}}
                                COP
                            </div>
                        </div>

                        <!-- Proyecto/Servicio -->
                        <div class="border-l-4 border-purple-500 pl-4">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-project-diagram text-purple-500 mr-2"></i>
                                Proyecto/Servicio
                            </h3>
                            <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                                <p class="text-gray-800 leading-relaxed">
                                    {{ $cuenta->proyecto_servicio }}
                                </p>
                            </div>
                        </div>

                        <!-- Estado y observaciones -->
                        <div class="border-l-4 border-indigo-500 pl-4">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-info-circle text-indigo-500 mr-2"></i>
                                Estado Actual
                            </h3>
                            <div class="space-y-2">
                                <div class="flex items-center space-x-2">
                                    <div class="w-3 h-3 rounded-full
                                        @if($cuenta->estado === 'pagado') bg-green-500
                                        @elseif($cuenta->estado === 'aprobado') bg-blue-500
                                        @elseif($cuenta->estado === 'rechazado') bg-red-500
                                        @elseif($cuenta->estado === 'revision') bg-yellow-500
                                        @elseif($cuenta->estado === 'pendiente') bg-orange-500
                                        @else bg-gray-500
                                        @endif
                                    "></div>
                                    <span class="text-gray-800 font-medium">{{ ucfirst($cuenta->estado) }}</span>
                                </div>
                                <div class="text-sm text-gray-600 bg-gray-50 rounded-lg p-3 mt-2">
                                    <p class="font-medium mb-1">Descripción del estado:</p>
                                    <p>
                                        @if($cuenta->estado === 'borrador')
                                            La cuenta de cobro está en borrador y puede ser editada.
                                        @elseif($cuenta->estado === 'pendiente')
                                            La cuenta ha sido enviada y está pendiente de revisión.
                                        @elseif($cuenta->estado === 'revision')
                                            La cuenta está siendo revisada por el supervisor.
                                        @elseif($cuenta->estado === 'aprobado')
                                            La cuenta ha sido aprobada y está lista para pago.
                                        @elseif($cuenta->estado === 'rechazado')
                                            La cuenta ha sido rechazada. Revisa las observaciones.
                                        @elseif($cuenta->estado === 'pagado')
                                            La cuenta ha sido pagada exitosamente.
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Timeline de estados (opcional) -->
                <div class="mt-8 pt-8 border-t border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-history text-gray-500 mr-2"></i>
                        Historial de la Cuenta
                    </h3>
                    <div class="bg-gray-50 rounded-lg p-6">
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-clock text-gray-400"></i>
                                <span class="text-gray-600">Creada hace: {{ $cuenta->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-edit text-gray-400"></i>
                                <span class="text-gray-600">Última modificación: {{ $cuenta->updated_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Acciones disponibles -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-cog text-blue-500 mr-2"></i>
                Acciones Disponibles
            </h3>            <div class="flex flex-wrap gap-3">
                <!-- Botón editar (solo si es editable) -->
                @php
                    $user = Auth::user();
                    $esContratista = $user && optional($user->role)->name === 'contratista';
                    $esSuCuenta = $cuenta->user_id === optional($user)->id;
                @endphp
                
                @if($cuenta->esEditable() && $esContratista && $esSuCuenta)
                <a href="{{ route('cuentas-cobro.edit', $cuenta->id) }}" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                    <i class="fas fa-edit mr-2"></i>
                    Editar Cuenta
                </a>
                @endif

                <!-- Botón volver -->
                <a href="{{ route('cuentas-cobro.mostrar') }}" class="inline-flex items-center px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all duration-300 border border-gray-300">
                    <i class="fas fa-list mr-2"></i>
                    Ver todas las cuentas
                </a>

                <!-- Botón imprimir -->
                <button onclick="window.print()" class="inline-flex items-center px-5 py-2.5 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-all duration-300 border border-green-300">
                    <i class="fas fa-print mr-2"></i>
                    Imprimir
                </button>

                @if($esContratista && $esSuCuenta && $cuenta->estado === 'borrador')
                <!-- Botón eliminar -->
                <form action="{{ route('cuentas-cobro.destroy', $cuenta->id) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta cuenta de cobro?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-all duration-300 border border-red-300">
                        <i class="fas fa-trash-alt mr-2"></i>
                        Eliminar
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Estilos para impresión */
    @media print {
        .no-print {
            display: none !important;
        }
        body {
            background: white !important;
        }
    }
</style>
@endpush
@endsection
