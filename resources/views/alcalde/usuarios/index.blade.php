{{-- Vista de gestión de usuarios para el alcalde --}}
@extends('layouts.dashboard')

@section('title', 'Gestión de Usuarios - Alcalde')

@section('breadcrumb')
    @include('components.navigation.breadcrumb', [
        'items' => [
            ['label' => 'Dashboard Alcalde', 'url' => route('dashboard')],
            ['label' => 'Gestión de Usuarios']
        ]
    ])
@endsection

@section('dashboard-header')
    <div class="glass-card p-6 slide-up">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="gradient-primary w-16 h-16 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-users text-white text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Gestión de Usuarios</h1>
                    <p class="text-gray-600">Administra usuarios y sus roles en el sistema</p>
                </div>
            </div>
            
            <a href="{{ route('register') }}" 
               class="gradient-secondary text-white px-6 py-3 rounded-xl hover:shadow-lg transition-all duration-300 font-medium">
                <i class="fas fa-user-plus mr-2"></i>
                Nuevo Usuario
            </a>
        </div>
    </div>
@endsection

@section('dashboard-content')
    <!-- Estadísticas de usuarios -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @include('components.cards.stats-card', [
            'title' => 'Total Usuarios',
            'value' => $totalUsers ?? 0,
            'subtitle' => 'Registrados',
            'subtitleColor' => 'text-blue-600',
            'icon' => 'users',
            'cardIcon' => 'users',
            'gradientFrom' => 'from-blue-400',
            'gradientTo' => 'to-blue-600'
        ])
        
        @include('components.cards.stats-card', [
            'title' => 'Con Roles',
            'value' => $usersWithRoles ?? 0,
            'subtitle' => 'Asignados',
            'subtitleColor' => 'text-green-600',
            'icon' => 'user-check',
            'cardIcon' => 'user-check',
            'gradientFrom' => 'from-green-400',
            'gradientTo' => 'to-green-600'
        ])
        
        @include('components.cards.stats-card', [
            'title' => 'Sin Roles',
            'value' => $usersWithoutRoles ?? 0,
            'subtitle' => 'Pendientes',
            'subtitleColor' => 'text-orange-600',
            'icon' => 'user-times',
            'cardIcon' => 'user-times',
            'gradientFrom' => 'from-orange-400',
            'gradientTo' => 'to-orange-600'
        ])
        
        @include('components.cards.stats-card', [
            'title' => 'Roles Activos',
            'value' => $totalRoles ?? 0,
            'subtitle' => 'Disponibles',
            'subtitleColor' => 'text-purple-600',
            'icon' => 'tags',
            'cardIcon' => 'tags',
            'gradientFrom' => 'from-purple-400',
            'gradientTo' => 'to-purple-600'
        ])
    </div>

    <!-- Tabla de usuarios -->
    <div class="glass-card p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-800">Usuarios del Sistema</h2>
            <div class="flex space-x-3">
                <a href="{{ route('roles.index') }}" 
                   class="bg-indigo-500 text-white px-4 py-2 rounded-xl hover:bg-indigo-600 transition-colors">
                    <i class="fas fa-cogs mr-2"></i>
                    Gestionar Roles
                </a>
            </div>
        </div>
        
        <!-- Aquí iría la tabla de usuarios -->
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-users text-4xl mb-4"></i>
            <p>Funcionalidad de gestión de usuarios en desarrollo</p>
        </div>
    </div>
@endsection
