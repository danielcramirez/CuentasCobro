<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->middleware('auth');
        $this->notificationService = $notificationService;
    }

    /**
     * Obtener notificaciones no leídas del usuario actual
     */
    public function getUnread()
    {
        $notifications = $this->notificationService->obtenerNoLeidasUsuario(Auth::id());
        
        return response()->json([
            'success' => true,
            'count' => $notifications->count(),
            'notifications' => $notifications
        ]);
    }

    /**
     * Marcar una notificación como leída
     */
    public function markAsRead($id)
    {
        $this->notificationService->marcarComoLeida($id);
        
        return response()->json([
            'success' => true,
            'message' => 'Notificación marcada como leída'
        ]);
    }

    /**
     * Marcar todas las notificaciones como leídas
     */
    public function markAllAsRead()
    {
        $this->notificationService->marcarTodasComoLeidas(Auth::id());
        
        return response()->json([
            'success' => true,
            'message' => 'Todas las notificaciones han sido marcadas como leídas'
        ]);
    }

    /**
     * Obtener todas las notificaciones del usuario
     */
    public function index()
    {
        $user = Auth::user();
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }
}
