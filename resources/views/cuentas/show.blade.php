@extends('layouts.app')

@section('title', 'Detalle Cuenta de Cobro')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">Cuenta #{{ $cuenta->id }}</h2>
            <small class="text-muted">Contratista: {{ $cuenta->contractor->name ?? 'N/A' }}</small>
        </div>
        <a href="{{ route('cuentas.index') }}" class="btn btn-outline-secondary">Volver</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-7">
            @php
                $esApoyoSupervision = $user->hasAnyRole(['apoyo a la supervisión', 'apoyo a la supervision', 'apoyo a la supervicion']);
            @endphp
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <strong>Documentos</strong>
                </div>
                <div class="card-body">
                    <p><strong>Mes cobrado:</strong> {{ $cuenta->billing_month }}</p>
                    <p><strong>Numero de cuenta:</strong> {{ $cuenta->numero_cuenta }}</p>

                    
                    @unless($esApoyoSupervision)
                        

                        <div class="mb-2">
                            <strong>Estado Supervisor:</strong>
                            @if($cuenta->mayor_status === 'aprobada')
                                <span class="badge bg-success">Aprobada</span>
                            @elseif($cuenta->mayor_status === 'rechazada')
                                <span class="badge bg-danger">Rechazada</span>
                            @else
                                <span class="badge bg-warning text-dark">Pendiente</span>
                            @endif
                        </div>

                        <hr>
                        <p class="mb-2"><strong>Documentos cargados</strong></p>
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Doc</th>
                                        <th>Estado</th>
                                        <th>Comentario</th>
                                        <th>Validado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($cuenta->documentos->sortBy('numero_documento') as $documento)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ $documento->numero_documento }}</div>
                                                <div class="small text-muted">{{ $documento->nombre_documento }}</div>
                                            </td>
                                            <td>
                                                @if($documento->estado === 'validado')
                                                    <span class="badge bg-success">Aprobado</span>
                                                @elseif($documento->estado === 'rechazado')
                                                    <span class="badge bg-danger">Rechazado</span>
                                                @elseif($documento->estado === 'cargado')
                                                    <span class="badge bg-warning text-dark">Por revisar</span>
                                                @else
                                                    <span class="badge bg-secondary">Pendiente</span>
                                                @endif
                                            </td>
                                            <td class="small">{{ $documento->comentario_supervisor ?: '-' }}</td>
                                            <td class="small">{{ $documento->validado_at ? $documento->validado_at->format('d/m/Y H:i') : '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-muted">No hay documentos registrados.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @endunless
                </div>
            </div>

            @if($user->hasAnyRole(['apoyo a la supervisión', 'apoyo a la supervision', 'apoyo a la supervicion', 'supervisor']))
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Visor PDF</strong>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">Selecciona un archivo para visualizarlo sin descargarlo.</p>

                        @if($cuenta->documentos->isNotEmpty())
                            <div class="mb-3">
                                <label class="form-label"><strong>Documentos cargados</strong></label>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($cuenta->documentos->sortBy('numero_documento') as $documento)
                                        <button
                                            type="button"
                                            class="btn btn-outline-secondary btn-sm js-pdf-viewer-btn"
                                            data-pdf-url="{{ route('cuentas.documento.preview', [$cuenta, $documento]) }}"
                                        >
                                            Doc {{ $documento->numero_documento }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="ratio ratio-16x9 border rounded">
                            <iframe id="pdf-viewer-frame" src="{{ route('cuentas.preview', [$cuenta, 'cuenta']) }}" title="Visor PDF" style="border: 0;"></iframe>
                        </div>
                    </div>
                </div>
            @endif

            @if($user->hasRole('contratista') && ($cuenta->hasSupervisorRejection() || $cuenta->mayor_status === 'rechazada'))
                <div class="card shadow-sm">
                    <div class="card-header bg-warning-subtle">
                        <strong>Reenvío por Rechazo</strong>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Debes cargar nuevamente ambos archivos para reiniciar la revisión de apoyo a la supervisión.</p>
                        <form method="POST" action="{{ route('cuentas.resubmit', $cuenta) }}" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Mes cobrado</label>
                                <input type="month" class="form-control" name="billing_month" value="{{ $cuenta->billing_month }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Cuenta de cobro (PDF)</label>
                                <input type="file" class="form-control" name="cuenta_pdf" accept="application/pdf" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Pago de planilla (PDF)</label>
                                <input type="file" class="form-control" name="planilla_pdf" accept="application/pdf" required>
                            </div>
                            <button class="btn btn-warning" type="submit">Reenviar a Apoyo de Supervisión</button>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <strong>Observaciones</strong>
                </div>
                <div class="card-body">
                    <p><strong>Comentario supervisor (cuenta):</strong><br>{{ $cuenta->cuenta_supervisor_comment ?: 'Sin comentarios' }}</p>
                    <p><strong>Comentario supervisor (planilla):</strong><br>{{ $cuenta->planilla_supervisor_comment ?: 'Sin comentarios' }}</p>
                    <p><strong>Comentario administrador:</strong><br>{{ $cuenta->mayor_comment ?: 'Sin comentarios' }}</p>
                </div>
            </div>

            @if($user->hasAnyRole(['apoyo a la supervisión', 'apoyo a la supervision', 'apoyo a la supervicion', 'supervisor']))
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-info-subtle">
                        <strong>Aprobación por Documento (Apoyo a la Supervisión)</strong>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm align-middle">
                                <thead>
                                    <tr>
                                        <th>Doc</th>
                                        <th>Estado</th>
                                        <th>Comentario</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cuenta->documentos->sortBy('numero_documento') as $documento)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ $documento->numero_documento }}</div>
                                                <div class="small text-muted">{{ $documento->nombre_documento }}</div>
                                            </td>
                                            <td>
                                                @if($documento->estado === 'validado')
                                                    <span class="badge bg-success">Aprobado</span>
                                                @elseif($documento->estado === 'rechazado')
                                                    <span class="badge bg-danger">Rechazado</span>
                                                @elseif($documento->estado === 'cargado')
                                                    <span class="badge bg-warning text-dark">Por revisar</span>
                                                @else
                                                    <span class="badge bg-secondary">Pendiente</span>
                                                @endif
                                            </td>
                                            <td class="small">{{ $documento->comentario_supervisor ?: '-' }}</td>
                                            <td>
                                                <form method="POST" action="{{ route('cuentas.documento.review', [$cuenta, $documento]) }}" class="d-flex flex-column gap-2">
                                                    @csrf
                                                    <select name="decision" class="form-select form-select-sm" required>
                                                        <option value="aprobada">Aprobar</option>
                                                        <option value="rechazada">Rechazar</option>
                                                    </select>
                                                    <input type="text" name="comentario" class="form-control form-control-sm" placeholder="Comentario (obligatorio si rechaza)">
                                                    <button type="submit" class="btn btn-sm btn-primary">Guardar</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            @if($user->hasRole('admin'))
                <div class="card shadow-sm">
                    <div class="card-header bg-success-subtle">
                        <strong>Decisión del Administrador</strong>
                    </div>
                    <div class="card-body">
                        @if($cuenta->canGoToMayor())
                            <form method="POST" action="{{ route('cuentas.alcalde.review', $cuenta) }}">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label">Decisión final de pago</label>
                                    <select name="decision" class="form-select" required>
                                        <option value="aprobada">Aprobar pago</option>
                                        <option value="rechazada">Rechazar pago</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Comentario (obligatorio si rechaza)</label>
                                    <textarea name="comment" class="form-control" rows="3"></textarea>
                                </div>

                                <button type="submit" class="btn btn-success">Guardar decisión</button>
                            </form>
                        @else
                            <div class="alert alert-warning mb-0">
                                Esta cuenta aún no cumple condición para aprobación: primero deben aprobar cuenta y planilla en supervisor.
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@if($user->hasAnyRole(['apoyo a la supervisión', 'supervisor']))
    <script>
        (function () {
            const frame = document.getElementById('pdf-viewer-frame');
            const buttons = document.querySelectorAll('.js-pdf-viewer-btn');

            if (!frame || buttons.length === 0) {
                return;
            }

            buttons.forEach((button) => {
                button.addEventListener('click', () => {
                    const url = button.getAttribute('data-pdf-url');
                    if (url) {
                        frame.src = url;
                    }
                });
            });
        })();
    </script>
@endif
@endsection
