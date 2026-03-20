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
        'cuenta_status',
        'planilla_status',
        'cuenta_supervisor_comment',
        'planilla_supervisor_comment',
        'supervisor_id',
        'supervisor_reviewed_at',
        'mayor_status',
        'mayor_comment',
        'mayor_id',
        'mayor_reviewed_at',
        'returned_at',
    ];

    protected function casts(): array
    {
        return [
            'supervisor_reviewed_at' => 'datetime',
            'mayor_reviewed_at' => 'datetime',
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

    public function canGoToMayor(): bool
    {
        return $this->cuenta_status === 'aprobada' && $this->planilla_status === 'aprobada';
    }

    public function hasSupervisorRejection(): bool
    {
        return $this->cuenta_status === 'rechazada' || $this->planilla_status === 'rechazada';
    }
}
