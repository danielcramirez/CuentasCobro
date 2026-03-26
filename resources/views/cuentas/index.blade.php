@extends('layouts.app')

@section('title', 'Cuentas de Cobro')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">Cuentas de Cobro</h2>
            <small class="text-muted">Flujo: Contratista -> Supervisión -> Supervisor -> Central de Cuentas -> Fiduprevisora -> Pago</small>
        </div>

        @if($user->hasRole('contratista'))
            <a href="{{ route('cuentas.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Nueva Cuenta
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Contratista</th>
                            <th>Mes Cobrado</th>
                            <th>Apoyo a la supervisión</th>
                            <th>Supervisor</th>
                            <th>Central de Cuentas</th>
                            <th>Fiduprevisora</th>
                            <th>Actualizado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cuentas as $cuenta)
                            @php
                                $estadoApoyo = $cuenta->getEstadoApoyoRevision();
                                $estadoSupervisor = $cuenta->getEstadoSupervisorRevision();
                                $estadoCentral = $cuenta->getEstadoCentralRevision();
                            @endphp
                            <tr>
                                <td>{{ $cuenta->id }}</td>
                                <td>{{ $cuenta->contractor->name ?? 'N/A' }}</td>
                                <td>{{ $cuenta->billing_month }}</td>
                                <td>
                                    @if($estadoApoyo === 'aprobada')
                                        <span class="badge bg-success">Aprobada</span>
                                    @elseif($estadoApoyo === 'rechazada')
                                        <span class="badge bg-danger">Rechazada</span>
                                    @else
                                        <span class="badge bg-secondary">Pendiente</span>
                                    @endif
                                </td>
                                <td>
                                    @if($estadoSupervisor === 'aprobada')
                                        <span class="badge bg-success">Aprobada</span>
                                    @else
                                        <span class="badge bg-secondary">Pendiente</span>
                                    @endif
                                </td>
                                <td>
                                    @if($estadoCentral === 'aprobada')
                                        <span class="badge bg-success">Aprobada</span>
                                    @elseif($estadoCentral === 'rechazada')
                                        <span class="badge bg-danger">Rechazada</span>
                                    @elseif($estadoCentral === 'pendiente_central')
                                        <span class="badge bg-warning text-dark">Pendiente Central</span>
                                    @else
                                        <span class="badge bg-secondary">Pendiente</span>
                                    @endif
                                </td>
                                <td>
                                    @if($cuenta->fiduprevisora_status === 'pagado')
                                        <span class="badge bg-success">Pagado</span>
                                    @elseif($cuenta->fiduprevisora_status === 'rechazada')
                                        <span class="badge bg-danger">Rechazada</span>
                                    @elseif($cuenta->fiduprevisora_status === 'en_revision')
                                        <span class="badge bg-info text-dark">En revision</span>
                                    @elseif($cuenta->fiduprevisora_status === 'en_tramite')
                                        <span class="badge bg-warning text-dark">En tramite</span>
                                    @else
                                        <span class="badge bg-secondary">Pendiente</span>
                                    @endif
                                </td>
                                <td>{{ $cuenta->updated_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('cuentas.show', $cuenta) }}" class="btn btn-sm btn-outline-primary">
                                        Ver detalle
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">No hay cuentas de cobro registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
