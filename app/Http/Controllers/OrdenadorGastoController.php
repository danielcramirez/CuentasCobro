<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CuentaCobro;
use Carbon\Carbon;

class OrdenadorGastoController extends Controller
{
    /**
     * Display dashboard
     */
    public function index()
    {
        return $this->dashboard();
    }

    /**
     * Dashboard del ordenador de gasto
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        if (!$user->hasRole('ordenador_gasto')) {
            abort(403, 'Acceso denegado. Solo el ordenador de gasto puede acceder a esta vista.');
        }

        $data = $this->getOrdenadorData($user);
        
        return view('ordenador-gasto.dashboard', $data);
    }

    /**
     * Get ordenador-specific data
     */
    private function getOrdenadorData($user)
    {
        $now = Carbon::now();

        // Estadísticas principales
        $totalCuentas = CuentaCobro::count();
        $cuentasPendientes = CuentaCobro::where('estado', CuentaCobro::ESTADO_PENDIENTE_ORDENADOR)->count();
        $cuentasAprobadas = CuentaCobro::where('estado', CuentaCobro::ESTADO_APROBADA)->count();
        $cuentasPagadas = CuentaCobro::where('estado', CuentaCobro::ESTADO_PAGADA)->count();

        // Valores monetarios
        $valorPendiente = CuentaCobro::where('estado', CuentaCobro::ESTADO_PENDIENTE_ORDENADOR)->sum('valor');
        $valorAprobado = CuentaCobro::where('estado', CuentaCobro::ESTADO_APROBADA)->sum('valor');
        $valorTotal = CuentaCobro::sum('valor');

        // Cuentas pendientes ordenador (últimas 10)
        $cuentasRecientes = CuentaCobro::where('estado', CuentaCobro::ESTADO_PENDIENTE_ORDENADOR)
            ->with(['user'])
            ->latest('created_at')
            ->limit(10)
            ->get();

        // Estadísticas del mes
        $estadisticasMes = [
            'aprobadas_mes' => CuentaCobro::where('estado', CuentaCobro::ESTADO_APROBADA)
                ->whereMonth('updated_at', $now->month)
                ->count(),
            'valor_aprobado_mes' => CuentaCobro::where('estado', CuentaCobro::ESTADO_APROBADA)
                ->whereMonth('updated_at', $now->month)
                ->sum('valor'),
        ];

        // Notificaciones
        $notificaciones = $this->getOrdenadorNotifications();

        return [
            'user' => $user,
            'totalCuentas' => $totalCuentas,
            'cuentasPendientes' => $cuentasPendientes,
            'cuentasAprobadas' => $cuentasAprobadas,
            'cuentasPagadas' => $cuentasPagadas,
            'valorPendiente' => $valorPendiente,
            'valorAprobado' => $valorAprobado,
            'valorTotal' => $valorTotal,
            'cuentasRecientes' => $cuentasRecientes,
            'estadisticasMes' => $estadisticasMes,
            'notificaciones' => $notificaciones,
        ];
    }

    /**
     * Lista todas las cuentas
     */
    public function cuentas(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->hasRole('ordenador_gasto')) {
            abort(403, 'Acceso denegado');
        }

        $query = CuentaCobro::with(['user']);

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('proyecto_servicio', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('mes')) {
            $query->whereMonth('created_at', $request->mes);
        }

        // Ordenar: pendiente_ordenador primero
        $cuentas = $query->orderByRaw("
            CASE 
                WHEN estado = 'pendiente_ordenador' THEN 1
                WHEN estado = 'aprobada' THEN 2
                WHEN estado = 'pagada' THEN 3
                ELSE 4
            END
        ")
        ->orderBy('created_at', 'desc')
        ->paginate(20);

        // Estadísticas
        $estadisticas = [
            'total' => CuentaCobro::count(),
            'pendientes' => CuentaCobro::where('estado', CuentaCobro::ESTADO_PENDIENTE_ORDENADOR)->count(),
            'aprobadas' => CuentaCobro::where('estado', CuentaCobro::ESTADO_APROBADA)->count(),
            'pagadas' => CuentaCobro::where('estado', CuentaCobro::ESTADO_PAGADA)->count(),
            'valor_pendiente' => CuentaCobro::where('estado', CuentaCobro::ESTADO_PENDIENTE_ORDENADOR)->sum('valor'),
        ];

        return view('ordenador-gasto.cuentas', compact('cuentas', 'estadisticas'));
    }

    /**
     * Ver detalle de una cuenta
     */
    public function show($id)
    {
        $user = Auth::user();
        
        if (!$user->hasRole('ordenador_gasto')) {
            abort(403, 'Acceso denegado');
        }

        $cuenta = CuentaCobro::with(['user'])->findOrFail($id);
        
        return view('cuentas-cobro.show', compact('cuenta'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $user = Auth::user();
        
        if (!$user->hasRole('ordenador_gasto')) {
            abort(403, 'Acceso denegado');
        }

        $cuenta = CuentaCobro::with(['user'])->findOrFail($id);
        
        if ($cuenta->estado !== CuentaCobro::ESTADO_PENDIENTE_ORDENADOR) {
            return redirect()->back()
                ->with('error', 'Solo puedes editar cuentas en estado pendiente de ordenador de gasto.');
        }
        
        return view('ordenador-gasto.edit', compact('cuenta'));
    }

    /**
     * Actualizar datos de cuenta (sin cambiar estado)
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!$user->hasRole('ordenador_gasto')) {
            abort(403, 'Acceso denegado');
        }

        $request->validate([
            'proyecto_servicio' => 'required|string|max:255',
            'valor' => 'required|numeric|min:0',
            'fecha_emision' => 'required|date',
            'observaciones' => 'nullable|string|max:1000'
        ]);

        $cuenta = CuentaCobro::findOrFail($id);
        
        if ($cuenta->estado !== CuentaCobro::ESTADO_PENDIENTE_ORDENADOR) {
            return redirect()->back()
                ->with('error', 'Solo puedes editar cuentas en estado pendiente de ordenador de gasto.');
        }

        $cuenta->update([
            'proyecto_servicio' => $request->proyecto_servicio,
            'valor' => $request->valor,
            'fecha_emision' => $request->fecha_emision,
            'observaciones' => $request->observaciones,
        ]);

        return redirect()->route('ordenador-gasto.show', $cuenta->id)
            ->with('success', 'Cuenta de cobro actualizada exitosamente.');
    }

    /**
     * Get ordenador notifications
     */
    private function getOrdenadorNotifications()
    {
        $notifications = [];

        $pendientes = CuentaCobro::where('estado', CuentaCobro::ESTADO_PENDIENTE_ORDENADOR)->count();
        if ($pendientes > 0) {
            $notifications[] = [
                'type' => 'warning',
                'icon' => 'fas fa-clock',
                'title' => 'Pendientes de aprobación',
                'message' => "Tienes {$pendientes} cuenta(s) pendiente(s) de aprobación",
                'action' => route('ordenador-gasto.cuentas', ['estado' => 'pendiente_ordenador']),
                'action_text' => 'Revisar',
                'priority' => 'high'
            ];
        }

        $urgentes = CuentaCobro::where('estado', CuentaCobro::ESTADO_PENDIENTE_ORDENADOR)
            ->where('created_at', '<=', Carbon::now()->subDays(3))
            ->count();

        if ($urgentes > 0) {
            $notifications[] = [
                'type' => 'danger',
                'icon' => 'fas fa-exclamation-triangle',
                'title' => 'Cuentas urgentes',
                'message' => "Hay {$urgentes} cuenta(s) pendiente(s) por más de 3 días",
                'action' => route('ordenador-gasto.cuentas', ['estado' => 'pendiente_ordenador']),
                'action_text' => 'Ver urgentes',
                'priority' => 'urgent'
            ];
        }

        return $notifications;
    }
}
