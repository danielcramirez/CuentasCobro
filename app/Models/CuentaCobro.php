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
        'aprobado_supervisor_at',
        'aprobado_supervisor_by',
        'aprobado_contratacion_at',
        'aprobado_contratacion_by',
        'aprobado_tesoreria_at',
        'aprobado_tesoreria_by',
        'aprobado_ordenador_at',
        'aprobado_ordenador_by',
        'comentarios_rechazo',
        'rechazado_by',
        'rechazado_at',
    ];

    protected $casts = [
        'fecha_emision' => 'date',
        'proyecto_servicio' => 'string',
        'valor' => 'decimal:2',
        'estado' => 'string',
        'descripcion' => 'string',
        'ruta_archivo' => 'string',
        'aprobado_supervisor_at' => 'datetime',
        'aprobado_contratacion_at' => 'datetime',
        'aprobado_tesoreria_at' => 'datetime',
        'aprobado_ordenador_at' => 'datetime',
        'rechazado_at' => 'datetime',
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
     * Relación con supervisor que aprobó
     */
    public function aprobadoPorSupervisor()
    {
        return $this->belongsTo(User::class, 'aprobado_supervisor_by');
    }

    /**
     * Relación con contratación que aprobó
     */
    public function aprobadoPorContratacion()
    {
        return $this->belongsTo(User::class, 'aprobado_contratacion_by');
    }

    /**
     * Relación con tesorería que aprobó
     */
    public function aprobadoPorTesoreria()
    {
        return $this->belongsTo(User::class, 'aprobado_tesoreria_by');
    }

    /**
     * Relación con ordenador del gasto que aprobó
     */
    public function aprobadoPorOrdenador()
    {
        return $this->belongsTo(User::class, 'aprobado_ordenador_by');
    }

    /**
     * Relación con quien rechazó
     */
    public function rechazadoPor()
    {
        return $this->belongsTo(User::class, 'rechazado_by');
    }

    /**
     * Relación con notificaciones
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
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
    public const ESTADO_PENDIENTE_SUPERVISOR = 'pendiente_supervisor';
    public const ESTADO_PENDIENTE_CONTRATACION = 'pendiente_contratacion';
    public const ESTADO_PENDIENTE_TESORERIA = 'pendiente_tesoreria';
    public const ESTADO_PENDIENTE_ORDENADOR = 'pendiente_ordenador';
    public const ESTADO_APROBADA = 'aprobada';
    public const ESTADO_RECHAZADA = 'rechazada';
    public const ESTADO_PAGADA = 'pagada';

    /**
     * Obtener todos los estados disponibles
     */
    public static function getEstados()
    {
        return [
            self::ESTADO_BORRADOR => 'Borrador',
            self::ESTADO_PENDIENTE_SUPERVISOR => 'Pendiente Supervisor',
            self::ESTADO_PENDIENTE_CONTRATACION => 'Pendiente Contratación',
            self::ESTADO_PENDIENTE_TESORERIA => 'Pendiente Tesorería',
            self::ESTADO_PENDIENTE_ORDENADOR => 'Pendiente Ordenador del Gasto',
            self::ESTADO_APROBADA => 'Aprobada',
            self::ESTADO_RECHAZADA => 'Rechazada',
            self::ESTADO_PAGADA => 'Pagada'
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
     * Scope para cuentas de cobro pendientes (cualquier estado pendiente)
     */
    public function scopePendientes($query)
    {
        return $query->whereIn('estado', [
            self::ESTADO_PENDIENTE_SUPERVISOR,
            self::ESTADO_PENDIENTE_CONTRATACION,
            self::ESTADO_PENDIENTE_TESORERIA,
            self::ESTADO_PENDIENTE_ORDENADOR
        ]);
    }

    /**
     * Scope para cuentas de cobro aprobadas
     */
    public function scopeAprobadas($query)
    {
        return $query->where('estado', self::ESTADO_APROBADA);
    }

    /**
     * Scope para cuentas de cobro pagadas
     */
    public function scopePagadas($query)
    {
        return $query->where('estado', self::ESTADO_PAGADA);
    }

    /**
     * Verificar si la cuenta está en estado editable
     */
    public function esEditable()
    {
        return in_array($this->estado, [self::ESTADO_BORRADOR, self::ESTADO_RECHAZADA]);
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
            self::ESTADO_PENDIENTE_SUPERVISOR => 'yellow',
            self::ESTADO_PENDIENTE_CONTRATACION => 'orange',
            self::ESTADO_PENDIENTE_TESORERIA => 'blue',
            self::ESTADO_PENDIENTE_ORDENADOR => 'purple',
            self::ESTADO_APROBADA => 'green',
            self::ESTADO_PAGADA => 'emerald',
            self::ESTADO_RECHAZADA => 'red',
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
            self::ESTADO_PENDIENTE_SUPERVISOR => 'fas fa-user-check',
            self::ESTADO_PENDIENTE_CONTRATACION => 'fas fa-file-contract',
            self::ESTADO_PENDIENTE_TESORERIA => 'fas fa-coins',
            self::ESTADO_PENDIENTE_ORDENADOR => 'fas fa-stamp',
            self::ESTADO_APROBADA => 'fas fa-thumbs-up',
            self::ESTADO_PAGADA => 'fas fa-check-circle',
            self::ESTADO_RECHAZADA => 'fas fa-times-circle',
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
