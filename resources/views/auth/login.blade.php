@extends('layouts.app')

@section('title', 'Iniciar Sesión - CuentasCobro')

@section('content')
<!-- Contenedor principal con fondo dinámico -->
<div class="min-h-screen flex items-center justify-center relative overflow-hidden pt-20">
    <!-- Formas de fondo animadas -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute top-0 left-0 w-72 h-72 gradient-primary rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse"></div>
        <div class="absolute top-0 right-0 w-96 h-96 gradient-secondary rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse animation-delay-1000"></div>
        <div class="absolute bottom-0 left-1/2 w-80 h-80 bg-gradient-to-tr from-purple-400 to-pink-400 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse animation-delay-2000"></div>
    </div>
    
    <!-- Card de login con glassmorphism -->
    <div class="glass-card max-w-md w-full mx-4 p-8 bounce-in">
        <!-- Header del formulario -->
        <div class="text-center mb-8">
            <div class="gradient-primary w-20 h-20 rounded-2xl mx-auto mb-6 flex items-center justify-center shadow-xl transform hover:scale-105 transition-transform duration-300">
                <i class="fas fa-file-invoice-dollar text-white text-3xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2 font-poppins">¡Bienvenido!</h1>
            <p class="text-gray-600">Inicia sesión en tu cuenta de CuentasCobro</p>
        </div>

        <!-- Mensajes de error con diseño moderno -->
        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded-lg slide-up">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Se encontraron errores:</h3>
                        <ul class="mt-2 text-sm text-red-700 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li class="flex items-center">
                                    <i class="fas fa-dot-circle text-xs mr-2"></i>
                                    {{ $error }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Formulario principal -->
        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf
            
            <!-- Campo de email -->
            <div class="space-y-2">
                <label for="email" class="flex items-center text-sm font-medium text-gray-700">
                    <i class="fas fa-envelope text-primary-500 mr-2"></i>
                    Correo electrónico
                </label>
                <div class="relative">
                    <input type="email" 
                           class="w-full px-4 py-3 pl-12 bg-white/70 border-2 border-transparent rounded-xl focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-primary-100 transition-all duration-300 placeholder-gray-400 @error('email') border-red-300 focus:border-red-500 focus:ring-red-100 @enderror" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           autocomplete="email" 
                           autofocus
                           placeholder="tu@email.com">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-envelope text-gray-400"></i>
                    </div>
                </div>
                @error('email')
                    <p class="text-red-500 text-sm flex items-center mt-1">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Campo de contraseña -->
            <div class="space-y-2">
                <label for="password" class="flex items-center text-sm font-medium text-gray-700">
                    <i class="fas fa-lock text-primary-500 mr-2"></i>
                    Contraseña
                </label>
                <div class="relative">
                    <input type="password" 
                           class="w-full px-4 py-3 pl-12 pr-12 bg-white/70 border-2 border-transparent rounded-xl focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-primary-100 transition-all duration-300 placeholder-gray-400 @error('password') border-red-300 focus:border-red-500 focus:ring-red-100 @enderror" 
                           id="password" 
                           name="password" 
                           required 
                           autocomplete="current-password"
                           placeholder="••••••••">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-gray-400"></i>
                    </div>
                    <button type="button" class="absolute inset-y-0 right-0 pr-4 flex items-center" onclick="togglePassword()">
                        <i class="fas fa-eye text-gray-400 hover:text-primary-500 transition-colors duration-200" id="toggleIcon"></i>
                    </button>
                </div>
                @error('password')
                    <p class="text-red-500 text-sm flex items-center mt-1">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Checkbox recordarme -->
            <div class="flex items-center justify-between">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" 
                           class="w-4 h-4 text-primary-600 bg-white border-gray-300 rounded focus:ring-primary-500 focus:ring-2 transition-all duration-200" 
                           id="remember" 
                           name="remember">
                    <span class="ml-2 text-sm text-gray-600">Recordarme</span>
                </label>
                <a href="#" class="text-sm text-primary-600 hover:text-primary-500 transition-colors duration-200">
                    ¿Olvidaste tu contraseña?
                </a>
            </div>

            <!-- Botón de envío -->
            <button type="submit" 
                    class="w-full gradient-primary text-white py-4 px-6 rounded-xl font-semibold text-lg shadow-lg hover:shadow-xl transform hover:-translate-y-1 active:translate-y-0 transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-primary-200">
                <i class="fas fa-sign-in-alt mr-2"></i>
                Iniciar Sesión
            </button>
        </form>

        <!-- Divisor -->
        <div class="relative my-8">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-200"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-4 bg-white text-gray-500">¿No tienes cuenta?</span>
            </div>
        </div>

        <!-- Link de registro -->
        <div class="text-center">
            <a href="{{ route('register') }}" 
               class="inline-flex items-center justify-center w-full px-6 py-3 border-2 border-primary-200 text-primary-700 bg-primary-50 rounded-xl hover:bg-primary-100 hover:border-primary-300 transition-all duration-300 font-medium">
                <i class="fas fa-user-plus mr-2"></i>
                Crear nueva cuenta
            </a>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center text-xs text-gray-500">
            <p>© 2024 CuentasCobro. Sistema de Gestión Integral.</p>
        </div>
    </div>
</div>

<!-- Estilos y scripts adicionales -->
@push('styles')
<style>
    /* Animaciones personalizadas para el login */
    .animation-delay-1000 {
        animation-delay: 1s;
    }
    .animation-delay-2000 {
        animation-delay: 2s;
    }
    
    /* Efecto de olas en el fondo */
    @keyframes wave {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }
    
    .wave {
        position: relative;
        overflow: hidden;
    }
    
    .wave::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        animation: wave 2s infinite;
    }
    
    /* Efectos de enfoque mejorados */
    .input-focus-effect {
        position: relative;
    }
    
    .input-focus-effect::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        width: 0;
        height: 2px;
        background: linear-gradient(to right, #667eea, #764ba2);
        transition: all 0.3s ease;
        transform: translateX(-50%);
    }
    
    .input-focus-effect:focus-within::after {
        width: 100%;
    }
</style>
@endpush

@push('scripts')
<script>
    // Función para mostrar/ocultar contraseña
    function togglePassword() {
        const passwordField = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');
        
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }
    
    // Efectos de animación al cargar
    document.addEventListener('DOMContentLoaded', function() {
        // Animación de entrada para los campos
        const inputs = document.querySelectorAll('input');
        inputs.forEach((input, index) => {
            input.style.opacity = '0';
            input.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                input.style.transition = 'all 0.6s ease';
                input.style.opacity = '1';
                input.style.transform = 'translateY(0)';
            }, 100 * index);
        });
        
        // Efecto de typing en el placeholder
        const emailInput = document.getElementById('email');
        const originalPlaceholder = emailInput.placeholder;
        emailInput.placeholder = '';
        
        let i = 0;
        const typeEffect = setInterval(() => {
            if (i < originalPlaceholder.length) {
                emailInput.placeholder += originalPlaceholder.charAt(i);
                i++;
            } else {
                clearInterval(typeEffect);
            }
        }, 100);
        
        // Efecto de partículas en el fondo (opcional)
        createParticles();
    });
    
    // Función para crear partículas animadas
    function createParticles() {
        const container = document.querySelector('.min-h-screen');
        
        for (let i = 0; i < 10; i++) {
            const particle = document.createElement('div');
            particle.className = 'absolute w-2 h-2 bg-white rounded-full opacity-20';
            particle.style.left = Math.random() * 100 + '%';
            particle.style.top = Math.random() * 100 + '%';
            particle.style.animation = `float ${3 + Math.random() * 4}s infinite ease-in-out`;
            particle.style.animationDelay = Math.random() * 2 + 's';
            
            container.appendChild(particle);
        }
    }
    
    // Animación de flotación para partículas
    const style = document.createElement('style');
    style.textContent = `
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
    `;
    document.head.appendChild(style);
</script>
@endpush
@endsection