@extends('layouts.app')

@section('title', 'Dashboard - CuentasCobro')

@section('content')
<main class="container-fluid bg-[#DFDFDF] min-h-screen p-0">

    <header class="row mt-2 mb-3 bg-white rounded mx-2 md:mx-8 lg:mx-10" aria-labelledby="dashboard-heading">
        <!-- Header del Dashboard -->
        <div class="col-12">
            <h2 id="dashboard-heading" class="sr-only">Dashboard</h2>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-primary">
                        <i class="fas fa-tachometer-alt me-2"></i>
                        Dashboard
                    </h1>
                    <p class="mb-0 text-dark">
                        Bienvenido, <strong>{{ $user->name }}</strong>
                        @if($userRole)
                        - <span class="badge bg-primary text-light">{{ ucfirst(str_replace('_', ' ', $userRole))
                            }}</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </header>

    @yield('dashboardRoles')

</main>
@endsection

@push('styles')
<style>
    .custom-navbar.bg-primary {
        background-color: var(--color-primary) !important;
    }

    .custom-navbar .navbar-brand,
    .custom-navbar .nav-link {
        color: var(--color-light) !important;
    }

    .custom-navbar .nav-link.active,
    .custom-navbar .nav-link:focus,
    .custom-navbar .nav-link:hover {
        color: var(--color-primary) !important;
        background-color: var(--color-light) !important;
        border-radius: 0.25rem;
    }
</style>
@endpush