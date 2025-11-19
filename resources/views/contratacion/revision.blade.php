{{-- Vista de revisión de cuentas para supervisor --}}
@extends('layouts.dashboard')

@section('title', 'Revisión de Cuentas - Supervisor')

@section('breadcrumb')
    @include('components.navigation.breadcrumb', [
        'items' => [
            ['label' => 'Dashboard Supervisor', 'url' => route('dashboard')],
            ['label' => 'Revisión de Cuentas']
        ]
    ])
@endsection

@section('dashboard-header')
    <div class="glass-card p-6 slide-up">
        <div class="flex items-center space-x-4">
            <div class="gradient-secondary w-16 h-16 rounded-2xl flex items-center justify-center">
                <i class="fas fa-clipboard-check text-white text-2xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Revisión de Cuentas</h1>
                <p class="text-gray-600">Revisa y aprueba las cuentas de cobro pendientes</p>
            </div>
        </div>
    </div>
@endsection

@section('dashboard-content')
    <!-- Estadísticas de revisión -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @include('components.cards.stats-card', [
            'title' => 'Por Revisar',
            'value' => $pendingReviews ?? 0,
            'subtitle' => 'Pendientes',
            'subtitleColor' => 'text-orange-600',
            'icon' => 'clock',
            'cardIcon' => 'hourglass-half',
            'gradientFrom' => 'from-orange-400',
            'gradientTo' => 'to-red-500'
        ])
        
        @include('components.cards.stats-card', [
            'title' => 'Aprobadas Hoy',
            'value' => $approvedToday ?? 0,
            'subtitle' => 'Procesadas',
            'subtitleColor' => 'text-green-600',
            'icon' => 'check-circle',
            'cardIcon' => 'check-circle',
            'gradientFrom' => 'from-green-400',
            'gradientTo' => 'to-green-600'
        ])
        
        @include('components.cards.stats-card', [
            'title' => 'Rechazadas Hoy',
            'value' => $rejectedToday ?? 0,
            'subtitle' => 'Devueltas',
            'subtitleColor' => 'text-red-600',
            'icon' => 'times-circle',
            'cardIcon' => 'times-circle',
            'gradientFrom' => 'from-red-400',
            'gradientTo' => 'to-red-600'
        ])
        
        @include('components.cards.stats-card', [
            'title' => 'Total Cuentas',
            'value' => $totalCuentasCobro ?? 0,
            'subtitle' => 'En sistema',
            'subtitleColor' => 'text-blue-600',
            'icon' => 'file-invoice-dollar',
            'cardIcon' => 'file-invoice-dollar',
            'gradientFrom' => 'from-blue-400',
            'gradientTo' => 'to-blue-600'
        ])
    </div>

    <!-- Lista de cuentas por revisar -->
    <div class="glass-card p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-800">Cuentas Pendientes de Revisión</h2>
            <div class="flex space-x-3">
                <button class="bg-green-500 text-white px-4 py-2 rounded-xl hover:bg-green-600 transition-colors">
                    <i class="fas fa-filter mr-2"></i>
                    Filtrar
                </button>
            </div>
        </div>
        
        <!-- Aquí iría la lista de cuentas pendientes -->
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-clipboard-list text-4xl mb-4"></i>
            <p>No hay cuentas pendientes de revisión</p>
            <p class="text-sm mt-2">Las nuevas cuentas aparecerán aquí para su revisión</p>
        </div>
    </div>
@endsection
