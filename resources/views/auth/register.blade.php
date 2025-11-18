@extends('layouts.app')

@section('title', 'Registro - CuentasCobro')

@section('content')
<!-- Contenedor principal con fondo dinámico -->
<div class="min-h-screen flex items-center justify-center relative overflow-hidden py-20">
    <!-- Formas de fondo animadas -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute top-1/4 left-0 w-80 h-80 bg-gradient-to-br from-green-400 to-blue-500 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse"></div>
        <div class="absolute top-0 right-1/4 w-96 h-96 bg-gradient-to-br from-purple-400 to-pink-400 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse animation-delay-1000"></div>
        <div class="absolute bottom-1/4 left-1/3 w-72 h-72 bg-gradient-to-br from-yellow-400 to-red-400 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse animation-delay-2000"></div>
    </div>
    
    <!-- Card de registro con glassmorphism -->
    <div class="glass-card max-w-lg w-full mx-4 p-8 bounce-in">
        <!-- Header del formulario -->
        <div class="text-center mb-8">
            <div class="gradient-secondary w-20 h-20 rounded-2xl mx-auto mb-6 flex items-center justify-center shadow-xl transform hover:scale-105 transition-transform duration-300">
                <i class="fas fa-user-plus text-white text-3xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2 font-poppins">¡Únete a nosotros!</h1>
            <p class="text-gray-600">Crea tu cuenta en CuentasCobro</p>
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
        <form method="POST" action="{{ route('register') }}" class="space-y-6">
            @csrf
            
            <!-- Campo de nombre -->
            <div class="space-y-2">
                <label for="name" class="flex items-center text-sm font-medium text-gray-700">
                    <i class="fas fa-user text-green-500 mr-2"></i>
                    Nombre completo
                </label>
                <div class="relative">
                    <input type="text" 
                           class="w-full px-4 py-3 pl-12 bg-white/70 border-2 border-transparent rounded-xl focus:border-green-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-green-100 transition-all duration-300 placeholder-gray-400 @error('name') border-red-300 focus:border-red-500 focus:ring-red-100 @enderror" 
                           id="name" 
                           name="name" 
                           value="{{ old('name') }}" 
                           required 
                           autocomplete="name" 
                           autofocus
                           placeholder="Tu nombre completo">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-user text-gray-400"></i>
                    </div>
                </div>
                @error('name')
                    <p class="text-red-500 text-sm flex items-center mt-1">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Campo de email -->
            <div class="space-y-2">
                <label for="email" class="flex items-center text-sm font-medium text-gray-700">
                    <i class="fas fa-envelope text-blue-500 mr-2"></i>
                    Correo electrónico
                </label>
                <div class="relative">
                    <input type="email" 
                           class="w-full px-4 py-3 pl-12 bg-white/70 border-2 border-transparent rounded-xl focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-100 transition-all duration-300 placeholder-gray-400 @error('email') border-red-300 focus:border-red-500 focus:ring-red-100 @enderror" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           autocomplete="email"
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
                    <i class="fas fa-lock text-purple-500 mr-2"></i>
                    Contraseña
                </label>
                <div class="relative">
                    <input type="password" 
                           class="w-full px-4 py-3 pl-12 pr-12 bg-white/70 border-2 border-transparent rounded-xl focus:border-purple-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-purple-100 transition-all duration-300 placeholder-gray-400 @error('password') border-red-300 focus:border-red-500 focus:ring-red-100 @enderror" 
                           id="password" 
                           name="password" 
                           required 
                           autocomplete="new-password"
                           placeholder="••••••••">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-gray-400"></i>
                    </div>
                    <button type="button" class="absolute inset-y-0 right-0 pr-4 flex items-center" onclick="togglePassword('password')">
                        <i class="fas fa-eye text-gray-400 hover:text-purple-500 transition-colors duration-200" id="toggleIconPassword"></i>
                    </button>
                </div>
                @error('password')
                    <p class="text-red-500 text-sm flex items-center mt-1">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Campo de confirmación de contraseña -->
            <div class="space-y-2">
                <label for="password_confirmation" class="flex items-center text-sm font-medium text-gray-700">
                    <i class="fas fa-shield-alt text-orange-500 mr-2"></i>
                    Confirmar contraseña
                </label>
                <div class="relative">
                    <input type="password" 
                           class="w-full px-4 py-3 pl-12 pr-12 bg-white/70 border-2 border-transparent rounded-xl focus:border-orange-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-orange-100 transition-all duration-300 placeholder-gray-400 @error('password_confirmation') border-red-300 focus:border-red-500 focus:ring-red-100 @enderror" 
                           id="password_confirmation" 
                           name="password_confirmation" 
                           required 
                           autocomplete="new-password"
                           placeholder="••••••••">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-shield-alt text-gray-400"></i>
                    </div>
                    <button type="button" class="absolute inset-y-0 right-0 pr-4 flex items-center" onclick="togglePassword('password_confirmation')">
                        <i class="fas fa-eye text-gray-400 hover:text-orange-500 transition-colors duration-200" id="toggleIconPasswordConfirmation"></i>
                    </button>
                </div>
                @error('password_confirmation')
                    <p class="text-red-500 text-sm flex items-center mt-1">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Checkbox términos y condiciones -->
            <div class="flex items-start space-x-3">
                <input type="checkbox" 
                       class="mt-1 w-4 h-4 text-primary-600 bg-white border-gray-300 rounded focus:ring-primary-500 focus:ring-2 transition-all duration-200" 
                       id="terms" 
                       name="terms"
                       required>
                <label for="terms" class="text-sm text-gray-600 cursor-pointer">
                    Acepto los 
                    <a href="#" class="text-primary-600 hover:text-primary-500 underline">términos y condiciones</a> 
                    y la 
                    <a href="#" class="text-primary-600 hover:text-primary-500 underline">política de privacidad</a>
                </label>
            </div>

            <!-- Botón de envío -->
            <button type="submit" 
                    class="w-full gradient-secondary text-white py-4 px-6 rounded-xl font-semibold text-lg shadow-lg hover:shadow-xl transform hover:-translate-y-1 active:translate-y-0 transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-pink-200">
                <i class="fas fa-user-plus mr-2"></i>
                Crear mi cuenta
            </button>
        </form>

        <!-- Divisor -->
        <div class="relative my-8">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-200"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-4 bg-white text-gray-500">¿Ya tienes cuenta?</span>
            </div>
        </div>

        <!-- Link de login -->
        <div class="text-center">
            <a href="{{ route('login') }}" 
               class="inline-flex items-center justify-center w-full px-6 py-3 border-2 border-primary-200 text-primary-700 bg-primary-50 rounded-xl hover:bg-primary-100 hover:border-primary-300 transition-all duration-300 font-medium">
                <i class="fas fa-sign-in-alt mr-2"></i>
                Iniciar sesión
            </a>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center text-xs text-gray-500">
            <p>© 2024 CuentasCobro. Sistema de Gestión Integral.</p>
        </div>
    </div>
</div>

<!-- Indicador de fortaleza de contraseña -->
<div id="password-strength" class="hidden fixed bottom-4 right-4 glass-card p-4 rounded-lg shadow-lg">
    <div class="flex items-center space-x-2">
        <i class="fas fa-shield-alt text-gray-400"></i>
        <div class="flex-1">
            <div class="text-xs text-gray-600 mb-1">Fortaleza de contraseña</div>
            <div class="w-24 h-2 bg-gray-200 rounded-full overflow-hidden">
                <div id="strength-bar" class="h-full transition-all duration-300 bg-red-400"></div>
            </div>
        </div>
        <span id="strength-text" class="text-xs font-medium text-gray-600">Débil</span>
    </div>
</div>

<!-- Estilos y scripts adicionales -->
@push('styles')
<style>
    /* Animaciones personalizadas para el registro */
    .animation-delay-1000 {
        animation-delay: 1s;
    }
    .animation-delay-2000 {
        animation-delay: 2s;
    }
    
    /* Efecto de progreso en tiempo real */
    .progress-step {
        transition: all 0.3s ease;
    }
    
    .progress-step.active {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
    }
    
    /* Efecto de validación en tiempo real */
    .field-valid {
        border-color: #10b981 !important;
    }
    
    .field-invalid {
        border-color: #ef4444 !important;
    }
</style>
@endpush

@push('scripts')
<script>
    // Función para mostrar/ocultar contraseña
    function togglePassword(fieldId) {
        const passwordField = document.getElementById(fieldId);
        const toggleIcon = document.getElementById('toggleIcon' + fieldId.charAt(0).toUpperCase() + fieldId.slice(1));
        
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
    
    // Validación en tiempo real de la fortaleza de contraseña
    function checkPasswordStrength(password) {
        const strengthIndicator = document.getElementById('password-strength');
        const strengthBar = document.getElementById('strength-bar');
        const strengthText = document.getElementById('strength-text');
        
        if (password.length === 0) {
            strengthIndicator.classList.add('hidden');
            return;
        }
        
        strengthIndicator.classList.remove('hidden');
        
        let strength = 0;
        let feedback = '';
        
        // Criterios de fortaleza
        if (password.length >= 8) strength += 1;
        if (/[a-z]/.test(password)) strength += 1;
        if (/[A-Z]/.test(password)) strength += 1;
        if (/[0-9]/.test(password)) strength += 1;
        if (/[^A-Za-z0-9]/.test(password)) strength += 1;
        
        // Actualizar indicador
        const percentage = (strength / 5) * 100;
        strengthBar.style.width = percentage + '%';
        
        if (strength <= 2) {
            strengthBar.className = 'h-full transition-all duration-300 bg-red-400';
            strengthText.textContent = 'Débil';
            strengthText.className = 'text-xs font-medium text-red-600';
        } else if (strength <= 3) {
            strengthBar.className = 'h-full transition-all duration-300 bg-yellow-400';
            strengthText.textContent = 'Media';
            strengthText.className = 'text-xs font-medium text-yellow-600';
        } else if (strength <= 4) {
            strengthBar.className = 'h-full transition-all duration-300 bg-blue-400';
            strengthText.textContent = 'Buena';
            strengthText.className = 'text-xs font-medium text-blue-600';
        } else {
            strengthBar.className = 'h-full transition-all duration-300 bg-green-400';
            strengthText.textContent = 'Excelente';
            strengthText.className = 'text-xs font-medium text-green-600';
        }
    }
    
    // Validación de confirmación de contraseña
    function validatePasswordConfirmation() {
        const password = document.getElementById('password').value;
        const confirmation = document.getElementById('password_confirmation').value;
        const confirmationField = document.getElementById('password_confirmation');
        
        if (confirmation.length === 0) {
            confirmationField.classList.remove('field-valid', 'field-invalid');
            return;
        }
        
        if (password === confirmation) {
            confirmationField.classList.add('field-valid');
            confirmationField.classList.remove('field-invalid');
        } else {
            confirmationField.classList.add('field-invalid');
            confirmationField.classList.remove('field-valid');
        }
    }
    
    // Efectos de animación al cargar
    document.addEventListener('DOMContentLoaded', function() {
        // Animación de entrada para los campos
        const inputs = document.querySelectorAll('input');
        inputs.forEach((input, index) => {
            input.style.opacity = '0';
            input.style.transform = 'translateX(-20px)';
            
            setTimeout(() => {
                input.style.transition = 'all 0.6s ease';
                input.style.opacity = '1';
                input.style.transform = 'translateX(0)';
            }, 100 * index);
        });
        
        // Event listeners para validación en tiempo real
        document.getElementById('password').addEventListener('input', function() {
            checkPasswordStrength(this.value);
            validatePasswordConfirmation();
        });
        
        document.getElementById('password_confirmation').addEventListener('input', validatePasswordConfirmation);
        
        // Validación de email en tiempo real
        document.getElementById('email').addEventListener('blur', function() {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (this.value && emailRegex.test(this.value)) {
                this.classList.add('field-valid');
                this.classList.remove('field-invalid');
            } else if (this.value) {
                this.classList.add('field-invalid');
                this.classList.remove('field-valid');
            }
        });
        
        // Validación de nombre
        document.getElementById('name').addEventListener('blur', function() {
            if (this.value.length >= 2) {
                this.classList.add('field-valid');
                this.classList.remove('field-invalid');
            } else if (this.value) {
                this.classList.add('field-invalid');
                this.classList.remove('field-valid');
            }
        });
    });
</script>
@endpush
@endsection