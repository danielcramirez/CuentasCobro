@if (!request()->routeIs('login'))
<nav class="navbar navbar-expand-lg bg-primary w-100 p-0 m-0 border-0 custom-navbar" style="width: 100vw; min-height: 56px;">
    <div class="container-fluid p-0 m-0">
        <div class="d-flex align-items-center w-100">
            <a class="navbar-brand text-light fw-bold ms-3" href="#">Cuentas de Cobro</a>
            <button class="navbar-toggler text-light border-0" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto ms-lg-4">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-light" href="#" id="cuentasDropdown" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            Cuentas de Cobro
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="cuentasDropdown">
                            <li>
                                <a class="dropdown-item" href="{{ route('cuentas-cobro.mostrar') }}">
                                    Ver cuentas de cobro
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('cuentas-cobro.crear') }}">
                                    Crear cuenta de cobro
                                </a>
                            </li>
                        </ul>
                    </li>
                    @if($userRole === 'alcalde')
                    <li class="nav-item">
                        <a class="nav-link text-light" href="#">Reportes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-light" href="#">Administración</a>
                    </li>
                    @elseif($userRole === 'contratista')
                    <li class="nav-item">
                        <a class="nav-link text-light" href="#">Contrato</a>
                    </li>
                    @elseif($userRole === 'supervisor')
                    <li class="nav-item">
                        <a class="nav-link text-light" href="#">Reportes</a>
                    </li>
                    @elseif($userRole === 'ordenador_gasto')
                    <li class="nav-item">
                        <a class="nav-link text-light" href="#">Presupuesto</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-light" href="#">Reportes Financieros</a>
                    </li>
                    @elseif($userRole === 'tesoreria')
                    <li class="nav-item">
                        <a class="nav-link text-light" href="#">Procesar Pagos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-light" href="#">Reportes Financieros</a>
                    </li>
                    @elseif($userRole === 'contratacion')
                    <li class="nav-item">
                        <a class="nav-link text-light" href="#">Contratos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-light" href="#">Reportes</a>
                    </li>
                    @endif
                </ul>
                <!-- Botón de sidebar a la derecha -->
                <button class="btn text-light ms-auto me-3" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#sidebarMenu" aria-controls="sidebarMenu">
                    <i class="fas fa-bars fa-lg"></i>
                </button>
            </div>
        </div>
</nav>

<div class="offcanvas offcanvas-end" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="sidebarMenuLabel">Menú</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
    </div>
    <div class="offcanvas-body">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="#">Perfil</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Configuración</a>
            </li>
            <li class="nav-item">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="nav-link btn btn-link px-0 text-start">Salir</button>
                </form>
            </li>
        </ul>
    </div>
</div>
@endif