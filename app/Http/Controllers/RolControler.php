<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Roles;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RolControler extends Controller
{
    /**
     * Constructor - Middleware de autenticación
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Mostrar lista de todos los roles
     */
    public function index(Roles $role)
    {
        // Solo admins pueden ver todos los roles
        if (!Auth::user()->isAdmin()) {
            return redirect('/dashboard')->with('error', 'No tienes permisos para acceder a esta sección.');
        }

        $user = Auth::user();

        // CORREGIR: usar withCount con la relación correcta
        $roles = Roles::withCount('users')->paginate(10);
        return view('roles.index', compact('roles'));
    }

    /**
     * Mostrar formulario para crear nuevo rol
     */
    public function create()
    {
        if (!Auth::user()->hasRole('alcalde')) {
            return redirect('/dashboard')->with('error', 'No tienes permisos para crear roles.');
        }

        $availablePermissions = $this->getAvailablePermissions();
        return view('roles.create', compact('availablePermissions'));
    }

    /**
     * Almacenar nuevo rol
     */
    public function store(Request $request)
    {
        if (!Auth::user()->hasRole('alcalde')) {
            return redirect('/dashboard')->with('error', 'No tienes permisos para crear roles.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name|regex:/^[a-z_]+$/',
            'description' => 'required|string|max:500',
            'permissions' => 'array'
        ]);

        $role = Roles::create([
            'name' => $request->name,
            'description' => $request->description,
            'permissions' => $request->permissions ?? []
        ]);

        return redirect()->route('roles.index')
            ->with('success', 'Rol creado exitosamente.');
    }

    /**
     * Mostrar detalles de un rol específico
     */
    public function show(Roles $role)
    {
        if (!Auth::user()->isAdmin()) {
            return redirect('/dashboard')->with('error', 'No tienes permisos para ver esta información.');
        }

        // CORREGIR: cargar usuarios con paginación
        $users = $role->users()->paginate(10);
        $availablePermissions = $this->getAvailablePermissions();
        
        return view('roles.show', compact('role', 'users', 'availablePermissions'));
    }

    /**
     * Mostrar página de gestión de usuarios con roles
     */
    public function showUsuarios()
    {
        if (!Auth::user()->hasAnyRole(['alcalde', 'contratacion'])) {
            return redirect('/dashboard')->with('error', 'No tienes permisos para gestionar usuarios.');
        }

        // Obtener todos los usuarios con sus roles
        $usuarios = User::with('role')
            ->orderBy('name')
            ->paginate(15);

        // Obtener todos los roles disponibles
        $roles = Roles::orderBy('name')->get();

        // Estadísticas
        $totalUsuarios = User::count();
        $usuariosSinRol = User::whereNull('role_id')->count();
        $usuariosConRol = User::whereNotNull('role_id')->count();

        return view('roles.usuarios', compact('usuarios', 'roles', 'totalUsuarios', 'usuariosSinRol', 'usuariosConRol'));
    }

    /**
     * Otros métodos del controlador...
     */

    /**
     * Mostrar formulario de edición de un rol
     */
    public function edit(Roles $role)
    {
        if (!Auth::user()->hasRole('alcalde')) {
            return redirect()->route('roles.index')->with('error', 'No tienes permisos para editar roles.');
        }

        return view('roles.edit', compact('role'));
    }

    /**
     * Actualizar un rol existente
     */
    public function update(Request $request, Roles $role)
    {
        if (!Auth::user()->hasRole('alcalde')) {
            return redirect()->route('roles.index')->with('error', 'No tienes permisos para actualizar roles.');
        }

        $isSystemRole = in_array($role->name, ['contratista', 'supervisor', 'alcalde', 'ordenador_gasto', 'tesoreria', 'contratacion']);

        $rules = [
            'permissions' => 'array',
        ];

        if (!$isSystemRole) {
            $rules = array_merge($rules, [
                'name' => 'required|string|max:255|regex:/^[a-z_]+$/|unique:roles,name,' . $role->id,
                'description' => 'required|string|max:500',
            ]);
        } else {
            // Asegurar presencia (vienen como hidden en la vista), pero no se modifican
            $rules = array_merge($rules, [
                'name' => 'required|string',
                'description' => 'required|string',
            ]);
        }

        $validated = $request->validate($rules);

        if ($isSystemRole) {
            // Solo permisos en roles del sistema
            $role->permissions = $request->input('permissions', []);
        } else {
            $role->name = $request->input('name');
            $role->description = $request->input('description');
            $role->permissions = $request->input('permissions', []);
        }

        $role->save();

        return redirect()->route('roles.show', $role->id)->with('success', 'Rol actualizado correctamente.');
    }

    /**
     * Eliminar un rol
     */
    public function destroy(Roles $role)
    {
        if (!Auth::user()->hasRole('alcalde')) {
            return redirect()->route('roles.index')->with('error', 'No tienes permisos para eliminar roles.');
        }

        if (in_array($role->name, ['contratista', 'supervisor', 'alcalde', 'ordenador_gasto', 'tesoreria', 'contratacion'])) {
            return redirect()->route('roles.index')->with('error', 'No se pueden eliminar roles del sistema.');
        }

        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')->with('error', 'No se puede eliminar un rol con usuarios asignados.');
        }

        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Rol eliminado correctamente.');
    }

    /**
     * Asignar un rol a un usuario (AJAX)
     */
    public function assignRole(Request $request)
    {
        if (!Auth::user()->hasAnyRole(['alcalde', 'contratacion'])) {
            return response()->json(['success' => false, 'error' => 'No autorizado'], 403);
        }

        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id',
        ]);

        $user = User::find($data['user_id']);
        $user->role_id = $data['role_id'];
        $user->save();

        return response()->json(['success' => true]);
    }

    /**
     * Remover rol de un usuario (AJAX)
     */
    public function removeRole(Request $request)
    {
        if (!Auth::user()->hasAnyRole(['alcalde', 'contratacion'])) {
            return response()->json(['success' => false, 'error' => 'No autorizado'], 403);
        }

        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::find($data['user_id']);
        $user->role_id = null;
        $user->save();

        return response()->json(['success' => true]);
    }

    /**
     * Obtener usuarios sin rol (AJAX)
     */
    public function getUsersWithoutRole()
    {
        if (!Auth::user()->hasAnyRole(['alcalde', 'contratacion'])) {
            return response()->json(['success' => false, 'error' => 'No autorizado'], 403);
        }

        $users = User::whereNull('role_id')
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        return response()->json(['success' => true, 'data' => $users]);
    }

    /**
     * Obtener permisos disponibles
     */
    private function getAvailablePermissions()
    {
        return [
            // Cuentas de Cobro
            'create_cuenta_cobro',
            'view_cuenta_cobro',
            'view_own_cuenta_cobro',
            'view_all_cuenta_cobro',
            'edit_own_cuenta_cobro',
            'review_cuenta_cobro',
            'approve_cuenta_cobro',
            'reject_cuenta_cobro',
            'final_approval',
            
            // Documentos
            'upload_documents',
            'view_documents',
            
            // Contratos
            'view_contract_info',
            'manage_contracts',
            'contract_validation',
            
            // Pagos
            'authorize_payment',
            'process_payment',
            'generate_checks',
            'bank_transfers',
            'payment_confirmation',
            'generate_payment_orders',
            
            // Presupuesto
            'view_budget',
            'manage_budget',
            
            // Reportes
            'view_reports',
            'financial_reports',
            'view_financial_reports',
            'contract_reports',
            
            // Administración
            'manage_users',
            'manage_contractors',
            'contractor_registration',
            'system_admin',
            
            // Otros
            'add_comments',
            'request_corrections',
            'override_decisions'
        ];
    }
}
