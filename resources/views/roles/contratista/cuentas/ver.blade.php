@extends('layouts.app')

@section('title', 'Ver Cuenta de Cobro - Contratista')

@section('content')
<!-- Contenedor principal con padding superior para el navbar fijo -->
<div class="pt-24 pb-8 px-4 sm:px-6 lg:px-8 min-h-screen">
    
    <!-- Breadcrumb de navegación -->
    <div class="max-w-6xl mx-auto mb-4">
        <nav class="flex items-center space-x-2 text-sm text-gray-500" aria-label="Breadcrumb">
            <a href="{{ route('dashboard') }}" class="hover:text-gray-700 transition-colors flex items-center">
                <i class="fas fa-home mr-1"></i>
                Inicio
            </a>
            <i class="fas fa-chevron-right text-gray-300"></i>
            <a href="{{ route('contratista.dashboard') }}" class="hover:text-gray-700 transition-colors">
                Dashboard Contratista
            </a>
            <i class="fas fa-chevron-right text-gray-300"></i>
            <a href="{{ route('contratista.cuentas.index') }}" class="hover:text-gray-700 transition-colors">
                Mis Cuentas
            </a>
            <i class="fas fa-chevron-right text-gray-300"></i>
            <span class="text-gray-700 font-medium">Cuenta #{{ $cuenta->id }}</span>
        </nav>
    </div>

    <!-- Header de la página -->
    <div class="max-w-6xl mx-auto mb-8">
        <div class="glass-card p-6 slide-up">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center space-x-4 mb-4 lg:mb-0">
                    <div class="gradient-primary w-16 h-16 rounded-2xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-file-invoice-dollar text-white text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">Cuenta de Cobro #{{ $cuenta->id }}</h1>
                        <p class="text-gray-600">{{ $cuenta->proyecto_servicio }}</p>
                    </div>
                </div>
                
                <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-3 sm:space-y-0 sm:space-x-3">
                    <!-- Estado -->
                    <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-medium
                        @switch($cuenta->estado)
                            @case('borrador')
                                bg-gray-100 text-gray-800
                                @break
                            @case('pendiente')
                                bg-yellow-100 text-yellow-800
                                @break
                            @case('revision')
                                bg-blue-100 text-blue-800
                                @break
                            @case('aprobado')
                                bg-green-100 text-green-800
                                @break
                            @case('pagado')
                                bg-emerald-100 text-emerald-800
                                @break
                            @case('rechazado')
                                bg-red-100 text-red-800
                                @break
                            @default
                                bg-gray-100 text-gray-800
                        @endswitch
                    ">
                        @switch($cuenta->estado)
                            @case('borrador')
                                <i class="fas fa-edit mr-2"></i>
                                @break
                            @case('pendiente')
                                <i class="fas fa-clock mr-2"></i>
                                @break
                            @case('revision')
                                <i class="fas fa-search mr-2"></i>
                                @break
                            @case('aprobado')
                                <i class="fas fa-thumbs-up mr-2"></i>
                                @break
                            @case('pagado')
                                <i class="fas fa-check-circle mr-2"></i>
                                @break
                            @case('rechazado')
                                <i class="fas fa-times-circle mr-2"></i>
                                @break
                        @endswitch
                        {{ ucfirst($cuenta->estado) }}
                    </span>
                    
                    <!-- Acciones -->
                    <div class="flex space-x-3">
                        @if(in_array($cuenta->estado, ['borrador', 'rechazado']))
                            <a href="{{ route('contratista.cuentas.editar', $cuenta->id) }}" 
                               class="bg-green-600 text-white px-4 py-2 rounded-xl hover:bg-green-700 transition-colors font-medium flex items-center">
                                <i class="fas fa-edit mr-2"></i>
                                Editar
                            </a>
                        @endif
                        
                        <a href="{{ route('contratista.cuentas.index') }}" 
                           class="bg-gray-100 text-gray-700 px-4 py-2 rounded-xl hover:bg-gray-200 transition-colors font-medium flex items-center">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Volver
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Panel principal -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Información básica -->
            <div class="glass-card p-6">
                <div class="flex items-center mb-6">
                    <div class="bg-blue-100 p-3 rounded-xl mr-4">
                        <i class="fas fa-info-circle text-blue-600 text-xl"></i>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-800">Información General</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Fecha de emisión -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <label class="text-sm font-medium text-gray-500">Fecha de Emisión</label>
                        <div class="flex items-center mt-2">
                            <i class="fas fa-calendar-alt text-blue-500 mr-3"></i>
                            <span class="text-lg font-semibold text-gray-800">
                                {{ $cuenta->fecha_emision->format('d/m/Y') }}
                            </span>
                        </div>
                    </div>

                    <!-- Valor -->
                    <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                        <label class="text-sm font-medium text-green-700">Valor Total</label>
                        <div class="flex items-center mt-2">
                            <i class="fas fa-dollar-sign text-green-600 mr-3"></i>
                            <span class="text-2xl font-bold text-green-800">
                                ${{ number_format($cuenta->valor, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <!-- Fecha de creación -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <label class="text-sm font-medium text-gray-500">Fecha de Creación</label>
                        <div class="flex items-center mt-2">
                            <i class="fas fa-plus-circle text-gray-500 mr-3"></i>
                            <span class="text-sm font-semibold text-gray-600">
                                {{ $cuenta->created_at->format('d/m/Y H:i') }}
                            </span>
                        </div>
                    </div>

                    <!-- Última actualización -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <label class="text-sm font-medium text-gray-500">Última Actualización</label>
                        <div class="flex items-center mt-2">
                            <i class="fas fa-clock text-gray-500 mr-3"></i>
                            <span class="text-sm font-semibold text-gray-600">
                                {{ $cuenta->updated_at->format('d/m/Y H:i') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Descripción del proyecto/servicio -->
            <div class="glass-card p-6">
                <div class="flex items-center mb-6">
                    <div class="bg-purple-100 p-3 rounded-xl mr-4">
                        <i class="fas fa-project-diagram text-purple-600 text-xl"></i>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-800">Descripción del Servicio</h2>
                </div>

                <!-- Proyecto/Servicio -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-500 mb-2">Proyecto o Servicio</label>
                    <div class="bg-gray-50 p-4 rounded-lg border">
                        <p class="text-lg font-semibold text-gray-800">{{ $cuenta->proyecto_servicio }}</p>
                    </div>
                </div>

                <!-- Descripción detallada -->
                @if($cuenta->descripcion)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-2">Descripción Detallada</label>
                        <div class="bg-gray-50 p-4 rounded-lg border">
                            <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $cuenta->descripcion }}</p>
                        </div>
                    </div>
                @else
                    <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-yellow-600 mr-3"></i>
                            <p class="text-yellow-800">No se proporcionó una descripción detallada para este proyecto.</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Archivo adjunto -->
            <div class="glass-card p-6">
                <div class="flex items-center mb-6">
                    <div class="bg-green-100 p-3 rounded-xl mr-4">
                        <i class="fas fa-paperclip text-green-600 text-xl"></i>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-800">Documentos de Soporte</h2>
                </div>

                @if($cuenta->archivo_url)
                    <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <i class="fas fa-file-pdf text-red-500 mr-3 text-2xl"></i>
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $cuenta->archivo_nombre }}</p>
                                    <p class="text-sm text-gray-600">Documento adjunto</p>
                                </div>
                            </div>
                            <a href="{{ $cuenta->archivo_url }}" 
                               target="_blank"
                               class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors font-medium flex items-center">
                                <i class="fas fa-download mr-2"></i>
                                Descargar
                            </a>
                        </div>
                    </div>
                @else
                    <div class="bg-gray-50 p-8 rounded-lg border text-center">
                        <i class="fas fa-file-times text-gray-400 text-3xl mb-3"></i>
                        <p class="text-gray-600">No hay documentos adjuntos a esta cuenta de cobro.</p>
                        @if(in_array($cuenta->estado, ['borrador', 'rechazado']))
                            <p class="text-sm text-blue-600 mt-2">
                                <a href="{{ route('contratista.cuentas.editar', $cuenta->id) }}" class="hover:underline">
                                    Puedes agregar documentos editando esta cuenta
                                </a>
                            </p>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Panel lateral -->
        <div class="space-y-6">
            <!-- Información del contratista -->
            <div class="glass-card p-6 sticky top-24">
                <div class="flex items-center mb-4">
                    <div class="bg-blue-100 p-2 rounded-lg mr-3">
                        <i class="fas fa-user-tie text-blue-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">Contratista</h3>
                </div>

                <div class="flex items-center p-3 bg-gray-50 rounded-lg mb-4">
                    <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                        <i class="fas fa-user text-white"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">{{ $cuenta->user->name }}</p>
                        <p class="text-sm text-gray-600">{{ $cuenta->user->email }}</p>
                    </div>
                </div>

                <!-- Timeline del estado -->
                <div class="border-t pt-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Historial</h4>
                    <div class="space-y-3">
                        <!-- Creación -->
                        <div class="flex items-start">
                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3 mt-0.5">
                                <i class="fas fa-plus text-white text-xs"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">Cuenta creada</p>
                                <p class="text-xs text-gray-500">{{ $cuenta->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>

                        <!-- Estado actual -->
                        <div class="flex items-start">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center mr-3 mt-0.5
                                @switch($cuenta->estado)
                                    @case('borrador')
                                        bg-gray-500
                                        @break
                                    @case('pendiente')
                                        bg-yellow-500
                                        @break
                                    @case('revision')
                                        bg-blue-500
                                        @break
                                    @case('aprobado')
                                        bg-green-500
                                        @break
                                    @case('pagado')
                                        bg-emerald-500
                                        @break
                                    @case('rechazado')
                                        bg-red-500
                                        @break
                                @endswitch
                            ">
                                @switch($cuenta->estado)
                                    @case('borrador')
                                        <i class="fas fa-edit text-white text-xs"></i>
                                        @break
                                    @case('pendiente')
                                        <i class="fas fa-clock text-white text-xs"></i>
                                        @break
                                    @case('revision')
                                        <i class="fas fa-search text-white text-xs"></i>
                                        @break
                                    @case('aprobado')
                                        <i class="fas fa-check text-white text-xs"></i>
                                        @break
                                    @case('pagado')
                                        <i class="fas fa-money-check-alt text-white text-xs"></i>
                                        @break
                                    @case('rechazado')
                                        <i class="fas fa-times text-white text-xs"></i>
                                        @break
                                @endswitch
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ ucfirst($cuenta->estado) }}</p>
                                <p class="text-xs text-gray-500">{{ $cuenta->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Acciones disponibles -->
                @if(in_array($cuenta->estado, ['borrador', 'rechazado']))
                    <div class="border-t pt-4 mt-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Acciones Disponibles</h4>
                        <div class="space-y-2">
                            <a href="{{ route('contratista.cuentas.editar', $cuenta->id) }}" 
                               class="w-full bg-green-600 text-white px-3 py-2 rounded-lg hover:bg-green-700 transition-colors font-medium text-sm flex items-center justify-center">
                                <i class="fas fa-edit mr-2"></i>
                                Editar Cuenta
                            </a>
                            
                            @if($cuenta->estado === 'borrador')
                                <a href="{{ route('contratista.cuentas.eliminar', $cuenta->id) }}" 
                                   class="w-full bg-red-600 text-white px-3 py-2 rounded-lg hover:bg-red-700 transition-colors font-medium text-sm flex items-center justify-center">
                                    <i class="fas fa-trash mr-2"></i>
                                    Eliminar
                                </a>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Información del estado -->
                <div class="border-t pt-4 mt-4">
                    <div class="bg-blue-50 p-3 rounded-lg border border-blue-200">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-blue-500 mt-0.5 mr-2"></i>
                            <div class="text-sm text-blue-700">
                                @switch($cuenta->estado)
                                    @case('borrador')
                                        <p class="font-medium">Estado: Borrador</p>
                                        <p class="text-xs mt-1">Esta cuenta aún no ha sido enviada. Puedes editarla o eliminarla.</p>
                                        @break
                                    @case('pendiente')
                                        <p class="font-medium">Estado: Pendiente</p>
                                        <p class="text-xs mt-1">Tu cuenta está esperando revisión por parte del supervisor.</p>
                                        @break
                                    @case('revision')
                                        <p class="font-medium">Estado: En Revisión</p>
                                        <p class="text-xs mt-1">La cuenta está siendo evaluada por el equipo de supervisión.</p>
                                        @break
                                    @case('aprobado')
                                        <p class="font-medium">Estado: Aprobada</p>
                                        <p class="text-xs mt-1">Tu cuenta ha sido aprobada y está lista para el pago.</p>
                                        @break
                                    @case('pagado')
                                        <p class="font-medium">Estado: Pagada</p>
                                        <p class="text-xs mt-1">El pago de esta cuenta ha sido procesado exitosamente.</p>
                                        @break
                                    @case('rechazado')
                                        <p class="font-medium">Estado: Rechazada</p>
                                        <p class="text-xs mt-1">La cuenta fue rechazada. Puedes editarla y volver a enviarla.</p>
                                        @break
                                @endswitch
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
    
    .gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
@endpush
@endsection
