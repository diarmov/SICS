<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Bitacora;
use App\User;

class BitacoraController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
        // Verificar el rol manualmente
        if (!auth()->user()->hasRole(['SuperUsuario', 'AdministradorCS'])) {
            abort(403, 'No autorizado para acceder a la bitácora.');
        }

        // Cambiar get() por paginate(20)
        $bitacoras = Bitacora::with('user')
            ->latest()
            ->paginate(20); // 20 registros por página

        $users = User::all();
        return view('bitacora.index', compact('bitacoras', 'users'));
    }

    public function filter(Request $request)
    {
        // Verificar el rol manualmente
        if (!auth()->user()->hasRole(['SuperUsuario', 'AdministradorCS'])) {
            abort(403, 'No autorizado para acceder a la bitácora.');
        }

        $query = Bitacora::with('user');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('modulo')) {
            $query->where('modulo', 'like', '%' . $request->modulo . '%');
        }

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }

        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }

        // Cambiar get() por paginate(20) también en el filtro
        $bitacoras = $query->latest()->paginate(20);

        // Mantener los parámetros de filtro en la paginación
        $bitacoras->appends($request->all());

        $users = User::all();

        return view('bitacora.index', compact('bitacoras', 'users'));
    }
}
