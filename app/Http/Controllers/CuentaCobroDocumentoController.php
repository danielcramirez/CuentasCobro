<?php

namespace App\Http\Controllers;

use App\Http\Requests\CargarDocumentoCuentaCobroRequest;
use App\Models\CuentaCobro;
use App\Models\CuentaCobroDocumento;
use App\Services\CuentaCobroDocumentoService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CuentaCobroDocumentoController extends Controller
{
    protected CuentaCobroDocumentoService $service;

    public function __construct(CuentaCobroDocumentoService $service)
    {
        $this->service = $service;
        $this->middleware('auth');
    }

    /**
     * Mostrar resumen de documentos de una cuenta
     */
    public function index(CuentaCobro $cuenta)
    {
        // Autorizar: contratista o supervisor
        $this->authorize('view', $cuenta);

        $resumen = $this->service->getResumen($cuenta);
        
        return response()->json($resumen);
    }

    /**
     * Cargar un documento
     */
    public function store(CuentaCobro $cuenta, CargarDocumentoCuentaCobroRequest $request)
    {
        // Autorizar: solo el contratista
        $this->authorize('create', [CuentaCobroDocumento::class, $cuenta]);

        try {
            $numero_documento = $request->input('numero_documento');
            $archivo = $request->file('archivo');
            
            // Guardar archivo
            $ruta = $archivo->store('documentos_cobro', 'private');
            
            // Crear documento
            $documento = $this->service->cargarDocumento($cuenta, $numero_documento, $ruta);
            
            return response()->json([
                'success' => true,
                'mensaje' => 'Documento cargado exitosamente.',
                'documento' => $documento,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errores' => $e->errors(),
            ], 422);
        }
    }

    /**
     * Descargar documento
     */
    public function download(CuentaCobro $cuenta, CuentaCobroDocumento $documento)
    {
        // Autorizar: contratista o supervisor
        $this->authorize('view', $cuenta);
        
        if ($documento->cuenta_cobro_id !== $cuenta->id) {
            abort(404);
        }

        return response()->download(
            storage_path('app/private/' . $documento->archivo_path),
            "documento_{$documento->numero_documento}.pdf"
        );
    }

    /**
     * Validar documento (supervisor)
     */
    public function validar(Request $request, CuentaCobro $cuenta, CuentaCobroDocumento $documento)
    {
        // Autorizar: solo supervisor
        $this->authorize('review', $cuenta);
        
        if ($documento->cuenta_cobro_id !== $cuenta->id) {
            abort(404);
        }

        $request->validate([
            'aprobado' => ['required', 'boolean'],
            'comentario' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->service->validarDocumento(
            $documento,
            $request->boolean('aprobado'),
            $request->input('comentario')
        );

        return response()->json([
            'success' => true,
            'mensaje' => 'Documento ' . ($request->boolean('aprobado') ? 'aprobado' : 'rechazado') . '.',
            'documento' => $documento->fresh(),
        ]);
    }

    /**
     * Obtener documentos pendientes de validar (para supervisor)
     */
    public function pendientesDeValidar(CuentaCobro $cuenta)
    {
        // Autorizar: solo supervisor
        $this->authorize('review', $cuenta);

        $documentos = $cuenta->documentos()
            ->where('estado', '!=', 'validado')
            ->where('estado', '!=', 'pendiente')
            ->get();

        return response()->json($documentos);
    }
}
