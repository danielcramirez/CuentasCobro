<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\CuentaCobro;
use App\Models\Roles;
use Carbon\Carbon;

class ContratacionController extends Controller
{
    /**
     * Display contratacion dashboard with real data
     */
    public function index()
    {
        $user = Auth::user();
        
        // Verificar que el usuario sea de contratación
        if (!$user->hasRole('contratacion')) {
            abort(403, 'Acceso denegado. Solo usuarios de contratación pueden acceder a esta vista.');
        }

        // Obtener datos principales de contratación
        $dashboardData = $this->getContratacionData($user);
        
        return view('contratacion.dashboard', $dashboardData);
    }

    /**
     * Display contratacion dashboard (alias)
     */
    public function dashboard()
    {
        return $this->index();
    }

    /**
     * Get all contratacion-specific data
     */
    private function getContratacionData($user)
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
        $cuentasPendientesContratacion = CuentaCobro::where('estado', CuentaCobro::ESTADO_PENDIENTE_CONTRATACION)->count();
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
        $cuentasRecientes = CuentaCobro::where('estado', CuentaCobro::ESTADO_PENDIENTE_CONTRATACION)
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
                ->whereMonth('aprobado_contratacion_at', $now->month)
                ->count(),
            'rechazadas_mes' => CuentaCobro::where('estado', CuentaCobro::ESTADO_RECHAZADA)
                ->whereMonth('rechazado_at', $now->month)
                ->count()
        ];

        // Evolución semanal (últimas 4 semanas)
        $evolucionSemanal = $this->getWeeklyEvolution();

        // Notificaciones dinámicas para contratación
        $notificaciones = $this->getContratacionNotifications();

        // Tiempo promedio de revisión
        $tiempoPromedioRevision = $this->getAverageReviewTime();

        return [
            'user' => $user,
            'userRole' => $user->role->name,
            
            // Estadísticas principales
            'totalCuentas' => $totalCuentas,
            'cuentasPendientes' => $cuentasPendientes,
            'cuentasPendientesContratacion' => $cuentasPendientesContratacion,
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
     * Lista todas las cuentas de cobro para contratación
     */
    public function cuentasCobro(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->hasRole('contratacion')) {
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

        // Ordenar por prioridad: pendientes contratación primero, luego por fecha
        $cuentas = $query->orderByRaw("
            CASE 
                WHEN estado = 'pendiente_contratacion' THEN 1
                WHEN estado = 'pendiente_tesoreria' THEN 2
                WHEN estado = 'pendiente_ordenador' THEN 3
                WHEN estado = 'rechazada' THEN 4
                ELSE 5
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

        return view('contratacion.cuentas-cobro.index', compact('cuentas'));
    }

    /**
     * Mostrar una cuenta de cobro específica para revisión
     */
    public function showCuentaCobro($id)
    {
        $user = Auth::user();
        
        if (!$user->hasRole('contratacion')) {
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

        return view('contratacion.cuentas-cobro.show', compact('cuenta'));
    }

    /**
     * Mostrar formulario de edición de una cuenta de cobro
     */
    public function editCuentaCobro($id)
    {
        $user = Auth::user();
        
        if (!$user->hasRole('contratacion')) {
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

        return view('contratacion.cuentas-cobro.edit', compact('cuenta'));
    }

    /**
     * Actualizar una cuenta de cobro (solo campos editables, no estado)
     */
    public function updateCuentaCobro(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!$user->hasRole('contratacion')) {
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

        return redirect()->route('contratacion.cuentas-cobro.show', $id)
            ->with('success', 'Cuenta de cobro actualizada exitosamente.');
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
     * Get contratacion-specific notifications
     */
    private function getContratacionNotifications()
    {
        $notifications = [];

        // Cuentas urgentes pendientes de revisión
        $urgentes = CuentaCobro::where('estado', CuentaCobro::ESTADO_PENDIENTE_CONTRATACION)
            ->where('aprobado_supervisor_at', '<=', Carbon::now()->subDays(3))
            ->count();

        if ($urgentes > 0) {
            $notifications[] = [
                'type' => 'error',
                'icon' => 'fas fa-exclamation-triangle',
                'title' => 'Cuentas urgentes',
                'message' => "Tienes {$urgentes} cuenta(s) pendiente(s) de revisión por más de 3 días",
                'action' => route('contratacion.cuentas-cobro.index', ['estado' => 'pendiente_contratacion']),
                'action_text' => 'Revisar ahora',
                'priority' => 'high'
            ];
        }

        // Cuentas pendientes de contratación
        $pendientes = CuentaCobro::where('estado', CuentaCobro::ESTADO_PENDIENTE_CONTRATACION)->count();
        if ($pendientes > 0) {
            $notifications[] = [
                'type' => 'info',
                'icon' => 'fas fa-search',
                'title' => 'Pendientes de tu aprobación',
                'message' => "Hay {$pendientes} cuenta(s) esperando tu aprobación",
                'action' => route('contratacion.cuentas-cobro.index', ['estado' => 'pendiente_contratacion']),
                'action_text' => 'Ver detalles',
                'priority' => 'medium'
            ];
        }

        // Cuentas aprobadas recientes por contratación
        $aprobadas = CuentaCobro::whereNotNull('aprobado_contratacion_at')
            ->where('aprobado_contratacion_at', '>=', Carbon::now()->subDays(7))
            ->count();

        if ($aprobadas > 0) {
            $notifications[] = [
                'type' => 'success',
                'icon' => 'fas fa-check-circle',
                'title' => 'Cuentas aprobadas',
                'message' => "Has aprobado {$aprobadas} cuenta(s) en los últimos 7 días",
                'action' => route('contratacion.cuentas-cobro.index'),
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
        $reviewedCuentas = CuentaCobro::whereNotNull('aprobado_contratacion_at')
            ->orWhereNotNull('rechazado_at')
            ->get();

        if ($reviewedCuentas->isEmpty()) {
            return null;
        }

        $totalHours = 0;
        $count = 0;

        foreach ($reviewedCuentas as $cuenta) {
            if ($cuenta->aprobado_supervisor_at && $cuenta->aprobado_contratacion_at) {
                $supervisor = Carbon::parse($cuenta->aprobado_supervisor_at);
                $contratacion = Carbon::parse($cuenta->aprobado_contratacion_at);
                $totalHours += $supervisor->diffInHours($contratacion);
                $count++;
            }
        }

        return $count > 0 ? round($totalHours / $count, 1) : null;
    }

    /**
     * API endpoint for real-time dashboard updates
     */
    public function getDashboardData()
    {
        $user = Auth::user();
        
        if (!$user->hasRole('contratacion')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = $this->getContratacionData($user);
        
        return response()->json([
            'success' => true,
            'data' => $data,
            'timestamp' => now()->toISOString()
        ]);
    }
}
