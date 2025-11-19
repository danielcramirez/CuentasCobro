<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id', // Asegurarse de que esté en fillable
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relación con roles - un usuario pertenece a un rol
     */
    public function role()
    {
        return $this->belongsTo(Roles::class, 'role_id', 'id');
    }

    /**
     * Verificar si el usuario tiene un rol específico
     */
    public function hasRole($roleName)
    {
        return $this->role && $this->role->name === $roleName;
    }

    /**
     * Verificar si el usuario tiene alguno de los roles especificados
     */
    public function hasAnyRole($roles)
    {
        if (!$this->role) {
            return false;
        }
        
        if (is_string($roles)) {
            $roles = explode(',', $roles);
        }
        
        return in_array($this->role->name, $roles);
    }

    /**
     * Verificar si el usuario es administrador
     */
    public function isAdmin()
    {
        return $this->hasAnyRole(['alcalde', 'ordenador_gasto']);
    }

    /**
     * Obtener las cuentas de cobro del usuario
     */
    public function cuentasCobro()
    {
        return $this->hasMany(CuentaCobro::class);
    }

    /**
     * Obtener las notificaciones del usuario
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Obtener notificaciones no leídas
     */
    public function unreadNotifications()
    {
        return $this->hasMany(Notification::class)->where('leida', false);
    }

    /**
     * Obtener estadísticas rápidas del usuario
     */
    public function getEstadisticasAttribute()
    {
        if (!$this->hasRole('contratista')) {
            return null;
        }

        return [
            'total_cuentas' => $this->cuentasCobro()->count(),
            'pendientes' => $this->cuentasCobro()->where('estado', CuentaCobro::ESTADO_PENDIENTE)->count(),
            'aprobadas' => $this->cuentasCobro()->where('estado', CuentaCobro::ESTADO_PAGADO)->count(),
            'valor_total_pagado' => $this->cuentasCobro()->where('estado', CuentaCobro::ESTADO_PAGADO)->sum('valor')
        ];
    }

    /**
     * Obtener el nombre del rol formateado
     */
    public function getRoleNameFormattedAttribute()
    {
        if (!$this->role) {
            return 'Sin rol asignado';
        }

        return ucfirst(str_replace('_', ' ', $this->role->name));
    }

    /**
     * Verificar si el usuario puede ver todas las cuentas de cobro
     */
    public function canViewAllCuentasCobro()
    {
        return $this->hasAnyRole(['supervisor', 'ordenador_gasto', 'tesoreria', 'alcalde']);
    }

    /**
     * Verificar si el usuario puede aprobar cuentas de cobro
     */
    public function canApproveCuentasCobro()
    {
        return $this->hasAnyRole(['supervisor', 'ordenador_gasto']);
    }

    /**
     * Verificar si el usuario puede procesar pagos
     */
    public function canProcessPayments()
    {
        return $this->hasRole('tesoreria');
    }
}