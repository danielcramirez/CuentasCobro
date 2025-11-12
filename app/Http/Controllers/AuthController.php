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

        $dashboardLink = 'dashboard.dashboard';

        // Datos específicos para el alcalde
        if ($user->hasRole('alcalde')) {
            $dashboardLink = 'dashboard.alcalde';
            $dashboardData = array_merge($dashboardData, [
                'totalUsers' => User::count(),
                'totalRoles' => Roles::count(),
                'usersWithRoles' => User::whereNotNull('role_id')->count(),
                'usersWithoutRoles' => User::whereNull('role_id')->count(),
                'rolesStats' => Roles::withCount('users')->get(),
                'recentUsers' => User::with('role')->latest()->limit(5)->get(),
                'systemRoles' => ['contratista', 'supervisor', 'alcalde', 'ordenador_gasto', 'tesoreria', 'contratacion']
            ]);
        }

        // Datos específicos para supervisor
        if ($user->hasRole('supervisor')) {
            $dashboardLink = 'dashboard.supervisor';
            $dashboardData = array_merge($dashboardData, [
                'pendingReviews' => CuentaCobro::where('estado', CuentaCobro::ESTADO_PENDIENTE)->count(),
                'approvedToday' => CuentaCobro::where('estado', CuentaCobro::ESTADO_PAGADO)
                    ->whereDate('updated_at', today())->count(),
                'rejectedToday' => CuentaCobro::where('estado', 'rechazado')
                    ->whereDate('updated_at', today())->count(),
                'totalCuentasCobro' => CuentaCobro::count(),
                'recentCuentasCobro' => CuentaCobro::with('user')->latest()->limit(5)->get()
            ]);
        }

        // Datos específicos para contratista
        if ($user->hasRole('contratista')) {
            // Redirigir a dashboard específico de contratista
            return redirect()->route('contratista.dashboard');
        }

        // Datos específicos para tesorería
        if ($user->hasRole('tesoreria')) {
            $dashboardLink = 'dashboard.other_roles';
            $dashboardData = array_merge($dashboardData, [
                'pendingPayments' => CuentaCobro::where('estado', 'aprobado')->count(),
                'paymentsToday' => CuentaCobro::where('estado', CuentaCobro::ESTADO_PAGADO)
                    ->whereDate('updated_at', today())->count(),
                'totalPaid' => CuentaCobro::where('estado', CuentaCobro::ESTADO_PAGADO)->sum('valor'),
                'monthlyPayments' => CuentaCobro::where('estado', CuentaCobro::ESTADO_PAGADO)
                    ->whereMonth('updated_at', now()->month)->sum('valor'),
                'recentPayments' => CuentaCobro::with('user')
                    ->where('estado', CuentaCobro::ESTADO_PAGADO)
                    ->latest()->limit(5)->get()
            ]);
        }

        // Datos específicos para ordenador del gasto
        if ($user->hasRole('ordenador_gasto')) {
            $dashboardLink = 'dashboard.other_roles';
            $dashboardData = array_merge($dashboardData, [
                'pendingAuthorizations' => CuentaCobro::where('estado', 'revision')->count(),
                'authorizedToday' => CuentaCobro::where('estado', 'aprobado')
                    ->whereDate('updated_at', today())->count(),
                'budgetStatus' => CuentaCobro::where('estado', 'aprobado')->sum('valor'),
                'monthlyBudget' => CuentaCobro::where('estado', 'aprobado')
                    ->whereMonth('updated_at', now()->month)->sum('valor'),
                'recentAuthorizations' => CuentaCobro::with('user')
                    ->whereIn('estado', ['revision', 'aprobado'])
                    ->latest()->limit(5)->get()
            ]);
        }

        // Datos específicos para contratación
        if ($user->hasRole('contratacion')) {
            $dashboardLink = 'dashboard.other_roles';
            $dashboardData = array_merge($dashboardData, [
                'activeContracts' => User::whereHas('role', function($query) {
                    $query->where('name', 'contratista');
                })->count(),
                'pendingContracts' => CuentaCobro::where('estado', CuentaCobro::ESTADO_BORRADOR)->count(),
                'totalContractors' => User::whereHas('role', function($query) {
                    $query->where('name', 'contratista');
                })->count(),
                'monthlyContracts' => CuentaCobro::whereMonth('created_at', now()->month)->count(),
                'recentContracts' => CuentaCobro::with('user')
                    ->latest()->limit(5)->get()
            ]);
        }

        return view($dashboardLink, $dashboardData);
    }
}