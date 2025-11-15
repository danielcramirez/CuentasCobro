<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\CuentaCobro;
use App\Models\Roles;

class DashboardApiController extends Controller
{
    /**
     * Obtener datos actualizados del dashboard
     */
    public function getDashboardData(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'No autenticado'], 401);
        }

        $data = [
            'user' => [
                'name' => $user->name,
                'role' => $user->role ? $user->role->name : null,
                'role_formatted' => $user->role_name_formatted
            ],
            'timestamp' => now()->toISOString()
        ];

        // Datos específicos según el rol
        switch ($user->role?->name) {
            case 'alcalde':
                $data['stats'] = [
                    'totalUsers' => User::count(),
                    'totalRoles' => Roles::count(),
                    'usersWithRoles' => User::whereNotNull('role_id')->count(),
                    'usersWithoutRoles' => User::whereNull('role_id')->count(),
                    'totalCuentasCobro' => CuentaCobro::count(),
                    'cuentasPendientes' => CuentaCobro::where('estado', CuentaCobro::ESTADO_PENDIENTE)->count()
                ];
                break;

            case 'contratista':
                $data['stats'] = [
                    'myCuentasCobro' => CuentaCobro::where('user_id', $user->id)->count(),
                    'pendingApproval' => CuentaCobro::where('user_id', $user->id)
                        ->where('estado', CuentaCobro::ESTADO_PENDIENTE)->count(),
                    'approved' => CuentaCobro::where('user_id', $user->id)
                        ->where('estado', CuentaCobro::ESTADO_PAGADO)->count(),
                    'totalValor' => CuentaCobro::where('user_id', $user->id)
                        ->where('estado', CuentaCobro::ESTADO_PAGADO)->sum('valor')
                ];
                break;

            case 'supervisor':
                $data['stats'] = [
                    'pendingReviews' => CuentaCobro::where('estado', CuentaCobro::ESTADO_PENDIENTE)->count(),
                    'approvedToday' => CuentaCobro::where('estado', CuentaCobro::ESTADO_PAGADO)
                        ->whereDate('updated_at', today())->count(),
                    'totalCuentasCobro' => CuentaCobro::count()
                ];
                break;

            case 'tesoreria':
                $data['stats'] = [
                    'pendingPayments' => CuentaCobro::where('estado', CuentaCobro::ESTADO_APROBADO)->count(),
                    'paymentsToday' => CuentaCobro::where('estado', CuentaCobro::ESTADO_PAGADO)
                        ->whereDate('updated_at', today())->count(),
                    'totalPaid' => CuentaCobro::where('estado', CuentaCobro::ESTADO_PAGADO)->sum('valor'),
                    'monthlyPayments' => CuentaCobro::where('estado', CuentaCobro::ESTADO_PAGADO)
                        ->whereMonth('updated_at', now()->month)->sum('valor')
                ];
                break;

            default:
                $data['stats'] = [];
        }

        // Notificaciones
        $data['notifications'] = $this->getNotifications($user);

        return response()->json($data);
    }

    /**
     * Obtener notificaciones para el usuario
     */
    private function getNotifications($user)
    {
        $notifications = [];

        switch ($user->role?->name) {
            case 'alcalde':
                $usersWithoutRoles = User::whereNull('role_id')->count();
                if ($usersWithoutRoles > 0) {
                    $notifications[] = [
                        'type' => 'warning',
                        'message' => "{$usersWithoutRoles} usuarios sin rol asignado",
                        'action' => 'roles.index'
                    ];
                }
                break;

            case 'supervisor':
                $pendingReviews = CuentaCobro::where('estado', CuentaCobro::ESTADO_PENDIENTE)->count();
                if ($pendingReviews > 0) {
                    $notifications[] = [
                        'type' => 'info',
                        'message' => "{$pendingReviews} cuentas de cobro pendientes de revisión",
                        'action' => 'cuentas-cobro.mostrar'
                    ];
                }
                break;

            case 'contratista':
                $rejected = CuentaCobro::where('user_id', $user->id)
                    ->where('estado', CuentaCobro::ESTADO_RECHAZADO)->count();
                if ($rejected > 0) {
                    $notifications[] = [
                        'type' => 'error',
                        'message' => "{$rejected} cuentas de cobro rechazadas",
                        'action' => 'cuentas-cobro.mostrar'
                    ];
                }
                break;

            case 'tesoreria':
                $pendingPayments = CuentaCobro::where('estado', CuentaCobro::ESTADO_APROBADO)->count();
                if ($pendingPayments > 0) {
                    $notifications[] = [
                        'type' => 'info',
                        'message' => "{$pendingPayments} pagos pendientes",
                        'action' => 'cuentas-cobro.mostrar'
                    ];
                }
                break;
        }

        return $notifications;
    }

    /**
     * Obtener actividad reciente
     */
    public function getRecentActivity(Request $request)
    {
        $user = Auth::user();
        $limit = $request->get('limit', 10);

        $activity = [];

        if ($user->canViewAllCuentasCobro()) {
            $recentCuentas = CuentaCobro::with('user')
                ->latest()
                ->limit($limit)
                ->get();

            foreach ($recentCuentas as $cuenta) {
                $activity[] = [
                    'type' => 'cuenta_cobro',
                    'message' => "Cuenta de cobro #{$cuenta->id} - {$cuenta->estado_formateado}",
                    'user' => $cuenta->user->name,
                    'created_at' => $cuenta->created_at->diffForHumans(),
                    'valor' => number_format($cuenta->valor, 2)
                ];
            }
        } else {
            $myCuentas = CuentaCobro::where('user_id', $user->id)
                ->latest()
                ->limit($limit)
                ->get();

            foreach ($myCuentas as $cuenta) {
                $activity[] = [
                    'type' => 'mi_cuenta_cobro',
                    'message' => "Mi cuenta de cobro #{$cuenta->id} - {$cuenta->estado_formateado}",
                    'created_at' => $cuenta->created_at->diffForHumans(),
                    'valor' => number_format($cuenta->valor, 2)
                ];
            }
        }

        return response()->json($activity);
    }
}
