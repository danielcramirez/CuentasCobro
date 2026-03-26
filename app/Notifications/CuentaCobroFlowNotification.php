<?php

namespace App\Notifications;

use App\Models\CuentaCobro;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CuentaCobroFlowNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly CuentaCobro $cuentaCobro,
        private readonly string $subject,
        private readonly string $message,
    ) {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->subject)
            ->greeting('Hola ' . $notifiable->name . ',')
            ->line($this->message)
            ->line('Cuenta de cobro: #' . $this->cuentaCobro->id)
            ->line('Contratista: ' . ($this->cuentaCobro->contractor->name ?? 'N/A'))
            ->line('Mes cobrado: ' . $this->cuentaCobro->billing_month)
            ->action('Ver cuenta de cobro', route('cuentas.show', $this->cuentaCobro))
            ->line('Este es un correo automático del sistema de cuentas de cobro.');
    }
}
