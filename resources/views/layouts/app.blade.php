<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CuentasCobro')</title>
    
    <!-- TailwindCSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts - Inter y Poppins para diseño moderno -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* Variables CSS para colores modernos con degradados */
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --neutral-gradient: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
            --success-gradient: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
            --dark-gradient: linear-gradient(135deg, #232526 0%, #414345 100%);
            --glass-effect: rgba(255, 255, 255, 0.1);
            --shadow-soft: 0 10px 25px rgba(0, 0, 0, 0.1);
            --shadow-medium: 0 15px 35px rgba(0, 0, 0, 0.15);
            --shadow-strong: 0 20px 40px rgba(0, 0, 0, 0.2);
        }
        
        /* Tipografía moderna */
        body {
            font-family: 'Inter', 'Poppins', system-ui, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        /* Efectos de glassmorphism y animaciones suaves */
        .glass-card {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 16px;
            box-shadow: var(--shadow-soft);
        }
        
        .gradient-primary {
            background: var(--primary-gradient);
        }
        
        .gradient-secondary {
            background: var(--secondary-gradient);
        }
        
        /* Animaciones personalizadas */
        .fade-in {
            animation: fadeIn 0.6s ease-in-out;
        }
        
        .slide-up {
            animation: slideUp 0.8s ease-out;
        }
        
        .bounce-in {
            animation: bounceIn 0.7s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes bounceIn {
            0% { opacity: 0; transform: scale(0.8); }
            50% { opacity: 1; transform: scale(1.05); }
            100% { opacity: 1; transform: scale(1); }
        }
        
        /* Hover effects modernos */
        .btn-modern {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            transform: translateY(0);
        }
        
        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-medium);
        }
        
        .btn-modern:active {
            transform: translateY(0);
            transition: all 0.1s;
        }
        
        /* Inputs modernos */
        .input-modern {
            transition: all 0.3s ease;
            border: 2px solid transparent;
            background: rgba(255, 255, 255, 0.9);
        }
        
        .input-modern:focus {
            border-color: #667eea;
            background: rgba(255, 255, 255, 1);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            transform: translateY(-1px);
        }
        
        /* Scroll personalizado */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #764ba2, #667eea);
        }
    </style>
    
    <!-- Configuración de TailwindCSS personalizada -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
                        'poppins': ['Poppins', 'sans-serif'],
                    },
                    colors: {
                        'primary': {
                            50: '#f0f4ff',
                            500: '#667eea',
                            600: '#5a67d8',
                            700: '#4c51bf',
                        },
                        'secondary': {
                            500: '#f093fb',
                            600: '#f5576c',
                        }
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.6s ease-in-out',
                        'slide-up': 'slideUp 0.8s ease-out',
                        'bounce-in': 'bounceIn 0.7s ease-out',
                    }
                }
            }
        }
    </script>
    
    @stack('styles')
</head>
<body class="font-inter antialiased">
    <!-- Overlay de loading con animación -->
    <div id="page-loader" class="fixed inset-0 bg-gradient-to-br from-indigo-500 to-purple-600 z-50 flex items-center justify-center transition-opacity duration-500">
        <div class="text-center text-white">
            <div class="animate-spin rounded-full h-16 w-16 border-4 border-white border-t-transparent mx-auto mb-4"></div>
            <p class="text-lg font-medium">Cargando...</p>
        </div>
    </div>
    
    <!-- Contenido principal -->
    <div id="main-content" class="opacity-0 transition-opacity duration-500">
        @include('partials.navbar')
        
        <!-- Wrapper principal con animación -->
        <main class="fade-in">
            @yield('content')
        </main>
    </div>
    
    <!-- Scripts para animaciones de carga -->
    <script>
        // Efecto de carga de página moderna
        window.addEventListener('load', function() {
            const loader = document.getElementById('page-loader');
            const content = document.getElementById('main-content');
            
            setTimeout(() => {
                loader.classList.add('opacity-0');
                content.classList.remove('opacity-0');
                
                setTimeout(() => {
                    loader.style.display = 'none';
                }, 500);
            }, 800);
        });
        
        // Efectos de hover modernos para elementos interactivos
        document.addEventListener('DOMContentLoaded', function() {
            // Agregar clases de hover a botones
            const buttons = document.querySelectorAll('button, .btn, [role="button"]');
            buttons.forEach(btn => {
                if (!btn.classList.contains('btn-modern')) {
                    btn.classList.add('btn-modern');
                }
            });
            
            // Agregar clases a inputs
            const inputs = document.querySelectorAll('input, textarea, select');
            inputs.forEach(input => {
                if (!input.classList.contains('input-modern')) {
                    input.classList.add('input-modern');
                }
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>