{{-- Vista de cuentas de cobro específica para contratistas --}}
@extends('layouts.app')

@section('title', 'Mis Cuentas de Cobro - Contratista')

@section('content')
<div class="pt-24 pb-8 px-4 sm:px-6 lg:px-8 min-h-screen">
    
    <!-- Breadcrumb -->
    <div class="max-w-7xl mx-auto mb-4">
        @include('components.navigation.breadcrumb', [
            'items' => [
                ['label' => 'Dashboard Contratista', 'url' => route('contratista.dashboard')],
                ['label' => 'Mis Cuentas de Cobro']
            ]
        ])
    </div>

    <!-- Header específico para contratista -->
    <div class="max-w-7xl mx-auto mb-8">
        <div class="glass-card p-6 slide-up">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center space-x-4 mb-4 lg:mb-0">
                    <div class="gradient-primary w-12 h-12 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-file-invoice-dollar text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Mis Cuentas de Cobro</h1>
                        <p class="text-gray-600">Gestiona y revisa tus cuentas de cobro</p>
                    </div>
                </div>
                
                <a href="{{ route('cuentas-cobro.crear') }}" 
                   class="gradient-primary text-white px-4 py-2 rounded-xl hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300 font-medium flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Nueva Cuenta
                </a>
            </div>
        </div>
    </div>

    <!-- Estadísticas rápidas para contratista -->
    <div class="max-w-7xl mx-auto mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @include('components.cards.stats-card', [
                'title' => 'Borradores',
                'value' => $cuentas->where('estado', 'borrador')->count(),
                'subtitle' => 'Sin enviar',
                'subtitleColor' => 'text-gray-600',
                'icon' => 'edit',
                'cardIcon' => 'edit',
                'gradientFrom' => 'from-gray-400',
                'gradientTo' => 'to-gray-600'
            ])
            
            @include('components.cards.stats-card', [
                'title' => 'Pendientes',
                'value' => $cuentas->where('estado', 'pendiente')->count(),
                'subtitle' => 'En revisión',
                'subtitleColor' => 'text-yellow-600',
                'icon' => 'clock',
                'cardIcon' => 'clock',
                'gradientFrom' => 'from-yellow-400',
                'gradientTo' => 'to-orange-500'
            ])
            
            @include('components.cards.stats-card', [
                'title' => 'Aprobadas',
                'value' => $cuentas->where('estado', 'aprobado')->count(),
                'subtitle' => 'Listas para pago',
                'subtitleColor' => 'text-green-600',
                'icon' => 'check-circle',
                'cardIcon' => 'check-circle',
                'gradientFrom' => 'from-green-400',
                'gradientTo' => 'to-green-600'
            ])
            
            @include('components.cards.stats-card', [
                'title' => 'Pagadas',
                'value' => $cuentas->where('estado', 'pagado')->count(),
                'subtitle' => 'Completadas',
                'subtitleColor' => 'text-blue-600',
                'icon' => 'money-check-alt',
                'cardIcon' => 'money-check-alt',
                'gradientFrom' => 'from-blue-400',
                'gradientTo' => 'to-blue-600'
            ])
        </div>
    </div>

    <!-- Lista de cuentas -->
    <div class="max-w-7xl mx-auto">
        @if($cuentas->count() > 0)
            <!-- Vista de tabla o cards según preferencia -->
            <!-- Aquí se incluiría el contenido de la vista original -->
            <div class="glass-card p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Tus Cuentas de Cobro</h2>
                <!-- Tabla o lista de cuentas -->
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="px-6 py-3">Fecha</th>
                                <th class="px-6 py-3">Proyecto/Servicio</th>
                                <th class="px-6 py-3">Valor</th>
                                <th class="px-6 py-3">Estado</th>
                                <th class="px-6 py-3">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cuentas as $cuenta)
                                <tr class="bg-white border-b hover:bg-gray-50">
                                    <td class="px-6 py-4">{{ $cuenta->fecha_emision->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4">{{ $cuenta->proyecto_servicio }}</td>
                                    <td class="px-6 py-4">${{ number_format($cuenta->valor, 2) }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-xs rounded-full
                                            @if($cuenta->estado === 'borrador') bg-gray-100 text-gray-800
                                            @elseif($cuenta->estado === 'pendiente') bg-yellow-100 text-yellow-800
                                            @elseif($cuenta->estado === 'aprobado') bg-green-100 text-green-800
                                            @elseif($cuenta->estado === 'pagado') bg-blue-100 text-blue-800
                                            @else bg-red-100 text-red-800
                                            @endif">
                                            {{ ucfirst($cuenta->estado) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('cuentas-cobro.ver', $cuenta->id) }}" 
                                               class="text-blue-600 hover:text-blue-900">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($cuenta->estado === 'borrador')
                                                <a href="{{ route('cuentas-cobro.edit', $cuenta->id) }}" 
                                                   class="text-green-600 hover:text-green-900">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <!-- Estado vacío -->
            <div class="glass-card p-12 text-center">
                <div class="w-20 h-20 mx-auto mb-6 gradient-primary rounded-2xl flex items-center justify-center">
                    <i class="fas fa-file-invoice text-white text-3xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">No tienes cuentas de cobro</h3>
                <p class="text-gray-600 mb-6">Comienza creando tu primera cuenta de cobro</p>
                <a href="{{ route('cuentas-cobro.crear') }}" 
                   class="gradient-primary text-white px-6 py-3 rounded-xl hover:shadow-lg transition-all duration-300 font-medium">
                    <i class="fas fa-plus mr-2"></i>
                    Crear Primera Cuenta
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
