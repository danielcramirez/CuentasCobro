<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CuentaCobroDocumento extends Model
{
    protected $table = 'cuenta_cobro_documentos';

    protected $fillable = [
        'cuenta_cobro_id',
        'numero_documento',
        'nombre_documento',
        'archivo_path',
        'estado',
        'comentario_supervisor',
        'cargado_at',
        'validado_at',
    ];

    protected $casts = [
        'cargado_at' => 'datetime',
        'validado_at' => 'datetime',
    ];

    /**
     * Relación: Este documento pertenece a una cuenta de cobro
     */
    public function cuentaCobro(): BelongsTo
    {
        return $this->belongsTo(CuentaCobro::class);
    }

    /**
     * Obtener el catálogo de documentos disponibles
     */
    public static function getCatalogo()
    {
        return [
            1 => 'Formato Cumplimiento de Obligaciones para Trámite de Pago (GF-1-01-F-01)',
            2 => 'Informe de gestión obligaciones contractuales SECOP II (GF-F-07)',
            3 => 'Documento Equivalente a Factura o Factura Electrónica',
            4 => 'Declaración juramentada',
            5 => 'Documentos soporte para la disminución de la base de retención',
            6 => 'Planilla de pago de seguridad social del mes',
            7 => 'Copia de la cédula de ciudadanía',
            8 => 'Certificación bancaria con fecha inferior a treinta (30) días',
            9 => 'Clausulado del contrato y estudios previos',
            10 => 'Memorando de delegación de la supervisión con captura de pantalla SECOP II',
            11 => 'Acta de inicio o captura de pantalla SECOP II',
            12 => 'Póliza del contrato',
            13 => 'Aprobación de pólizas (Captura de pantalla SECOP II)',
            14 => 'Certificado de Disponibilidad Presupuestal - CDP',
            15 => 'Registro Presupuestal - RP',
            16 => 'Registro Único Tributario - RUT con fecha inferior a treinta (30) días',
        ];
    }

    /**
     * Obtener documentos requeridos según el número de cuenta
     */
    public static function getDocumentosRequeridos($numero_cuenta)
    {
        if ($numero_cuenta == 1) {
            // Primera cuenta: todos los documentos (1-16)
            return array_keys(self::getCatalogo());
        } else {
            // Cuentas siguientes: solo 1-6
            return [1, 2, 3, 4, 5, 6];
        }
    }
}
