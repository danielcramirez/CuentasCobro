<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CuentaCobro;
use Illuminate\Support\Facades\Auth;

class CuentaCobroController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $query = CuentaCobro::with('user')->orderBy('fecha_emision', 'desc');

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
}
