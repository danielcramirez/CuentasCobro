@extends('layouts.app')

@section('title', 'Dashboard - CuentasCobro')

@section('content')
<!-- Contenedor principal del dashboard con padding superior para el navbar fijo -->
<div class="pt-24 pb-8 px-4 sm:px-6 lg:px-8 min-h-screen">
    <!-- Header del dashboard con animación -->
    <div class="max-w-7xl mx-auto mb-8">
        <div class="glass-card p-6 slide-up">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <!-- Información del usuario -->
                <div class="flex items-center space-x-4 mb-4 lg:mb-0">
                    <div class="gradient-primary w-16 h-16 rounded-2xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-user text-white text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800 font-poppins">
                            ¡Bienvenido, {{ $user->name }}!
                        </h1>
                        <div class="flex items-center space-x-2 mt-1">
                            <span class="text-gray-600">Rol:</span>
                            @if($userRole)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium gradient-secondary text-white shadow-sm">
                                    <i class="fas fa-badge-check mr-1"></i>
                                    {{ ucfirst(str_replace('_', ' ', $userRole)) }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-600">
                                    <i class="fas fa-question-circle mr-1"></i>
                                    Sin rol asignado
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Acciones rápidas -->
                <div class="flex items-center space-x-3">
                    <button class="gradient-primary text-white px-4 py-2 rounded-xl hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300 font-medium">
                        <i class="fas fa-bell mr-2"></i>
                        Notificaciones
                        <span class="ml-2 bg-white/20 text-xs px-2 py-1 rounded-full">3</span>
                    </button>
                    <button class="bg-white/70 text-gray-700 px-4 py-2 rounded-xl border border-gray-200 hover:bg-white hover:shadow-md transition-all duration-300 font-medium">
                        <i class="fas fa-cog mr-2"></i>
                        Configuración
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Estadísticas rápidas -->
    <div class="max-w-7xl mx-auto mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Tarjeta de estadística 1 -->
            <div class="glass-card p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 fade-in">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Cuentas Activas</p>
                        <p class="text-3xl font-bold text-gray-800">24</p>
                        <p class="text-sm text-green-600 flex items-center mt-1">
                            <i class="fas fa-arrow-up mr-1"></i>
                            +12% este mes
                        </p>
                    </div>
                    <div class="bg-gradient-to-br from-blue-400 to-blue-600 w-12 h-12 rounded-xl flex items-center justify-center">
                        <i class="fas fa-file-invoice text-white"></i>
                    </div>
                </div>
            </div>
            
            <!-- Tarjeta de estadística 2 -->
            <div class="glass-card p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 fade-in" style="animation-delay: 0.1s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Pendientes</p>
                        <p class="text-3xl font-bold text-gray-800">8</p>
                        <p class="text-sm text-yellow-600 flex items-center mt-1">
                            <i class="fas fa-clock mr-1"></i>
                            En revisión
                        </p>
                    </div>
                    <div class="bg-gradient-to-br from-yellow-400 to-orange-500 w-12 h-12 rounded-xl flex items-center justify-center">
                        <i class="fas fa-hourglass-half text-white"></i>
                    </div>
                </div>
            </div>
            
            <!-- Tarjeta de estadística 3 -->
            <div class="glass-card p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 fade-in" style="animation-delay: 0.2s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Completadas</p>
                        <p class="text-3xl font-bold text-gray-800">156</p>
                        <p class="text-sm text-green-600 flex items-center mt-1">
                            <i class="fas fa-check-circle mr-1"></i>
                            Este año
                        </p>
                    </div>
                    <div class="bg-gradient-to-br from-green-400 to-emerald-600 w-12 h-12 rounded-xl flex items-center justify-center">
                        <i class="fas fa-check-double text-white"></i>
                    </div>
                </div>
            </div>
            
            <!-- Tarjeta de estadística 4 -->
            <div class="glass-card p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 fade-in" style="animation-delay: 0.3s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Total Facturado</p>
                        <p class="text-3xl font-bold text-gray-800">$2.4M</p>
                        <p class="text-sm text-blue-600 flex items-center mt-1">
                            <i class="fas fa-dollar-sign mr-1"></i>
                            +18% vs anterior
                        </p>
                    </div>
                    <div class="bg-gradient-to-br from-purple-400 to-pink-600 w-12 h-12 rounded-xl flex items-center justify-center">
                        <i class="fas fa-chart-line text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Acciones rápidas -->
    <div class="max-w-7xl mx-auto mb-8">
        <div class="glass-card p-6 slide-up">
            <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                <i class="fas fa-bolt text-yellow-500 mr-2"></i>
                Acciones Rápidas
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Acción 1: Crear cuenta -->
                <a href="{{ route('cuentas-cobro.crear') }}" class="group block">
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-100 p-6 rounded-xl border border-blue-200 hover:border-blue-300 transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1">
                        <div class="flex items-center space-x-4">
                            <div class="bg-gradient-to-br from-blue-500 to-indigo-600 w-12 h-12 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                <i class="fas fa-plus text-white"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Crear Cuenta</h3>
                                <p class="text-sm text-gray-600">Nueva cuenta de cobro</p>
                            </div>
                        </div>
                    </div>
                </a>
                
                <!-- Acción 2: Ver cuentas -->
                <a href="{{ route('cuentas-cobro.mostrar') }}" class="group block">
                    <div class="bg-gradient-to-br from-green-50 to-emerald-100 p-6 rounded-xl border border-green-200 hover:border-green-300 transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1">
                        <div class="flex items-center space-x-4">
                            <div class="bg-gradient-to-br from-green-500 to-emerald-600 w-12 h-12 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                <i class="fas fa-list text-white"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Ver Cuentas</h3>
                                <p class="text-sm text-gray-600">Consultar existentes</p>
                            </div>
                        </div>
                    </div>
                </a>
                
                <!-- Acción 3: Reportes -->
                <a href="#" class="group block">
                    <div class="bg-gradient-to-br from-purple-50 to-violet-100 p-6 rounded-xl border border-purple-200 hover:border-purple-300 transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1">
                        <div class="flex items-center space-x-4">
                            <div class="bg-gradient-to-br from-purple-500 to-violet-600 w-12 h-12 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                <i class="fas fa-chart-bar text-white"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Reportes</h3>
                                <p class="text-sm text-gray-600">Análisis y estadísticas</p>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Contenido específico por rol -->
    <div class="max-w-7xl mx-auto">
        @yield('dashboardRoles')
    </div>
</div>

<!-- Widget de ayuda flotante -->
<div class="fixed bottom-6 right-6 z-30">
    <button class="gradient-primary w-14 h-14 rounded-full shadow-xl hover:shadow-2xl transform hover:scale-110 transition-all duration-300 flex items-center justify-center text-white group">
        <i class="fas fa-question-circle text-xl group-hover:rotate-12 transition-transform duration-300"></i>
    </button>
    
    <!-- Tooltip de ayuda -->
    <div class="absolute bottom-16 right-0 bg-gray-900 text-white px-3 py-2 rounded-lg text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap">
        ¿Necesitas ayuda?
        <div class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900"></div>
    </div>
</div>

<!-- Notificación toast -->
<div id="welcome-toast" class="fixed top-24 right-6 glass-card p-4 shadow-xl transform translate-x-full transition-transform duration-500 z-40">
    <div class="flex items-center space-x-3">
        <div class="gradient-primary w-10 h-10 rounded-full flex items-center justify-center">
            <i class="fas fa-check text-white"></i>
        </div>
        <div>
            <p class="font-medium text-gray-800">¡Bienvenido de vuelta!</p>
            <p class="text-sm text-gray-600">Tienes 3 notificaciones nuevas</p>
        </div>
        <button onclick="closeToast()" class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Animaciones personalizadas para el dashboard */
    .fade-in {
        animation: fadeInUp 0.6s ease-out;
    }
    
    .slide-up {
        animation: slideUp 0.8s ease-out;
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
    
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(40px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Efecto parallax suave para las tarjetas */
    .glass-card:hover {
        transform: translateY(-5px) scale(1.02);
    }
    
    /* Gradientes adicionales */
    .gradient-stats-1 {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .gradient-stats-2 {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    
    .gradient-stats-3 {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    
    .gradient-stats-4 {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mostrar toast de bienvenida
        setTimeout(() => {
            const toast = document.getElementById('welcome-toast');
            toast.classList.remove('translate-x-full');
            
            // Auto ocultar después de 5 segundos
            setTimeout(() => {
                toast.classList.add('translate-x-full');
            }, 5000);
        }, 1000);
        
        // Animación de contadores
        animateCounters();
        
        // Efectos de hover en las tarjetas estadísticas
        const statCards = document.querySelectorAll('.glass-card');
        statCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });
    });
    
    // Función para cerrar el toast
    function closeToast() {
        const toast = document.getElementById('welcome-toast');
        toast.classList.add('translate-x-full');
    }
    
    // Animación de contadores numéricos
    function animateCounters() {
        const counters = document.querySelectorAll('.text-3xl');
        
        counters.forEach(counter => {
            const text = counter.textContent;
            const number = parseInt(text.replace(/\D/g, ''));
            
            if (number && !isNaN(number)) {
                let current = 0;
                const increment = number / 50;
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= number) {
                        counter.textContent = text;
                        clearInterval(timer);
                    } else {
                        const prefix = text.includes('$') ? '$' : '';
                        const suffix = text.includes('M') ? 'M' : text.includes('K') ? 'K' : '';
                        counter.textContent = prefix + Math.floor(current) + suffix;
                    }
                }, 30);
            }
        });
    }
    
    // Efecto de partículas de fondo (opcional)
    function createBackgroundAnimation() {
        const container = document.body;
        
        for (let i = 0; i < 5; i++) {
            const particle = document.createElement('div');
            particle.className = 'fixed w-4 h-4 bg-primary-200 rounded-full opacity-20 pointer-events-none';
            particle.style.left = Math.random() * 100 + '%';
            particle.style.top = Math.random() * 100 + '%';
            particle.style.animation = `floatParticle ${10 + Math.random() * 20}s infinite linear`;
            particle.style.animationDelay = Math.random() * 5 + 's';
            
            container.appendChild(particle);
        }
    }
    
    // Llamar a la animación de fondo
    createBackgroundAnimation();
    
    // Agregar animación CSS para las partículas
    const particleStyle = document.createElement('style');
    particleStyle.textContent = `
        @keyframes floatParticle {
            0% { transform: translateY(100vh) rotate(0deg); }
            100% { transform: translateY(-100px) rotate(360deg); }
        }
    `;
    document.head.appendChild(particleStyle);
</script>
@endpush