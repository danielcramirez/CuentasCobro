<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CuentaCobro;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CuentaCobroController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $query = CuentaCobro::select('cuenta_cobros.*', 'users.name as user_name')
            ->join('users', 'users.id', '=', 'cuenta_cobros.user_id')
            ->orderBy('fecha_emision', 'desc');

        // Si el usuario es contratista, solo ver sus propias cuentas
        if ($user && optional($user->role)->name === 'contratista') {
            $query->where('user_id', $user->id);
        } elseif ($user && optional($user->role)->name === 'admin') {
            // No filtrar, el admin ve todo
        } else {
            // Aquí puedes agregar otras restricciones para otros roles si lo necesitas
        }

        $cuentas = $query->paginate(15);

        return view('cuentasCobro.mostrarCuenta', compact('cuentas'));
    }

    public function create()
    {
        return view('cuentasCobro.crearCuenta');
    }

    public function store(Request $request)
    {

        $user = Auth::user();

        $rutasDocumentos = [];
        $disk = config('filesystems.upload_disk', 'ftp');
        if ($request->hasFile('documentos')) {
            foreach ($request->file('documentos') as $file) {
                $nombre = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                $ext = $file->getClientOriginalExtension();
                $ruta = 'CuentasCobro/' . date('Y-m') . '/' . $user->id . '/' . $nombre . '-' . time() . '.' . $ext;
                $saved = Storage::disk($disk)->put($ruta, fopen($file->getRealPath(), 'r+'));
                if ($saved) {
                    $rutasDocumentos[] = $ruta;
                }
            }
        }

        $request->validate([
            'fecha_emision' => 'required|date',
            'proyecto_servicio' => 'required|string|max:255',
            'valor' => 'required|numeric|min:0',
        ]);

        $cuenta = new CuentaCobro();
        $cuenta->user_id = Auth::id();
        $cuenta->fecha_emision = $request->input('fecha_emision');
        $cuenta->proyecto_servicio = $request->input('proyecto_servicio');
        $cuenta->valor = $request->input('valor');
        $cuenta->estado = CuentaCobro::ESTADO_BORRADOR; // Estado inicial
        $cuenta->save();

        return redirect()->route('cuentas-cobro.mostrar')->with('success', 'Cuenta de cobro creada exitosamente.');
    }

    public function edit($id)
    {
        // Buscar la cuenta de cobro con la relación del usuario
        $cuenta = CuentaCobro::with('user')->findOrFail($id);
        
        // Verificar permisos
        $user = Auth::user();
        $userRole = optional($user->role)->name;
        
        // Los admins y alcaldes pueden editar cualquier cuenta
        $esAdmin = in_array($userRole, ['admin', 'alcalde']);
        
        // Si no es admin y es contratista, solo puede editar sus propias cuentas
        if (!$esAdmin && $userRole === 'contratista' && $cuenta->user_id !== $user->id) {
            return redirect()->route('cuentas-cobro.mostrar')
                ->with('error', 'No tienes permiso para editar esta cuenta de cobro.');
        }
        
        // Verificar si la cuenta es editable (solo borradores y rechazadas)
        // Pero permitir a admin/alcalde editar cualquier estado
        if (!$esAdmin && !$cuenta->esEditable()) {
            return redirect()->route('cuentas-cobro.mostrar')
                ->with('error', 'Esta cuenta de cobro no puede ser editada en su estado actual (' . ucfirst($cuenta->estado) . '). Solo se pueden editar cuentas en estado Borrador o Rechazado.');
        }
        
        return view('cuentasCobro.editarCuenta', compact('cuenta'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $cuenta = CuentaCobro::findOrFail($id);
        $userRole = optional($user->role)->name;
        
        // Verificar permisos
        if ($userRole === 'contratista' && $cuenta->user_id !== $user->id) {
            abort(403, 'No tienes permiso para editar esta cuenta de cobro.');
        }
        
        // Validar estado según rol
        $estadosPermitidos = ['borrador', 'pendiente'];
        if ($userRole === 'supervisor') {
            $estadosPermitidos = array_merge($estadosPermitidos, ['revision', 'aprobado', 'rechazado']);
        }
        if ($userRole === 'ordenador_gasto') {
            $estadosPermitidos = array_merge($estadosPermitidos, ['aprobado', 'rechazado']);
        }
        if ($userRole === 'tesoreria') {
            $estadosPermitidos = array_merge($estadosPermitidos, ['pagado']);
        }
        
        $request->validate([
            'fecha_emision' => 'required|date',
            'proyecto_servicio' => 'required|string|max:255',
            'valor' => 'required|numeric|min:0',
            'estado' => 'required|string|in:' . implode(',', $estadosPermitidos),
        ]);

        $cuenta->fecha_emision = $request->input('fecha_emision');
        $cuenta->proyecto_servicio = $request->input('proyecto_servicio');
        $cuenta->valor = $request->input('valor');
        $cuenta->estado = $request->input('estado');
        $cuenta->save();

        return redirect()->route('cuentas-cobro.mostrar')->with('success', 'Cuenta de cobro actualizada exitosamente.');
    }

    public function destroy(CuentaCobro $cuenta, $id)
    {
        $cuenta = CuentaCobro::findOrFail($id);
        
        // Verificar permisos
        $user = Auth::user();
        $userRole = optional($user->role)->name;
        if ($userRole === 'contratista' && $cuenta->user_id !== $user->id) {
            abort(403, 'No tienes permiso para eliminar esta cuenta de cobro.');
        }
        
        // Solo permitir eliminar cuentas en borrador
        if ($cuenta->estado !== CuentaCobro::ESTADO_BORRADOR) {
            return redirect()->route('cuentas-cobro.mostrar')
                ->with('error', 'Solo se pueden eliminar cuentas de cobro en estado borrador.');
        }
        
        $cuenta->delete();
        return redirect()->route('cuentas-cobro.mostrar')->with('success', 'Cuenta de cobro eliminada exitosamente.');
    }

    /**
     * Cambiar estado de una cuenta de cobro (usado por AJAX)
     */
    public function cambiarEstado(Request $request, $id)
    {
        $user = Auth::user();
        $cuenta = CuentaCobro::findOrFail($id);
        $userRole = optional($user->role)->name;
        
        $nuevoEstado = $request->input('estado');
        $comentario = $request->input('comentario', '');
        
        // Verificar permisos según rol
        $puedeActualizar = false;
        
        if ($userRole === 'contratista' && $cuenta->user_id === $user->id) {
            // Contratista solo puede enviar a pendiente desde borrador
            $puedeActualizar = $cuenta->estado === CuentaCobro::ESTADO_BORRADOR && 
                              $nuevoEstado === CuentaCobro::ESTADO_PENDIENTE;
        } elseif ($userRole === 'supervisor') {
            // Supervisor puede aprobar/rechazar desde pendiente
            $puedeActualizar = $cuenta->estado === CuentaCobro::ESTADO_PENDIENTE && 
                              in_array($nuevoEstado, [CuentaCobro::ESTADO_REVISION, CuentaCobro::ESTADO_RECHAZADO]);
        } elseif ($userRole === 'ordenador_gasto') {
            // Ordenador del gasto puede aprobar desde revisión
            $puedeActualizar = $cuenta->estado === CuentaCobro::ESTADO_REVISION && 
                              in_array($nuevoEstado, [CuentaCobro::ESTADO_APROBADO, CuentaCobro::ESTADO_RECHAZADO]);
        } elseif ($userRole === 'tesoreria') {
            // Tesorería puede marcar como pagado desde aprobado
            $puedeActualizar = $cuenta->estado === CuentaCobro::ESTADO_APROBADO && 
                              $nuevoEstado === CuentaCobro::ESTADO_PAGADO;
        }
        
        if (!$puedeActualizar) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para realizar esta acción.'
            ], 403);
        }
        
        $cuenta->estado = $nuevoEstado;
        $cuenta->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado correctamente.',
            'nuevo_estado' => $cuenta->estado_formateado
        ]);
    }

    /**
     * Obtener estadísticas para el dashboard
     */
    public function estadisticas()
    {
        $user = Auth::user();
        $userRole = optional($user->role)->name;
        $estadisticas = [];
        
        if ($userRole === 'contratista') {
            $estadisticas = [
                'total' => CuentaCobro::where('user_id', $user->id)->count(),
                'borradores' => CuentaCobro::where('user_id', $user->id)->where('estado', CuentaCobro::ESTADO_BORRADOR)->count(),
                'pendientes' => CuentaCobro::where('user_id', $user->id)->where('estado', CuentaCobro::ESTADO_PENDIENTE)->count(),
                'aprobadas' => CuentaCobro::where('user_id', $user->id)->where('estado', CuentaCobro::ESTADO_PAGADO)->count(),
                'valor_total' => CuentaCobro::where('user_id', $user->id)->where('estado', CuentaCobro::ESTADO_PAGADO)->sum('valor')
            ];
        } elseif ($userRole === 'supervisor') {
            $estadisticas = [
                'pendientes_revision' => CuentaCobro::where('estado', CuentaCobro::ESTADO_PENDIENTE)->count(),
                'revisadas_hoy' => CuentaCobro::whereIn('estado', [CuentaCobro::ESTADO_REVISION, CuentaCobro::ESTADO_RECHAZADO])
                    ->whereDate('updated_at', today())->count(),
                'total_sistema' => CuentaCobro::count()
            ];
        }
        
        return response()->json($estadisticas);
    }

}
