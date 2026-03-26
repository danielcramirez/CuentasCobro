<?php

namespace App\Http\Controllers;

use App\Models\CuentaCobro;
use App\Models\CuentaCobroDocumento;
use App\Models\User;
use App\Notifications\CuentaCobroFlowNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Throwable;

class CuentaCobroController extends Controller
{
    private const RICH_TEXT_MAX_LENGTH = 200000;

    public function index()
    {
        $user = Auth::user();

        $query = CuentaCobro::with(['contractor', 'supervisor', 'mayor', 'tesoreria', 'fiduprevisora'])->latest();

        if ($user->hasRole('contratista')) {
            $query->where('contractor_id', $user->id);
        } elseif ($user->hasRole('admin')) {
            $query->where('cuenta_status', 'aprobada')
                ->where('planilla_status', 'aprobada');
        } elseif ($user->hasAnyRole(['central de cuentas', 'tesoreria'])) {
            $query->where('cuenta_status', 'aprobada')
                ->where('planilla_status', 'aprobada')
                ->whereNotNull('documento_1_firmado_path')
                ->whereNotNull('documento_2_firmado_path');
        } elseif ($user->hasRole('fiduprevisora')) {
            $query->whereIn('fiduprevisora_status', ['en_revision', 'en_tramite', 'pagado', 'rechazada']);
        } elseif (!$user->hasAnyRole([
            'apoyo a la supervisión',
            'apoyo a la supervision',
            'apoyo a la supervicion',
            'supervisor',
            'tesoreria',
            'central de cuentas',
            'fiduprevisora',
        ])) {
            abort(403, 'No tienes permisos para acceder a este módulo.');
        }

        return view('cuentas.index', [
            'cuentas' => $query->get(),
            'user' => $user,
        ]);
    }

    public function create()
    {
        $this->authorizeRole('contratista');

        return view('cuentas.create');
    }

    public function store(Request $request)
    {
        $this->authorizeRole('contratista');

        $validated = $request->validate([
            'billing_month' => ['required', 'date_format:Y-m'],
            'numero_cuenta' => ['required', 'integer', 'min:1'],
            'documentos' => ['required', 'array'],
        ]);

        $user = Auth::user();
        $numeroCuenta = (int) $validated['numero_cuenta'];
        $requeridos = CuentaCobroDocumento::getDocumentosRequeridos($numeroCuenta);

        foreach ($requeridos as $numeroDocumento) {
            $request->validate([
                "documentos.{$numeroDocumento}" => ['required', 'file', 'mimes:pdf', 'max:10240'],
            ], [
                "documentos.{$numeroDocumento}.required" => "El documento {$numeroDocumento} es obligatorio.",
            ]);
        }

        $catalogo = CuentaCobroDocumento::getCatalogo();
        $rutasDocumentos = [];

        try {
            foreach ($requeridos as $numeroDocumento) {
                $rutasDocumentos[$numeroDocumento] = $request->file("documentos.{$numeroDocumento}")
                    ->store("cuentas/{$user->id}/documentos", 'local');
            }

            $cuentaCobro = DB::transaction(function () use ($user, $validated, $numeroCuenta, $rutasDocumentos, $catalogo, $requeridos) {
                $cuentaCobro = CuentaCobro::create($this->filterCuentaCobroAttributes([
                    'contractor_id' => $user->id,
                    'billing_month' => $validated['billing_month'],
                    'numero_cuenta' => $numeroCuenta,
                    'cuenta_pdf_path' => $rutasDocumentos[1],
                    'planilla_pdf_path' => $rutasDocumentos[6],
                    'cuenta_status' => 'pendiente',
                    'planilla_status' => 'pendiente',
                    'mayor_status' => 'pendiente',
                    'tesoreria_status' => 'pendiente',
                    'fiduprevisora_status' => 'pendiente',
                    'returned_at' => null,
                    'returned_stage' => null,
                ]));

                foreach ($requeridos as $numeroDocumento) {
                    CuentaCobroDocumento::create($this->filterCuentaCobroDocumentoAttributes([
                        'cuenta_cobro_id' => $cuentaCobro->id,
                        'numero_documento' => $numeroDocumento,
                        'nombre_documento' => $catalogo[$numeroDocumento] ?? "Documento {$numeroDocumento}",
                        'archivo_path' => $rutasDocumentos[$numeroDocumento],
                        'estado' => 'cargado',
                        'fiduprevisora_estado' => 'pendiente',
                        'cargado_at' => now(),
                    ]));
                }

                return $cuentaCobro;
            });
        } catch (Throwable $exception) {
            foreach ($rutasDocumentos as $rutaDocumento) {
                Storage::disk('local')->delete($rutaDocumento);
            }

            throw $exception;
        }

        $this->notifyRole(
            ['apoyo a la supervisión', 'apoyo a la supervision', 'apoyo a la supervicion'],
            $cuentaCobro->load('contractor'),
            'Nueva cuenta de cobro para revisión',
            'Se registró una nueva cuenta de cobro y ya está disponible para revisión documental.'
        );

        return redirect()->route('cuentas.index')->with('success', 'Cuenta de cobro enviada para revisión de apoyo a la supervisión.');
    }

    public function show(CuentaCobro $cuentaCobro)
    {
        $user = Auth::user();

        $this->authorizeAccessToCuenta($cuentaCobro, $user->id);

        return view('cuentas.show', [
            'cuenta' => $cuentaCobro->load(['contractor', 'supervisor', 'mayor', 'tesoreria', 'fiduprevisora', 'documentos']),
            'user' => $user,
            'documentosRequeridos' => CuentaCobroDocumento::getDocumentosRequeridos((int) $cuentaCobro->numero_cuenta),
            'documentosParaReenvio' => $this->getDocumentosParaReenvio($cuentaCobro->loadMissing('documentos')),
        ]);
    }

    public function uploadFirmados(Request $request, CuentaCobro $cuentaCobro)
    {
        $this->authorizeRole('supervisor');

        $request->validate([
            'documento_1_firmado' => ['required', 'file', 'mimes:pdf', 'max:10240'],
            'documento_2_firmado' => ['required', 'file', 'mimes:pdf', 'max:10240'],
            'comment' => ['nullable', 'string', 'max:' . self::RICH_TEXT_MAX_LENGTH],
        ]);

        $path1 = $request->file('documento_1_firmado')->store("cuentas/{$cuentaCobro->id}/firmados", 'local');
        $path2 = $request->file('documento_2_firmado')->store("cuentas/{$cuentaCobro->id}/firmados", 'local');

        $cuentaCobro->update($this->filterCuentaCobroAttributes([
            'documento_1_firmado_path' => $path1,
            'documento_2_firmado_path' => $path2,
            'supervisor_comment' => $this->sanitizeRichText($request->input('comment')),
            'tesoreria_status' => 'pendiente',
            'tesoreria_comment' => '',
            'tesoreria_id' => null,
            'tesoreria_reviewed_at' => null,
            'fiduprevisora_status' => 'pendiente',
            'fiduprevisora_comment' => null,
            'fiduprevisora_id' => null,
            'fiduprevisora_reviewed_at' => null,
            'returned_at' => null,
            'returned_stage' => null,
        ]));

        $this->notifyRole(
            ['central de cuentas', 'tesoreria'],
            $cuentaCobro->fresh('contractor'),
            'Documentos firmados cargados',
            'El supervisor cargó los documentos 1 y 2 firmados. La cuenta ya está disponible para revisión en Central de Cuentas.'
        );

        return redirect()->route('cuentas.show', $cuentaCobro)->with('success', 'Documentos firmados cargados correctamente. La cuenta ya está disponible para Central de Cuentas.');
    }

    public function reviewDocumento(Request $request, CuentaCobro $cuentaCobro, CuentaCobroDocumento $documento)
    {
        if (!Auth::check() || !Auth::user()->hasAnyRole(['apoyo a la supervisión', 'apoyo a la supervision', 'apoyo a la supervicion'])) {
            abort(403, 'No autorizado para esta acción.');
        }

        if ($documento->cuenta_cobro_id !== $cuentaCobro->id) {
            abort(404, 'El documento no pertenece a esta cuenta.');
        }

        $validated = $request->validate([
            'decision' => ['required', 'in:aprobada,rechazada'],
            'comentario' => ['nullable', 'string', 'max:' . self::RICH_TEXT_MAX_LENGTH],
        ]);

        if ($validated['decision'] === 'rechazada' && blank($validated['comentario'] ?? null)) {
            return back()->withErrors(['comentario' => 'Debes indicar el motivo del rechazo del documento.'])->withInput();
        }

        $documento->update($this->filterCuentaCobroDocumentoAttributes([
            'estado' => $validated['decision'] === 'aprobada' ? 'validado' : 'rechazado',
            'comentario_supervisor' => $this->sanitizeRichText($validated['comentario'] ?? null),
            'validado_at' => now(),
        ]));

        $this->syncSupervisorStatuses($cuentaCobro->fresh('documentos'));

        if ($validated['decision'] === 'rechazada') {
            $this->notifyUser(
                $cuentaCobro->contractor,
                $cuentaCobro->fresh('contractor'),
                'Cuenta devuelta por supervisión',
                'Uno de los documentos fue rechazado en supervisión. Debes corregir y reenviar los documentos requeridos.'
            );
        }

        return redirect()->route('cuentas.show', $cuentaCobro)->with('success', 'Documento revisado correctamente.');
    }

    public function reviewDocumentoFiduprevisora(Request $request, CuentaCobro $cuentaCobro, CuentaCobroDocumento $documento)
    {
        $this->authorizeRole('fiduprevisora');

        if ($documento->cuenta_cobro_id !== $cuentaCobro->id) {
            abort(404, 'El documento no pertenece a esta cuenta.');
        }

        if (!$cuentaCobro->canGoToFiduprevisora()) {
            return back()->with('error', 'La cuenta aún no está lista para revisión documental en Fiduprevisora.');
        }

        $validated = $request->validate([
            'decision' => ['required', 'in:aprobada,rechazada'],
            'comentario' => ['nullable', 'string', 'max:' . self::RICH_TEXT_MAX_LENGTH],
        ]);

        if ($validated['decision'] === 'rechazada' && blank($validated['comentario'] ?? null)) {
            return back()->withErrors(['comentario' => 'Debes indicar el motivo del rechazo del documento en Fiduprevisora.'])->withInput();
        }

        $documento->update($this->filterCuentaCobroDocumentoAttributes([
            'fiduprevisora_estado' => $validated['decision'] === 'aprobada' ? 'aprobado' : 'rechazado',
            'fiduprevisora_comentario' => $this->sanitizeRichText($validated['comentario'] ?? null),
            'fiduprevisora_validado_at' => now(),
        ]));

        $this->syncFiduprevisoraStatuses($cuentaCobro->fresh('documentos'));

        return redirect()->route('cuentas.show', $cuentaCobro)->with('success', 'Revisión de Fiduprevisora actualizada correctamente.');
    }

    public function mayorReview(Request $request, CuentaCobro $cuentaCobro)
    {
        $this->authorizeRole('admin');

        if (!$cuentaCobro->canGoToMayor()) {
            return back()->with('error', 'Esta cuenta aún no está aprobada por apoyo a la supervision.');
        }

        $validated = $request->validate([
            'decision' => ['required', 'in:aprobada,rechazada'],
            'comment' => ['nullable', 'string', 'max:' . self::RICH_TEXT_MAX_LENGTH],
        ]);

        if ($validated['decision'] === 'rechazada' && blank($validated['comment'] ?? null)) {
            return back()->withErrors(['comment' => 'Debes indicar el motivo del rechazo.'])->withInput();
        }

        $rechazada = $validated['decision'] === 'rechazada';

        $cuentaCobro->update($this->filterCuentaCobroAttributes([
            'mayor_status' => $validated['decision'],
            'mayor_comment' => $this->sanitizeRichText($validated['comment'] ?? null),
            'mayor_id' => Auth::id(),
            'mayor_reviewed_at' => now(),
            'tesoreria_status' => $rechazada ? 'pendiente' : $cuentaCobro->tesoreria_status,
            'fiduprevisora_status' => $rechazada ? 'pendiente' : $cuentaCobro->fiduprevisora_status,
            'returned_at' => $rechazada ? now() : null,
            'returned_stage' => $rechazada ? 'admin' : null,
        ]));

        if ($rechazada) {
            $this->notifyUser(
                $cuentaCobro->contractor,
                $cuentaCobro->fresh('contractor'),
                'Cuenta devuelta por administración',
                'La cuenta fue rechazada en la revisión administrativa y regresó al contratista para corrección.'
            );
        } else {
            $this->notifyRecipients(
                $cuentaCobro->fresh('contractor'),
                'Cuenta aprobada por administración',
                'La cuenta fue aprobada en la revisión administrativa y continúa dentro del flujo.',
                User::query()
                    ->whereHas('role', fn ($query) => $query->where('name', 'supervisor'))
                    ->get()
                    ->all()
            );
        }

        return redirect()->route('cuentas.show', $cuentaCobro)->with('success', 'Decisión del administrador registrada correctamente.');
    }

    public function resubmit(Request $request, CuentaCobro $cuentaCobro)
    {
        $this->authorizeRole('contratista');

        if ($cuentaCobro->contractor_id !== Auth::id()) {
            abort(403, 'Solo el contratista dueño de la cuenta puede reenviar documentos.');
        }

        if (is_null($cuentaCobro->returned_at)) {
            return back()->with('error', 'Solo puedes reenviar cuando exista devolución previa.');
        }

        $documentosParaReenvio = $this->getDocumentosParaReenvio($cuentaCobro->loadMissing('documentos'));

        $validated = $request->validate([
            'billing_month' => ['required', 'date_format:Y-m'],
        ]);

        foreach ($documentosParaReenvio as $numeroDocumento) {
            $request->validate([
                "documentos.{$numeroDocumento}" => ['required', 'file', 'mimes:pdf', 'max:10240'],
            ], [
                "documentos.{$numeroDocumento}.required" => "El documento {$numeroDocumento} es obligatorio para el reenvío.",
            ]);
        }

        foreach ($documentosParaReenvio as $numeroDocumento) {
            $path = $request->file("documentos.{$numeroDocumento}")
                ->store("cuentas/{$cuentaCobro->contractor_id}/documentos", 'local');

            $cuentaCobro->documentos()
                ->where('numero_documento', $numeroDocumento)
                ->update([
                    'archivo_path' => $path,
                    'estado' => 'cargado',
                    'comentario_supervisor' => null,
                    'cargado_at' => now(),
                    'validado_at' => null,
                ] + $this->filterCuentaCobroDocumentoAttributes([
                    'fiduprevisora_estado' => 'pendiente',
                    'fiduprevisora_comentario' => null,
                    'fiduprevisora_validado_at' => null,
                ]));
        }

        if ($cuentaCobro->returned_stage === 'supervisor') {
            $this->resetAfterSupervisorResubmission($cuentaCobro, $validated['billing_month']);
        } else {
            $cuentaCobro->update($this->filterCuentaCobroAttributes([
                'billing_month' => $validated['billing_month'],
                'cuenta_pdf_path' => $cuentaCobro->documentos()->where('numero_documento', 1)->value('archivo_path'),
                'planilla_pdf_path' => $cuentaCobro->documentos()->where('numero_documento', 6)->value('archivo_path'),
                'documento_1_firmado_path' => null,
                'documento_2_firmado_path' => null,
                'cuenta_status' => 'pendiente',
                'planilla_status' => 'pendiente',
                'cuenta_supervisor_comment' => null,
                'planilla_supervisor_comment' => null,
                'supervisor_comment' => null,
                'supervisor_id' => null,
                'supervisor_reviewed_at' => null,
                'tesoreria_status' => 'pendiente',
                'tesoreria_comment' => null,
                'tesoreria_id' => null,
                'tesoreria_reviewed_at' => null,
                'fiduprevisora_status' => 'pendiente',
                'fiduprevisora_comment' => null,
                'fiduprevisora_id' => null,
                'fiduprevisora_reviewed_at' => null,
                'returned_at' => null,
                'returned_stage' => null,
            ]));
        }

        $this->notifyRole(
            ['apoyo a la supervisión', 'apoyo a la supervision', 'apoyo a la supervicion'],
            $cuentaCobro->fresh('contractor'),
            'Cuenta reenviada para revisión',
            'El contratista corrigio los documentos y reenvio la cuenta para una nueva validacion.'
        );

        return redirect()->route('cuentas.show', $cuentaCobro)->with('success', 'Documentos reenviados para nueva revisión.');
    }

    public function tesoreriaReview(Request $request, CuentaCobro $cuentaCobro)
    {
        $this->authorizeRole(['central de cuentas', 'tesoreria']);

        if (!$cuentaCobro->canGoToTesoreria()) {
            return back()->with('error', 'La cuenta debe estar aprobada por apoyo a la supervisión y tener los documentos firmados antes de Central de Cuentas.');
        }

        if (!$cuentaCobro->documentosFirmadosCompletos()) {
            return back()->with('error', 'El supervisor aún no ha cargado los documentos 1 y 2 firmados.');
        }

        $validated = $request->validate([
            'decision' => ['required', 'in:aprobada,rechazada'],
            'comment' => ['nullable', 'string', 'max:' . self::RICH_TEXT_MAX_LENGTH],
        ]);

        if ($validated['decision'] === 'rechazada' && blank($validated['comment'] ?? null)) {
            return back()->withErrors(['comment' => 'Debes indicar el motivo del rechazo.'])->withInput();
        }

        $rechazada = $validated['decision'] === 'rechazada';

        $cuentaCobro->update($this->filterCuentaCobroAttributes([
            'tesoreria_status' => $validated['decision'],
            'tesoreria_comment' => $this->sanitizeRichText($validated['comment'] ?? null),
            'tesoreria_id' => Auth::id(),
            'tesoreria_reviewed_at' => now(),
            'fiduprevisora_status' => $rechazada ? 'pendiente' : 'en_revision',
            'fiduprevisora_comment' => $rechazada ? null : 'Central de Cuentas remitió la cuenta a Fiduprevisora para revisión documento a documento.',
            'fiduprevisora_id' => null,
            'fiduprevisora_reviewed_at' => null,
            'returned_at' => $rechazada ? now() : null,
            'returned_stage' => $rechazada ? 'tesoreria' : null,
        ]));

        if ($rechazada) {
            $this->notifyUser(
                $cuentaCobro->contractor,
                $cuentaCobro->fresh('contractor'),
                'Cuenta devuelta por Central de Cuentas',
                'La cuenta fue devuelta en Central de Cuentas. Revisa los comentarios y vuelve a enviarla.'
            );
        } else {
            $cuentaCobro->documentos()->update([
                ...$this->filterCuentaCobroDocumentoAttributes([
                    'fiduprevisora_estado' => 'pendiente',
                    'fiduprevisora_comentario' => null,
                    'fiduprevisora_validado_at' => null,
                ]),
            ]);

            $this->notifyRole(
                ['fiduprevisora'],
                $cuentaCobro->fresh('contractor'),
                'Cuenta disponible para revisión en Fiduprevisora',
                'Central de Cuentas aprobó la cuenta y quedó disponible para revisión documento por documento.'
            );
        }

        return redirect()->route('cuentas.show', $cuentaCobro)->with('success', 'Decisión de Central de Cuentas registrada correctamente.');
    }

    public function fiduprevisoraReview(Request $request, CuentaCobro $cuentaCobro)
    {
        $this->authorizeRole('fiduprevisora');

        if ($cuentaCobro->fiduprevisora_status !== 'en_tramite') {
            return back()->with('error', 'Solo puedes enviar a pago una cuenta con revisión documental completa en Fiduprevisora.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:en_tramite,pagado'],
            'comment' => ['nullable', 'string', 'max:' . self::RICH_TEXT_MAX_LENGTH],
        ]);

        $cuentaCobro->update($this->filterCuentaCobroAttributes([
            'fiduprevisora_status' => $validated['status'],
            'fiduprevisora_comment' => $this->sanitizeRichText($validated['comment'] ?? null) ?: $cuentaCobro->fiduprevisora_comment,
            'fiduprevisora_id' => Auth::id(),
            'fiduprevisora_reviewed_at' => now(),
        ]));

        $this->notifyUser(
            $cuentaCobro->contractor,
            $cuentaCobro->fresh('contractor'),
            $validated['status'] === 'pagado' ? 'Pago confirmado' : 'Cuenta en trámite de pago',
            $validated['status'] === 'pagado'
                ? 'La cuenta de cobro fue marcada como pagada por Fiduprevisora.'
                : 'La cuenta de cobro terminó la revisión documental de Fiduprevisora y quedó en trámite de pago.'
        );

        return redirect()->route('cuentas.show', $cuentaCobro)->with('success', 'Estado en Fiduprevisora actualizado correctamente.');
    }

    public function download(CuentaCobro $cuentaCobro, string $type)
    {
        $user = Auth::user();

        $this->authorizeAccessToCuenta($cuentaCobro, $user->id);

        if (!in_array($type, ['cuenta', 'planilla'], true)) {
            abort(404);
        }

        $path = $type === 'cuenta' ? $cuentaCobro->cuenta_pdf_path : $cuentaCobro->planilla_pdf_path;

        if (!Storage::disk('local')->exists($path)) {
            abort(404, 'Archivo no encontrado.');
        }

        return Storage::disk('local')->download($path);
    }

    public function preview(CuentaCobro $cuentaCobro, string $type)
    {
        $user = Auth::user();

        $this->authorizeAccessToCuenta($cuentaCobro, $user->id);

        if (!in_array($type, ['cuenta', 'planilla'], true)) {
            abort(404);
        }

        $path = $type === 'cuenta' ? $cuentaCobro->cuenta_pdf_path : $cuentaCobro->planilla_pdf_path;

        if (!Storage::disk('local')->exists($path)) {
            abort(404, 'Archivo no encontrado.');
        }

        return Storage::disk('local')->response($path, basename($path), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
        ]);
    }

    public function previewDocumento(CuentaCobro $cuentaCobro, CuentaCobroDocumento $documento)
    {
        $user = Auth::user();

        $this->authorizeAccessToCuenta($cuentaCobro, $user->id);

        if ($documento->cuenta_cobro_id !== $cuentaCobro->id) {
            abort(404);
        }

        if (!Storage::disk('local')->exists($documento->archivo_path)) {
            abort(404, 'Archivo no encontrado.');
        }

        return Storage::disk('local')->response($documento->archivo_path, basename($documento->archivo_path), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($documento->archivo_path) . '"',
        ]);
    }

    public function downloadDocumento(CuentaCobro $cuentaCobro, CuentaCobroDocumento $documento)
    {
        $user = Auth::user();

        $this->authorizeAccessToCuenta($cuentaCobro, $user->id);

        if ($documento->cuenta_cobro_id !== $cuentaCobro->id) {
            abort(404);
        }

        if (!Storage::disk('local')->exists($documento->archivo_path)) {
            abort(404, 'Archivo no encontrado.');
        }

        return Storage::disk('local')->download($documento->archivo_path);
    }

    public function previewFirmado(CuentaCobro $cuentaCobro, int $numeroDocumento)
    {
        $user = Auth::user();

        $this->authorizeAccessToCuenta($cuentaCobro, $user->id);

        if (!in_array($numeroDocumento, [1, 2], true)) {
            abort(404);
        }

        $path = $numeroDocumento === 1 ? $cuentaCobro->documento_1_firmado_path : $cuentaCobro->documento_2_firmado_path;

        if (empty($path) || !Storage::disk('local')->exists($path)) {
            abort(404, 'Documento firmado no encontrado.');
        }

        return Storage::disk('local')->response($path, basename($path), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
        ]);
    }

    public function downloadFirmado(CuentaCobro $cuentaCobro, int $numeroDocumento)
    {
        $user = Auth::user();

        $this->authorizeAccessToCuenta($cuentaCobro, $user->id);

        if (!in_array($numeroDocumento, [1, 2], true)) {
            abort(404);
        }

        $path = $numeroDocumento === 1 ? $cuentaCobro->documento_1_firmado_path : $cuentaCobro->documento_2_firmado_path;

        if (empty($path) || !Storage::disk('local')->exists($path)) {
            abort(404, 'Documento firmado no encontrado.');
        }

        return Storage::disk('local')->download($path);
    }

    private function syncSupervisorStatuses(CuentaCobro $cuentaCobro): void
    {
        $requeridos = CuentaCobroDocumento::getDocumentosRequeridos((int) $cuentaCobro->numero_cuenta);
        $documentos = $cuentaCobro->documentos
            ->whereIn('numero_documento', $requeridos)
            ->keyBy('numero_documento');

        $hasRejected = $documentos->contains(fn ($doc) => $doc->estado === 'rechazado');
        $allValidated = $documentos->count() === count($requeridos)
            && $documentos->every(fn ($doc) => $doc->estado === 'validado');

        $docCuenta = $documentos->get(1);
        $docPlanilla = $documentos->get(6);

        $cuentaCobro->update($this->filterCuentaCobroAttributes([
            'cuenta_status' => $allValidated ? 'aprobada' : ($docCuenta?->estado === 'rechazado' ? 'rechazada' : ($docCuenta?->estado === 'validado' ? 'aprobada' : 'pendiente')),
            'planilla_status' => $allValidated ? 'aprobada' : ($docPlanilla?->estado === 'rechazado' ? 'rechazada' : ($docPlanilla?->estado === 'validado' ? 'aprobada' : 'pendiente')),
            'cuenta_supervisor_comment' => $docCuenta?->comentario_supervisor,
            'planilla_supervisor_comment' => $docPlanilla?->comentario_supervisor,
            'supervisor_comment' => null,
            'supervisor_id' => Auth::id(),
            'supervisor_reviewed_at' => now(),
            'tesoreria_status' => 'pendiente',
            'tesoreria_comment' => null,
            'tesoreria_id' => null,
            'tesoreria_reviewed_at' => null,
            'fiduprevisora_status' => 'pendiente',
            'fiduprevisora_comment' => null,
            'fiduprevisora_id' => null,
            'fiduprevisora_reviewed_at' => null,
            'documento_1_firmado_path' => $hasRejected ? null : $cuentaCobro->documento_1_firmado_path,
            'documento_2_firmado_path' => $hasRejected ? null : $cuentaCobro->documento_2_firmado_path,
            'returned_at' => $hasRejected ? now() : null,
            'returned_stage' => $hasRejected ? 'supervisor' : null,
        ]));

        if ($allValidated) {
            $this->notifyRecipients(
                $cuentaCobro->fresh('contractor'),
                'Cuenta lista tras revisión documental',
                'Todos los documentos requeridos fueron aprobados por apoyo a la supervisión.',
                User::query()
                    ->whereHas('role', fn ($query) => $query->whereIn('name', ['supervisor', 'central de cuentas', 'tesoreria']))
                    ->get()
                    ->all()
            );
        }
    }

    private function getDocumentosParaReenvio(CuentaCobro $cuentaCobro): array
    {
        $requeridos = CuentaCobroDocumento::getDocumentosRequeridos((int) $cuentaCobro->numero_cuenta);

        if ($cuentaCobro->returned_stage === 'supervisor') {
            $rechazados = $cuentaCobro->documentos
                ->whereIn('numero_documento', $requeridos)
                ->where('estado', 'rechazado')
                ->pluck('numero_documento')
                ->map(fn ($numero) => (int) $numero)
                ->values()
                ->all();

            return !empty($rechazados) ? $rechazados : $requeridos;
        }

        return $requeridos;
    }

    private function resetAfterSupervisorResubmission(CuentaCobro $cuentaCobro, string $billingMonth): void
    {
        $cuentaCobro->loadMissing('documentos');

        $docCuenta = $cuentaCobro->documentos->firstWhere('numero_documento', 1);
        $docPlanilla = $cuentaCobro->documentos->firstWhere('numero_documento', 6);

        $cuentaCobro->update($this->filterCuentaCobroAttributes([
            'billing_month' => $billingMonth,
            'cuenta_pdf_path' => $docCuenta?->archivo_path ?? $cuentaCobro->cuenta_pdf_path,
            'planilla_pdf_path' => $docPlanilla?->archivo_path ?? $cuentaCobro->planilla_pdf_path,
            'documento_1_firmado_path' => null,
            'documento_2_firmado_path' => null,
            'cuenta_status' => $docCuenta?->estado === 'validado' ? 'aprobada' : 'pendiente',
            'planilla_status' => $docPlanilla?->estado === 'validado' ? 'aprobada' : 'pendiente',
            'cuenta_supervisor_comment' => $docCuenta?->comentario_supervisor,
            'planilla_supervisor_comment' => $docPlanilla?->comentario_supervisor,
            'supervisor_comment' => null,
            'supervisor_id' => null,
            'supervisor_reviewed_at' => null,
            'tesoreria_status' => 'pendiente',
            'tesoreria_comment' => null,
            'tesoreria_id' => null,
            'tesoreria_reviewed_at' => null,
            'fiduprevisora_status' => 'pendiente',
            'fiduprevisora_comment' => null,
            'fiduprevisora_id' => null,
            'fiduprevisora_reviewed_at' => null,
            'returned_at' => null,
            'returned_stage' => null,
        ]));
    }

    private function syncFiduprevisoraStatuses(CuentaCobro $cuentaCobro): void
    {
        $requeridos = CuentaCobroDocumento::getDocumentosRequeridos((int) $cuentaCobro->numero_cuenta);
        $documentos = $cuentaCobro->documentos
            ->whereIn('numero_documento', $requeridos);

        $hasRejected = $documentos->contains(fn ($doc) => $doc->fiduprevisora_estado === 'rechazado');
        $allApproved = $documentos->count() === count($requeridos)
            && $documentos->every(fn ($doc) => $doc->fiduprevisora_estado === 'aprobado');

        if ($hasRejected) {
            $comentario = $documentos->firstWhere('fiduprevisora_estado', 'rechazado')?->fiduprevisora_comentario;

            $cuentaCobro->update($this->filterCuentaCobroAttributes([
                'fiduprevisora_status' => 'rechazada',
                'fiduprevisora_comment' => $comentario ?: 'Fiduprevisora devolvió la cuenta por inconsistencias documentales.',
                'fiduprevisora_id' => Auth::id(),
                'fiduprevisora_reviewed_at' => now(),
                'returned_at' => now(),
                'returned_stage' => 'fiduprevisora',
                'documento_1_firmado_path' => null,
                'documento_2_firmado_path' => null,
            ]));

            $this->notifyUser(
                $cuentaCobro->contractor,
                $cuentaCobro->fresh('contractor'),
                'Cuenta devuelta por Fiduprevisora',
                'Fiduprevisora rechazó al menos un documento y la cuenta regresó al contratista para corrección.'
            );

            return;
        }

        if ($allApproved) {
            $cuentaCobro->update($this->filterCuentaCobroAttributes([
                'fiduprevisora_status' => 'en_tramite',
                'fiduprevisora_comment' => 'Fiduprevisora aprobó todos los documentos. La cuenta quedó lista para envío a pago.',
                'fiduprevisora_id' => Auth::id(),
                'fiduprevisora_reviewed_at' => now(),
                'returned_at' => null,
                'returned_stage' => null,
            ]));

            $this->notifyUser(
                $cuentaCobro->contractor,
                $cuentaCobro->fresh('contractor'),
                'Cuenta lista para pago',
                'Fiduprevisora aprobó todos los documentos y la cuenta quedó lista para trámite de pago.'
            );
        }
    }

    private function notifyRole(array $roleNames, CuentaCobro $cuentaCobro, string $subject, string $message): void
    {
        $this->notifyRecipients(
            $cuentaCobro,
            $subject,
            $message,
            User::query()
                ->whereHas('role', fn ($query) => $query->whereIn('name', $roleNames))
                ->get()
                ->all()
        );
    }

    private function notifyUser(?User $user, CuentaCobro $cuentaCobro, string $subject, string $message): void
    {
        $this->notifyRecipients($cuentaCobro, $subject, $message, $user ? [$user] : []);
    }

    private function authorizeRole(string|array $role): void
    {
        if (!Auth::check()) {
            abort(403, 'No autorizado para esta acción.');
        }

        $user = Auth::user();
        $authorized = is_array($role)
            ? $user->hasAnyRole($role)
            : $user->hasRole($role);

        if (!$authorized) {
            abort(403, 'No autorizado para esta acción.');
        }
    }

    private function authorizeAccessToCuenta(CuentaCobro $cuentaCobro, int $userId): void
    {
        $user = Auth::user();

        if ($user->hasRole('contratista') && $cuentaCobro->contractor_id !== $userId) {
            abort(403, 'No puedes acceder a esta cuenta de cobro.');
        }

        if (!$user->hasAnyRole(['contratista', 'apoyo a la supervisión', 'apoyo a la supervision', 'apoyo a la supervicion', 'supervisor', 'admin', 'tesoreria', 'central de cuentas', 'fiduprevisora'])) {
            abort(403, 'No autorizado para acceder a esta cuenta de cobro.');
        }
    }

    private function sanitizeRichText(?string $html): ?string
    {
        if (blank($html)) {
            return null;
        }

        $clean = strip_tags($html, '<p><br><strong><em><ul><ol><li><b><i><u><s><strike><blockquote><a><img><h1><h2><h3><h4><h5><h6><div><span>');
        $clean = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $clean);
        $clean = preg_replace('/\son\w+="[^"]*"/i', '', $clean);
        $clean = preg_replace("/\son\w+='[^']*'/i", '', $clean);
        $clean = preg_replace('/\s(href|src)=("|\')\s*javascript:[^"\']*("|\')/i', '', $clean);

        return trim($clean) !== '' ? $clean : null;
    }

    private function filterCuentaCobroAttributes(array $attributes): array
    {
        static $columns = null;

        if ($columns === null) {
            $columns = array_flip(Schema::getColumnListing('cuentas_cobro'));
        }

        return array_filter(
            $attributes,
            fn ($value, $key) => isset($columns[$key]),
            ARRAY_FILTER_USE_BOTH
        );
    }

    private function filterCuentaCobroDocumentoAttributes(array $attributes): array
    {
        static $columns = null;

        if ($columns === null) {
            $columns = array_flip(Schema::getColumnListing('cuenta_cobro_documentos'));
        }

        return array_filter(
            $attributes,
            fn ($value, $key) => isset($columns[$key]),
            ARRAY_FILTER_USE_BOTH
        );
    }

    private function notifyRecipients(CuentaCobro $cuentaCobro, string $subject, string $message, array $extraRecipients = []): void
    {
        $participantIds = array_filter([
            $cuentaCobro->contractor_id,
            $cuentaCobro->supervisor_id,
            $cuentaCobro->mayor_id,
            $cuentaCobro->tesoreria_id,
            $cuentaCobro->fiduprevisora_id,
        ]);

        $participants = empty($participantIds)
            ? collect()
            : User::query()->whereIn('id', $participantIds)->get();

        $recipients = collect($extraRecipients)
            ->filter()
            ->merge($participants)
            ->unique('id')
            ->values();

        if ($recipients->isNotEmpty()) {
            Notification::send($recipients, new CuentaCobroFlowNotification($cuentaCobro->fresh('contractor'), $subject, $message));
        }
    }
}
