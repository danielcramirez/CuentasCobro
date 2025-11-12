<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class CuentaCobro extends Model
{
    use HasFactory;

    protected $table = 'cuenta_cobros';

    protected $fillable = [
        'user_id',
        'fecha_emision',
        'proyecto_servicio',
        'valor',
        'estado',
    ];

    protected $casts = [
        'fecha_emision' => 'date',
        'proyecto_servicio' => 'string',
        'valor' => 'decimal:2',
        'estado' => 'string',
    ];

    /**
     * Relación con usuario (propietario / creador de la cuenta de cobro)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para filtrar por estado
     */
    public function scopeEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    /**
     * Constantes para estados
     */
    public const ESTADO_BORRADOR = 'borrador';
    public const ESTADO_PENDIENTE = 'pendiente';
    public const ESTADO_REVISION = 'revision';
    public const ESTADO_APROBADO = 'aprobado';
    public const ESTADO_RECHAZADO = 'rechazado';
    public const ESTADO_PAGADO = 'pagado';

    /**
     * Obtener todos los estados disponibles
     */
    public static function getEstados()
    {
        return [
            self::ESTADO_BORRADOR => 'Borrador',
            self::ESTADO_PENDIENTE => 'Pendiente',
            self::ESTADO_REVISION => 'En Revisión',
            self::ESTADO_APROBADO => 'Aprobado',
            self::ESTADO_RECHAZADO => 'Rechazado',
            self::ESTADO_PAGADO => 'Pagado'
        ];
    }

    /**
     * Obtener el estado formateado
     */
    public function getEstadoFormateadoAttribute()
    {
        return self::getEstados()[$this->estado] ?? 'Desconocido';
    }

    /**
     * Scope para cuentas de cobro pendientes
     */
    public function scopePendientes($query)
    {
        return $query->where('estado', self::ESTADO_PENDIENTE);
    }

    /**
     * Scope para cuentas de cobro aprobadas
     */
    public function scopeAprobadas($query)
    {
        return $query->where('estado', self::ESTADO_APROBADO);
    }

    /**
     * Scope para cuentas de cobro pagadas
     */
    public function scopePagadas($query)
    {
        return $query->where('estado', self::ESTADO_PAGADO);
    }

    /**
     * Verificar si la cuenta está en estado editable
     */
    public function esEditable()
    {
        return in_array($this->estado, [self::ESTADO_BORRADOR, self::ESTADO_RECHAZADO]);
    }
}
