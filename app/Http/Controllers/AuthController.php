<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Roles;
use App\Models\CuentaCobro;

class AuthController extends Controller
{
    /**
     * Mostrar el formulario de login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Procesar el login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            return redirect()->intended('/dashboard')
                ->with('success', 'Has iniciado sesión exitosamente.');
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')
            ->with('success', 'Has cerrado sesión exitosamente.');
    }

    /**
     * Mostrar el dashboard según el rol del usuario
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Datos básicos para todos los usuarios
        $dashboardData = [
            'user' => $user,
            'userRole' => $user->role ? $user->role->name : null,
            'userRoleDescription' => $user->role ? $user->role->description : 'Sin rol asignado'
        ];

        // Datos específicos para el admin
        if ($user->hasRole('admin')) {
            $dashboardData = array_merge($dashboardData, [
                'totalUsers' => User::count(),
                'totalRoles' => Roles::count(),
                'usersWithRoles' => User::whereNotNull('role_id')->count(),
                'usersWithoutRoles' => User::whereNull('role_id')->count(),
                'rolesStats' => Roles::withCount('users')->get(),
                'recentUsers' => User::with('role')->latest()->limit(5)->get(),
                'pendingMayorApprovals' => CuentaCobro::where('cuenta_status', 'aprobada')
                    ->where('planilla_status', 'aprobada')
                    ->where('mayor_status', 'pendiente')
                    ->count(),
                'systemRoles' => ['contratista', 'apoyo a la supervisión', 'supervisor', 'admin']
            ]);
        }

        // Datos específicos para apoyo a la supervisión y supervisor
        if ($user->hasAnyRole(['apoyo a la supervisión', 'apoyo a la supervision', 'apoyo a la supervicion', 'supervisor'])) {
            $dashboardData = array_merge($dashboardData, [
                'pendingReviews' => CuentaCobro::where(function ($query) {
                    $query->where('cuenta_status', 'pendiente')
                        ->orWhere('planilla_status', 'pendiente');
                })->count(),
                'approvedToday' => CuentaCobro::whereDate('supervisor_reviewed_at', now()->toDateString())
                    ->where('cuenta_status', 'aprobada')
                    ->where('planilla_status', 'aprobada')
                    ->count(),
                'rejectedToday' => CuentaCobro::whereDate('supervisor_reviewed_at', now()->toDateString())
                    ->where(function ($query) {
                        $query->where('cuenta_status', 'rechazada')
                            ->orWhere('planilla_status', 'rechazada');
                    })->count()
            ]);
        }

        // Datos específicos para contratista
        if ($user->hasRole('contratista')) {
            $dashboardData = array_merge($dashboardData, [
                'myCuentasCobro' => CuentaCobro::where('contractor_id', $user->id)->count(),
                'pendingApproval' => CuentaCobro::where('contractor_id', $user->id)
                    ->where(function ($query) {
                        $query->where('cuenta_status', 'pendiente')
                            ->orWhere('planilla_status', 'pendiente')
                            ->orWhere('mayor_status', 'pendiente');
                    })
                    ->count(),
                'approved' => CuentaCobro::where('contractor_id', $user->id)
                    ->where('mayor_status', 'aprobada')
                    ->count(),
                'rejected' => CuentaCobro::where('contractor_id', $user->id)
                    ->where(function ($query) {
                        $query->where('cuenta_status', 'rechazada')
                            ->orWhere('planilla_status', 'rechazada')
                            ->orWhere('mayor_status', 'rechazada');
                    })
                    ->count()
            ]);
        }

        // Datos específicos para tesorería
        if ($user->hasRole('tesoreria')) {
            $dashboardData = array_merge($dashboardData, [
                'pendingPayments' => 0,
                'paymentsToday' => 0,
                'totalPaid' => 0
            ]);
        }

        // Datos específicos para ordenador del gasto
        if ($user->hasRole('ordenador_gasto')) {
            $dashboardData = array_merge($dashboardData, [
                'pendingAuthorizations' => 0,
                'authorizedToday' => 0,
                'budgetStatus' => 0
            ]);
        }

        // Datos específicos para contratación
        if ($user->hasRole('contratacion')) {
            $dashboardData = array_merge($dashboardData, [
                'activeContracts' => 0,
                'pendingContracts' => 0,
                'totalContractors' => 0
            ]);
        }

        return view('dashboard', $dashboardData);
    }
}
