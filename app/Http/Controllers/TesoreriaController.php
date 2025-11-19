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
        return $this->dashboard();
    }

    /**
     * Dashboard de tesorería
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Verificar que el usuario sea de tesorería
        if (!$user->hasRole('tesoreria')) {
            abort(403, 'Acceso denegado. Solo personal de tesorería puede acceder a esta vista.');
        }

        // Obtener datos principales de tesorería
        $dashboardData = $this->getTesoreriaDataPrivate($user);
        
        return view('tesoreria.dashboard', $dashboardData);
    }

    /**
     * Get all tesorería-specific data
     */
    private function getTesoreriaDataPrivate($user)
    {
        $now = Carbon::now();

        // Estadísticas principales de pagos
        $totalCuentas = CuentaCobro::count();
        $cuentasPorPagar = CuentaCobro::where('estado', CuentaCobro::ESTADO_APROBADA)->count();
        $cuentasPagadas = CuentaCobro::where('estado', CuentaCobro::ESTADO_PAGADA)->count();
        $cuentasPendientes = CuentaCobro::whereIn('estado', [
            CuentaCobro::ESTADO_PENDIENTE_SUPERVISOR,
            CuentaCobro::ESTADO_PENDIENTE_CONTRATACION,
            CuentaCobro::ESTADO_PENDIENTE_TESORERIA,
            CuentaCobro::ESTADO_PENDIENTE_ORDENADOR
        ])->count();

        // Valores monetarios
        $valorPorPagar = CuentaCobro::where('estado', CuentaCobro::ESTADO_APROBADA)->sum('valor');
        $valorPagado = CuentaCobro::where('estado', CuentaCobro::ESTADO_PAGADA)->sum('valor');
        $valorTotal = CuentaCobro::sum('valor');
        $valorPendienteAprobacion = CuentaCobro::whereIn('estado', [
            CuentaCobro::ESTADO_PENDIENTE_SUPERVISOR,
            CuentaCobro::ESTADO_PENDIENTE_CONTRATACION,
            CuentaCobro::ESTADO_PENDIENTE_TESORERIA,
            CuentaCobro::ESTADO_PENDIENTE_ORDENADOR
        ])->sum('valor');

        // Cuentas listas para pago (aprobadas)
        $cuentasListasPago = CuentaCobro::where('estado', CuentaCobro::ESTADO_APROBADA)
            ->with(['user'])
            ->orderBy('created_at', 'asc')
            ->limit(10)
            ->get();
        
        // Pagos realizados recientemente (últimos 10)
        $pagosRecientes = CuentaCobro::where('estado', CuentaCobro::ESTADO_PAGADA)
            ->with(['user'])
            ->latest('updated_at')
            ->limit(10)
            ->get();

        // Cuentas pendientes para mostrar en dashboard (últimas 10)
        $cuentasPendientesPago = CuentaCobro::whereIn('estado', [
            CuentaCobro::ESTADO_PENDIENTE_SUPERVISOR,
            CuentaCobro::ESTADO_PENDIENTE_CONTRATACION,
            CuentaCobro::ESTADO_PENDIENTE_TESORERIA,
            CuentaCobro::ESTADO_PENDIENTE_ORDENADOR
        ])
            ->with(['user'])
            ->latest('created_at')
            ->limit(10)
            ->get();

        // Estadísticas del mes actual
        $estadisticasMes = [
            'pagos_realizados' => CuentaCobro::where('estado', CuentaCobro::ESTADO_PAGADA)
                ->whereMonth('updated_at', $now->month)
                ->count(),
            'valor_pagado_mes' => CuentaCobro::where('estado', CuentaCobro::ESTADO_PAGADA)
                ->whereMonth('updated_at', $now->month)
                ->sum('valor'),
            'cuentas_aprobadas_mes' => CuentaCobro::where('estado', CuentaCobro::ESTADO_APROBADA)
                ->whereMonth('updated_at', $now->month)
                ->count(),
            'valor_por_pagar_mes' => CuentaCobro::where('estado', CuentaCobro::ESTADO_APROBADA)
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
            CuentaCobro::ESTADO_APROBADA, 
            CuentaCobro::ESTADO_PAGADA
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

        // Ordenar por prioridad: pendiente_tesoreria primero, luego aprobadas, pagadas
        $cuentas = $query->orderByRaw("
            CASE 
                WHEN estado = 'pendiente_tesoreria' THEN 1
                WHEN estado = 'aprobada' THEN 2
                WHEN estado = 'pagada' THEN 3
                WHEN estado = 'pendiente_ordenador' THEN 4
                ELSE 5
            END
        ")
        ->orderBy('created_at', 'desc')
        ->paginate(20);

        // Estadísticas para mostrar en la vista
        $estadisticas = [
            'total' => CuentaCobro::count(),
            'aprobadas' => CuentaCobro::where('estado', CuentaCobro::ESTADO_APROBADA)->count(),
            'pagadas' => CuentaCobro::where('estado', CuentaCobro::ESTADO_PAGADA)->count(),
            'valor_total' => CuentaCobro::sum('valor'),
            'valor_por_pagar' => CuentaCobro::where('estado', CuentaCobro::ESTADO_APROBADA)->sum('valor'),
        ];

        return view('tesoreria.cuentas', compact('cuentas', 'estadisticas'));
    }

    /**
     * Ver detalle de una cuenta de cobro
     */
    public function show($id)
    {
        $user = Auth::user();
        
        if (!$user->hasRole('tesoreria')) {
            abort(403, 'Acceso denegado');
        }

        $cuenta = CuentaCobro::with(['user'])->findOrFail($id);
        
        return view('cuentas-cobro.show', compact('cuenta'));
    }

    /**
     * Mostrar formulario de edición de cuenta
     */
    public function edit($id)
    {
        $user = Auth::user();
        
        if (!$user->hasRole('tesoreria')) {
            abort(403, 'Acceso denegado');
        }

        $cuenta = CuentaCobro::with(['user'])->findOrFail($id);
        
        // Solo se pueden editar cuentas en estado pendiente_tesoreria
        if ($cuenta->estado !== CuentaCobro::ESTADO_PENDIENTE_TESORERIA) {
            return redirect()->back()
                ->with('error', 'Solo puedes editar cuentas en estado pendiente de tesorería.');
        }
        
        return view('tesoreria.edit', compact('cuenta'));
    }

    /**
     * Actualizar los datos de una cuenta (sin cambiar estado)
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!$user->hasRole('tesoreria')) {
            abort(403, 'Acceso denegado');
        }

        $request->validate([
            'proyecto_servicio' => 'required|string|max:255',
            'valor' => 'required|numeric|min:0',
            'fecha_emision' => 'required|date',
            'observaciones' => 'nullable|string|max:1000'
        ]);

        $cuenta = CuentaCobro::findOrFail($id);
        
        // Solo se pueden editar cuentas en estado pendiente_tesoreria
        if ($cuenta->estado !== CuentaCobro::ESTADO_PENDIENTE_TESORERIA) {
            return redirect()->back()
                ->with('error', 'Solo puedes editar cuentas en estado pendiente de tesorería.');
        }

        $cuenta->update([
            'proyecto_servicio' => $request->proyecto_servicio,
            'valor' => $request->valor,
            'fecha_emision' => $request->fecha_emision,
            'observaciones' => $request->observaciones,
        ]);

        return redirect()->route('tesoreria.show', $cuenta->id)
            ->with('success', 'Cuenta de cobro actualizada exitosamente.');
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

        $query = CuentaCobro::where('estado', CuentaCobro::ESTADO_PAGADA)
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
            'total_pagos' => CuentaCobro::where('estado', CuentaCobro::ESTADO_PAGADA)->count(),
            'valor_total' => CuentaCobro::where('estado', CuentaCobro::ESTADO_PAGADA)->sum('valor'),
            'valor_total_pagado' => CuentaCobro::where('estado', CuentaCobro::ESTADO_PAGADA)->sum('valor'),
            'pagos_mes_actual' => CuentaCobro::where('estado', CuentaCobro::ESTADO_PAGADA)
                ->whereMonth('updated_at', Carbon::now()->month)
                ->count(),
            'valor_mes_actual' => CuentaCobro::where('estado', CuentaCobro::ESTADO_PAGADA)
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
                    CuentaCobro::ESTADO_PENDIENTE_SUPERVISOR,
                    CuentaCobro::ESTADO_PENDIENTE_CONTRATACION,
                    CuentaCobro::ESTADO_PENDIENTE_TESORERIA,
                    CuentaCobro::ESTADO_PENDIENTE_ORDENADOR
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
                CuentaCobro::ESTADO_PENDIENTE_SUPERVISOR,
                CuentaCobro::ESTADO_PENDIENTE_CONTRATACION,
                CuentaCobro::ESTADO_PENDIENTE_TESORERIA,
                CuentaCobro::ESTADO_PENDIENTE_ORDENADOR
            ])->count(),
            'pendiente_supervisor' => CuentaCobro::where('estado', CuentaCobro::ESTADO_PENDIENTE_SUPERVISOR)->count(),
            'pendiente_contratacion' => CuentaCobro::where('estado', CuentaCobro::ESTADO_PENDIENTE_CONTRATACION)->count(),
            'pendiente_tesoreria' => CuentaCobro::where('estado', CuentaCobro::ESTADO_PENDIENTE_TESORERIA)->count(),
            'pendiente_ordenador' => CuentaCobro::where('estado', CuentaCobro::ESTADO_PENDIENTE_ORDENADOR)->count(),
            'total_aprobadas' => CuentaCobro::where('estado', CuentaCobro::ESTADO_APROBADA)->count(),
            'valor_pendiente' => CuentaCobro::whereIn('estado', [
                CuentaCobro::ESTADO_PENDIENTE_SUPERVISOR,
                CuentaCobro::ESTADO_PENDIENTE_CONTRATACION,
                CuentaCobro::ESTADO_PENDIENTE_TESORERIA,
                CuentaCobro::ESTADO_PENDIENTE_ORDENADOR
            ])->sum('valor'),
            'valor_aprobado' => CuentaCobro::where('estado', CuentaCobro::ESTADO_APROBADA)->sum('valor'),
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
        if ($cuenta->estado !== CuentaCobro::ESTADO_APROBADA) {
            return redirect()->back()
                ->with('error', 'Solo se pueden marcar como pagadas las cuentas que están aprobadas.');
        }

        $cuenta->update([
            'estado' => CuentaCobro::ESTADO_PAGADA,
            'descripcion' => $request->observaciones ? 
                ($cuenta->descripcion . "\n\n--- Observaciones de Pago ---\n" . $request->observaciones) : 
                $cuenta->descripcion
        ]);

        return redirect()->back()
            ->with('success', 'Cuenta marcada como pagada exitosamente.');
    }

    /**
     * Obtener tiempo promedio de revisión
     */
    public function getAverageReviewTime()
    {
        $user = Auth::user();
        
        if (!$user->hasRole('tesoreria')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $avgTime = CuentaCobro::whereNotNull('aprobado_tesoreria_at')
            ->whereNotNull('aprobado_contratacion_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, aprobado_contratacion_at, aprobado_tesoreria_at)) as avg_hours')
            ->first();
        
        return response()->json([
            'success' => true,
            'average_hours' => round($avgTime->avg_hours ?? 0, 1)
        ]);
    }
    
    /**
     * Obtener evolución semanal de pagos
     */
    public function getWeeklyEvolution()
    {
        $user = Auth::user();
        
        if (!$user->hasRole('tesoreria')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $evolution = $this->getPagosPorDia();
        
        return response()->json([
            'success' => true,
            'evolution' => $evolution
        ]);
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
            $dayData = CuentaCobro::where('estado', CuentaCobro::ESTADO_PAGADA)
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
        
        $pagosDelMes = CuentaCobro::where('estado', CuentaCobro::ESTADO_PAGADA)
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
        $listasParaPago = CuentaCobro::where('estado', CuentaCobro::ESTADO_APROBADA)->count();
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

        // Cuentas pendientes tesorería que podrían necesitar revisión
        $pendientesRevision = CuentaCobro::where('estado', CuentaCobro::ESTADO_PENDIENTE_TESORERIA)
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
        $pagosHoy = CuentaCobro::where('estado', CuentaCobro::ESTADO_PAGADA)
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

        $data = $this->getTesoreriaDataPrivate($user);
        
        return response()->json([
            'success' => true,
            'data' => $data,
            'timestamp' => now()->toISOString()
        ]);
    }
}