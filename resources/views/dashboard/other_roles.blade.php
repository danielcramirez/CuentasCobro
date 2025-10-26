@extends('layouts.app')
@section('dashboardRoles')
<!-- Parcial: Otros roles (ordenador_gasto, tesoreria, contratacion) -->
<section aria-labelledby="other-roles" class="row">
    <h2 id="other-roles" class="sr-only">Paneles por rol</h2>
    <div class="col-12">
        <article class="card shadow">
            <div class="card-body text-center py-5">
                @switch($userRole)
                    @case('ordenador_gasto')
                        <i class="fas fa-money-check-alt fa-4x text-info mb-3"></i>
                        <h4>Panel de Ordenador del Gasto</h4>
                        <p class="text-muted">Aquí podrás autorizar pagos y gestionar presupuestos.</p>
                        @break
                    @case('tesoreria')
                        <i class="fas fa-coins fa-4x text-success mb-3"></i>
                        <h4>Panel de Tesorería</h4>
                        <p class="text-muted">Aquí podrás procesar pagos y generar reportes financieros.</p>
                        @break
                    @case('contratacion')
                        <i class="fas fa-handshake fa-4x text-primary mb-3"></i>
                        <h4>Panel de Contratación</h4>
                        <p class="text-muted">Aquí podrás gestionar contratos y contratistas.</p>
                        @break
                @endswitch
                <p class="text-muted">Esta funcionalidad se implementará próximamente.</p>
            </div>
        </article>
    </div>
</section>
@endsection
