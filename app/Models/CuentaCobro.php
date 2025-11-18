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
        'descripcion',
        'ruta_archivo',
    ];

    protected $casts = [
        'fecha_emision' => 'date',
        'proyecto_servicio' => 'string',
        'valor' => 'decimal:2',
        'estado' => 'string',
        'descripcion' => 'string',
        'ruta_archivo' => 'string',
    ];

    /**
     * Accesor para mantener compatibilidad con propiedad `description` usada en vistas.
     * Mapea `$cuenta->description` a la columna `descripcion`.
     */
    public function getDescriptionAttribute()
    {
        return $this->attributes['descripcion'] ?? null;
    }

    /**
     * Mutator para permitir asignar `$cuenta->description = '...'` y guardarlo en `descripcion`.
     */
    public function setDescriptionAttribute($value)
    {
        $this->attributes['descripcion'] = $value;
    }

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

    /**
     * Verificar si la cuenta se puede eliminar
     */
    public function sePuedeEliminar()
    {
        return $this->estado === self::ESTADO_BORRADOR;
    }

    /**
     * Obtener el número de cuenta formateado
     */
    public function getNumeroFormateadoAttribute()
    {
        return str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Obtener el color del estado para la UI
     */
    public function getColorEstadoAttribute()
    {
        return match($this->estado) {
            self::ESTADO_BORRADOR => 'gray',
            self::ESTADO_PENDIENTE => 'yellow',
            self::ESTADO_REVISION => 'blue',
            self::ESTADO_APROBADO => 'green',
            self::ESTADO_PAGADO => 'emerald',
            self::ESTADO_RECHAZADO => 'red',
            default => 'gray'
        };
    }

    /**
     * Obtener el icono del estado para la UI
     */
    public function getIconoEstadoAttribute()
    {
        return match($this->estado) {
            self::ESTADO_BORRADOR => 'fas fa-edit',
            self::ESTADO_PENDIENTE => 'fas fa-clock',
            self::ESTADO_REVISION => 'fas fa-search',
            self::ESTADO_APROBADO => 'fas fa-thumbs-up',
            self::ESTADO_PAGADO => 'fas fa-check-circle',
            self::ESTADO_RECHAZADO => 'fas fa-times-circle',
            default => 'fas fa-question'
        };
    }

    /**
     * Scope para cuentas del mes actual
     */
    public function scopeDelMes($query)
    {
        return $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
    }

    /**
     * Scope para cuentas del año actual
     */
    public function scopeDelAno($query)
    {
        return $query->whereYear('created_at', now()->year);
    }
}
