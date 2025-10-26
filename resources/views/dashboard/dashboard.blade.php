@extends('layouts.app')

@section('title', 'Dashboard - CuentasCobro')

@section('content')
<main class="container-fluid">
    <header class="row mb-4" aria-labelledby="dashboard-heading">
        <!-- Header del Dashboard -->
        <div class="col-12">
            <h2 id="dashboard-heading" class="sr-only">Dashboard</h2>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-tachometer-alt me-2"></i>
                        Dashboard
                    </h1>
                    <p class="text-muted mb-0">
                        Bienvenido, <strong>{{ $user->name }}</strong>
                        @if($userRole)
                        - <span class="badge bg-primary">{{ ucfirst(str_replace('_', ' ', $userRole)) }}</span>
                        @endif
                    </p>
                </div>
                <div class="text-end">
                    <small class="text-muted">
                        <i class="fas fa-calendar me-1"></i>
                        {{ now()->format('d/m/Y H:i') }}
                    </small>
                </div>
            </div>
        </div>
    </div>

    @includeWhen($userRole === 'alcalde', 'dashboard.alcalde', [
        'totalUsers' => $totalUsers ?? 0,
        'usersWithRoles' => $usersWithRoles ?? 0,
        'totalRoles' => $totalRoles ?? 0,
        'usersWithoutRoles' => $usersWithoutRoles ?? 0,
        'systemRoles' => $systemRoles ?? collect(),
        'rolesStats' => $rolesStats ?? collect(),
        'recentUsers' => $recentUsers ?? collect(),
    ])

    @includeWhen($userRole === 'supervisor', 'dashboard.supervisor')

    @includeWhen($userRole === 'contratista', 'dashboard.contratista')

    @includeWhen(!$userRole, 'dashboard.no_role')

    @includeWhen(in_array($userRole, ['ordenador_gasto', 'tesoreria', 'contratacion']), 'dashboard.other_roles', ['userRole' => $userRole])
    </main>

@push('styles')
<style>
    .border-left-primary {
        border-left: 0.25rem solid #4e73df !important;
    }
    .border-left-success {
        border-left: 0.25rem solid #1cc88a !important;
    }
    .border-left-info {
        border-left: 0.25rem solid #36b9cc !important;
    }
    .border-left-warning {
        border-left: 0.25rem solid #f6c23e !important;
    }
</style>
@endpush
@endsection