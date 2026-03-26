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

    @php
        $esApoyo = $user->hasAnyRole(['apoyo a la supervisión', 'apoyo a la supervision', 'apoyo a la supervicion']);
        $esSupervisor = $user->hasRole('supervisor');
        $esRevisionSupervisor = $esApoyo;
        $documentosOrdenados = $cuenta->documentos->sortBy('numero_documento')->values();
        $primerDocumento = $documentosOrdenados->first();
        $etiquetaDevolucion = match($cuenta->returned_stage) {
            'supervisor' => 'Devuelta por Apoyo a la Supervision',
            'admin' => 'Devuelta por Administración',
            'tesoreria' => 'Devuelta por Central de Cuentas',
            'fiduprevisora' => 'Devuelta por Fiduprevisora',
            default => null,
        };
    @endphp

    <div class="row g-4">
        <div class="col-lg-7">
            @if($esRevisionSupervisor || $user->hasAnyRole(['central de cuentas', 'tesoreria', 'fiduprevisora']))
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Visor documental</strong>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">Consulta cada PDF en pantalla. Los documentos firmados por supervisor aparecen al final cuando ya fueron cargados.</p>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                                <label class="form-label mb-0"><strong>Documentos disponibles</strong></label>
                                @if($primerDocumento)
                                    <a
                                        id="pdf-open-large"
                                        href="{{ route('cuentas.documento.preview', [$cuenta, $primerDocumento]) }}"
                                        target="_blank"
                                        class="btn btn-outline-primary btn-sm"
                                    >
                                        Ver documento grande
                                    </a>
                                @endif
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($documentosOrdenados as $documento)
                                    <button
                                        type="button"
                                        class="btn btn-outline-secondary btn-sm js-pdf-viewer-btn"
                                        data-pdf-url="{{ route('cuentas.documento.preview', [$cuenta, $documento]) }}"
                                    >
                                        Doc {{ $documento->numero_documento }}
                                    </button>
                                @endforeach

                                @if($cuenta->documento_1_firmado_path)
                                    <button
                                        type="button"
                                        class="btn btn-outline-success btn-sm js-pdf-viewer-btn"
                                        data-pdf-url="{{ route('cuentas.documento.firmado.preview', [$cuenta, 1]) }}"
                                    >
                                        Doc 1 firmado
                                    </button>
                                @endif

                                @if($cuenta->documento_2_firmado_path)
                                    <button
                                        type="button"
                                        class="btn btn-outline-success btn-sm js-pdf-viewer-btn"
                                        data-pdf-url="{{ route('cuentas.documento.firmado.preview', [$cuenta, 2]) }}"
                                    >
                                        Doc 2 firmado
                                    </button>
                                @endif
                            </div>
                        </div>

                        @if($primerDocumento)
                            <div class="ratio ratio-16x9 border rounded">
                                <iframe id="pdf-viewer-frame" src="{{ route('cuentas.documento.preview', [$cuenta, $primerDocumento]) }}" title="Visor PDF" style="border: 0;"></iframe>
                            </div>
                        @else
                            <div class="alert alert-warning mb-0">Esta cuenta aún no tiene documentos disponibles para visualizar.</div>
                        @endif
                    </div>
                </div>
            @endif

            @if($esSupervisor)
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <strong>Firma del supervisor para documentos 1 y 2</strong>
                    </div>
                    <div class="card-body">
                        @php
                            $doc1 = $cuenta->documentos->firstWhere('numero_documento', 1);
                            $doc2 = $cuenta->documentos->firstWhere('numero_documento', 2);
                        @endphp

                        @if($cuenta->documentosFirmadosCompletos())
                            <p class="mb-3 text-muted">Los documentos firmados ya fueron cargados. En esta etapa solo se muestran los archivos firmados.</p>

                            <div class="mb-3">
                                <label class="form-label"><strong>Visor de documentos firmados</strong></label>
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    <button
                                        type="button"
                                        class="btn btn-outline-success btn-sm js-pdf-viewer-btn"
                                        data-pdf-url="{{ route('cuentas.documento.firmado.preview', [$cuenta, 1]) }}"
                                    >
                                        Ver doc 1 firmado
                                    </button>
                                    <button
                                        type="button"
                                        class="btn btn-outline-success btn-sm js-pdf-viewer-btn"
                                        data-pdf-url="{{ route('cuentas.documento.firmado.preview', [$cuenta, 2]) }}"
                                    >
                                        Ver doc 2 firmado
                                    </button>
                                </div>

                                <div class="ratio ratio-16x9 border rounded">
                                    <iframe
                                        id="pdf-viewer-frame-supervisor"
                                        src="{{ route('cuentas.documento.firmado.preview', [$cuenta, 1]) }}"
                                        title="Visor PDF supervisor"
                                        style="border: 0;"
                                    ></iframe>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('cuentas.documento.firmado.preview', [$cuenta, 1]) }}" target="_blank" class="btn btn-outline-success btn-sm">Ver doc 1 firmado</a>
                                <a href="{{ route('cuentas.documento.firmado.download', [$cuenta, 1]) }}" class="btn btn-outline-secondary btn-sm">Descargar doc 1 firmado</a>
                                <a href="{{ route('cuentas.documento.firmado.preview', [$cuenta, 2]) }}" target="_blank" class="btn btn-outline-success btn-sm">Ver doc 2 firmado</a>
                                <a href="{{ route('cuentas.documento.firmado.download', [$cuenta, 2]) }}" class="btn btn-outline-secondary btn-sm">Descargar doc 2 firmado</a>
                            </div>
                        @else
                            <p class="mb-3 text-muted">Descarga los documentos 1 y 2, fírmalos y vuelve a cargarlos para que Central de Cuentas continúe el proceso.</p>

                            <div class="mb-3">
                                <label class="form-label"><strong>Visor de documentos para firma</strong></label>
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    @if($doc1)
                                        <button
                                            type="button"
                                            class="btn btn-outline-primary btn-sm js-pdf-viewer-btn"
                                            data-pdf-url="{{ route('cuentas.documento.preview', [$cuenta, $doc1]) }}"
                                        >
                                            Ver doc 1
                                        </button>
                                    @endif
                                    @if($doc2)
                                        <button
                                            type="button"
                                            class="btn btn-outline-primary btn-sm js-pdf-viewer-btn"
                                            data-pdf-url="{{ route('cuentas.documento.preview', [$cuenta, $doc2]) }}"
                                        >
                                            Ver doc 2
                                        </button>
                                    @endif
                                </div>

                                @if($doc1)
                                    <div class="ratio ratio-16x9 border rounded">
                                        <iframe
                                            id="pdf-viewer-frame-supervisor"
                                            src="{{ route('cuentas.documento.preview', [$cuenta, $doc1]) }}"
                                            title="Visor PDF supervisor"
                                            style="border: 0;"
                                        ></iframe>
                                    </div>
                                @endif
                            </div>

                            <div class="d-flex flex-wrap gap-2 mb-3">
                                @if($doc1)
                                    <a href="{{ route('cuentas.documento.preview', [$cuenta, $doc1]) }}" target="_blank" class="btn btn-outline-primary btn-sm">Ver doc 1</a>
                                    <a href="{{ route('cuentas.documento.download', [$cuenta, $doc1]) }}" class="btn btn-outline-secondary btn-sm">Descargar doc 1</a>
                                @endif
                                @if($doc2)
                                    <a href="{{ route('cuentas.documento.preview', [$cuenta, $doc2]) }}" target="_blank" class="btn btn-outline-primary btn-sm">Ver doc 2</a>
                                    <a href="{{ route('cuentas.documento.download', [$cuenta, $doc2]) }}" class="btn btn-outline-secondary btn-sm">Descargar doc 2</a>
                                @endif
                            </div>

                            <form method="POST" action="{{ route('cuentas.documentos.firmados.upload', $cuenta) }}" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Documento 1 firmado (PDF)</label>
                                    <input type="file" class="form-control" name="documento_1_firmado" accept="application/pdf" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Documento 2 firmado (PDF)</label>
                                    <input type="file" class="form-control" name="documento_2_firmado" accept="application/pdf" required>
                                </div>
                                <button class="btn btn-primary" type="submit">Cargar firmados y enviar a Central de Cuentas</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endif

            @if($user->hasRole('contratista') && $cuenta->returned_at)
                <div class="card shadow-sm">
                    <div class="card-header bg-warning-subtle">
                        <strong>Reenvío por devolución</strong>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">
                            @if($cuenta->returned_stage === 'supervisor')
                                Solo debes reemplazar los documentos rechazados por apoyo a la supervisión.
                            @else
                                Debes cargar nuevamente todos los documentos requeridos para que el flujo se reinicie correctamente.
                            @endif
                        </p>
                        <form method="POST" action="{{ route('cuentas.resubmit', $cuenta) }}" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Mes cobrado</label>
                                <input type="month" class="form-control" name="billing_month" value="{{ $cuenta->billing_month }}" required>
                            </div>

                            @foreach($documentosParaReenvio as $numeroDocumento)
                                @php
                                    $documento = $cuenta->documentos->firstWhere('numero_documento', $numeroDocumento);
                                @endphp
                                <div class="mb-3">
                                    <label class="form-label">{{ $numeroDocumento }}. {{ $documento?->nombre_documento ?? ('Documento ' . $numeroDocumento) }}</label>
                                    @if($documento?->comentario_supervisor)
                                        <div class="small text-danger mb-2">
                                            Motivo de rechazo:
                                            <div>{!! $documento->comentario_supervisor !!}</div>
                                        </div>
                                    @endif
                                    <input type="file" class="form-control" name="documentos[{{ $numeroDocumento }}]" accept="application/pdf" required>
                                </div>
                            @endforeach

                            <button class="btn btn-warning" type="submit">Reenviar documentos</button>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <strong>Estado del flujo</strong>
                </div>
                <div class="card-body">
                    <p><strong>Cuenta:</strong> {{ ucfirst($cuenta->cuenta_status) }}</p>
                    <p><strong>Planilla:</strong> {{ ucfirst($cuenta->planilla_status) }}</p>
                    <p><strong>Central de Cuentas:</strong> {{ ucfirst($cuenta->tesoreria_status) }}</p>
                    <p><strong>Fiduprevisora:</strong>
                        @if($cuenta->fiduprevisora_status === 'en_revision')
                            En revisión documental
                        @elseif($cuenta->fiduprevisora_status === 'en_tramite')
                            En trámite de pago
                        @else
                            {{ ucfirst($cuenta->fiduprevisora_status) }}
                        @endif
                    </p>
                    @if($etiquetaDevolucion)
                        <div class="alert alert-warning py-2 mb-0">{{ $etiquetaDevolucion }}</div>
                    @endif
                    @if($cuenta->documentosFirmadosCompletos() && $cuenta->tesoreria_status === 'pendiente')
                        <div class="alert alert-info py-2 mt-3 mb-0">
                            Los documentos firmados ya fueron cargados y la cuenta está lista para revisión en Central de Cuentas.
                        </div>
                    @endif
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <strong>Observaciones</strong>
                </div>
                <div class="card-body">
                    <p><strong>Apoyo a supervision cuenta:</strong><br>{!! $cuenta->cuenta_supervisor_comment ?: 'Sin comentarios' !!}</p>
                    <p><strong>Apoyo a supervision planilla:</strong><br>{!! $cuenta->planilla_supervisor_comment ?: 'Sin comentarios' !!}</p>
                    <p><strong>Central de Cuentas:</strong><br>{!! $cuenta->tesoreria_comment ?: 'Sin comentarios' !!}</p>
                    <p class="mb-0"><strong>Fiduprevisora:</strong><br>{!! $cuenta->fiduprevisora_comment ?: 'Sin comentarios' !!}</p>
                </div>
            </div>

        </div>
    </div>

    @if($esRevisionSupervisor)
        <div class="card shadow-sm mt-4">
            <div class="card-header bg-info-subtle">
                <strong>Revision por documento en apoyo a supervision</strong>
            </div>
            <div class="card-body">
                <p class="text-muted mb-4">Revisa cada soporte con espacio suficiente para observaciones detalladas. Puedes dar formato al comentario antes de guardar.</p>

                <div class="d-flex flex-column gap-3">
                    @foreach($documentosOrdenados as $documento)
                        <div class="border rounded p-3 review-document-card">
                            <div class="row g-3 align-items-start">
                                <div class="col-lg-3">
                                    <div class="small text-uppercase text-muted fw-semibold mb-2">Documento {{ $documento->numero_documento }}</div>
                                    <div class="fw-semibold mb-2">{{ $documento->nombre_documento }}</div>
                                    <a href="{{ route('cuentas.documento.preview', [$cuenta, $documento]) }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                                        Ver PDF
                                    </a>
                                </div>

                                <div class="col-lg-2">
                                    <div class="small text-uppercase text-muted fw-semibold mb-2">Estado actual</div>
                                    @if($documento->estado === 'validado')
                                        <span class="badge bg-success">Aprobado</span>
                                    @elseif($documento->estado === 'rechazado')
                                        <span class="badge bg-danger">Rechazado</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Por revisar</span>
                                    @endif

                                    <div class="small text-uppercase text-muted fw-semibold mt-3 mb-2">Observación guardada</div>
                                    <div class="review-comment-preview small">
                                        {!! $documento->comentario_supervisor ?: 'Sin observaciones.' !!}
                                    </div>
                                </div>

                                <div class="col-lg-7">
                                    <form method="POST" action="{{ route('cuentas.documento.review', [$cuenta, $documento]) }}" class="d-flex flex-column gap-3">
                                        @csrf
                                        <div>
                                            <label class="form-label fw-semibold">Decisión</label>
                                            <select name="decision" class="form-select" required>
                                                <option value="aprobada">Aprobar</option>
                                                <option value="rechazada">Rechazar</option>
                                            </select>
                                        </div>

                                        <div class="js-wysiwyg-wrapper">
                                            <label class="form-label fw-semibold">Observaciones</label>
                                            <div class="js-wysiwyg-toolbar d-flex flex-wrap gap-2 mb-2" role="group" aria-label="Herramientas de edicion">
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button type="button" class="btn btn-outline-secondary" data-command="undo" title="Deshacer">↶</button>
                                                    <button type="button" class="btn btn-outline-secondary" data-command="redo" title="Rehacer">↷</button>
                                                </div>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button type="button" class="btn btn-outline-secondary" data-command="bold" title="Negrita"><strong>B</strong></button>
                                                    <button type="button" class="btn btn-outline-secondary" data-command="italic" title="Cursiva"><em>I</em></button>
                                                    <button type="button" class="btn btn-outline-secondary" data-command="underline" title="Subrayado"><u>U</u></button>
                                                    <button type="button" class="btn btn-outline-secondary" data-command="strikeThrough" title="Tachado"><s>S</s></button>
                                                </div>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button type="button" class="btn btn-outline-secondary" data-command="formatBlock" data-value="h2">H2</button>
                                                    <button type="button" class="btn btn-outline-secondary" data-command="formatBlock" data-value="h3">H3</button>
                                                    <button type="button" class="btn btn-outline-secondary" data-command="formatBlock" data-value="p">Párrafo</button>
                                                    <button type="button" class="btn btn-outline-secondary" data-command="blockquote">Cita</button>
                                                </div>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button type="button" class="btn btn-outline-secondary" data-command="insertUnorderedList">Lista</button>
                                                    <button type="button" class="btn btn-outline-secondary" data-command="insertOrderedList">Numerada</button>
                                                </div>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button type="button" class="btn btn-outline-secondary" data-command="justifyLeft">Izq</button>
                                                    <button type="button" class="btn btn-outline-secondary" data-command="justifyCenter">Centro</button>
                                                    <button type="button" class="btn btn-outline-secondary" data-command="justifyRight">Der</button>
                                                </div>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button type="button" class="btn btn-outline-secondary js-wysiwyg-link">Enlace</button>
                                                    <button type="button" class="btn btn-outline-secondary js-wysiwyg-image">Imagen</button>
                                                    <button type="button" class="btn btn-outline-secondary" data-command="removeFormat">Limpiar</button>
                                                </div>
                                                <input type="file" class="d-none js-wysiwyg-image-input" accept="image/*">
                                            </div>
                                            <div
                                                class="form-control js-wysiwyg-editor review-wysiwyg"
                                                contenteditable="true"
                                                data-target="supervisor-comment-{{ $documento->id }}"
                                                data-placeholder="Escribe la observación. Puedes pegar texto enriquecido o una imagen."
                                            >{!! $documento->comentario_supervisor !!}</div>
                                            <textarea name="comentario" id="supervisor-comment-{{ $documento->id }}" class="d-none">{!! $documento->comentario_supervisor !!}</textarea>
                                            <div class="form-text">Puedes pegar contenido desde Word, enlaces e imágenes. Las imágenes se incrustan dentro de la observación.</div>
                                        </div>

                                        <div>
                                            <button type="submit" class="btn btn-primary">Guardar revisión</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    @if($user->hasAnyRole(['central de cuentas', 'tesoreria']))
        <div class="card shadow-sm mt-4">
            <div class="card-header bg-warning-subtle">
                <strong>Central de Cuentas</strong>
            </div>
            <div class="card-body">
                @if($cuenta->canGoToTesoreria())
                    <p class="text-muted mb-4">Revisa los documentos firmados, registra tu decisión y agrega un comentario detallado con formato enriquecido si hace falta.</p>

                    <div class="row g-4 align-items-start">
                        <div class="col-lg-4">
                            <div class="small text-uppercase text-muted fw-semibold mb-2">Documentos firmados disponibles</div>
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <a href="{{ route('cuentas.documento.firmado.preview', [$cuenta, 1]) }}" target="_blank" class="btn btn-outline-secondary btn-sm">Ver doc 1 firmado</a>
                                <a href="{{ route('cuentas.documento.firmado.download', [$cuenta, 1]) }}" class="btn btn-outline-secondary btn-sm">Descargar doc 1 firmado</a>
                                <a href="{{ route('cuentas.documento.firmado.preview', [$cuenta, 2]) }}" target="_blank" class="btn btn-outline-secondary btn-sm">Ver doc 2 firmado</a>
                                <a href="{{ route('cuentas.documento.firmado.download', [$cuenta, 2]) }}" class="btn btn-outline-secondary btn-sm">Descargar doc 2 firmado</a>
                            </div>

                            <div class="small text-uppercase text-muted fw-semibold mb-2">Comentario actual</div>
                            <div class="review-comment-preview">
                                {!! $cuenta->tesoreria_comment ?: 'Sin comentarios.' !!}
                            </div>
                        </div>

                        <div class="col-lg-8">
                            <form method="POST" action="{{ route('cuentas.tesoreria.review', $cuenta) }}" class="d-flex flex-column gap-3">
                                @csrf
                                <div>
                                    <label class="form-label fw-semibold">Decisión</label>
                                    <select name="decision" class="form-select" required>
                                        <option value="aprobada">Aprobar y enviar a Fiduprevisora</option>
                                        <option value="rechazada">Devolver al contratista</option>
                                    </select>
                                </div>

                                <div class="js-wysiwyg-wrapper">
                                    <label class="form-label fw-semibold">Comentario</label>
                                    <div class="js-wysiwyg-toolbar d-flex flex-wrap gap-2 mb-2" role="group" aria-label="Herramientas de edicion">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-secondary" data-command="undo" title="Deshacer">↶</button>
                                            <button type="button" class="btn btn-outline-secondary" data-command="redo" title="Rehacer">↷</button>
                                        </div>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-secondary" data-command="bold" title="Negrita"><strong>B</strong></button>
                                            <button type="button" class="btn btn-outline-secondary" data-command="italic" title="Cursiva"><em>I</em></button>
                                            <button type="button" class="btn btn-outline-secondary" data-command="underline" title="Subrayado"><u>U</u></button>
                                            <button type="button" class="btn btn-outline-secondary" data-command="strikeThrough" title="Tachado"><s>S</s></button>
                                        </div>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-secondary" data-command="formatBlock" data-value="h2">H2</button>
                                            <button type="button" class="btn btn-outline-secondary" data-command="formatBlock" data-value="h3">H3</button>
                                            <button type="button" class="btn btn-outline-secondary" data-command="formatBlock" data-value="p">Párrafo</button>
                                            <button type="button" class="btn btn-outline-secondary" data-command="blockquote">Cita</button>
                                        </div>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-secondary" data-command="insertUnorderedList">Lista</button>
                                            <button type="button" class="btn btn-outline-secondary" data-command="insertOrderedList">Numerada</button>
                                        </div>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-secondary" data-command="justifyLeft">Izq</button>
                                            <button type="button" class="btn btn-outline-secondary" data-command="justifyCenter">Centro</button>
                                            <button type="button" class="btn btn-outline-secondary" data-command="justifyRight">Der</button>
                                        </div>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-secondary js-wysiwyg-link">Enlace</button>
                                            <button type="button" class="btn btn-outline-secondary js-wysiwyg-image">Imagen</button>
                                            <button type="button" class="btn btn-outline-secondary" data-command="removeFormat">Limpiar</button>
                                        </div>
                                        <input type="file" class="d-none js-wysiwyg-image-input" accept="image/*">
                                    </div>
                                    <div
                                        class="form-control js-wysiwyg-editor review-wysiwyg"
                                        contenteditable="true"
                                        data-target="tesoreria-comment"
                                        data-placeholder="Escribe el comentario de Central de Cuentas. Puedes pegar texto enriquecido o una imagen."
                                    >{!! $cuenta->tesoreria_comment !!}</div>
                                    <textarea name="comment" id="tesoreria-comment" class="d-none">{!! $cuenta->tesoreria_comment !!}</textarea>
                                    <div class="form-text">Puedes usar formato enriquecido, pegar imágenes o insertarlas manualmente.</div>
                                </div>

                                <div>
                                    <button type="submit" class="btn btn-warning">Guardar decisión</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning mb-0">Esta cuenta aún no está lista para Central de Cuentas.</div>
                @endif
            </div>
        </div>
    @endif

    @if($user->hasRole('fiduprevisora'))
        <div class="card shadow-sm mt-4">
            <div class="card-header bg-primary-subtle">
                <strong>Revisión documental Fiduprevisora</strong>
            </div>
            <div class="card-body">
                @if($cuenta->canGoToFiduprevisora() || $cuenta->fiduprevisora_status === 'en_revision' || $cuenta->fiduprevisora_status === 'en_tramite')
                    <p class="text-muted mb-4">Revisa cada documento con espacio suficiente para observaciones detalladas. Puedes usar formato enriquecido e incluir imágenes.</p>

                    <div class="d-flex flex-column gap-3">
                        @foreach($documentosOrdenados as $documento)
                            <div class="border rounded p-3 review-document-card">
                                <div class="row g-3 align-items-start">
                                    <div class="col-lg-3">
                                        <div class="small text-uppercase text-muted fw-semibold mb-2">Documento {{ $documento->numero_documento }}</div>
                                        <div class="fw-semibold mb-2">{{ $documento->nombre_documento }}</div>
                                        <a href="{{ route('cuentas.documento.preview', [$cuenta, $documento]) }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                                            Ver PDF
                                        </a>
                                    </div>

                                    <div class="col-lg-2">
                                        <div class="small text-uppercase text-muted fw-semibold mb-2">Estado Fidu</div>
                                        @if($documento->fiduprevisora_estado === 'aprobado')
                                            <span class="badge bg-success">Aprobado</span>
                                        @elseif($documento->fiduprevisora_estado === 'rechazado')
                                            <span class="badge bg-danger">Rechazado</span>
                                        @else
                                            <span class="badge bg-secondary">Pendiente</span>
                                        @endif

                                        <div class="small text-uppercase text-muted fw-semibold mt-3 mb-2">Observación guardada</div>
                                        <div class="review-comment-preview small">
                                            {!! $documento->fiduprevisora_comentario ?: 'Sin observaciones.' !!}
                                        </div>
                                    </div>

                                    <div class="col-lg-7">
                                        <form method="POST" action="{{ route('cuentas.fiduprevisora.documento.review', [$cuenta, $documento]) }}" class="d-flex flex-column gap-3">
                                            @csrf
                                            <div>
                                                <label class="form-label fw-semibold">Decisión</label>
                                                <select name="decision" class="form-select" required>
                                                    <option value="aprobada">Aprobar</option>
                                                    <option value="rechazada">Rechazar</option>
                                                </select>
                                            </div>

                                            <div class="js-wysiwyg-wrapper">
                                                <label class="form-label fw-semibold">Observaciones</label>
                                                <div class="js-wysiwyg-toolbar d-flex flex-wrap gap-2 mb-2" role="group" aria-label="Herramientas de edicion">
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <button type="button" class="btn btn-outline-secondary" data-command="undo" title="Deshacer">↶</button>
                                                        <button type="button" class="btn btn-outline-secondary" data-command="redo" title="Rehacer">↷</button>
                                                    </div>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <button type="button" class="btn btn-outline-secondary" data-command="bold" title="Negrita"><strong>B</strong></button>
                                                        <button type="button" class="btn btn-outline-secondary" data-command="italic" title="Cursiva"><em>I</em></button>
                                                        <button type="button" class="btn btn-outline-secondary" data-command="underline" title="Subrayado"><u>U</u></button>
                                                        <button type="button" class="btn btn-outline-secondary" data-command="strikeThrough" title="Tachado"><s>S</s></button>
                                                    </div>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <button type="button" class="btn btn-outline-secondary" data-command="formatBlock" data-value="h2">H2</button>
                                                        <button type="button" class="btn btn-outline-secondary" data-command="formatBlock" data-value="h3">H3</button>
                                                        <button type="button" class="btn btn-outline-secondary" data-command="formatBlock" data-value="p">Párrafo</button>
                                                        <button type="button" class="btn btn-outline-secondary" data-command="blockquote">Cita</button>
                                                    </div>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <button type="button" class="btn btn-outline-secondary" data-command="insertUnorderedList">Lista</button>
                                                        <button type="button" class="btn btn-outline-secondary" data-command="insertOrderedList">Numerada</button>
                                                    </div>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <button type="button" class="btn btn-outline-secondary" data-command="justifyLeft">Izq</button>
                                                        <button type="button" class="btn btn-outline-secondary" data-command="justifyCenter">Centro</button>
                                                        <button type="button" class="btn btn-outline-secondary" data-command="justifyRight">Der</button>
                                                    </div>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <button type="button" class="btn btn-outline-secondary js-wysiwyg-link">Enlace</button>
                                                        <button type="button" class="btn btn-outline-secondary js-wysiwyg-image">Imagen</button>
                                                        <button type="button" class="btn btn-outline-secondary" data-command="removeFormat">Limpiar</button>
                                                    </div>
                                                    <input type="file" class="d-none js-wysiwyg-image-input" accept="image/*">
                                                </div>
                                                <div
                                                    class="form-control js-wysiwyg-editor review-wysiwyg"
                                                    contenteditable="true"
                                                    data-target="fidu-comment-{{ $documento->id }}"
                                                    data-placeholder="Escribe la observación de Fiduprevisora. Puedes pegar texto enriquecido o una imagen."
                                                >{!! $documento->fiduprevisora_comentario !!}</div>
                                                <textarea name="comentario" id="fidu-comment-{{ $documento->id }}" class="d-none">{!! $documento->fiduprevisora_comentario !!}</textarea>
                                                <div class="form-text">Puedes pegar contenido desde Word, enlaces e imágenes.</div>
                                            </div>

                                            <div>
                                                <button type="submit" class="btn btn-primary">Guardar revisión</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($cuenta->fiduprevisora_status === 'en_tramite')
                        <div class="card shadow-sm mt-4">
                            <div class="card-header">
                                <strong>Estado final en Fiduprevisora</strong>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('cuentas.fiduprevisora.review', $cuenta) }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">Enviar a pago</label>
                                        <select name="status" class="form-select" required>
                                            <option value="en_tramite">Mantener en trámite</option>
                                            <option value="pagado">Marcar como pagado</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Comentario</label>
                                        <div class="js-wysiwyg-wrapper">
                                            <div class="form-control js-wysiwyg-editor review-wysiwyg" contenteditable="true" data-target="fidu-final-comment" data-placeholder="Escribe el comentario final de Fiduprevisora."></div>
                                            <textarea name="comment" id="fidu-final-comment" class="d-none"></textarea>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Actualizar estado</button>
                                </form>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="alert alert-warning mb-0">Esta cuenta aún no fue remitida por Central de Cuentas.</div>
                @endif
            </div>
        </div>
    @endif
</div>

<script>
    (function () {
        const frame = document.getElementById('pdf-viewer-frame');
        const supervisorFrame = document.getElementById('pdf-viewer-frame-supervisor');
        const openLargeLink = document.getElementById('pdf-open-large');
        const buttons = document.querySelectorAll('.js-pdf-viewer-btn');

        buttons.forEach((button) => {
            button.addEventListener('click', () => {
                const url = button.getAttribute('data-pdf-url');
                if (frame && url) {
                    frame.src = url;
                }
                if (openLargeLink && url) {
                    openLargeLink.href = url;
                }
                if (supervisorFrame && url) {
                    supervisorFrame.src = url;
                }
            });
        });

        document.querySelectorAll('.js-wysiwyg-editor').forEach((editor) => {
            const targetId = editor.getAttribute('data-target');
            const target = targetId ? document.getElementById(targetId) : null;

            if (!target) {
                return;
            }

            const sync = () => {
                target.value = editor.innerHTML.trim();
            };

            editor.addEventListener('input', sync);
            editor.closest('form')?.addEventListener('submit', sync);

            if (!editor.innerHTML.trim()) {
                editor.innerHTML = '';
            }

            editor.addEventListener('paste', async (event) => {
                const clipboard = event.clipboardData;

                if (!clipboard) {
                    return;
                }

                const imageItem = Array.from(clipboard.items).find((item) => item.type.startsWith('image/'));

                if (!imageItem) {
                    return;
                }

                event.preventDefault();

                const file = imageItem.getAsFile();
                if (!file) {
                    return;
                }

                const dataUrl = await readFileAsDataUrl(file);
                insertHtmlAtCursor(editor, `<p><img src="${dataUrl}" alt="Imagen pegada" class="wysiwyg-inline-image"></p>`);
                sync();
            });
        });

        document.querySelectorAll('.js-wysiwyg-toolbar button').forEach((button) => {
            button.addEventListener('click', () => {
                const wrapper = button.closest('.js-wysiwyg-wrapper');
                const editor = wrapper?.querySelector('.js-wysiwyg-editor');
                const command = button.getAttribute('data-command');
                const value = button.getAttribute('data-value');

                if (!editor || !command) {
                    return;
                }

                editor.focus();
                document.execCommand(command, false, value ?? null);
                editor.dispatchEvent(new Event('input'));
            });
        });

        document.querySelectorAll('.js-wysiwyg-link').forEach((button) => {
            button.addEventListener('click', () => {
                const wrapper = button.closest('.js-wysiwyg-wrapper');
                const editor = wrapper?.querySelector('.js-wysiwyg-editor');

                if (!editor) {
                    return;
                }

                const url = window.prompt('Ingresa la URL del enlace');
                if (!url) {
                    return;
                }

                editor.focus();
                document.execCommand('createLink', false, url);
                editor.dispatchEvent(new Event('input'));
            });
        });

        document.querySelectorAll('.js-wysiwyg-image').forEach((button) => {
            button.addEventListener('click', () => {
                const wrapper = button.closest('.js-wysiwyg-wrapper');
                const input = wrapper?.querySelector('.js-wysiwyg-image-input');

                input?.click();
            });
        });

        document.querySelectorAll('.js-wysiwyg-image-input').forEach((input) => {
            input.addEventListener('change', async () => {
                const wrapper = input.closest('.js-wysiwyg-wrapper');
                const editor = wrapper?.querySelector('.js-wysiwyg-editor');
                const file = input.files?.[0];

                if (!editor || !file) {
                    return;
                }

                const dataUrl = await readFileAsDataUrl(file);
                editor.focus();
                insertHtmlAtCursor(editor, `<p><img src="${dataUrl}" alt="Imagen insertada" class="wysiwyg-inline-image"></p>`);
                editor.dispatchEvent(new Event('input'));
                input.value = '';
            });
        });

        function insertHtmlAtCursor(editor, html) {
            const selection = window.getSelection();

            if (!selection || selection.rangeCount === 0) {
                editor.insertAdjacentHTML('beforeend', html);
                return;
            }

            const range = selection.getRangeAt(0);
            range.deleteContents();

            const temp = document.createElement('div');
            temp.innerHTML = html;

            const fragment = document.createDocumentFragment();
            let node;
            let lastNode = null;

            while ((node = temp.firstChild)) {
                lastNode = fragment.appendChild(node);
            }

            range.insertNode(fragment);

            if (lastNode) {
                range.setStartAfter(lastNode);
                range.collapse(true);
                selection.removeAllRanges();
                selection.addRange(range);
            }
        }

        function readFileAsDataUrl(file) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onload = () => resolve(reader.result);
                reader.onerror = reject;
                reader.readAsDataURL(file);
            });
        }
    })();
</script>

@push('styles')
<style>
    .review-document-card {
        background: #fbfdff;
    }

    .review-comment-preview {
        min-height: 72px;
        padding: 0.75rem;
        border: 1px solid #dee2e6;
        border-radius: 0.5rem;
        background: #fff;
    }

    .review-wysiwyg {
        min-height: 320px;
        overflow-y: auto;
        background: #fff;
        resize: vertical;
    }

    .js-wysiwyg-toolbar {
        flex-wrap: wrap;
    }

    .review-wysiwyg:empty::before {
        content: attr(data-placeholder);
        color: #6c757d;
    }

    .wysiwyg-inline-image {
        display: block;
        max-width: 100%;
        height: auto;
        margin: 0.5rem 0;
        border-radius: 0.5rem;
    }
</style>
@endpush
@endsection
