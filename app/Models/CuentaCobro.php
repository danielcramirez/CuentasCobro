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
     * Ejemplo de constantes para estados (ajusta según tu migración)
     */
    public const ESTADO_BORRADOR = 'borrador';
    public const ESTADO_PENDIENTE = 'pendiente';
    public const ESTADO_PAGADO = 'pagado';
}
