<?php

namespace App\Services;

use App\Models\CuentaCobro;
use App\Models\CuentaCobroDocumento;
use Illuminate\Validation\ValidationException;

class CuentaCobroDocumentoService
{
    /**
     * Validar que se cumplan las reglas de documentos para una cuenta
     */
    public function validar(CuentaCobro $cuenta): array
    {
        $numero_cuenta = $cuenta->getNumeroCuenta();
        $documentos_requeridos = CuentaCobroDocumento::getDocumentosRequeridos($numero_cuenta);
        
        $errores = [];
        
        foreach ($documentos_requeridos as $numero) {
            $documento = $cuenta->documentos()
                ->where('numero_documento', $numero)
                ->first();
            
            if (!$documento || $documento->estado === 'pendiente') {
                $catalogo = CuentaCobroDocumento::getCatalogo();
                $errores[] = "Documento {$numero}: " . $catalogo[$numero] . " - PENDIENTE";
            }
            
            if ($documento && $documento->estado === 'rechazado') {
                $catalogo = CuentaCobroDocumento::getCatalogo();
                $errores[] = "Documento {$numero}: " . $catalogo[$numero] . " - RECHAZADO";
            }
        }
        
        return $errores;
    }

    /**
     * Obtener estado resumen de documentos
     */
    public function getResumen(CuentaCobro $cuenta): array
    {
        $numero_cuenta = $cuenta->getNumeroCuenta();
        $requeridos = CuentaCobroDocumento::getDocumentosRequeridos($numero_cuenta);
        $catalogo = CuentaCobroDocumento::getCatalogo();
        
        $documentos = [];
        foreach ($requeridos as $numero) {
            $doc = $cuenta->documentos()
                ->where('numero_documento', $numero)
                ->first();
            
            $documentos[] = [
                'numero' => $numero,
                'nombre' => $catalogo[$numero],
                'estado' => $doc?->estado ?? 'pendiente',
                'cargado_at' => $doc?->cargado_at,
                'validado_at' => $doc?->validado_at,
                'comentario' => $doc?->comentario_supervisor,
            ];
        }
        
        return [
            'numero_cuenta' => $numero_cuenta,
            'total_requeridos' => count($requeridos),
            'documentos' => $documentos,
            'completos' => $cuenta->sonTodosDocumentosCargados(),
        ];
    }

    /**
     * Cargar un documento
     */
    public function cargarDocumento(CuentaCobro $cuenta, int $numero_documento, string $archivo_path): CuentaCobroDocumento
    {
        // Validar que el documento sea requerido
        $requeridos = $cuenta->getDocumentosRequeridos();
        
        if (!in_array($numero_documento, $requeridos)) {
            throw ValidationException::withMessages([
                'documento' => "El documento {$numero_documento} no es requerido para esta cuenta de cobro."
            ]);
        }

        $catalogo = CuentaCobroDocumento::getCatalogo();
        
        return CuentaCobroDocumento::updateOrCreate(
            [
                'cuenta_cobro_id' => $cuenta->id,
                'numero_documento' => $numero_documento,
            ],
            [
                'nombre_documento' => $catalogo[$numero_documento],
                'archivo_path' => $archivo_path,
                'estado' => 'cargado',
                'cargado_at' => now(),
            ]
        );
    }

    /**
     * Validar documento por supervisor
     */
    public function validarDocumento(CuentaCobroDocumento $documento, bool $aprobado, ?string $comentario = null): void
    {
        $documento->update([
            'estado' => $aprobado ? 'validado' : 'rechazado',
            'comentario_supervisor' => $comentario,
            'validado_at' => now(),
        ]);
    }
}
