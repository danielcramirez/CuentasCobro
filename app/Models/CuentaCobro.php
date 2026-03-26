<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CuentaCobro extends Model
{
    use HasFactory;

    protected $table = 'cuentas_cobro';

    protected $fillable = [
        'contractor_id',
        'billing_month',
        'numero_cuenta',
        'cuenta_pdf_path',
        'planilla_pdf_path',
        'documento_1_firmado_path',
        'documento_2_firmado_path',
        'cuenta_status',
        'planilla_status',
        'cuenta_supervisor_comment',
        'planilla_supervisor_comment',
        'supervisor_comment',
        'supervisor_id',
        'supervisor_reviewed_at',
        'mayor_status',
        'mayor_comment',
        'mayor_id',
        'mayor_reviewed_at',
        'tesoreria_status',
        'tesoreria_comment',
        'tesoreria_id',
        'tesoreria_reviewed_at',
        'fiduprevisora_status',
        'fiduprevisora_comment',
        'fiduprevisora_id',
        'fiduprevisora_reviewed_at',
        'returned_at',
        'returned_stage',
    ];

    protected function casts(): array
    {
        return [
            'supervisor_reviewed_at' => 'datetime',
            'mayor_reviewed_at' => 'datetime',
            'tesoreria_reviewed_at' => 'datetime',
            'fiduprevisora_reviewed_at' => 'datetime',
            'returned_at' => 'datetime',
            'numero_cuenta' => 'integer',
        ];
    }

    public function contractor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'contractor_id');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }


    public function documentos(): HasMany
    {
        return $this->hasMany(CuentaCobroDocumento::class);
    }

    /**
     * Obtener el número de cuenta de cobro del contratista
     */
    public function getNumeroCuenta(): int
    {
        if (!empty($this->numero_cuenta)) {
            return (int) $this->numero_cuenta;
        }

        return $this->contractor->cuentasCobro()
            ->where('billing_month', '<=', $this->billing_month)
            ->orderBy('billing_month')
            ->get()
            ->search(fn($cc) => $cc->id === $this->id) + 1;
    }

    /**
     * Obtener documentos requeridos para esta cuenta
     */
    public function getDocumentosRequeridos(): array
    {
        $numero_cuenta = $this->getNumeroCuenta();
        return CuentaCobroDocumento::getDocumentosRequeridos($numero_cuenta);
    }

    /**
     * Validar si están todos los documentos requeridos cargados
     */
    public function sonTodosDocumentosCargados(): bool
    {
        $requeridos = $this->getDocumentosRequeridos();
        $cargados = $this->documentos()
            ->whereIn('numero_documento', $requeridos)
            ->where('estado', '!=', 'pendiente')
            ->count();
        
        return $cargados === count($requeridos);
    }

    public function mayor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mayor_id');
    }

    public function tesoreria(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tesoreria_id');
    }

    public function fiduprevisora(): BelongsTo
    {
        return $this->belongsTo(User::class, 'fiduprevisora_id');
    }

    public function canGoToMayor(): bool
    {
        return $this->cuenta_status === 'aprobada' && $this->planilla_status === 'aprobada';
    }

    public function hasSupervisorRejection(): bool
    {
        return $this->cuenta_status === 'rechazada' || $this->planilla_status === 'rechazada';
    }

    public function canGoToTesoreria(): bool
    {
        return $this->cuenta_status === 'aprobada'
            && $this->planilla_status === 'aprobada'
            && $this->documentosFirmadosCompletos();
    }

    public function canGoToFiduprevisora(): bool
    {
        return $this->tesoreria_status === 'aprobada' && $this->documentosFirmadosCompletos();
    }

    public function getEstadoApoyoRevision(): string
    {
        $requeridos = $this->getDocumentosRequeridos();
        $documentos = $this->documentos
            ->whereIn('numero_documento', $requeridos);

        if (
            $this->returned_stage === 'supervisor'
            || $documentos->contains(fn ($doc) => $doc->estado === 'rechazado')
            || $this->cuenta_status === 'rechazada'
            || $this->planilla_status === 'rechazada'
        ) {
            return 'rechazada';
        }

        if (
            $documentos->count() === count($requeridos)
            && $documentos->every(fn ($doc) => $doc->estado === 'validado')
        ) {
            return 'aprobada';
        }

        return 'pendiente';
    }

    public function getEstadoSupervisorRevision(): string
    {
        return $this->documentosFirmadosCompletos() ? 'aprobada' : 'pendiente';
    }

    public function getEstadoCentralRevision(): string
    {
        if ($this->tesoreria_status === 'aprobada') {
            return 'aprobada';
        }

        if ($this->tesoreria_status === 'rechazada' || $this->returned_stage === 'tesoreria') {
            return 'rechazada';
        }

        if ($this->getEstadoApoyoRevision() === 'aprobada' && $this->documentosFirmadosCompletos()) {
            return 'pendiente_central';
        }

        return 'pendiente';
    }

    public function documentosFirmadosCompletos(): bool
    {
        return !empty($this->documento_1_firmado_path) && !empty($this->documento_2_firmado_path);
    }

    public function hasReturnedBy(string $stage): bool
    {
        return $this->returned_stage === $stage && !is_null($this->returned_at);
    }
}
