<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Roles;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

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

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')
            ->with('success', 'Has cerrado sesión exitosamente.');
    }

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
                'pendingReviews' => 0, // Aquí irían las cuentas de cobro pendientes
                'approvedToday' => 0,
                'rejectedToday' => 0
            ]);
        }

        // Datos específicos para contratista
        if ($user->hasRole('contratista')) {
            $dashboardLink = 'dashboard.contratista';
            $dashboardData = array_merge($dashboardData, [
                'myCuentasCobro' => 0, // Aquí irían sus cuentas de cobro
                'pendingApproval' => 0,
                'approved' => 0,
                'rejected' => 0
            ]);
        }

        // Datos específicos para tesorería
        if ($user->hasRole('tesoreria')) {
            $dashboardLink = 'dashboard.other_roles';
            $dashboardData = array_merge($dashboardData, [
                'pendingPayments' => 0,
                'paymentsToday' => 0,
                'totalPaid' => 0
            ]);
        }

        // Datos específicos para ordenador del gasto
        if ($user->hasRole('ordenador_gasto')) {
            $dashboardLink = 'dashboard.other_roles';
            $dashboardData = array_merge($dashboardData, [
                'pendingAuthorizations' => 0,
                'authorizedToday' => 0,
                'budgetStatus' => 0
            ]);
        }

        // Datos específicos para contratación
        if ($user->hasRole('contratacion')) {
            $dashboardLink = 'dashboard.other_roles';
            $dashboardData = array_merge($dashboardData, [
                'activeContracts' => 0,
                'pendingContracts' => 0,
                'totalContractors' => 0
            ]);
        }

        return view($dashboardLink, $dashboardData);
    }
}