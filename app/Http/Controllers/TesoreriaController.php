<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\CuentaCobro;
use App\Models\Roles;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TesoreriaController extends Controller
{
    /**
     * Display tesorería dashboard with real data
     */
    public function index()
    {
        $user = Auth::user();
        
        // Verificar que el usuario sea de tesorería
        if (!$user->hasRole('tesoreria')) {
            abort(403, 'Acceso denegado. Solo personal de tesorería puede acceder a esta vista.');
        }

        // Obtener datos principales de tesorería
        $dashboardData = $this->getTesoreriaData($user);
        
        return view('tesoreria.dashboard', $dashboardData);
    }

    /**
     * Get all tesorería-specific data
     */
    private function getTesoreriaData($user)
    {
        $now = Carbon::now();

        // Estadísticas principales de pagos
        $totalCuentas = CuentaCobro::count();
        $cuentasPorPagar = CuentaCobro::where('estado', CuentaCobro::ESTADO_APROBADO)->count();
        $cuentasPagadas = CuentaCobro::where('estado', CuentaCobro::ESTADO_PAGADO)->count();
        $cuentasPendientes = CuentaCobro::whereIn('estado', [
            CuentaCobro::ESTADO_PENDIENTE, 
            CuentaCobro::ESTADO_REVISION
        ])->count();

        // Valores monetarios
        $valorPorPagar = CuentaCobro::where('estado', CuentaCobro::ESTADO_APROBADO)->sum('valor');
        $valorPagado = CuentaCobro::where('estado', CuentaCobro::ESTADO_PAGADO)->sum('valor');
        $valorTotal = CuentaCobro::sum('valor');
        $valorPendienteAprobacion = CuentaCobro::whereIn('estado', [
            CuentaCobro::ESTADO_PENDIENTE, 
            CuentaCobro::ESTADO_REVISION
        ])->sum('valor');

        // Cuentas listas para pago (aprobadas)
        $cuentasListasPago = CuentaCobro::where('estado', CuentaCobro::ESTADO_APROBADO)
            ->with(['user'])
            ->orderBy('created_at', 'asc')
            ->limit(10)
            ->get();        // Pagos realizados recientemente (últimos 10)
        $pagosRecientes = CuentaCobro::where('estado', CuentaCobro::ESTADO_PAGADO)
            ->with(['user'])
            ->latest('updated_at')
            ->limit(10)
            ->get();

        // Cuentas pendientes para mostrar en dashboard (últimas 10)
        $cuentasPendientesPago = CuentaCobro::whereIn('estado', [
            CuentaCobro::ESTADO_PENDIENTE, 
            CuentaCobro::ESTADO_REVISION
        ])
            ->with(['user'])
            ->latest('created_at')
            ->limit(10)
            ->get();

        // Estadísticas del mes actual
        $estadisticasMes = [
            'pagos_realizados' => CuentaCobro::where('estado', CuentaCobro::ESTADO_PAGADO)
                ->whereMonth('updated_at', $now->month)
                ->count(),
            'valor_pagado_mes' => CuentaCobro::where('estado', CuentaCobro::ESTADO_PAGADO)
                ->whereMonth('updated_at', $now->month)
                ->sum('valor'),
            'cuentas_aprobadas_mes' => CuentaCobro::where('estado', CuentaCobro::ESTADO_APROBADO)
                ->whereMonth('updated_at', $now->month)
                ->count(),
            'valor_por_pagar_mes' => CuentaCobro::where('estado', CuentaCobro::ESTADO_APROBADO)
                ->whereMonth('updated_at', $now->month)
                ->sum('valor')
        ];

        // Evolución de pagos por día (últimos 30 días)
        $evolucionPagos = $this->getPagosPorDia();

        // Notificaciones para tesorería
        $notificaciones = $this->getTesoreriaNotifications();

        // Promedio diario de pagos del mes
        $promedioDiario = $this->getPromedioDiarioMes();

        // Eficiencia de pagos (% de cuentas aprobadas que ya fueron pagadas)
        $totalAprobadas = CuentaCobro::whereIn('estado', [
            CuentaCobro::ESTADO_APROBADO, 
            CuentaCobro::ESTADO_PAGADO
        ])->count();
        $eficienciaPagos = $totalAprobadas > 0 ? round(($cuentasPagadas / $totalAprobadas) * 100, 1) : 0;

        return [
            'user' => $user,
            'userRole' => $user->role->name,
            
            // Estadísticas principales
            'totalCuentas' => $totalCuentas,
            'cuentasPorPagar' => $cuentasPorPagar,
            'cuentasPagadas' => $cuentasPagadas,
            'cuentasPendientes' => $cuentasPendientes,
            
            // Valores monetarios
            'valorPorPagar' => $valorPorPagar,
            'valorPagado' => $valorPagado,
            'valorTotal' => $valorTotal,
            'valorPendienteAprobacion' => $valorPendienteAprobacion,
              // Colecciones de datos
            'cuentasListasPago' => $cuentasListasPago,
            'pagosRecientes' => $pagosRecientes,
            'cuentasPendientesPago' => $cuentasPendientesPago,
            'estadisticasMes' => $estadisticasMes,
            'evolucionPagos' => $evolucionPagos,
            'notificaciones' => $notificaciones,
            
            // Datos calculados
            'eficienciaPagos' => $eficienciaPagos,
            'promedioDiario' => $promedioDiario,
        ];
    }

    /**
     * Lista todas las cuentas para gestión de tesorería
     */
    public function cuentas(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->hasRole('tesoreria')) {
            abort(403, 'Acceso denegado');
        }

        $query = CuentaCobro::with(['user']);

        // Filtros de búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('proyecto_servicio', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%")
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

        if ($request->filled('valor_min')) {
            $query->where('valor', '>=', $request->valor_min);
        }

        if ($request->filled('valor_max')) {
            $query->where('valor', '<=', $request->valor_max);
        }

        // Ordenar por prioridad: aprobadas primero, luego por valor descendente
        $cuentas = $query->orderByRaw("
            CASE 
                WHEN estado = 'aprobado' THEN 1
                WHEN estado = 'pagado' THEN 2
                WHEN estado = 'pendiente' THEN 3
                ELSE 4
            END
        ")
        ->orderBy('valor', 'desc')
        ->paginate(20);

        // Estadísticas para mostrar en la vista
        $estadisticas = [
            'total' => CuentaCobro::count(),
            'aprobadas' => CuentaCobro::where('estado', CuentaCobro::ESTADO_APROBADO)->count(),
            'pagadas' => CuentaCobro::where('estado', CuentaCobro::ESTADO_PAGADO)->count(),
            'valor_total' => CuentaCobro::sum('valor'),
            'valor_por_pagar' => CuentaCobro::where('estado', CuentaCobro::ESTADO_APROBADO)->sum('valor'),
        ];

        return view('tesoreria.cuentas', compact('cuentas', 'estadisticas'));
    }

    /**
     * Ver historial de pagos realizados
     */
    public function pagosRealizados(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->hasRole('tesoreria')) {
            abort(403, 'Acceso denegado');
        }

        $query = CuentaCobro::where('estado', CuentaCobro::ESTADO_PAGADO)
                            ->with(['user']);

        // Filtros de búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('proyecto_servicio', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('mes')) {
            $query->whereMonth('updated_at', $request->mes);
        }

        if ($request->filled('año')) {
            $query->whereYear('updated_at', $request->año);
        }

        $pagos = $query->orderBy('updated_at', 'desc')
                      ->paginate(15);        // Estadísticas de pagos
        $estadisticasPagos = [
            'total_pagos' => CuentaCobro::where('estado', CuentaCobro::ESTADO_PAGADO)->count(),
            'valor_total' => CuentaCobro::where('estado', CuentaCobro::ESTADO_PAGADO)->sum('valor'),
            'valor_total_pagado' => CuentaCobro::where('estado', CuentaCobro::ESTADO_PAGADO)->sum('valor'),
            'pagos_mes_actual' => CuentaCobro::where('estado', CuentaCobro::ESTADO_PAGADO)
                ->whereMonth('updated_at', Carbon::now()->month)
                ->count(),
            'valor_mes_actual' => CuentaCobro::where('estado', CuentaCobro::ESTADO_PAGADO)
                ->whereMonth('updated_at', Carbon::now()->month)
                ->sum('valor'),
        ];

        return view('tesoreria.pagos-realizados', compact('pagos', 'estadisticasPagos'));
    }

    /**
     * Ver cuentas pendientes de aprobación para revisión de tesorería
     */
    public function pendientes(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->hasRole('tesoreria')) {
            abort(403, 'Acceso denegado');
        }

        $query = CuentaCobro::whereIn('estado', [
                    CuentaCobro::ESTADO_PENDIENTE, 
                    CuentaCobro::ESTADO_REVISION
                ])
                ->with(['user']);

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
        }        $cuentasPendientes = $query->orderBy('created_at', 'asc')
                                  ->paginate(20);        // Estadísticas para mostrar en la vista
        $estadisticasPendientes = [
            'total_pendientes' => CuentaCobro::whereIn('estado', [
                CuentaCobro::ESTADO_PENDIENTE, 
                CuentaCobro::ESTADO_REVISION
            ])->count(),
            'solo_pendientes' => CuentaCobro::where('estado', CuentaCobro::ESTADO_PENDIENTE)->count(),
            'en_revision' => CuentaCobro::where('estado', CuentaCobro::ESTADO_REVISION)->count(),
            'total_aprobadas' => CuentaCobro::where('estado', CuentaCobro::ESTADO_APROBADO)->count(),
            'valor_pendiente' => CuentaCobro::whereIn('estado', [
                CuentaCobro::ESTADO_PENDIENTE, 
                CuentaCobro::ESTADO_REVISION
            ])->sum('valor'),
            'valor_aprobado' => CuentaCobro::where('estado', CuentaCobro::ESTADO_APROBADO)->sum('valor'),
        ];

        return view('tesoreria.cuentas-pendientes', [
            'pendientes' => $cuentasPendientes,
            'estadisticasPendientes' => $estadisticasPendientes
        ]);
    }

    /**
     * Marcar una cuenta como pagada
     */
    public function marcarPagada(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!$user->hasRole('tesoreria')) {
            abort(403, 'Acceso denegado');
        }

        $request->validate([
            'observaciones' => 'nullable|string|max:500'
        ]);

        $cuenta = CuentaCobro::findOrFail($id);
        
        // Solo se pueden marcar como pagadas las cuentas aprobadas
        if ($cuenta->estado !== CuentaCobro::ESTADO_APROBADO) {
            return redirect()->back()
                ->with('error', 'Solo se pueden marcar como pagadas las cuentas que están aprobadas.');
        }

        $cuenta->update([
            'estado' => CuentaCobro::ESTADO_PAGADO,
            'descripcion' => $request->observaciones ? 
                ($cuenta->descripcion . "\n\n--- Observaciones de Pago ---\n" . $request->observaciones) : 
                $cuenta->descripcion
        ]);

        return redirect()->back()
            ->with('success', 'Cuenta marcada como pagada exitosamente.');
    }

    /**
     * Actualizar estado de cuenta (aprobar/rechazar desde tesorería)
     */
    public function actualizarEstado(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!$user->hasRole('tesoreria')) {
            abort(403, 'Acceso denegado');
        }

        $request->validate([
            'estado' => 'required|in:aprobado,rechazado',
            'observaciones' => 'nullable|string|max:1000'
        ]);

        $cuenta = CuentaCobro::findOrFail($id);
        
        // Solo se pueden actualizar cuentas pendientes o en revisión
        if (!in_array($cuenta->estado, [CuentaCobro::ESTADO_PENDIENTE, CuentaCobro::ESTADO_REVISION])) {
            return redirect()->back()
                ->with('error', 'Esta cuenta no puede ser modificada en su estado actual.');
        }

        $cuenta->update([
            'estado' => $request->estado,
            'descripcion' => $request->observaciones ? 
                ($cuenta->descripcion . "\n\n--- Observaciones de Tesorería ---\n" . $request->observaciones) : 
                $cuenta->descripcion
        ]);

        $mensaje = $request->estado === 'aprobado' ? 
            'Cuenta aprobada para pago.' : 
            'Cuenta rechazada con observaciones.';

        return redirect()->back()
            ->with('success', $mensaje);
    }

    /**
     * Get evolution of payments per day (last 30 days)
     */
    private function getPagosPorDia()
    {
        $evolution = [];
        $now = Carbon::now();

        for ($i = 29; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i);
            $dayData = CuentaCobro::where('estado', CuentaCobro::ESTADO_PAGADO)
                ->whereDate('updated_at', $date)
                ->selectRaw('COUNT(*) as count, SUM(valor) as total')
                ->first();

            $evolution[] = [
                'date' => $date->format('Y-m-d'),
                'day' => $date->format('d/m'),
                'count' => $dayData->count ?? 0,
                'total' => $dayData->total ?? 0,
            ];
        }

        return $evolution;
    }

    /**
     * Get average daily payments for current month
     */
    private function getPromedioDiarioMes()
    {
        $now = Carbon::now();
        $daysInMonth = $now->daysInMonth;
        $currentDay = $now->day;
        
        $pagosDelMes = CuentaCobro::where('estado', CuentaCobro::ESTADO_PAGADO)
            ->whereMonth('updated_at', $now->month)
            ->whereYear('updated_at', $now->year)
            ->count();

        return $currentDay > 0 ? round($pagosDelMes / $currentDay, 1) : 0;
    }

    /**
     * Get tesorería-specific notifications
     */
    private function getTesoreriaNotifications()
    {
        $notifications = [];

        // Cuentas aprobadas listas para pago
        $listasParaPago = CuentaCobro::where('estado', CuentaCobro::ESTADO_APROBADO)->count();
        if ($listasParaPago > 0) {
            $notifications[] = [
                'type' => 'info',
                'icon' => 'fas fa-money-check-alt',
                'title' => 'Listas para pago',
                'message' => "Tienes {$listasParaPago} cuenta(s) aprobada(s) lista(s) para pago",
                'action' => route('tesoreria.cuentas', ['estado' => 'aprobado']),
                'action_text' => 'Procesar pagos',
                'priority' => 'high'
            ];
        }

        // Cuentas pendientes que podrían necesitar revisión
        $pendientesRevision = CuentaCobro::whereIn('estado', [
                CuentaCobro::ESTADO_PENDIENTE, 
                CuentaCobro::ESTADO_REVISION
            ])
            ->where('created_at', '<=', Carbon::now()->subDays(7))
            ->count();

        if ($pendientesRevision > 0) {
            $notifications[] = [
                'type' => 'warning',
                'icon' => 'fas fa-clock',
                'title' => 'Revisión pendiente',
                'message' => "Hay {$pendientesRevision} cuenta(s) pendiente(s) por más de 7 días",
                'action' => route('tesoreria.pendientes'),
                'action_text' => 'Revisar',
                'priority' => 'medium'
            ];
        }

        // Pagos realizados hoy
        $pagosHoy = CuentaCobro::where('estado', CuentaCobro::ESTADO_PAGADO)
            ->whereDate('updated_at', Carbon::today())
            ->count();

        if ($pagosHoy > 0) {
            $notifications[] = [
                'type' => 'success',
                'icon' => 'fas fa-check-circle',
                'title' => 'Pagos de hoy',
                'message' => "Se han procesado {$pagosHoy} pago(s) el día de hoy",
                'action' => route('tesoreria.pagos-realizados'),
                'action_text' => 'Ver historial',
                'priority' => 'low'
            ];
        }

        // Ordenar por prioridad
        $priorityOrder = ['high' => 1, 'medium' => 2, 'low' => 3];
        usort($notifications, function($a, $b) use ($priorityOrder) {
            return $priorityOrder[$a['priority']] - $priorityOrder[$b['priority']];
        });

        return $notifications;
    }

    /**
     * API endpoint for real-time dashboard updates
     */
    public function getDashboardData()
    {
        $user = Auth::user();
        
        if (!$user->hasRole('tesoreria')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = $this->getTesoreriaData($user);
        
        return response()->json([
            'success' => true,
            'data' => $data,
            'timestamp' => now()->toISOString()
        ]);
    }
}