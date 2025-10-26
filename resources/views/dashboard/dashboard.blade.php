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
                        <span id="clock" data-server-ts="{{ now()->timestamp }}">{{ now()->format('d/m/Y H:i:s') }}</span>
                    </small>
                </div>
            </div>
        </div>
        </div>

        @yield('dashboardRoles')

</main>

<script>
    (function() {
        const el = document.getElementById('clock');
        if (!el) return;

        // Server timestamp in seconds (provided by Blade). If missing, fallback to client time.
        const serverTsSec = parseInt(el.dataset.serverTs, 10);
        let current = Number.isFinite(serverTsSec) ? serverTsSec * 1000 : Date.now();

        function pad(n) {
            return String(n).padStart(2, '0');
        }

        function formatDate(ms) {
            const d = new Date(ms);
            const day = pad(d.getDate());
            const month = pad(d.getMonth() + 1);
            const year = d.getFullYear();
            const hours = pad(d.getHours());
            const minutes = pad(d.getMinutes());
            const seconds = pad(d.getSeconds());
            return `${day}/${month}/${year} ${hours}:${minutes}:${seconds}`;
        }

        function update() {
            el.textContent = formatDate(current);
            current += 1000; // increment one second
        }

        // Initial render and start ticking every second
        update();
        setInterval(update, 1000);
    })();
</script>

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