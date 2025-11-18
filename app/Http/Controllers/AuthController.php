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

        $dashboardLink = 'shared.dashboard-base';

        // Datos específicos para el alcalde
        if ($user->hasRole('alcalde')) {
            $dashboardLink = 'alcalde.dashboard';
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
            // Redirigir a dashboard específico de supervisor
            return redirect()->route('supervisor.dashboard');
        }

        // Datos específicos para contratista
        if ($user->hasRole('contratista')) {
            // Redirigir a dashboard específico de contratista
            return redirect()->route('contratista.dashboard');
        }

        // Datos específicos para tesorería
        if ($user->hasRole('tesoreria')) {
            // Redirigir a dashboard específico de tesorería
            return redirect()->route('tesoreria.dashboard');
        }

        // Datos específicos para ordenador del gasto
        if ($user->hasRole('ordenador_gasto')) {
            $dashboardLink = 'shared.other-roles';
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
            $dashboardLink = 'shared.other-roles';
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