{{-- Página de error 403 - Acceso denegado --}}
@extends('layouts.app')

@section('title', 'Acceso Denegado - CuentasCobro')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4">
    <div class="max-w-md w-full text-center">
        <div class="glass-card p-8">
            <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-red-400 to-red-600 rounded-2xl flex items-center justify-center">
                <i class="fas fa-lock text-white text-3xl"></i>
            </div>
            
            <h1 class="text-4xl font-bold text-gray-800 mb-2">403</h1>
            <h2 class="text-xl font-semibold text-gray-600 mb-4">Acceso Denegado</h2>
            <p class="text-gray-500 mb-8">No tienes permisos para acceder a esta página o realizar esta acción.</p>
            
            <div class="space-y-4">
                <a href="{{ route('dashboard') }}" 
                   class="block w-full gradient-primary text-white py-3 px-6 rounded-xl hover:shadow-lg transition-all duration-300 font-medium">
                    <i class="fas fa-home mr-2"></i>
                    Volver al Dashboard
                </a>
                
                <a href="javascript:history.back()" 
                   class="block w-full bg-gray-100 text-gray-700 py-3 px-6 rounded-xl hover:bg-gray-200 transition-all duration-300 font-medium">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Página Anterior
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
