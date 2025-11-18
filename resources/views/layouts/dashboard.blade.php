{{-- Layout específico para dashboards --}}
@extends('layouts.app')

@section('content')
<!-- Contenedor principal del dashboard con padding superior para el navbar fijo -->
<div class="pt-32 pb-8 px-4 sm:px-6 lg:px-8 min-h-screen">
    
    <!-- Breadcrumb de navegación -->
    <div class="max-w-7xl mx-auto mb-4">
        @yield('breadcrumb')
    </div>

    <!-- Header del dashboard -->
    <div class="max-w-7xl mx-auto mb-8">
        @yield('dashboard-header')
    </div>

    <!-- Contenido específico del rol -->
    <div class="max-w-7xl mx-auto">
        @yield('dashboard-content')
    </div>
</div>
@endsection
