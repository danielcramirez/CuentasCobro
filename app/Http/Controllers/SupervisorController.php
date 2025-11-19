<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\CuentaCobro;
use App\Models\Roles;
use Carbon\Carbon;

class SupervisorController extends Controller
{
    /**
     * Display supervisor dashboard with real data
     */
    public function index()
    {
        $user = Auth::user();
        
        // Verificar que el usuario sea supervisor
        if (!$user->hasRole('supervisor')) {
            abort(403, 'Acceso denegado. Solo supervisores pueden acceder a esta vista.');
        }

        // Obtener datos principales del supervisor
        $dashboardData = $this->getSupervisorData($user);
        
        return view('supervisor.dashboard', $dashboardData);
    }

    /**
     * Display supervisor dashboard (alias)
     */
    public function dashboard()
    {
        return $this->index();
    }

    /**
     * Get all supervisor-specific data
     */
    private function getSupervisorData($user)
    {
        $now = Carbon::now();

        // Estadísticas principales de cuentas de cobro
        $totalCuentas = CuentaCobro::count();
        $cuentasPendientes = CuentaCobro::whereIn('estado', [
            CuentaCobro::ESTADO_PENDIENTE_SUPERVISOR,
            CuentaCobro::ESTADO_PENDIENTE_CONTRATACION,
            CuentaCobro::ESTADO_PENDIENTE_TESORERIA,
            CuentaCobro::ESTADO_PENDIENTE_ORDENADOR
        ])->count();
        $cuentasRevision = CuentaCobro::where('estado', CuentaCobro::ESTADO_PENDIENTE_SUPERVISOR)->count();
        $cuentasAprobadas = CuentaCobro::where('estado', CuentaCobro::ESTADO_APROBADA)->count();
        $cuentasRechazadas = CuentaCobro::where('estado', CuentaCobro::ESTADO_RECHAZADA)->count();
        $cuentasPagadas = CuentaCobro::where('estado', CuentaCobro::ESTADO_PAGADA)->count();

        // Valores monetarios
        $valorTotalPendiente = CuentaCobro::whereIn('estado', [
            CuentaCobro::ESTADO_PENDIENTE_SUPERVISOR,
            CuentaCobro::ESTADO_PENDIENTE_CONTRATACION,
            CuentaCobro::ESTADO_PENDIENTE_TESORERIA,
            CuentaCobro::ESTADO_PENDIENTE_ORDENADOR,
            CuentaCobro::ESTADO_APROBADA
        ])->sum('valor');
        
        $valorTotalAprobado = CuentaCobro::where('estado', CuentaCobro::ESTADO_APROBADA)->sum('valor');
        $valorTotalPagado = CuentaCobro::where('estado', CuentaCobro::ESTADO_PAGADA)->sum('valor');

        // Cuentas recientes para revisión (últimas 10)
        $cuentasRecientes = CuentaCobro::where('estado', CuentaCobro::ESTADO_PENDIENTE_SUPERVISOR)
            ->with(['user'])
            ->latest()
            ->limit(10)
            ->get();

        // Contratistas activos
        $contratistasActivos = User::whereHas('role', function($query) {
            $query->where('name', 'contratista');
        })
            ->whereHas('cuentasCobro', function($query) use ($now) {
                $query->whereMonth('created_at', $now->month);
            })
            ->count();

        // Estadísticas del mes actual
        $estadisticasMes = [
            'cuentas_mes' => CuentaCobro::whereMonth('created_at', $now->month)->count(),
            'valor_mes' => CuentaCobro::whereMonth('created_at', $now->month)->sum('valor'),
            'aprobadas_mes' => CuentaCobro::where('estado', CuentaCobro::ESTADO_APROBADA)
                ->whereMonth('aprobado_supervisor_at', $now->month)
                ->count(),
            'rechazadas_mes' => CuentaCobro::where('estado', CuentaCobro::ESTADO_RECHAZADA)
                ->whereMonth('rechazado_at', $now->month)
                ->count()
        ];

        // Evolución semanal (últimas 4 semanas)
        $evolucionSemanal = $this->getWeeklyEvolution();

        // Notificaciones dinámicas para supervisor
        $notificaciones = $this->getSupervisorNotifications();

        // Tiempo promedio de revisión
        $tiempoPromedioRevision = $this->getAverageReviewTime();

        return [
            'user' => $user,
            'userRole' => $user->role->name,
            
            // Estadísticas principales
            'totalCuentas' => $totalCuentas,
            'cuentasPendientes' => $cuentasPendientes,
            'cuentasRevision' => $cuentasRevision,
            'cuentasAprobadas' => $cuentasAprobadas,
            'cuentasRechazadas' => $cuentasRechazadas,
            'cuentasPagadas' => $cuentasPagadas,
            
            // Valores monetarios
            'valorTotalPendiente' => $valorTotalPendiente,
            'valorTotalAprobado' => $valorTotalAprobado,
            'valorTotalPagado' => $valorTotalPagado,
            
            // Colecciones de datos
            'cuentasRecientes' => $cuentasRecientes,
            'contratistasActivos' => $contratistasActivos,
            'estadisticasMes' => $estadisticasMes,
            'evolucionSemanal' => $evolucionSemanal,
            'notificaciones' => $notificaciones,
            
            // Datos calculados
            'porcentajeEficiencia' => $totalCuentas > 0 ? round((($cuentasAprobadas + $cuentasPagadas) / $totalCuentas) * 100, 1) : 0,
            'tiempoPromedioRevision' => $tiempoPromedioRevision,
        ];
    }

    /**
     * Lista todas las cuentas de cobro para supervisión
     */
    public function cuentasCobro(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->hasRole('supervisor')) {
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

        // Ordenar por prioridad: pendientes primero, luego por fecha
        $cuentas = $query->orderByRaw("
            CASE 
                WHEN estado = 'pendiente_supervisor' THEN 1
                WHEN estado = 'pendiente_contratacion' THEN 2
                WHEN estado = 'pendiente_tesoreria' THEN 3
                WHEN estado = 'pendiente_ordenador' THEN 4
                WHEN estado = 'rechazada' THEN 5
                ELSE 6
            END
        ")
        ->orderBy('created_at', 'desc')
        ->paginate(20);

        // Preparar información de archivos para cada cuenta
        foreach ($cuentas as $cuenta) {
            $cuenta->archivo_url = null;
            $cuenta->archivo_nombre = 'Sin archivo';
            if ($cuenta->ruta_archivo) {
                $cuenta->archivo_url = route('cuentas-cobro.descargar', $cuenta->id);
                $cuenta->archivo_nombre = basename($cuenta->ruta_archivo);
            }
        }

        return view('supervisor.cuentas-cobro.index', compact('cuentas'));
    }

    /**
     * Mostrar una cuenta de cobro específica para revisión
     */
    public function showCuentaCobro($id)
    {
        $user = Auth::user();
        
        if (!$user->hasRole('supervisor')) {
            abort(403, 'Acceso denegado');
        }

        $cuenta = CuentaCobro::with(['user'])
                            ->findOrFail($id);

        // Preparar archivo
        $cuenta->archivo_url = null;
        $cuenta->archivo_nombre = 'Sin archivo';
        if ($cuenta->ruta_archivo) {
            $cuenta->archivo_url = route('cuentas-cobro.descargar', $cuenta->id);
            $cuenta->archivo_nombre = basename($cuenta->ruta_archivo);
        }

        return view('supervisor.cuentas-cobro.show', compact('cuenta'));
    }

    /**
     * Mostrar formulario de edición de una cuenta de cobro
     */
    public function editCuentaCobro($id)
    {
        $user = Auth::user();
        
        if (!$user->hasRole('supervisor')) {
            abort(403, 'Acceso denegado');
        }

        $cuenta = CuentaCobro::with(['user'])->findOrFail($id);

        // Preparar archivo
        $cuenta->archivo_url = null;
        $cuenta->archivo_nombre = 'Sin archivo';
        if ($cuenta->ruta_archivo) {
            $cuenta->archivo_url = route('cuentas-cobro.descargar', $cuenta->id);
            $cuenta->archivo_nombre = basename($cuenta->ruta_archivo);
        }

        return view('supervisor.cuentas-cobro.edit', compact('cuenta'));
    }

    /**
     * Actualizar una cuenta de cobro (solo campos editables, no estado)
     */
    public function updateCuentaCobro(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!$user->hasRole('supervisor')) {
            abort(403, 'Acceso denegado');
        }

        $request->validate([
            'proyecto_servicio' => 'required|string|max:500',
            'valor' => 'required|numeric|min:0',
            'fecha_emision' => 'required|date',
            'observaciones' => 'nullable|string|max:1000'
        ]);

        $cuenta = CuentaCobro::findOrFail($id);

        $cuenta->update([
            'proyecto_servicio' => $request->proyecto_servicio,
            'valor' => $request->valor,
            'fecha_emision' => $request->fecha_emision,
            'observaciones' => $request->observaciones
        ]);

        return redirect()->route('supervisor.cuentas-cobro.show', $id)
            ->with('success', 'Cuenta de cobro actualizada exitosamente.');
    }

    /**
     * Lista de contratistas bajo supervisión
     */
    public function contratistas(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->hasRole('supervisor')) {
            abort(403, 'Acceso denegado');
        }

        $query = User::whereHas('role', function($q) {
            $q->where('name', 'contratista');
        })->with(['role', 'cuentasCobro']);

        // Filtro de búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $contratistas = $query->get();

        // Agregar estadísticas a cada contratista
        foreach ($contratistas as $contratista) {
            $contratista->total_cuentas = $contratista->cuentasCobro->count();
            $contratista->pendientes = $contratista->cuentasCobro->whereIn('estado', [
                CuentaCobro::ESTADO_PENDIENTE_SUPERVISOR,
                CuentaCobro::ESTADO_PENDIENTE_CONTRATACION,
                CuentaCobro::ESTADO_PENDIENTE_TESORERIA,
                CuentaCobro::ESTADO_PENDIENTE_ORDENADOR
            ])->count();
            $contratista->aprobadas = $contratista->cuentasCobro->where('estado', CuentaCobro::ESTADO_APROBADA)->count();
            $contratista->valor_total = $contratista->cuentasCobro->sum('valor');
            $contratista->ultima_actividad = $contratista->cuentasCobro->max('created_at');
        }

        return view('supervisor.contratistas.index', compact('contratistas'));
    }

    /**
     * Ver detalles de un contratista específico
     */
    public function showContratista($id)
    {
        $user = Auth::user();
        
        if (!$user->hasRole('supervisor')) {
            abort(403, 'Acceso denegado');
        }

        $contratista = User::whereHas('role', function($q) {
            $q->where('name', 'contratista');
        })
        ->with(['role', 'cuentasCobro'])
        ->findOrFail($id);

        // Estadísticas del contratista
        $estadisticas = [
            'total_cuentas' => $contratista->cuentasCobro->count(),
            'pendientes' => $contratista->cuentasCobro->whereIn('estado', [
                CuentaCobro::ESTADO_PENDIENTE_SUPERVISOR,
                CuentaCobro::ESTADO_PENDIENTE_CONTRATACION,
                CuentaCobro::ESTADO_PENDIENTE_TESORERIA,
                CuentaCobro::ESTADO_PENDIENTE_ORDENADOR
            ])->count(),
            'aprobadas' => $contratista->cuentasCobro->where('estado', CuentaCobro::ESTADO_APROBADA)->count(),
            'rechazadas' => $contratista->cuentasCobro->where('estado', CuentaCobro::ESTADO_RECHAZADA)->count(),
            'valor_total' => $contratista->cuentasCobro->sum('valor'),
            'valor_aprobado' => $contratista->cuentasCobro->where('estado', CuentaCobro::ESTADO_APROBADA)->sum('valor'),
        ];

        // Cuentas recientes del contratista
        $cuentasRecientes = $contratista->cuentasCobro()
            ->latest()
            ->limit(10)
            ->get();

        return view('supervisor.contratistas.show', compact('contratista', 'estadisticas', 'cuentasRecientes'));
    }

    /**
     * Get weekly evolution data for charts
     */
    private function getWeeklyEvolution()
    {
        $evolution = [];
        $now = Carbon::now();

        for ($i = 3; $i >= 0; $i--) {
            $startWeek = $now->copy()->subWeeks($i)->startOfWeek();
            $endWeek = $now->copy()->subWeeks($i)->endOfWeek();
            $weekName = 'Semana ' . ($i == 0 ? 'actual' : "del {$startWeek->format('d/m')}");

            $weekData = CuentaCobro::whereBetween('created_at', [$startWeek, $endWeek])
                ->selectRaw('
                    COUNT(*) as total_cuentas,
                    SUM(valor) as total_valor,
                    COUNT(CASE WHEN estado = ? THEN 1 END) as aprobadas,
                    COUNT(CASE WHEN estado = ? THEN 1 END) as rechazadas
                ', [CuentaCobro::ESTADO_APROBADA, CuentaCobro::ESTADO_RECHAZADA])
                ->first();

            $evolution[] = [
                'week' => $weekName,
                'total_cuentas' => $weekData->total_cuentas ?? 0,
                'total_valor' => $weekData->total_valor ?? 0,
                'aprobadas' => $weekData->aprobadas ?? 0,
                'rechazadas' => $weekData->rechazadas ?? 0,
            ];
        }

        return $evolution;
    }

    /**
     * Get supervisor-specific notifications
     */
    private function getSupervisorNotifications()
    {
        $notifications = [];

        // Cuentas urgentes pendientes de revisión
        $urgentes = CuentaCobro::where('estado', CuentaCobro::ESTADO_PENDIENTE_SUPERVISOR)
            ->where('created_at', '<=', Carbon::now()->subDays(3))
            ->count();

        if ($urgentes > 0) {
            $notifications[] = [
                'type' => 'error',
                'icon' => 'fas fa-exclamation-triangle',
                'title' => 'Cuentas urgentes',
                'message' => "Tienes {$urgentes} cuenta(s) pendiente(s) de revisión por más de 3 días",
                'action' => route('supervisor.cuentas-cobro.index', ['estado' => 'pendiente']),
                'action_text' => 'Revisar ahora',
                'priority' => 'high'
            ];
        }

        // Cuentas pendientes de supervisor
        $enRevision = CuentaCobro::where('estado', CuentaCobro::ESTADO_PENDIENTE_SUPERVISOR)->count();
        if ($enRevision > 0) {
            $notifications[] = [
                'type' => 'info',
                'icon' => 'fas fa-search',
                'title' => 'Pendientes de tu aprobación',
                'message' => "Hay {$enRevision} cuenta(s) esperando tu aprobación",
                'action' => route('supervisor.cuentas-cobro.index', ['estado' => 'pendiente_supervisor']),
                'action_text' => 'Ver detalles',
                'priority' => 'medium'
            ];
        }

        // Cuentas aprobadas recientes por supervisor
        $aprobadas = CuentaCobro::whereNotNull('aprobado_supervisor_at')
            ->where('aprobado_supervisor_at', '>=', Carbon::now()->subDays(7))
            ->count();

        if ($aprobadas > 0) {
            $notifications[] = [
                'type' => 'success',
                'icon' => 'fas fa-check-circle',
                'title' => 'Cuentas aprobadas',
                'message' => "Has aprobado {$aprobadas} cuenta(s) en los últimos 7 días",
                'action' => route('supervisor.cuentas-cobro.index'),
                'action_text' => 'Ver listado',
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
     * Calculate average review time
     */
    private function getAverageReviewTime()
    {
        $reviewedCuentas = CuentaCobro::whereNotNull('aprobado_supervisor_at')
            ->orWhereNotNull('rechazado_at')
            ->get();

        if ($reviewedCuentas->isEmpty()) {
            return null;
        }

        $totalHours = 0;
        $count = 0;

        foreach ($reviewedCuentas as $cuenta) {
            $created = Carbon::parse($cuenta->created_at);
            $updated = Carbon::parse($cuenta->updated_at);
            $totalHours += $created->diffInHours($updated);
            $count++;
        }

        return $count > 0 ? round($totalHours / $count, 1) : null;
    }

    /**
     * API endpoint for real-time dashboard updates
     */
    public function getDashboardData()
    {
        $user = Auth::user();
        
        if (!$user->hasRole('supervisor')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = $this->getSupervisorData($user);
        
        return response()->json([
            'success' => true,
            'data' => $data,
            'timestamp' => now()->toISOString()
        ]);
    }
}
