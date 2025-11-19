@if (!request()->routeIs('login'))
<!-- Navbar moderno con glassmorphism y animaciones suaves -->
<nav class="fixed top-0 left-0 right-0 z-40 transition-all duration-300" id="modern-navbar">
    <div class="glass-card mx-4 mt-4 rounded-2xl">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <!-- Logo y brand con animación -->
                <a href="{{ route('dashboard') }}">
                <div class="flex items-center space-x-3">
                    <div class="gradient-primary p-3 rounded-xl shadow-lg">
                        <i class="fas fa-file-invoice-dollar text-white text-xl"></i>
                    </div>
                    <div class="hidden md:block">
                            <h1 class="text-xl font-bold text-gray-800 tracking-tight">CuentasCobro</h1>
                        <p class="text-sm text-gray-500 -mt-1">Sistema de Gestión</p>
                    </div>
                </div>
                </a>
                
                <!-- Navigation links - versión desktop -->
                <div class="hidden lg:flex items-center space-x-8">
                    <!-- Dropdown de Cuentas de Cobro con diseño moderno -->
                    <div class="relative group">
                        <button class="flex items-center space-x-2 px-4 py-2 rounded-xl text-gray-700 hover:text-primary-600 hover:bg-white/50 transition-all duration-300 font-medium">
                            <i class="fas fa-file-alt text-sm"></i>
                            <span>Cuentas de Cobro</span>
                            <i class="fas fa-chevron-down text-xs transition-transform duration-300 group-hover:rotate-180"></i>
                        </button>
                        
                        <!-- Dropdown menu con glassmorphism -->
                        <div class="absolute top-full left-0 mt-2 w-64 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform translate-y-2 group-hover:translate-y-0">
                            <div class="glass-card p-2 shadow-xl">
                                <a href="{{ route('cuentas-cobro.mostrar') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-white/70 hover:text-primary-600 transition-all duration-200">
                                    <i class="fas fa-eye text-primary-500"></i>
                                    <div>
                                        <p class="font-medium">Ver cuentas</p>
                                        <p class="text-xs text-gray-500">Consultar existentes</p>
                                    </div>
                                </a>
                                <a href="{{ route('cuentas-cobro.crear') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-white/70 hover:text-primary-600 transition-all duration-200">
                                    <i class="fas fa-plus-circle text-green-500"></i>
                                    <div>
                                        <p class="font-medium">Crear cuenta</p>
                                        <p class="text-xs text-gray-500">Nueva cuenta de cobro</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Links dinámicos según el rol del usuario -->
                    @if($userRole === 'alcalde')
                        <a href="#" class="flex items-center space-x-2 px-4 py-2 rounded-xl text-gray-700 hover:text-primary-600 hover:bg-white/50 transition-all duration-300 font-medium">
                            <i class="fas fa-chart-bar text-sm"></i>
                            <span>Reportes</span>
                        </a>
                        <a href="{{ route('roles.index') }}" class="flex items-center space-x-2 px-4 py-2 rounded-xl text-gray-700 hover:text-primary-600 hover:bg-white/50 transition-all duration-300 font-medium">
                            <i class="fas fa-cogs text-sm"></i>
                            <span>Administración</span>
                        </a>
                    @elseif($userRole === 'contratista')
                        <a href="{{ route('contratista.dashboard') }}" class="flex items-center space-x-2 px-4 py-2 rounded-xl text-gray-700 hover:text-primary-600 hover:bg-white/50 transition-all duration-300 font-medium">
                            <i class="fas fa-tachometer-alt text-sm"></i>
                            <span>Mi Dashboard</span>
                        </a>
                        <a href="#" class="flex items-center space-x-2 px-4 py-2 rounded-xl text-gray-700 hover:text-primary-600 hover:bg-white/50 transition-all duration-300 font-medium">
                            <i class="fas fa-handshake text-sm"></i>
                            <span>Contrato</span>
                        </a>
                    @elseif($userRole === 'supervisor')
                        <a href="{{ route('supervisor.dashboard') }}" class="flex items-center space-x-2 px-4 py-2 rounded-xl text-gray-700 hover:text-primary-600 hover:bg-white/50 transition-all duration-300 font-medium">
                            <i class="fas fa-tachometer-alt text-sm"></i>
                            <span>Mi Dashboard</span>
                        </a>
                        <!-- Dropdown de Supervisión -->
                        <div class="relative group">
                            <button class="flex items-center space-x-2 px-4 py-2 rounded-xl text-gray-700 hover:text-primary-600 hover:bg-white/50 transition-all duration-300 font-medium">
                                <i class="fas fa-clipboard-check text-sm"></i>
                                <span>Supervisión</span>
                                <i class="fas fa-chevron-down text-xs transition-transform duration-300 group-hover:rotate-180"></i>
                            </button>
                            
                            <!-- Dropdown menu -->
                            <div class="absolute top-full left-0 mt-2 w-64 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform translate-y-2 group-hover:translate-y-0">
                                <div class="glass-card p-2 shadow-xl">
                                    <a href="{{ route('supervisor.cuentas-cobro.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-white/70 hover:text-primary-600 transition-all duration-200">
                                        <i class="fas fa-file-invoice text-orange-500"></i>
                                        <div>
                                            <p class="font-medium">Revisar Cuentas</p>
                                            <p class="text-xs text-gray-500">Aprobar y rechazar</p>
                                        </div>
                                    </a>
                                    <a href="{{ route('supervisor.contratistas.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-white/70 hover:text-primary-600 transition-all duration-200">
                                        <i class="fas fa-users text-blue-500"></i>
                                        <div>
                                            <p class="font-medium">Contratistas</p>
                                            <p class="text-xs text-gray-500">Gestión y seguimiento</p>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @elseif($userRole === 'ordenador_gasto')
                        <a href="#" class="flex items-center space-x-2 px-4 py-2 rounded-xl text-gray-700 hover:text-primary-600 hover:bg-white/50 transition-all duration-300 font-medium">
                            <i class="fas fa-calculator text-sm"></i>
                            <span>Presupuesto</span>
                        </a>
                        <a href="#" class="flex items-center space-x-2 px-4 py-2 rounded-xl text-gray-700 hover:text-primary-600 hover:bg-white/50 transition-all duration-300 font-medium">
                            <i class="fas fa-chart-pie text-sm"></i>
                            <span>Reportes Financieros</span>
                        </a>
                    @elseif($userRole === 'tesoreria')
                        <a href="{{ route('tesoreria.dashboard') }}" class="flex items-center space-x-2 px-4 py-2 rounded-xl text-gray-700 hover:text-primary-600 hover:bg-white/50 transition-all duration-300 font-medium">
                            <i class="fas fa-tachometer-alt text-sm"></i>
                            <span>Mi Dashboard</span>
                        </a>
                        <!-- Dropdown de Tesorería -->
                        <div class="relative group">
                            <button class="flex items-center space-x-2 px-4 py-2 rounded-xl text-gray-700 hover:text-primary-600 hover:bg-white/50 transition-all duration-300 font-medium">
                                <i class="fas fa-university text-sm"></i>
                                <span>Tesorería</span>
                                <i class="fas fa-chevron-down text-xs transition-transform duration-300 group-hover:rotate-180"></i>
                            </button>
                            
                            <!-- Dropdown menu -->
                            <div class="absolute top-full left-0 mt-2 w-64 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform translate-y-2 group-hover:translate-y-0">
                                <div class="glass-card p-2 shadow-xl">
                                    <a href="{{ route('tesoreria.cuentas') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-white/70 hover:text-primary-600 transition-all duration-200">
                                        <i class="fas fa-file-invoice text-blue-500"></i>
                                        <div>
                                            <p class="font-medium">Gestionar Cuentas</p>
                                            <p class="text-xs text-gray-500">Ver y procesar pagos</p>
                                        </div>
                                    </a>
                                    <a href="{{ route('tesoreria.pendientes') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-white/70 hover:text-primary-600 transition-all duration-200">
                                        <i class="fas fa-clock text-orange-500"></i>
                                        <div>
                                            <p class="font-medium">Cuentas Pendientes</p>
                                            <p class="text-xs text-gray-500">Revisar y aprobar</p>
                                        </div>
                                    </a>
                                    <a href="{{ route('tesoreria.pagos-realizados') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-white/70 hover:text-primary-600 transition-all duration-200">
                                        <i class="fas fa-check-circle text-green-500"></i>
                                        <div>
                                            <p class="font-medium">Pagos Realizados</p>
                                            <p class="text-xs text-gray-500">Historial de pagos</p>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @elseif($userRole === 'contratacion')
                        <a href="#" class="flex items-center space-x-2 px-4 py-2 rounded-xl text-gray-700 hover:text-primary-600 hover:bg-white/50 transition-all duration-300 font-medium">
                            <i class="fas fa-file-contract text-sm"></i>
                            <span>Contratos</span>
                        </a>
                        <a href="#" class="flex items-center space-x-2 px-4 py-2 rounded-xl text-gray-700 hover:text-primary-600 hover:bg-white/50 transition-all duration-300 font-medium">
                            <i class="fas fa-chart-bar text-sm"></i>
                            <span>Reportes</span>
                        </a>
                    @endif
                </div>
                
                <!-- User menu y mobile toggle -->
                <div class="flex items-center space-x-4">
                    {{-- Componente de Notificaciones --}}
                    @auth
                        @include('components.notifications')
                    @endauth
                    
                    <!-- User dropdown -->
                    <div class="relative group">
                        <button class="flex items-center space-x-3 px-4 py-2 rounded-xl hover:bg-white/50 transition-all duration-300">
                            <div class="w-8 h-8 gradient-primary rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-white text-sm"></i>
                            </div>
                            <span class="hidden md:block text-gray-700 font-medium">Perfil</span>
                            <i class="fas fa-chevron-down text-xs text-gray-500 transition-transform duration-300 group-hover:rotate-180"></i>
                        </button>
                        
                        <!-- User dropdown menu -->
                        <div class="absolute top-full right-0 mt-2 w-56 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform translate-y-2 group-hover:translate-y-0">
                            <div class="glass-card p-2 shadow-xl">
                                <div class="px-4 py-3 border-b border-white/20">
                                    <p class="text-sm font-medium text-gray-800">{{ Auth::user()->name ?? 'Usuario' }}</p>
                                    <p class="text-xs text-gray-500">{{ ucfirst(str_replace('_', ' ', $userRole ?? 'Sin rol')) }}</p>
                                </div>
                                <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-white/70 transition-all duration-200">
                                    <i class="fas fa-user-circle text-primary-500"></i>
                                    <span>Mi Perfil</span>
                                </a>
                                <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-white/70 transition-all duration-200">
                                    <i class="fas fa-cog text-gray-500"></i>
                                    <span>Configuración</span>
                                </a>
                                <hr class="my-2 border-white/20">
                                <form method="POST" action="{{ route('logout') }}" class="block">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-red-600 hover:bg-red-50 transition-all duration-200">
                                        <i class="fas fa-sign-out-alt"></i>
                                        <span>Cerrar Sesión</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Mobile menu button -->
                    <button class="lg:hidden p-2 rounded-xl hover:bg-white/50 transition-all duration-300" id="mobile-menu-toggle">
                        <i class="fas fa-bars text-gray-700 text-lg"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Mobile menu overlay -->
<div class="fixed inset-0 z-50 lg:hidden opacity-0 invisible transition-all duration-300" id="mobile-menu-overlay">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
    <div class="absolute top-0 right-0 h-full w-80 max-w-[90vw] glass-card transform translate-x-full transition-transform duration-300" id="mobile-menu">
        <div class="p-6">
            <!-- Header del mobile menu -->
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center space-x-3">
                    <div class="gradient-primary p-2 rounded-lg">
                        <i class="fas fa-file-invoice-dollar text-white"></i>
                    </div>
                    <span class="font-bold text-gray-800">CuentasCobro</span>
                </div>
                <button class="p-2 rounded-lg hover:bg-white/50 transition-all duration-200" id="mobile-menu-close">
                    <i class="fas fa-times text-gray-700"></i>
                </button>
            </div>
            
            <!-- Mobile navigation links -->
            <div class="space-y-4">
                <div class="space-y-2">
                    <p class="text-xs uppercase tracking-wider text-gray-500 font-semibold px-4">GESTIÓN</p>
                    <a href="{{ route('cuentas-cobro.mostrar') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-white/70 transition-all duration-200">
                        <i class="fas fa-eye text-primary-500"></i>
                        <span>Ver cuentas de cobro</span>
                    </a>
                    <a href="{{ route('cuentas-cobro.crear') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-white/70 transition-all duration-200">
                        <i class="fas fa-plus-circle text-green-500"></i>
                        <span>Crear cuenta de cobro</span>
                    </a>
                </div>
                
                @if($userRole)
                <div class="space-y-2">
                    <p class="text-xs uppercase tracking-wider text-gray-500 font-semibold px-4">FUNCIONES DE {{ strtoupper(str_replace('_', ' ', $userRole)) }}</p>
                    @if($userRole === 'alcalde')
                        <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-white/70 transition-all duration-200">
                            <i class="fas fa-chart-bar text-blue-500"></i>
                            <span>Reportes</span>
                        </a>
                        <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-white/70 transition-all duration-200">
                            <i class="fas fa-cogs text-gray-500"></i>
                            <span>Administración</span>
                        </a>
                    @elseif($userRole === 'contratista')
                        <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-white/70 transition-all duration-200">
                            <i class="fas fa-handshake text-green-500"></i>
                            <span>Contrato</span>
                        </a>
                    @elseif($userRole === 'supervisor')
                        <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-white/70 transition-all duration-200">
                            <i class="fas fa-chart-line text-blue-500"></i>
                            <span>Reportes</span>
                        </a>
                    @elseif($userRole === 'ordenador_gasto')
                        <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-white/70 transition-all duration-200">
                            <i class="fas fa-calculator text-yellow-500"></i>
                            <span>Presupuesto</span>
                        </a>
                        <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-white/70 transition-all duration-200">
                            <i class="fas fa-chart-pie text-purple-500"></i>
                            <span>Reportes Financieros</span>
                        </a>
                    @elseif($userRole === 'tesoreria')
                        <a href="{{ route('tesoreria.dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-white/70 transition-all duration-200">
                            <i class="fas fa-tachometer-alt text-blue-500"></i>
                            <span>Dashboard Tesorería</span>
                        </a>
                        <a href="{{ route('tesoreria.cuentas') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-white/70 transition-all duration-200">
                            <i class="fas fa-file-invoice text-blue-500"></i>
                            <span>Gestionar Cuentas</span>
                        </a>
                        <a href="{{ route('tesoreria.pendientes') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-white/70 transition-all duration-200">
                            <i class="fas fa-clock text-orange-500"></i>
                            <span>Cuentas Pendientes</span>
                        </a>
                        <a href="{{ route('tesoreria.pagos-realizados') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-white/70 transition-all duration-200">
                            <i class="fas fa-check-circle text-green-500"></i>
                            <span>Pagos Realizados</span>
                        </a>
                    @elseif($userRole === 'contratacion')
                        <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-white/70 transition-all duration-200">
                            <i class="fas fa-file-contract text-blue-500"></i>
                            <span>Contratos</span>
                        </a>
                        <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-white/70 transition-all duration-200">
                            <i class="fas fa-chart-bar text-blue-500"></i>
                            <span>Reportes</span>
                        </a>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Script para funcionalidad del mobile menu -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');
    const mobileMenu = document.getElementById('mobile-menu');
    const mobileMenuClose = document.getElementById('mobile-menu-close');
    
    // Abrir mobile menu
    mobileMenuToggle?.addEventListener('click', function() {
        mobileMenuOverlay.classList.remove('opacity-0', 'invisible');
        mobileMenu.classList.remove('translate-x-full');
    });
    
    // Cerrar mobile menu
    function closeMobileMenu() {
        mobileMenuOverlay.classList.add('opacity-0', 'invisible');
        mobileMenu.classList.add('translate-x-full');
    }
    
    mobileMenuClose?.addEventListener('click', closeMobileMenu);
    mobileMenuOverlay?.addEventListener('click', function(e) {
        if (e.target === mobileMenuOverlay) {
            closeMobileMenu();
        }
    });
    
    // Navbar scroll effect
    const navbar = document.getElementById('modern-navbar');
    let lastScrollY = window.scrollY;
    
    window.addEventListener('scroll', function() {
        if (window.scrollY > lastScrollY && window.scrollY > 100) {
            // Scrolling down
            navbar.style.transform = 'translateY(-100%)';
        } else {
            // Scrolling up
            navbar.style.transform = 'translateY(0)';
        }
        lastScrollY = window.scrollY;
    });
});
</script>
@endif