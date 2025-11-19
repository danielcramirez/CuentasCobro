<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'cuenta_cobro_id',
        'tipo',
        'titulo',
        'mensaje',
        'leida',
        'leida_at'
    ];

    protected $casts = [
        'leida' => 'boolean',
        'leida_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relación con el usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con la cuenta de cobro
     */
    public function cuentaCobro(): BelongsTo
    {
        return $this->belongsTo(CuentaCobro::class);
    }

    /**
     * Marcar notificación como leída
     */
    public function markAsRead(): void
    {
        $this->update([
            'leida' => true,
            'leida_at' => now()
        ]);
    }

    /**
     * Scope para notificaciones no leídas
     */
    public function scopeUnread($query)
    {
        return $query->where('leida', false);
    }

    /**
     * Scope para notificaciones de un usuario
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
