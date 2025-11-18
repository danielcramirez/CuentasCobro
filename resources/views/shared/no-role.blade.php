@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-amber-50 via-orange-50 to-red-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Main Alert Section -->
        <div class="glass-card p-8 mb-8 text-center transform animate-bounce-in">
            <div class="w-20 h-20 bg-gradient-to-br from-amber-400 to-orange-500 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg animate-pulse">
                <i class="fas fa-exclamation-triangle text-white text-3xl"></i>
            </div>
            
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Sin Rol Asignado</h1>
            <p class="text-xl text-gray-600 mb-6">
                No tienes un rol asignado en el sistema. Para acceder a todas las funcionalidades, 
                necesitas que un administrador te asigne un rol específico.
            </p>
            
            <div class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-amber-100 to-orange-100 border border-amber-200 rounded-xl text-amber-800 font-medium">
                <i class="fas fa-info-circle mr-3 text-lg"></i>
                Contacta al administrador del sistema
            </div>
        </div>

        <!-- Available Actions -->
        <div class="glass-card p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Funcionalidades Disponibles</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                
                <div class="p-4 bg-gradient-to-br from-blue-50 to-indigo-100 rounded-xl border border-blue-200 hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-user text-white text-xl"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Ver Perfil</h3>
                    <p class="text-gray-600 text-sm">Consulta y actualiza tu información personal</p>
                </div>

                <div class="p-4 bg-gradient-to-br from-green-50 to-emerald-100 rounded-xl border border-green-200 hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                    <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-eye text-white text-xl"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Ver Sistema</h3>
                    <p class="text-gray-600 text-sm">Explora las funcionalidades básicas del sistema</p>
                </div>

                <div class="p-4 bg-gradient-to-br from-purple-50 to-pink-100 rounded-xl border border-purple-200 hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-question-circle text-white text-xl"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Ayuda</h3>
                    <p class="text-gray-600 text-sm">Consulta la documentación y guías de uso</p>
                </div>

            </div>
        </div>

        <!-- Role Information -->
        <div class="glass-card p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Roles Disponibles en el Sistema</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <div class="flex items-start space-x-4 p-4 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-user-tie text-white"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Contratista</h3>
                        <p class="text-gray-600 text-sm">Gestión de cuentas de cobro y documentos</p>
                    </div>
                </div>

                <div class="flex items-start space-x-4 p-4 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl">
                    <div class="w-10 h-10 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-crown text-white"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Alcalde/Administrador</h3>
                        <p class="text-gray-600 text-sm">Administración completa del sistema</p>
                    </div>
                </div>

                <div class="flex items-start space-x-4 p-4 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl">
                    <div class="w-10 h-10 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-clipboard-check text-white"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Supervisor</h3>
                        <p class="text-gray-600 text-sm">Supervisión y aprobación de procesos</p>
                    </div>
                </div>

                <div class="flex items-start space-x-4 p-4 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl">
                    <div class="w-10 h-10 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-coins text-white"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Tesorería</h3>
                        <p class="text-gray-600 text-sm">Gestión financiera y procesamiento de pagos</p>
                    </div>
                </div>

            </div>
        </div>

        <!-- Contact Information -->
        <div class="glass-card p-6 text-center">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">¿Necesitas Ayuda?</h2>
            <p class="text-gray-600 mb-6">
                Si necesitas que te asignen un rol o tienes alguna pregunta, 
                contacta con el equipo de soporte.
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <button class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all duration-300 transform hover:scale-105">
                    <i class="fas fa-envelope mr-2"></i>
                    Contactar Soporte
                </button>
                
                <button class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl hover:from-green-600 hover:to-emerald-700 transition-all duration-300 transform hover:scale-105">
                    <i class="fas fa-phone mr-2"></i>
                    Llamar Administrador
                </button>
            </div>
        </div>

    </div>
</div>

<style>
@keyframes bounce-in {
    0% {
        opacity: 0;
        transform: scale(0.3);
    }
    50% {
        opacity: 1;
        transform: scale(1.05);
    }
    70% {
        transform: scale(0.9);
    }
    100% {
        opacity: 1;
        transform: scale(1);
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-bounce-in {
    animation: bounce-in 0.8s ease-out;
}

.glass-card {
    animation: fadeInUp 0.6s ease-out;
}

.glass-card:nth-child(2) { animation-delay: 0.2s; }
.glass-card:nth-child(3) { animation-delay: 0.4s; }
.glass-card:nth-child(4) { animation-delay: 0.6s; }
</style>
@endsection
