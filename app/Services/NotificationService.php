<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\CuentaCobro;

class NotificationService
{
    /**
     * Crear notificación para supervisor cuando se crea una cuenta de cobro
     */
    public function notificarNuevaCuentaCobro(CuentaCobro $cuenta)
    {
        $supervisores = User::whereHas('role', function($query) {
            $query->where('name', 'supervisor');
        })->get();

        foreach ($supervisores as $supervisor) {
            Notification::create([
                'user_id' => $supervisor->id,
                'cuenta_cobro_id' => $cuenta->id,
                'tipo' => 'nueva_cuenta',
                'titulo' => 'Nueva Cuenta de Cobro',
                'mensaje' => "El contratista {$cuenta->user->name} ha creado una nueva cuenta de cobro por valor de $" . number_format($cuenta->valor, 0, ',', '.') . " para el proyecto '{$cuenta->proyecto_servicio}'"
            ]);
        }
    }

    /**
     * Notificar a contratación después de aprobación del supervisor
     */
    public function notificarContratacion(CuentaCobro $cuenta)
    {
        $contratacion = User::whereHas('role', function($query) {
            $query->where('name', 'contratacion');
        })->get();

        foreach ($contratacion as $user) {
            Notification::create([
                'user_id' => $user->id,
                'cuenta_cobro_id' => $cuenta->id,
                'tipo' => 'requiere_aprobacion',
                'titulo' => 'Cuenta Aprobada por Supervisor',
                'mensaje' => "La cuenta de cobro #{$cuenta->id} del contratista {$cuenta->user->name} ha sido aprobada por el supervisor y requiere tu revisión."
            ]);
        }
    }

    /**
     * Notificar a tesorería después de aprobación de contratación
     */
    public function notificarTesoreria(CuentaCobro $cuenta)
    {
        $tesoreria = User::whereHas('role', function($query) {
            $query->where('name', 'tesoreria');
        })->get();

        foreach ($tesoreria as $user) {
            Notification::create([
                'user_id' => $user->id,
                'cuenta_cobro_id' => $cuenta->id,
                'tipo' => 'requiere_aprobacion',
                'titulo' => 'Cuenta Aprobada por Contratación',
                'mensaje' => "La cuenta de cobro #{$cuenta->id} por valor de $" . number_format($cuenta->valor, 0, ',', '.') . " requiere aprobación de disponibilidad presupuestal."
            ]);
        }
    }

    /**
     * Notificar al ordenador del gasto después de aprobación de tesorería
     */
    public function notificarOrdenadorGasto(CuentaCobro $cuenta)
    {
        $ordenadores = User::whereHas('role', function($query) {
            $query->where('name', 'ordenador_gasto');
        })->get();

        foreach ($ordenadores as $user) {
            Notification::create([
                'user_id' => $user->id,
                'cuenta_cobro_id' => $cuenta->id,
                'tipo' => 'requiere_aprobacion',
                'titulo' => 'Aprobación Final Requerida',
                'mensaje' => "La cuenta de cobro #{$cuenta->id} ha sido aprobada por Tesorería y requiere tu aprobación final para proceder al pago."
            ]);
        }
    }

    /**
     * Notificar al contratista sobre el estado de su cuenta
     */
    public function notificarContratista(CuentaCobro $cuenta, string $estado, string $comentarios = null)
    {
        $mensajes = [
            'aprobada' => "Tu cuenta de cobro #{$cuenta->id} ha sido completamente aprobada y está lista para pago.",
            'rechazada' => "Tu cuenta de cobro #{$cuenta->id} ha sido rechazada. " . ($comentarios ? "Motivo: $comentarios" : ""),
        ];

        Notification::create([
            'user_id' => $cuenta->user_id,
            'cuenta_cobro_id' => $cuenta->id,
            'tipo' => $estado,
            'titulo' => $estado === 'aprobada' ? 'Cuenta Aprobada' : 'Cuenta Rechazada',
            'mensaje' => $mensajes[$estado] ?? "Tu cuenta de cobro #{$cuenta->id} ha cambiado de estado."
        ]);
    }

    /**
     * Obtener notificaciones no leídas de un usuario
     */
    public function obtenerNoLeidasUsuario(int $userId)
    {
        return Notification::where('user_id', $userId)
            ->where('leida', false)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Marcar notificación como leída
     */
    public function marcarComoLeida(int $notificationId)
    {
        $notification = Notification::find($notificationId);
        if ($notification) {
            $notification->markAsRead();
        }
    }

    /**
     * Marcar todas las notificaciones de un usuario como leídas
     */
    public function marcarTodasComoLeidas(int $userId)
    {
        Notification::where('user_id', $userId)
            ->where('leida', false)
            ->update([
                'leida' => true,
                'leida_at' => now()
            ]);
    }
}
