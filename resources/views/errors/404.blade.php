{{-- Página de error 404 - No encontrado --}}
@extends('layouts.app')

@section('title', 'Página No Encontrada - CuentasCobro')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4">
    <div class="max-w-md w-full text-center">
        <div class="glass-card p-8">
            <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-2xl flex items-center justify-center">
                <i class="fas fa-question-circle text-white text-3xl"></i>
            </div>
            
            <h1 class="text-4xl font-bold text-gray-800 mb-2">404</h1>
            <h2 class="text-xl font-semibold text-gray-600 mb-4">Página No Encontrada</h2>
            <p class="text-gray-500 mb-8">La página que estás buscando no existe o ha sido movida.</p>
            
            <div class="space-y-4">
                <a href="{{ route('dashboard') }}" 
                   class="block w-full gradient-primary text-white py-3 px-6 rounded-xl hover:shadow-lg transition-all duration-300 font-medium">
                    <i class="fas fa-home mr-2"></i>
                    Ir al Dashboard
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
