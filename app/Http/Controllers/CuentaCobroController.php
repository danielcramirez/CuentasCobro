<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CuentaCobro;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\NotificationService;

class CuentaCobroController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
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

        // Preparar información de archivos para cada cuenta
        foreach ($cuentas as $cuenta) {
            $cuenta->archivo_url = null;
            $cuenta->archivo_nombre = 'Sin archivo';
            if ($cuenta->ruta_archivo) {
                // Para FTP, generamos una URL de descarga a través del controlador
                $cuenta->archivo_url = route('cuentas-cobro.descargar', $cuenta->id);
                $cuenta->archivo_nombre = basename($cuenta->ruta_archivo);
            }
        }

        return view('cuentas-cobro.index', compact('cuentas'))->with([
            'userRole' => $user->role ? $user->role->name : null
        ]);
    }

    public function create()
    {
        $user = Auth::user();
        return view('cuentas-cobro.create')->with([
            'userRole' => $user->role ? $user->role->name : null
        ]);
    }

    public function store(Request $request)
    {

        $user = Auth::user();

        $primerArchivoRuta = null;
        $disk = config('filesystems.upload_disk', 'ftp');
        if ($request->hasFile('documentos')) {
            foreach ($request->file('documentos') as $file) {
                $nombre = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                $ext = $file->getClientOriginalExtension();
                $ruta = 'CuentasCobro/' . date('Y-m') . '/' . $user->id . '/' . $nombre . '-' . time() . '.' . $ext;
                $saved = Storage::disk($disk)->put($ruta, fopen($file->getRealPath(), 'r+'));
                if ($saved && !$primerArchivoRuta) {
                    $primerArchivoRuta = $ruta;
                }
            }
        }

        $request->validate([
            'fecha_emision' => 'required|date',
            'proyecto_servicio' => 'required|string|max:255',
            'valor' => 'required|numeric|min:0',
            'descripcion' => 'nullable|string',
        ]);

        $cuenta = new CuentaCobro();
        $cuenta->user_id = Auth::id();
        $cuenta->fecha_emision = $request->input('fecha_emision');
        $cuenta->proyecto_servicio = $request->input('proyecto_servicio');
        $cuenta->valor = $request->input('valor');
        $cuenta->descripcion = $request->input('descripcion');
        $cuenta->ruta_archivo = $primerArchivoRuta;
        $cuenta->estado = CuentaCobro::ESTADO_PENDIENTE_SUPERVISOR; // Enviar directamente a supervisor
        $cuenta->save();

        // Notificar a supervisores
        $this->notificationService->notificarNuevaCuentaCobro($cuenta);

        return redirect()->route('cuentas-cobro.mostrar')->with('success', 'Cuenta de cobro creada y enviada a revisión del supervisor.');
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
        
        return view('cuentas-cobro.edit', compact('cuenta'))->with([
            'userRole' => $userRole
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $cuenta = CuentaCobro::findOrFail($id);
        $userRole = optional($user->role)->name;
        
        // Verificar permisos según rol y estado de la cuenta
        if ($userRole === 'contratista') {
            // Contratista solo puede editar sus propias cuentas
            if ($cuenta->user_id !== $user->id) {
                abort(403, 'No tienes permiso para editar esta cuenta de cobro.');
            }
            // Solo puede editar si está en borrador o rechazada
            if (!in_array($cuenta->estado, ['borrador', 'rechazada'])) {
                abort(403, 'Solo puedes editar cuentas en estado borrador o rechazada.');
            }
        }
        
        // Validación dinámica del archivo
        $archivoRules = [];
        if ($request->input('borrar_archivo') == '1' || !$cuenta->archivo_adjunto) {
            // Si se va a borrar el archivo o no existe, el nuevo archivo es requerido
            $archivoRules = ['required', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240'];
        } else {
            // Si ya existe y no se va a borrar, el archivo es opcional
            $archivoRules = ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240'];
        }
        
        $request->validate([
            'fecha_emision' => 'required|date',
            'proyecto_servicio' => 'required|string|max:255',
            'valor' => 'required|numeric|min:0',
            'archivo_adjunto' => $archivoRules,
            'borrar_archivo' => 'nullable|in:0,1',
        ]);

        // Actualizar datos editables
        $cuenta->fecha_emision = $request->input('fecha_emision');
        $cuenta->proyecto_servicio = $request->input('proyecto_servicio');
        $cuenta->valor = $request->input('valor');
        
        // Manejar archivo adjunto
        if ($request->input('borrar_archivo') == '1' && $cuenta->archivo_adjunto) {
            // Borrar archivo anterior del storage
            if (Storage::disk('public')->exists($cuenta->archivo_adjunto)) {
                Storage::disk('public')->delete($cuenta->archivo_adjunto);
            }
            $cuenta->archivo_adjunto = null;
        }
        
        // Si se sube un nuevo archivo (reemplazo)
        if ($request->hasFile('archivo_adjunto')) {
            // Borrar archivo anterior si existe
            if ($cuenta->archivo_adjunto && Storage::disk('public')->exists($cuenta->archivo_adjunto)) {
                Storage::disk('public')->delete($cuenta->archivo_adjunto);
            }
            
            // Guardar nuevo archivo
            $file = $request->file('archivo_adjunto');
            $fileName = time() . '_' . $user->id . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('cuentas-cobro', $fileName, 'public');
            $cuenta->archivo_adjunto = $filePath;
        }
        
        $cuenta->save();

        return redirect()->route('cuentas-cobro.mostrar')->with('success', 'Cuenta de cobro actualizada exitosamente.');
    }

    public function eliminarArchivo($id)
    {
        $user = Auth::user();
        $cuenta = CuentaCobro::findOrFail($id);
        $userRole = optional($user->role)->name;
        
        // Verificar permisos
        if ($userRole === 'contratista' && $cuenta->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'No tienes permiso para eliminar este archivo.'], 403);
        }
        
        // Solo permitir eliminar si está en borrador o rechazada
        if (!in_array($cuenta->estado, ['borrador', 'rechazada'])) {
            return response()->json(['success' => false, 'message' => 'Solo puedes eliminar archivos de cuentas en estado borrador o rechazada.'], 403);
        }
        
        // Verificar que exista archivo
        if (!$cuenta->archivo_adjunto) {
            return response()->json(['success' => false, 'message' => 'No hay archivo para eliminar.'], 404);
        }
        
        // Eliminar archivo del storage
        if (Storage::disk('public')->exists($cuenta->archivo_adjunto)) {
            Storage::disk('public')->delete($cuenta->archivo_adjunto);
        }
        
        // Actualizar base de datos
        $cuenta->archivo_adjunto = null;
        $cuenta->save();
        
        return response()->json(['success' => true, 'message' => 'Archivo eliminado exitosamente.']);
    }

    public function show($id)
    {
        $cuenta = CuentaCobro::with('user')->findOrFail($id);
        $user = Auth::user();
        $userRole = optional($user->role)->name;
        
        // Verificar permisos - los usuarios solo pueden ver sus propias cuentas (excepto admin)
        if ($userRole === 'contratista' && $cuenta->user_id !== $user->id) {
            return redirect()->route('cuentas-cobro.mostrar')
                ->with('error', 'No tienes permiso para ver esta cuenta de cobro.');
        }
        
        return view('cuentas-cobro.show', compact('cuenta'))->with([
            'userRole' => $userRole
        ]);
    }

    public function confirmarEliminacion($id)
    {
        $cuenta = CuentaCobro::with('user')->findOrFail($id);
        $user = Auth::user();
        $userRole = optional($user->role)->name;
        
        // Verificar permisos - solo contratistas pueden eliminar sus propias cuentas
        if ($userRole === 'contratista' && $cuenta->user_id !== $user->id) {
            return redirect()->route('cuentas-cobro.mostrar')
                ->with('error', 'No tienes permiso para eliminar esta cuenta de cobro.');
        }
        
        // Solo permitir eliminar cuentas en borrador
        if ($cuenta->estado !== CuentaCobro::ESTADO_BORRADOR) {
            return redirect()->route('cuentas-cobro.mostrar')
                ->with('error', 'Solo se pueden eliminar cuentas de cobro en estado borrador.');
        }
        
        return view('cuentas-cobro.delete', compact('cuenta'))->with([
            'userRole' => $userRole
        ]);
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

    /**
     * Descargar el archivo de una cuenta de cobro
     */
    public function descargar($id)
    {
        $cuenta = CuentaCobro::findOrFail($id);
        
        // Verificar permisos
        $user = Auth::user();
        $userRole = optional($user->role)->name;
        
        // Admins pueden descargar cualquier archivo
        // Contratistas solo pueden descargar sus propios archivos
        if ($userRole === 'contratista' && $cuenta->user_id !== $user->id) {
            abort(403, 'No tienes permiso para descargar este archivo.');
        }
        
        if (!$cuenta->ruta_archivo) {
            abort(404, 'No hay archivo asociado a esta cuenta de cobro.');
        }
        
        $disk = config('filesystems.upload_disk', 'ftp');
        
        if (!Storage::disk($disk)->exists($cuenta->ruta_archivo)) {
            abort(404, 'El archivo no existe.');
        }
        
        $filename = basename($cuenta->ruta_archivo);
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        // Determinar mime type básico por extensión
        $mimeTypes = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
        ];
        $mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';
        
        return response()->stream(function() use ($disk, $cuenta) {
            $stream = Storage::disk($disk)->readStream($cuenta->ruta_archivo);
            fpassthru($stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    // ==========================================
    // MÉTODOS ESPECÍFICOS PARA CONTRATISTA
    // ==========================================

    /**
     * Vista index específica para contratistas
     */
    public function indexContratista(Request $request)
    {
        $user = Auth::user();
        
        // Verificar que sea contratista
        if (!$user->hasRole('contratista')) {
            abort(403, 'Acceso denegado');
        }

        $query = CuentaCobro::where('user_id', $user->id)->with('user');

        // Filtros de búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('proyecto_servicio', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Ordenar por fecha más reciente
        $cuentas = $query->orderBy('created_at', 'desc')->paginate(15);

        // Preparar información de archivos
        foreach ($cuentas as $cuenta) {
            $cuenta->archivo_url = null;
            $cuenta->archivo_nombre = 'Sin archivo';
            if ($cuenta->ruta_archivo) {
                $cuenta->archivo_url = route('cuentas-cobro.descargar', $cuenta->id);
                $cuenta->archivo_nombre = basename($cuenta->ruta_archivo);
            }
        }

        return view('roles.contratista.cuentas.index', compact('cuentas'));
    }

    /**
     * Formulario de creación para contratistas
     */
    public function createContratista()
    {
        $user = Auth::user();
        
        if (!$user->hasRole('contratista')) {
            abort(403, 'Acceso denegado');
        }

        return view('roles.contratista.cuentas.crear');
    }

    /**
     * Almacenar cuenta de cobro del contratista
     */
    public function storeContratista(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->hasRole('contratista')) {
            abort(403, 'Acceso denegado');
        }

        $request->validate([
            'fecha_emision' => 'required|date',
            'proyecto_servicio' => 'required|string|max:255',
            'valor' => 'required|numeric|min:0',
            'descripcion' => 'nullable|string',
            'documentos.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png'
        ]);

        // Manejar archivos
        $primerArchivoRuta = null;
        $disk = config('filesystems.upload_disk', 'ftp');
        
        if ($request->hasFile('documentos')) {
            foreach ($request->file('documentos') as $file) {
                $nombre = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                $ext = $file->getClientOriginalExtension();
                $ruta = 'CuentasCobro/' . date('Y-m') . '/' . $user->id . '/' . $nombre . '-' . time() . '.' . $ext;
                
                $saved = Storage::disk($disk)->put($ruta, fopen($file->getRealPath(), 'r+'));
                if ($saved && !$primerArchivoRuta) {
                    $primerArchivoRuta = $ruta;
                }
            }
        }

        // Crear la cuenta de cobro
        $cuenta = CuentaCobro::create([
            'user_id' => $user->id,
            'fecha_emision' => $request->fecha_emision,
            'proyecto_servicio' => $request->proyecto_servicio,
            'valor' => $request->valor,
            'descripcion' => $request->descripcion,
            'ruta_archivo' => $primerArchivoRuta,
            'estado' => CuentaCobro::ESTADO_BORRADOR
        ]);

        return redirect()->route('contratista.cuentas.index')
            ->with('success', 'Cuenta de cobro creada exitosamente como borrador. Puedes editarla antes de enviarla.');
    }

    /**
     * Mostrar cuenta específica del contratista
     */
    public function showContratista($id)
    {
        $user = Auth::user();
        
        if (!$user->hasRole('contratista')) {
            abort(403, 'Acceso denegado');
        }

        $cuenta = CuentaCobro::where('id', $id)
                            ->where('user_id', $user->id)
                            ->with('user')
                            ->firstOrFail();

        // Preparar archivo
        $cuenta->archivo_url = null;
        $cuenta->archivo_nombre = 'Sin archivo';
        if ($cuenta->ruta_archivo) {
            $cuenta->archivo_url = route('cuentas-cobro.descargar', $cuenta->id);
            $cuenta->archivo_nombre = basename($cuenta->ruta_archivo);
        }

        return view('roles.contratista.cuentas.ver', compact('cuenta'));
    }

    /**
     * Formulario de edición para contratistas
     */
    public function editContratista($id)
    {
        $user = Auth::user();
        
        if (!$user->hasRole('contratista')) {
            abort(403, 'Acceso denegado');
        }

        $cuenta = CuentaCobro::where('id', $id)
                            ->where('user_id', $user->id)
                            ->firstOrFail();

        // Solo se pueden editar borradores y rechazadas
        if (!in_array($cuenta->estado, [CuentaCobro::ESTADO_BORRADOR, CuentaCobro::ESTADO_RECHAZADO])) {
            return redirect()->route('contratista.cuentas.index')
                ->with('error', 'Esta cuenta no puede ser editada en su estado actual: ' . ucfirst($cuenta->estado));
        }

        return view('roles.contratista.cuentas.editar', compact('cuenta'));
    }

    /**
     * Actualizar cuenta del contratista
     */
    public function updateContratista(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!$user->hasRole('contratista')) {
            abort(403, 'Acceso denegado');
        }

        $cuenta = CuentaCobro::where('id', $id)
                            ->where('user_id', $user->id)
                            ->firstOrFail();

        if (!in_array($cuenta->estado, [CuentaCobro::ESTADO_BORRADOR, CuentaCobro::ESTADO_RECHAZADO])) {
            return redirect()->route('contratista.cuentas.index')
                ->with('error', 'Esta cuenta no puede ser editada en su estado actual.');
        }

        $request->validate([
            'fecha_emision' => 'required|date',
            'proyecto_servicio' => 'required|string|max:255',
            'valor' => 'required|numeric|min:0',
            'descripcion' => 'nullable|string',
            'documentos.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png'
        ]);

        // Manejar nuevos archivos
        if ($request->hasFile('documentos')) {
            $disk = config('filesystems.upload_disk', 'ftp');
            
            foreach ($request->file('documentos') as $file) {
                $nombre = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                $ext = $file->getClientOriginalExtension();
                $ruta = 'CuentasCobro/' . date('Y-m') . '/' . $user->id . '/' . $nombre . '-' . time() . '.' . $ext;
                
                $saved = Storage::disk($disk)->put($ruta, fopen($file->getRealPath(), 'r+'));
                if ($saved) {
                    $cuenta->ruta_archivo = $ruta;
                }
            }
        }

        // Actualizar datos
        $cuenta->update([
            'fecha_emision' => $request->fecha_emision,
            'proyecto_servicio' => $request->proyecto_servicio,
            'valor' => $request->valor,
            'descripcion' => $request->descripcion,
        ]);

        return redirect()->route('contratista.cuentas.index')
            ->with('success', 'Cuenta de cobro actualizada exitosamente.');
    }

    /**
     * Confirmación de eliminación para contratistas
     */
    public function deleteContratista($id)
    {
        $user = Auth::user();
        
        if (!$user->hasRole('contratista')) {
            abort(403, 'Acceso denegado');
        }

        $cuenta = CuentaCobro::where('id', $id)
                            ->where('user_id', $user->id)
                            ->firstOrFail();

        // Solo se pueden eliminar borradores
        if ($cuenta->estado !== CuentaCobro::ESTADO_BORRADOR) {
            return redirect()->route('contratista.cuentas.index')
                ->with('error', 'Solo se pueden eliminar cuentas en estado borrador.');
        }

        return view('roles.contratista.cuentas.eliminar', compact('cuenta'));
    }

    /**
     * Eliminar cuenta del contratista
     */
    public function destroyContratista($id)
    {
        $user = Auth::user();
        
        if (!$user->hasRole('contratista')) {
            abort(403, 'Acceso denegado');
        }

        $cuenta = CuentaCobro::where('id', $id)
                            ->where('user_id', $user->id)
                            ->firstOrFail();

        if ($cuenta->estado !== CuentaCobro::ESTADO_BORRADOR) {
            return redirect()->route('contratista.cuentas.index')
                ->with('error', 'Solo se pueden eliminar cuentas en estado borrador.');
        }

        // Eliminar archivo si existe
        if ($cuenta->ruta_archivo) {
            $disk = config('filesystems.upload_disk', 'ftp');
            Storage::disk($disk)->delete($cuenta->ruta_archivo);
        }

        $cuenta->delete();

        return redirect()->route('contratista.cuentas.index')
            ->with('success', 'Cuenta de cobro eliminada exitosamente.');
    }

    /**
     * Enviar cuenta de cobro a revisión (Contratista)
     */
    public function enviarRevision($id)
    {
        $cuenta = CuentaCobro::findOrFail($id);
        $user = Auth::user();

        // Verificar que sea el propietario y esté en borrador
        if ($cuenta->user_id !== $user->id || $cuenta->estado !== CuentaCobro::ESTADO_BORRADOR) {
            return redirect()->back()->with('error', 'No puedes enviar esta cuenta a revisión.');
        }

        // Cambiar estado a pendiente supervisor
        $cuenta->estado = CuentaCobro::ESTADO_PENDIENTE_SUPERVISOR;
        $cuenta->save();

        // Notificar a supervisores
        $this->notificationService->notificarNuevaCuentaCobro($cuenta);

        return redirect()->back()->with('success', 'Cuenta de cobro enviada a revisión del supervisor.');
    }

    /**
     * Aprobar cuenta de cobro (Supervisor)
     */
    public function aprobarSupervisor($id)
    {
        $cuenta = CuentaCobro::findOrFail($id);
        $user = Auth::user();

        if (!$user->hasAnyRole(['supervisor'])) {
            return redirect()->back()->with('error', 'No tienes permisos para esta acción.');
        }

        if ($cuenta->estado !== CuentaCobro::ESTADO_PENDIENTE_SUPERVISOR) {
            return redirect()->back()->with('error', 'Esta cuenta no está en estado de revisión del supervisor.');
        }

        $cuenta->estado = CuentaCobro::ESTADO_PENDIENTE_CONTRATACION;
        $cuenta->aprobado_supervisor_at = now();
        $cuenta->aprobado_supervisor_by = $user->id;
        $cuenta->save();

        // Notificar a contratación
        $this->notificationService->notificarContratacion($cuenta);

        return redirect()->back()->with('success', 'Cuenta aprobada. Se ha notificado a contratación.');
    }

    /**
     * Aprobar cuenta de cobro (Contratación)
     */
    public function aprobarContratacion($id)
    {
        $cuenta = CuentaCobro::findOrFail($id);
        $user = Auth::user();

        if (!$user->hasAnyRole(['contratacion'])) {
            return redirect()->back()->with('error', 'No tienes permisos para esta acción.');
        }

        if ($cuenta->estado !== CuentaCobro::ESTADO_PENDIENTE_CONTRATACION) {
            return redirect()->back()->with('error', 'Esta cuenta no está en estado de revisión de contratación.');
        }

        $cuenta->estado = CuentaCobro::ESTADO_PENDIENTE_TESORERIA;
        $cuenta->aprobado_contratacion_at = now();
        $cuenta->aprobado_contratacion_by = $user->id;
        $cuenta->save();

        // Notificar a tesorería
        $this->notificationService->notificarTesoreria($cuenta);

        return redirect()->back()->with('success', 'Cuenta aprobada. Se ha notificado a tesorería.');
    }

    /**
     * Aprobar cuenta de cobro (Tesorería)
     */
    public function aprobarTesoreria($id)
    {
        $cuenta = CuentaCobro::findOrFail($id);
        $user = Auth::user();

        if (!$user->hasAnyRole(['tesoreria'])) {
            return redirect()->back()->with('error', 'No tienes permisos para esta acción.');
        }

        if ($cuenta->estado !== CuentaCobro::ESTADO_PENDIENTE_TESORERIA) {
            return redirect()->back()->with('error', 'Esta cuenta no está en estado de revisión de tesorería.');
        }

        $cuenta->estado = CuentaCobro::ESTADO_PENDIENTE_ORDENADOR;
        $cuenta->aprobado_tesoreria_at = now();
        $cuenta->aprobado_tesoreria_by = $user->id;
        $cuenta->save();

        // Notificar al ordenador del gasto
        $this->notificationService->notificarOrdenadorGasto($cuenta);

        return redirect()->back()->with('success', 'Cuenta aprobada. Se ha notificado al ordenador del gasto.');
    }

    /**
     * Aprobación final (Ordenador del Gasto)
     */
    public function aprobarOrdenador($id)
    {
        $cuenta = CuentaCobro::findOrFail($id);
        $user = Auth::user();

        if (!$user->hasAnyRole(['ordenador_gasto', 'alcalde'])) {
            return redirect()->back()->with('error', 'No tienes permisos para esta acción.');
        }

        if ($cuenta->estado !== CuentaCobro::ESTADO_PENDIENTE_ORDENADOR) {
            return redirect()->back()->with('error', 'Esta cuenta no está en estado de aprobación final.');
        }

        $cuenta->estado = CuentaCobro::ESTADO_APROBADA;
        $cuenta->aprobado_ordenador_at = now();
        $cuenta->aprobado_ordenador_by = $user->id;
        $cuenta->save();

        // Notificar al contratista
        $this->notificationService->notificarContratista($cuenta, 'aprobada');

        return redirect()->back()->with('success', 'Cuenta aprobada completamente. El contratista ha sido notificado.');
    }

    /**
     * Rechazar cuenta de cobro (cualquier rol en el flujo)
     */
    public function rechazar(Request $request, $id)
    {
        $request->validate([
            'comentarios' => 'required|string|max:500'
        ]);

        $cuenta = CuentaCobro::findOrFail($id);
        $user = Auth::user();

        // Verificar permisos según el estado actual
        $puedeRechazar = false;
        if ($cuenta->estado === CuentaCobro::ESTADO_PENDIENTE_SUPERVISOR && $user->hasAnyRole(['supervisor'])) {
            $puedeRechazar = true;
        } elseif ($cuenta->estado === CuentaCobro::ESTADO_PENDIENTE_CONTRATACION && $user->hasAnyRole(['contratacion'])) {
            $puedeRechazar = true;
        } elseif ($cuenta->estado === CuentaCobro::ESTADO_PENDIENTE_TESORERIA && $user->hasAnyRole(['tesoreria'])) {
            $puedeRechazar = true;
        } elseif ($cuenta->estado === CuentaCobro::ESTADO_PENDIENTE_ORDENADOR && $user->hasAnyRole(['ordenador_gasto', 'alcalde'])) {
            $puedeRechazar = true;
        }

        if (!$puedeRechazar) {
            return redirect()->back()->with('error', 'No tienes permisos para rechazar esta cuenta.');
        }

        $cuenta->estado = CuentaCobro::ESTADO_RECHAZADA;
        $cuenta->comentarios_rechazo = $request->input('comentarios');
        $cuenta->rechazado_by = $user->id;
        $cuenta->rechazado_at = now();
        $cuenta->save();

        // Notificar al contratista
        $this->notificationService->notificarContratista($cuenta, 'rechazada', $request->input('comentarios'));

        return redirect()->back()->with('success', 'Cuenta rechazada. El contratista ha sido notificado.');
    }

    /**
     * Marcar cuenta como pagada (Tesorería)
     */
    public function marcarPagada($id)
    {
        $cuenta = CuentaCobro::findOrFail($id);
        $user = Auth::user();

        if (!$user->hasAnyRole(['tesoreria'])) {
            return redirect()->back()->with('error', 'No tienes permisos para esta acción.');
        }

        if ($cuenta->estado !== CuentaCobro::ESTADO_APROBADA) {
            return redirect()->back()->with('error', 'Esta cuenta no está aprobada para pago.');
        }

        $cuenta->estado = CuentaCobro::ESTADO_PAGADA;
        $cuenta->save();

        return redirect()->back()->with('success', 'Cuenta marcada como pagada.');
    }

}
