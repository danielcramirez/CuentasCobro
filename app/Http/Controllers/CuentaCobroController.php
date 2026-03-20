<?php

namespace App\Http\Controllers;

use App\Models\CuentaCobro;
use App\Models\CuentaCobroDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CuentaCobroController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $query = CuentaCobro::with(['contractor', 'supervisor', 'mayor'])->latest();

        if ($user->hasRole('contratista')) {
            $query->where('contractor_id', $user->id);
        } elseif ($user->hasAnyRole(['apoyo a la supervisión', 'apoyo a la supervision', 'apoyo a la supervicion'])) {
            // Apoyo a la supervisión visualiza todas para análisis y trazabilidad.
        } elseif ($user->hasRole('supervisor')) {
            // Supervisor visualiza todas para trazabilidad.
        } elseif ($user->hasRole('admin')) {
            // Admin visualiza todas las cuentas aprobadas
            $query->where('cuenta_status', 'aprobada')
                ->where('planilla_status', 'aprobada');
        } else {
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
        $requeridos = $numeroCuenta === 1 ? range(1, 16) : range(1, 6);

        foreach ($requeridos as $numeroDocumento) {
            $request->validate([
                "documentos.{$numeroDocumento}" => ['required', 'file', 'mimes:pdf', 'max:10240'],
            ], [
                "documentos.{$numeroDocumento}.required" => "El documento {$numeroDocumento} es obligatorio.",
            ]);
        }

        $catalogo = CuentaCobroDocumento::getCatalogo();

        $rutasDocumentos = [];

        foreach ($requeridos as $numeroDocumento) {
            $rutasDocumentos[$numeroDocumento] = $request->file("documentos.{$numeroDocumento}")
                ->store("cuentas/{$user->id}/documentos", 'local');
        }

        $cuentaCobro = CuentaCobro::create([
            'contractor_id' => $user->id,
            'billing_month' => $validated['billing_month'],
            'numero_cuenta' => $numeroCuenta,
            'cuenta_pdf_path' => $rutasDocumentos[1],
            'planilla_pdf_path' => $rutasDocumentos[6],
            'cuenta_status' => 'pendiente',
            'planilla_status' => 'pendiente',
            'mayor_status' => 'pendiente',
        ]);

        foreach ($requeridos as $numeroDocumento) {
            CuentaCobroDocumento::create([
                'cuenta_cobro_id' => $cuentaCobro->id,
                'numero_documento' => $numeroDocumento,
                'nombre_documento' => $catalogo[$numeroDocumento] ?? "Documento {$numeroDocumento}",
                'archivo_path' => $rutasDocumentos[$numeroDocumento],
                'estado' => 'cargado',
                'cargado_at' => now(),
            ]);
        }

        return redirect()->route('cuentas.index')->with('success', 'Cuenta de cobro enviada para revisión del supervisor.');
    }

    public function show(CuentaCobro $cuentaCobro)
    {
        $user = Auth::user();

        $this->authorizeAccessToCuenta($cuentaCobro, $user->id);

        return view('cuentas.show', [
            'cuenta' => $cuentaCobro->load(['contractor', 'supervisor', 'mayor', 'documentos']),
            'user' => $user,
        ]);
    }

    public function supervisorReview(Request $request, CuentaCobro $cuentaCobro)
    {
        if (!Auth::check() || !Auth::user()->hasAnyRole(['apoyo a la supervisión', 'apoyo a la supervision', 'apoyo a la supervicion', 'supervisor'])) {
            abort(403, 'No autorizado para esta acción.');
        }

        $validated = $request->validate([
            'cuenta_decision' => ['required', 'in:aprobada,rechazada'],
            'planilla_decision' => ['required', 'in:aprobada,rechazada'],
            'cuenta_comment' => ['nullable', 'string', 'max:1000'],
            'planilla_comment' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($validated['cuenta_decision'] === 'rechazada' && empty($validated['cuenta_comment'])) {
            return back()->withErrors(['cuenta_comment' => 'Debes explicar por qué rechazas la cuenta de cobro.'])->withInput();
        }

        if ($validated['planilla_decision'] === 'rechazada' && empty($validated['planilla_comment'])) {
            return back()->withErrors(['planilla_comment' => 'Debes explicar por qué rechazas la planilla.'])->withInput();
        }

        $cuentaCobro->update([
            'cuenta_status' => $validated['cuenta_decision'],
            'planilla_status' => $validated['planilla_decision'],
            'cuenta_supervisor_comment' => $validated['cuenta_comment'] ?? null,
            'planilla_supervisor_comment' => $validated['planilla_comment'] ?? null,
            'supervisor_id' => Auth::id(),
            'supervisor_reviewed_at' => now(),
            'mayor_status' => 'pendiente',
            'mayor_comment' => null,
            'mayor_id' => null,
            'mayor_reviewed_at' => null,
            'returned_at' => ($validated['cuenta_decision'] === 'rechazada' || $validated['planilla_decision'] === 'rechazada') ? now() : null,
        ]);

        return redirect()->route('cuentas.show', $cuentaCobro)->with('success', 'Revisión del supervisor registrada.');
    }

    public function reviewDocumento(Request $request, CuentaCobro $cuentaCobro, CuentaCobroDocumento $documento)
    {
        if (!Auth::check() || !Auth::user()->hasAnyRole(['apoyo a la supervisión', 'apoyo a la supervision', 'apoyo a la supervicion', 'supervisor'])) {
            abort(403, 'No autorizado para esta acción.');
        }

        if ($documento->cuenta_cobro_id !== $cuentaCobro->id) {
            abort(404, 'El documento no pertenece a esta cuenta.');
        }

        $validated = $request->validate([
            'decision' => ['required', 'in:aprobada,rechazada'],
            'comentario' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($validated['decision'] === 'rechazada' && empty($validated['comentario'])) {
            return back()->withErrors(['comentario' => 'Debes indicar el motivo del rechazo del documento.'])->withInput();
        }

        $documento->update([
            'estado' => $validated['decision'] === 'aprobada' ? 'validado' : 'rechazado',
            'comentario_supervisor' => $validated['comentario'] ?? null,
            'validado_at' => now(),
        ]);

        $requeridos = CuentaCobroDocumento::getDocumentosRequeridos((int) $cuentaCobro->numero_cuenta);
        $documentos = $cuentaCobro->documentos()
            ->whereIn('numero_documento', $requeridos)
            ->get()
            ->keyBy('numero_documento');

        $hasRejected = $documentos->contains(fn ($doc) => $doc->estado === 'rechazado');
        $allValidated = $documentos->count() === count($requeridos)
            && $documentos->every(fn ($doc) => $doc->estado === 'validado');

        $docCuenta = $documentos->get(1);
        $docPlanilla = $documentos->get(6);

        $nuevoCuentaStatus = $docCuenta?->estado === 'validado' ? 'aprobada' : ($docCuenta?->estado === 'rechazado' ? 'rechazada' : 'pendiente');
        $nuevoPlanillaStatus = $docPlanilla?->estado === 'validado' ? 'aprobada' : ($docPlanilla?->estado === 'rechazado' ? 'rechazada' : 'pendiente');

        if ($allValidated) {
            $nuevoCuentaStatus = 'aprobada';
            $nuevoPlanillaStatus = 'aprobada';
        }

        if ($hasRejected) {
            if ($nuevoCuentaStatus === 'pendiente') {
                $nuevoCuentaStatus = 'rechazada';
            }
            if ($nuevoPlanillaStatus === 'pendiente') {
                $nuevoPlanillaStatus = 'rechazada';
            }
        }

        $cuentaCobro->update([
            'cuenta_status' => $nuevoCuentaStatus,
            'planilla_status' => $nuevoPlanillaStatus,
            'cuenta_supervisor_comment' => $docCuenta?->comentario_supervisor,
            'planilla_supervisor_comment' => $docPlanilla?->comentario_supervisor,
            'supervisor_id' => Auth::id(),
            'supervisor_reviewed_at' => now(),
            'returned_at' => $hasRejected ? now() : null,
        ]);

        return redirect()->route('cuentas.show', $cuentaCobro)->with('success', 'Documento revisado correctamente.');
    }

    public function mayorReview(Request $request, CuentaCobro $cuentaCobro)
    {
        $this->authorizeRole('admin');

        if (!$cuentaCobro->canGoToMayor()) {
            return back()->with('error', 'Esta cuenta aún no está aprobada por supervisor.');
        }

        $validated = $request->validate([
            'decision' => ['required', 'in:aprobada,rechazada'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($validated['decision'] === 'rechazada' && empty($validated['comment'])) {
            return back()->withErrors(['comment' => 'Debes indicar el motivo del rechazo.'])->withInput();
        }

        $cuentaCobro->update([
            'mayor_status' => $validated['decision'],
            'mayor_comment' => $validated['comment'] ?? null,
            'mayor_id' => Auth::id(),
            'mayor_reviewed_at' => now(),
            'returned_at' => $validated['decision'] === 'rechazada' ? now() : null,
        ]);

        return redirect()->route('cuentas.show', $cuentaCobro)->with('success', 'Decisión del administrador registrada correctamente.');
    }

    public function resubmit(Request $request, CuentaCobro $cuentaCobro)
    {
        $this->authorizeRole('contratista');

        if ($cuentaCobro->contractor_id !== Auth::id()) {
            abort(403, 'Solo el contratista dueño de la cuenta puede reenviar documentos.');
        }

        if (!$cuentaCobro->hasSupervisorRejection() && $cuentaCobro->mayor_status !== 'rechazada') {
            return back()->with('error', 'Solo puedes reenviar cuando exista rechazo previo.');
        }

        $validated = $request->validate([
            'cuenta_pdf' => ['required', 'file', 'mimes:pdf', 'max:5120'],
            'planilla_pdf' => ['required', 'file', 'mimes:pdf', 'max:5120'],
            'billing_month' => ['required', 'date_format:Y-m'],
        ]);

        $cuentaPdfPath = $request->file('cuenta_pdf')->store("cuentas/{$cuentaCobro->contractor_id}/cuenta", 'local');
        $planillaPdfPath = $request->file('planilla_pdf')->store("cuentas/{$cuentaCobro->contractor_id}/planilla", 'local');

        $cuentaCobro->update([
            'billing_month' => $validated['billing_month'],
            'cuenta_pdf_path' => $cuentaPdfPath,
            'planilla_pdf_path' => $planillaPdfPath,
            'cuenta_status' => 'pendiente',
            'planilla_status' => 'pendiente',
            'cuenta_supervisor_comment' => null,
            'planilla_supervisor_comment' => null,
            'supervisor_id' => null,
            'supervisor_reviewed_at' => null,
            'mayor_status' => 'pendiente',
            'mayor_comment' => null,
            'mayor_id' => null,
            'mayor_reviewed_at' => null,
            'returned_at' => null,
        ]);

        return redirect()->route('cuentas.show', $cuentaCobro)->with('success', 'Documentos reenviados para nueva revisión del supervisor.');
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

    private function authorizeRole(string $role): void
    {
        if (!Auth::check() || !Auth::user()->hasRole($role)) {
            abort(403, 'No autorizado para esta acción.');
        }
    }

    private function authorizeAccessToCuenta(CuentaCobro $cuentaCobro, int $userId): void
    {
        $user = Auth::user();

        if ($user->hasRole('contratista') && $cuentaCobro->contractor_id !== $userId) {
            abort(403, 'No puedes acceder a esta cuenta de cobro.');
        }

        if (!$user->hasAnyRole(['contratista', 'apoyo a la supervisión', 'apoyo a la supervision', 'apoyo a la supervicion', 'supervisor', 'admin'])) {
            abort(403, 'No autorizado para acceder a esta cuenta de cobro.');
        }
    }
}
