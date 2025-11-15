<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\CuentaCobro;
use Carbon\Carbon;

class ContratistaDashboardController extends Controller
{
    /**
     * Display contractor dashboard with real data
     */
    public function index()
    {
        $user = Auth::user();
        
        // Verificar que el usuario sea contratista
        if (!$user->hasRole('contratista')) {
            abort(403, 'Acceso denegado. Solo contratistas pueden acceder a esta vista.');
        }

        // Obtener datos principales del contratista
        $dashboardData = $this->getContractorData($user);
        
        return view('dashboard.contratista', $dashboardData);
    }

    /**
     * Get all contractor-specific data
     */
    private function getContractorData($user)
    {
        $now = Carbon::now();
        $startOfMonth = $now->startOfMonth();
        $startOfYear = $now->startOfYear();

        // Estadísticas principales
        $totalCuentas = $user->cuentasCobro()->count();
        $cuentasPendientes = $user->cuentasCobro()->where('estado', CuentaCobro::ESTADO_PENDIENTE)->count();
        $cuentasAprobadas = $user->cuentasCobro()->where('estado', CuentaCobro::ESTADO_APROBADO)->count();
        $cuentasPagadas = $user->cuentasCobro()->where('estado', CuentaCobro::ESTADO_PAGADO)->count();
        $cuentasRechazadas = $user->cuentasCobro()->where('estado', CuentaCobro::ESTADO_RECHAZADO)->count();
        $cuentasRevision = $user->cuentasCobro()->where('estado', CuentaCobro::ESTADO_REVISION)->count();

        // Valores monetarios
        $totalFacturado = $user->cuentasCobro()->sum('valor');
        $totalPagado = $user->cuentasCobro()->where('estado', CuentaCobro::ESTADO_PAGADO)->sum('valor');
        $valorPendiente = $user->cuentasCobro()
            ->whereIn('estado', [CuentaCobro::ESTADO_PENDIENTE, CuentaCobro::ESTADO_REVISION, CuentaCobro::ESTADO_APROBADO])
            ->sum('valor');

        // Fecha del último envío
        $ultimaFechaEnvio = $user->cuentasCobro()->latest('created_at')->first()?->created_at;

        // Cuentas recientes (últimas 5)
        $cuentasRecientes = $user->cuentasCobro()
            ->with(['user'])
            ->latest()
            ->limit(5)
            ->get();

        // Evolución mensual (últimos 6 meses)
        $evolucionMensual = $this->getMonthlyEvolution($user);

        // Notificaciones dinámicas
        $notificaciones = $this->getContractorNotifications($user);

        // Próximos pagos programados
        $proximosPagos = $user->cuentasCobro()
            ->whereIn('estado', [CuentaCobro::ESTADO_APROBADO, CuentaCobro::ESTADO_REVISION])
            ->orderBy('fecha_emision', 'asc')
            ->limit(5)
            ->get();

        // Estadísticas de este mes
        $estadisticasMes = [
            'cuentas_mes' => $user->cuentasCobro()->whereMonth('created_at', $now->month)->count(),
            'valor_mes' => $user->cuentasCobro()->whereMonth('created_at', $now->month)->sum('valor'),
            'aprobadas_mes' => $user->cuentasCobro()
                ->where('estado', CuentaCobro::ESTADO_PAGADO)
                ->whereMonth('updated_at', $now->month)
                ->count()
        ];

        return [
            'user' => $user,
            'userRole' => $user->role->name,
            
            // Estadísticas principales
            'totalCuentas' => $totalCuentas,
            'cuentasPendientes' => $cuentasPendientes,
            'cuentasAprobadas' => $cuentasAprobadas,
            'cuentasPagadas' => $cuentasPagadas,
            'cuentasRechazadas' => $cuentasRechazadas,
            'cuentasRevision' => $cuentasRevision,
            
            // Valores monetarios
            'totalFacturado' => $totalFacturado,
            'totalPagado' => $totalPagado,
            'valorPendiente' => $valorPendiente,
            
            // Fechas y plazos
            'ultimaFechaEnvio' => $ultimaFechaEnvio,
            
            // Colecciones de datos
            'cuentasRecientes' => $cuentasRecientes,
            'evolucionMensual' => $evolucionMensual,
            'notificaciones' => $notificaciones,
            'proximosPagos' => $proximosPagos,
            'estadisticasMes' => $estadisticasMes,
            
            // Datos calculados
            'porcentajeExito' => $totalCuentas > 0 ? round(($cuentasPagadas / $totalCuentas) * 100, 1) : 0,
            'tiempoPromedioAprobacion' => $this->getAverageApprovalTime($user),
        ];
    }

    /**
     * Get monthly evolution data for charts
     */
    private function getMonthlyEvolution($user)
    {
        $evolution = [];
        $now = Carbon::now();

        for ($i = 5; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);
            $month = $date->format('Y-m');
            $monthName = $date->translatedFormat('M Y');

            $monthData = $user->cuentasCobro()
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->selectRaw('
                    COUNT(*) as total_cuentas,
                    SUM(valor) as total_valor,
                    SUM(CASE WHEN estado = ? THEN valor ELSE 0 END) as valor_pagado,
                    COUNT(CASE WHEN estado = ? THEN 1 END) as cuentas_pagadas
                ', [CuentaCobro::ESTADO_PAGADO, CuentaCobro::ESTADO_PAGADO])
                ->first();

            $evolution[] = [
                'month' => $month,
                'month_name' => $monthName,
                'total_cuentas' => $monthData->total_cuentas ?? 0,
                'total_valor' => $monthData->total_valor ?? 0,
                'valor_pagado' => $monthData->valor_pagado ?? 0,
                'cuentas_pagadas' => $monthData->cuentas_pagadas ?? 0,
            ];
        }

        return $evolution;
    }

    /**
     * Get contractor-specific notifications
     */
    private function getContractorNotifications($user)
    {
        $notifications = [];

        // Cuentas rechazadas
        $rechazadas = $user->cuentasCobro()->where('estado', CuentaCobro::ESTADO_RECHAZADO)->count();
        if ($rechazadas > 0) {
            $notifications[] = [
                'type' => 'error',
                'icon' => 'fas fa-times-circle',
                'title' => 'Cuentas rechazadas',
                'message' => "Tienes {$rechazadas} cuenta(s) de cobro rechazada(s) que requieren corrección",
                'action' => route('cuentas-cobro.mostrar'),
                'action_text' => 'Ver detalles',
                'priority' => 'high'
            ];
        }

        // Cuentas en revisión
        $revision = $user->cuentasCobro()->where('estado', CuentaCobro::ESTADO_REVISION)->count();
        if ($revision > 0) {
            $notifications[] = [
                'type' => 'info',
                'icon' => 'fas fa-clock',
                'title' => 'En proceso de revisión',
                'message' => "Tienes {$revision} cuenta(s) de cobro siendo revisada(s)",
                'action' => route('cuentas-cobro.mostrar'),
                'action_text' => 'Ver estado',
                'priority' => 'medium'
            ];
        }

        // Pagos recientes
        $pagosRecientes = $user->cuentasCobro()
            ->where('estado', CuentaCobro::ESTADO_PAGADO)
            ->where('updated_at', '>=', Carbon::now()->subDays(7))
            ->count();
        
        if ($pagosRecientes > 0) {
            $notifications[] = [
                'type' => 'success',
                'icon' => 'fas fa-check-circle',
                'title' => 'Pagos procesados',
                'message' => "Se han procesado {$pagosRecientes} pago(s) en los últimos 7 días",
                'action' => route('cuentas-cobro.mostrar'),
                'action_text' => 'Ver historial',
                'priority' => 'low'
            ];
        }

        // Recordatorio de documentación
        $sinDocumentos = $user->cuentasCobro()
            ->where('estado', CuentaCobro::ESTADO_BORRADOR)
            ->count();
            
        if ($sinDocumentos > 0) {
            $notifications[] = [
                'type' => 'warning',
                'icon' => 'fas fa-exclamation-triangle',
                'title' => 'Documentación pendiente',
                'message' => "Tienes {$sinDocumentos} cuenta(s) en borrador pendiente(s) de envío",
                'action' => route('cuentas-cobro.mostrar'),
                'action_text' => 'Completar',
                'priority' => 'medium'
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
     * Calculate average approval time
     */
    private function getAverageApprovalTime($user)
    {
        $approvedCuentas = $user->cuentasCobro()
            ->whereIn('estado', [CuentaCobro::ESTADO_PAGADO, CuentaCobro::ESTADO_APROBADO])
            ->whereNotNull('updated_at')
            ->get();

        if ($approvedCuentas->isEmpty()) {
            return null;
        }

        $totalDays = 0;
        $count = 0;

        foreach ($approvedCuentas as $cuenta) {
            $created = Carbon::parse($cuenta->created_at);
            $updated = Carbon::parse($cuenta->updated_at);
            $totalDays += $created->diffInDays($updated);
            $count++;
        }

        return $count > 0 ? round($totalDays / $count, 1) : null;
    }

    /**
     * API endpoint for real-time dashboard updates
     */
    public function getDashboardData()
    {
        $user = Auth::user();
        
        if (!$user->hasRole('contratista')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = $this->getContractorData($user);
        
        return response()->json([
            'success' => true,
            'data' => $data,
            'timestamp' => now()->toISOString()
        ]);
    }
}
