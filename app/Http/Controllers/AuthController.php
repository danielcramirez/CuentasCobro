<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
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
        $hasSignedDocsColumns = Schema::hasColumns('cuentas_cobro', [
            'documento_1_firmado_path',
            'documento_2_firmado_path',
        ]);
        $hasReturnedStageColumn = Schema::hasColumn('cuentas_cobro', 'returned_stage');
        $roleDirectory = collect();

        if ($user->hasRole('admin')) {
            $roleDirectory = User::with('role')
                ->whereNotNull('role_id')
                ->orderBy('name')
                ->get()
                ->groupBy(fn ($item) => $item->role->name ?? 'sin_rol');
        }
        
        // Datos básicos para todos los usuarios
        $dashboardData = [
            'user' => $user,
            'userRole' => $user->role ? $user->role->name : null,
            'userRoleDescription' => $user->role ? $user->role->description : 'Sin rol asignado',
            'roleDirectory' => $roleDirectory,
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
                'systemRoles' => ['contratista', 'apoyo a la supervisión', 'supervisor', 'admin']
            ]);
        }

        // Datos específicos para apoyo a la supervisión
        if ($user->hasAnyRole(['apoyo a la supervisión', 'apoyo a la supervision', 'apoyo a la supervicion'])) {
            $dashboardData = array_merge($dashboardData, [
                'pendingReviews' => CuentaCobro::whereHas('documentos', function ($query) {
                    $query->where('estado', 'cargado');
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

        if ($user->hasRole('supervisor')) {
            $dashboardData = array_merge($dashboardData, [
                'pendingSignature' => $hasSignedDocsColumns
                    ? CuentaCobro::where('cuenta_status', 'aprobada')
                        ->where('planilla_status', 'aprobada')
                        ->whereNull('documento_1_firmado_path')
                        ->whereNull('documento_2_firmado_path')
                        ->count()
                    : 0,
                'signedToday' => $hasSignedDocsColumns
                    ? CuentaCobro::whereDate('updated_at', now()->toDateString())
                        ->whereNotNull('documento_1_firmado_path')
                        ->whereNotNull('documento_2_firmado_path')
                        ->count()
                    : 0,
            ]);
        }

        // Datos específicos para contratista
        if ($user->hasRole('contratista')) {
            $dashboardData = array_merge($dashboardData, [
                'myCuentasCobro' => CuentaCobro::where('contractor_id', $user->id)->count(),
                'pendingApproval' => CuentaCobro::where('contractor_id', $user->id)
                    ->where(function ($query) {
                        $query->where('cuenta_status', 'pendiente')
                            ->orWhere('planilla_status', 'pendiente');
                    })
                    ->count(),
                'approved' => CuentaCobro::where('contractor_id', $user->id)
                    ->where('cuenta_status', 'aprobada')
                    ->where('planilla_status', 'aprobada')
                    ->count(),
                'rejected' => CuentaCobro::where('contractor_id', $user->id)
                    ->where(function ($query) {
                        $query->where('cuenta_status', 'rechazada')
                            ->orWhere('planilla_status', 'rechazada');
                    })
                    ->count()
            ]);
        }

        // Datos específicos para tesorería
        if ($user->hasAnyRole(['central de cuentas', 'tesoreria'])) {
            $dashboardData = array_merge($dashboardData, [
                'pendingCentral' => CuentaCobro::where('cuenta_status', 'aprobada')
                    ->where('planilla_status', 'aprobada')
                    ->whereNotNull('documento_1_firmado_path')
                    ->whereNotNull('documento_2_firmado_path')
                    ->where('tesoreria_status', 'pendiente')
                    ->count(),
                'returnedByCentral' => $hasReturnedStageColumn
                    ? CuentaCobro::where('returned_stage', 'tesoreria')->count()
                    : 0,
                'sentToFidu' => CuentaCobro::where('fiduprevisora_status', 'en_revision')->count(),
            ]);
        }

        if ($user->hasRole('fiduprevisora')) {
            $dashboardData = array_merge($dashboardData, [
                'pendingFiduDocs' => CuentaCobro::where('fiduprevisora_status', 'en_revision')->count(),
                'readyForPayment' => CuentaCobro::where('fiduprevisora_status', 'en_tramite')->count(),
                'paidAccounts' => CuentaCobro::where('fiduprevisora_status', 'pagado')->count(),
            ]);
        }

        return view('dashboard', $dashboardData);
    }
}
