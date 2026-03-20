<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CuentasCobro')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f9;
        }

        .login-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        
        .navbar-brand {
            font-weight: bold;
            color: #2c3e50 !important;
        }
        
        .app-navbar {
            height: 60px;
            background: #2c3e50;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 16px;
            position: sticky;
            top: 0;
            z-index: 20;
        }

        .app-navbar .brand {
            font-weight: bold;
            color: #fff;
            text-decoration: none;
        }

        .app-navbar .right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .app-navbar .logout-btn {
            border: 1px solid rgba(255,255,255,.4);
            background: transparent;
            color: #fff;
            border-radius: 6px;
            padding: 6px 10px;
            cursor: pointer;
        }

        .app-layout {
            display: flex;
            min-height: calc(100vh - 60px);
        }

        .sidebar {
            width: 260px;
            background: #34495e;
            height: calc(100vh - 60px);
            overflow-y: auto;
            position: sticky;
            top: 60px;
        }
        
        .menu-group {
            border-bottom: 1px solid rgba(255,255,255,.1);
        }
        
        .sidebar a {
            display: block;
            padding: 12px 15px;
            color: #fff;
            text-decoration: none;
            cursor: pointer;
        }

        .sidebar a:hover {
            background: #1abc9c;
            color: #fff;
        }
        
        .menu-title {
            font-weight: bold;
            background: #2c3e50;
        }

        .submenu {
            display: none;
            background: #3d566e;
        }

        .submenu.open {
            display: block;
        }

        .submenu a {
            padding-left: 30px;
            font-size: 14px;
        }

        .sub-submenu {
            display: none;
            background: #4a6a85;
        }

        .sub-submenu.open {
            display: block;
        }

        .sub-submenu a {
            padding-left: 50px;
            font-size: 13px;
        }

        .menu-active {
            background: #1abc9c;
        }
        
        .main-content {
            flex: 1;
            padding: 20px;
        }

        @media (max-width: 992px) {
            .sidebar {
                width: 220px;
            }
        }

        @media (max-width: 768px) {
            .app-layout {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                height: auto;
                position: static;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    @auth
        <div class="app-navbar">
            <a class="brand" href="{{ route('dashboard') }}">Sistema Cuentas de Cobro</a>
            <div class="right">
                <span>{{ Auth::user()->name }} - {{ ucfirst(str_replace('_', ' ', Auth::user()->role->name ?? 'sin rol')) }}</span>
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="logout-btn">Cerrar sesión</button>
                </form>
            </div>
        </div>

        <div class="app-layout">
            <aside class="sidebar">
                <div class="menu-group">
                    <a class="menu-title" onclick="toggleMenu('dashboardMenu')">Dashboard</a>
                    <div class="submenu {{ request()->routeIs('dashboard') ? 'open' : '' }}" id="dashboardMenu">
                        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'menu-active' : '' }}">Inicio</a>
                    </div>
                </div>

                @if(Auth::user()->hasAnyRole(['contratista', 'apoyo a la supervisión', 'apoyo a la supervision', 'apoyo a la supervicion', 'supervisor', 'admin']))
                    <div class="menu-group">
                        <a class="menu-title" onclick="toggleMenu('cuentasMenu')">Cuentas de Cobro</a>
                        <div class="submenu {{ request()->routeIs('cuentas.*') ? 'open' : '' }}" id="cuentasMenu">
                            <a href="{{ route('cuentas.index') }}" class="{{ request()->routeIs('cuentas.index') ? 'menu-active' : '' }}">Listado</a>
                            @if(Auth::user()->hasRole('contratista'))
                                <a href="{{ route('cuentas.create') }}" class="{{ request()->routeIs('cuentas.create') ? 'menu-active' : '' }}">Nueva cuenta</a>
                            @endif
                        </div>
                    </div>
                @endif

                @if(Auth::user()->hasRole('admin'))
                    <div class="menu-group">
                        <a class="menu-title" onclick="toggleMenu('adminMenu')">Administración</a>
                        <div class="submenu {{ request()->routeIs('roles.*') || request()->routeIs('register') ? 'open' : '' }}" id="adminMenu">
                            <a href="{{ route('roles.index') }}" class="{{ request()->routeIs('roles.index') ? 'menu-active' : '' }}">Roles</a>
                            <a href="{{ route('roles.create') }}" class="{{ request()->routeIs('roles.create') ? 'menu-active' : '' }}">Crear rol</a>
                            <a href="{{ route('register') }}" class="{{ request()->routeIs('register') ? 'menu-active' : '' }}">Registrar usuario</a>
                        </div>
                    </div>
                @endif
            </aside>

            <main class="main-content">
                @yield('content')
            </main>
        </div>
    @else
        @yield('content')
    @endauth
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function toggleMenu(id) {
            const menu = document.getElementById(id);
            if (!menu) {
                return;
            }
            menu.classList.toggle('open');
        }

        function toggleSubMenu(event, id) {
            event.stopPropagation();
            const submenu = document.getElementById(id);
            if (!submenu) {
                return;
            }
            submenu.classList.toggle('open');
        }
    </script>
    
    @stack('scripts')
</body>
</html>
