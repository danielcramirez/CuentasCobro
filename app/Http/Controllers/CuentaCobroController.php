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
        }

        // Puedes ampliar aquí: si es otro rol restringir de otra forma, o dejar ver todo para admins
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
                $ruta = 'ArchivoAlmacenados/' . date('Y/m') . '/' . $user->id . '/' . $nombre . '-' . time() . '.' . $ext;
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
        $cuenta->save();

        return redirect()->route('cuentas-cobro.mostrar')->with('success', 'Cuenta de cobro creada exitosamente.');
    }

    public function edit($id)
    {
        $cuenta = CuentaCobro::findOrFail($id);
        return view('cuentasCobro.editarCuenta', compact('cuenta'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'fecha_emision' => 'required|date',
            'proyecto_servicio' => 'required|string|max:255',
            'valor' => 'required|numeric|min:0',
            'estado' => 'required|string|in:borrador,pendiente,pagado',
        ]);

        $cuenta = CuentaCobro::findOrFail($id);
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
        $cuenta->delete();

        return redirect()->route('cuentas-cobro.mostrar')->with('success', 'Cuenta de cobro eliminada exitosamente.');
    }

}
