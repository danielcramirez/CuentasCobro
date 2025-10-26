@extends('layouts.app')
@section('dashboardRoles')
<!-- Parcial: Usuario sin rol -->
<aside class="row" aria-labelledby="no-role-alert">
    <h2 id="no-role-alert" class="sr-only">Sin rol asignado</h2>
    <div class="col-12">
        <div class="alert alert-warning" role="alert">
            <h4 class="alert-heading">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Sin rol asignado
            </h4>
            <p>No tienes un rol asignado en el sistema. Contacta al administrador para que te asigne un rol.</p>
            <hr>
            <p class="mb-0">Mientras tanto, puedes explorar las funcionalidades básicas del sistema.</p>
        </div>
    </div>
</aside>
@endsection
