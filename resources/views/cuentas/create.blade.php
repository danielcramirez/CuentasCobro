@extends('layouts.app')

@section('title', 'Nueva Cuenta de Cobro')

@push('styles')
<style>
    .drop-zone {
        border: 2px dashed #6c757d;
        border-radius: 0.75rem;
        padding: 1rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
        background-color: #fff;
    }

    .drop-zone:hover {
        border-color: #0d6efd;
        background-color: #f8fbff;
    }

    .drop-zone.dragover {
        border-color: #198754;
        background-color: #eaf7ef;
    }

    .drop-zone-text {
        font-weight: 500;
        color: #343a40;
    }

    .drop-zone-hint {
        font-size: 0.85rem;
        color: #6c757d;
        margin-top: 0.25rem;
    }

    .file-info {
        margin-top: 0.5rem;
        font-size: 0.875rem;
        color: #495057;
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4 p-md-5 bg-light">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                        <div>
                            <h1 class="h3 mb-2">Nueva Cuenta de Cobro</h1>
                            <p class="text-secondary mb-0">Completa el formulario y carga los soportes en PDF. El flujo de revisión inicia automáticamente al enviar.</p>
                        </div>
                        <a href="{{ route('cuentas.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Volver al listado
                        </a>
                    </div>
                </div>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger border-0 shadow-sm">
                    <h2 class="h6 mb-2"><i class="fas fa-triangle-exclamation me-1"></i> Revisa los siguientes errores:</h2>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('cuentas.store') }}" enctype="multipart/form-data" class="vstack gap-4">
                @csrf

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h2 class="h5 mb-0">1. Datos generales</h2>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold" for="billing_month">Mes cobrado</label>
                                <input id="billing_month" type="month" class="form-control form-control-lg" name="billing_month" value="{{ old('billing_month') }}" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold d-block">Tipo de cuenta</label>
                                <div class="btn-group w-100" role="group" aria-label="Tipo de cuenta">
                                    <input type="radio" class="btn-check" name="tipo_visual" id="tipo_1" autocomplete="off" {{ old('numero_cuenta') == '1' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="tipo_1" data-value="1">Primera cuenta</label>

                                    <input type="radio" class="btn-check" name="tipo_visual" id="tipo_2" autocomplete="off" {{ old('numero_cuenta') == '2' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="tipo_2" data-value="2">Segunda o siguientes</label>
                                </div>

                                <select class="d-none" name="numero_cuenta" id="numero_cuenta" required>
                                    <option value="" disabled {{ old('numero_cuenta') ? '' : 'selected' }}>Seleccione una opcion</option>
                                    <option value="1" {{ old('numero_cuenta') == '1' ? 'selected' : '' }}>Primera cuenta</option>
                                    <option value="2" {{ old('numero_cuenta') == '2' ? 'selected' : '' }}>Segunda o siguientes</option>
                                </select>
                            </div>
                        </div>

                        <div id="regla-documentos" class="alert alert-info mt-4 mb-0">
                            <i class="fas fa-circle-info me-1"></i>
                            Regla activa: si es la primera cuenta debes cargar 16 documentos por separado. Desde la segunda en adelante debes cargar del 1 al 6 y el memorando de la cuenta anterior.
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h2 class="h5 mb-0">2. Documentos obligatorios (1 al 6 y 17 (memorando) desde la segunda cuenta)</h2>
                    </div>
                    <div class="card-body p-4">
                        <div class="vstack gap-3">
                            <div>
                                <label class="form-label">1. Formato Cumplimiento de Obligaciones para Trámite de Pago (Código: GF-I-01-F-01 o el que lo reemplace), debidamente diligenciado, firmado y radicado. (PDF)</label>
                                <input type="file" class="form-control" name="documentos[1]" accept="application/pdf" required>
                            </div>

                            <div>
                                <label class="form-label">2. Formato informe de gestión obligaciones contractuales SECOP II (Código: GF-F-07 o el que lo reemplace), debidamente diligenciado y firmado. (PDF)</label>
                                <input type="file" class="form-control" name="documentos[2]" accept="application/pdf" required>
                            </div>

                            <div>
                                <label class="form-label">3. Documento Equivalente a Factura o Factura electrónica, según aplique. (PDF)</label>
                                <input type="file" class="form-control" name="documentos[3]" accept="application/pdf" required>
                            </div>

                            <div>
                                <label class="form-label">4. Declaración juramentada. (PDF)</label>
                                <input type="file" class="form-control" name="documentos[4]" accept="application/pdf" required>
                            </div>

                            <div>
                                <label class="form-label">5. Documentos soporte para la disminución de la base de retención (cuando aplique). (PDF)</label>
                                <input type="file" class="form-control" name="documentos[5]" accept="application/pdf" required>
                            </div>

                            <div>
                                <label class="form-label">6. Planilla de pago de seguridad social del mes inmediatamente anterior o del mes actual. (PDF)</label>
                                <input type="file" class="form-control" name="documentos[6]" accept="application/pdf" required>
                            </div>

                            <div id="documento-17-wrapper" class="d-none">
                                <label class="form-label">17. Memorando de la cuenta anterior. (PDF)</label>
                                <input type="file" class="form-control" name="documentos[17]" accept="application/pdf">
                            </div>
                        </div>
                    </div>
                </div>

                <div id="docs-7-16" class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h2 class="h5 mb-0">3. Documentos adicionales (7 al 16)</h2>
                        <span class="badge text-bg-primary">Solo primera cuenta</span>
                    </div>
                    <div class="card-body p-4">
                        <div class="vstack gap-3">
                            <div class="documento-opcional" data-doc="7">
                                <label class="form-label">7. Copia de la cédula de ciudadanía. (PDF)</label>
                                <input type="file" class="form-control" name="documentos[7]" accept="application/pdf">
                            </div>

                            <div class="documento-opcional" data-doc="8">
                                <label class="form-label">8. Certificación bancaria con fecha de expedición inferior a treinta (30) días calendario. (PDF)</label>
                                <input type="file" class="form-control" name="documentos[8]" accept="application/pdf">
                            </div>

                            <div class="documento-opcional" data-doc="9">
                                <label class="form-label">9. Clausulado del contrato y estudios previos publicados en SECOP II. (PDF)</label>
                                <input type="file" class="form-control" name="documentos[9]" accept="application/pdf">
                            </div>

                            <div class="documento-opcional" data-doc="10">
                                <label class="form-label">10. Memorando de delegación de la supervisión o captura de pantalla del SECOP II donde aparece el supervisor del contrato. (PDF)</label>
                                <input type="file" class="form-control" name="documentos[10]" accept="application/pdf">
                            </div>

                            <div class="documento-opcional" data-doc="11">
                                <label class="form-label">11. Acta de inicio o captura de pantalla SECOP II donde aparece la fecha de inicio del contrato. (PDF)</label>
                                <input type="file" class="form-control" name="documentos[11]" accept="application/pdf">
                            </div>

                            <div class="documento-opcional" data-doc="12">
                                <label class="form-label">12. Póliza del contrato. (PDF)</label>
                                <input type="file" class="form-control" name="documentos[12]" accept="application/pdf">
                            </div>

                            <div class="documento-opcional" data-doc="13">
                                <label class="form-label">13. Aprobación de pólizas. (Captura de pantalla SECOP II donde aparece la aprobación de la póliza). (PDF)</label>
                                <input type="file" class="form-control" name="documentos[13]" accept="application/pdf">
                            </div>

                            <div class="documento-opcional" data-doc="14">
                                <label class="form-label">14. Certificado de Disponibilidad Presupuestal - CDP. (PDF)</label>
                                <input type="file" class="form-control" name="documentos[14]" accept="application/pdf">
                            </div>

                            <div class="documento-opcional" data-doc="15">
                                <label class="form-label">15. Registro Presupuestal - RP. (PDF)</label>
                                <input type="file" class="form-control" name="documentos[15]" accept="application/pdf">
                            </div>

                            <div class="documento-opcional" data-doc="16">
                                <label class="form-label">16. Registro Único Tributario - RUT con fecha de expedición inferior a treinta (30) días calendario. (PDF)</label>
                                <input type="file" class="form-control" name="documentos[16]" accept="application/pdf">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 flex-wrap">
                    <button type="submit" class="btn btn-primary btn-lg px-4">
                        <i class="fas fa-paper-plane me-1"></i> Enviar a revisión
                    </button>
                    <a href="{{ route('cuentas.index') }}" class="btn btn-outline-secondary btn-lg px-4">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    (function () {
        const numeroCuenta = document.getElementById('numero_cuenta');
        const contenedor = document.getElementById('docs-7-16');
        const reglaDocumentos = document.getElementById('regla-documentos');
        const labelsTipo = document.querySelectorAll('label[data-value]');

        function formatFileSize(bytes) {
            return (bytes / 1024).toFixed(1) + ' KB';
        }

        function updateZoneInfo(input) {
            const zoneId = input.dataset.zoneId;
            if (!zoneId) {
                return;
            }

            const info = document.querySelector('[data-zone-info="' + zoneId + '"]');
            if (!info) {
                return;
            }

            const file = input.files && input.files[0] ? input.files[0] : null;
            if (!file) {
                info.textContent = 'Sin archivo seleccionado';
                return;
            }

            info.textContent = file.name + ' (' + formatFileSize(file.size) + ')';
        }

        function buildDropZone(input, index) {
            const zoneId = 'doc-zone-' + index;
            input.dataset.zoneId = zoneId;
            input.classList.add('d-none');

            const zone = document.createElement('div');
            zone.className = 'drop-zone';
            zone.setAttribute('role', 'button');
            zone.setAttribute('tabindex', '0');
            zone.innerHTML =
                '<div class="drop-zone-text">Arrastra tu PDF aqui o haz clic</div>' +
                '<div class="drop-zone-hint">Solo archivos PDF</div>' +
                '<div class="file-info" data-zone-info="' + zoneId + '">Sin archivo seleccionado</div>';

            zone.addEventListener('click', () => input.click());
            zone.addEventListener('keydown', (event) => {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    input.click();
                }
            });

            zone.addEventListener('dragover', (event) => {
                event.preventDefault();
                zone.classList.add('dragover');
            });

            zone.addEventListener('dragleave', () => {
                zone.classList.remove('dragover');
            });

            zone.addEventListener('drop', (event) => {
                event.preventDefault();
                zone.classList.remove('dragover');

                const file = event.dataTransfer.files && event.dataTransfer.files[0] ? event.dataTransfer.files[0] : null;
                if (!file) {
                    return;
                }

                if (file.type !== 'application/pdf') {
                    alert('Solo se permiten archivos PDF.');
                    return;
                }

                const dt = new DataTransfer();
                dt.items.add(file);
                input.files = dt.files;
                updateZoneInfo(input);
            });

            input.addEventListener('change', () => {
                const file = input.files && input.files[0] ? input.files[0] : null;
                if (file && file.type !== 'application/pdf') {
                    alert('Solo se permiten archivos PDF.');
                    input.value = '';
                }
                updateZoneInfo(input);
            });

            input.insertAdjacentElement('afterend', zone);
            updateZoneInfo(input);
        }

        function setupDropZones() {
            const fileInputs = document.querySelectorAll('input[type="file"][name^="documentos["]');
            fileInputs.forEach((input, index) => {
                if (input.dataset.dropEnhanced === '1') {
                    return;
                }
                input.dataset.dropEnhanced = '1';
                buildDropZone(input, index + 1);
            });
        }

        labelsTipo.forEach((label) => {
            label.addEventListener('click', () => {
                numeroCuenta.value = label.dataset.value;
                actualizarReglas();
            });
        });

        function actualizarReglas() {
            const esPrimera = numeroCuenta.value === '1';
            contenedor.classList.toggle('d-none', !esPrimera);
            reglaDocumentos.className = esPrimera ? 'alert alert-primary mt-4 mb-0' : 'alert alert-info mt-4 mb-0';
            reglaDocumentos.innerHTML = esPrimera
                ? '<i class="fas fa-circle-info me-1"></i> Regla activa: primera cuenta. Debes cargar documentos 1 al 16.'
                : '<i class="fas fa-circle-info me-1"></i> Regla activa: segunda o siguientes. Debes cargar documentos 1 al 6 y el memorando de la cuenta anterior.';

            contenedor.querySelectorAll('input[type="file"]').forEach((input) => {
                input.required = esPrimera;
                if (!esPrimera) {
                    input.value = '';
                    updateZoneInfo(input);
                }
            });

            const documento17Wrapper = document.getElementById('documento-17-wrapper');
            const documento17Input = documento17Wrapper?.querySelector('input[type="file"]');

            if (documento17Wrapper && documento17Input) {
                documento17Wrapper.classList.toggle('d-none', esPrimera);
                documento17Input.required = !esPrimera;

                if (esPrimera) {
                    documento17Input.value = '';
                    updateZoneInfo(documento17Input);
                }
            }
        }

        setupDropZones();
        actualizarReglas();
    })();
</script>
@endsection
