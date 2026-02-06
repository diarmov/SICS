<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Dependencia;
use App\Traits\RegistraBitacora;

class DependenciaController extends Controller
{
    use RegistraBitacora;

    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
        // Verificar rol manualmente
        if (!auth()->user()->hasRole(['SuperUsuario', 'AdministradorCS'])) {
            abort(403, 'No autorizado para acceder a la gestión de dependencias.');
        }

        $dependencias = Dependencia::all();
        return view('dependencias.index', compact('dependencias'));
    }

    public function create()
    {
        return view('dependencias.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'dependencia' => 'required|string|max:255',
            'siglas' => 'required|string|max:50',
        ]);

        Dependencia::create([
            'dependencia' => $request->dependencia,
            'siglas' => $request->siglas,
            'activo' => $request->has('activo'),
        ]);

        return redirect()->route('dependencias.index')->with('success', 'Dependencia creada exitosamente.');
    }

    public function edit(Dependencia $dependencia)
    {
        return view('dependencias.edit', compact('dependencia'));
    }

    public function update(Request $request, Dependencia $dependencia)
    {
        $request->validate([
            'dependencia' => 'required|string|max:255',
            'siglas' => 'required|string|max:50',
        ]);

        $dependencia->update([
            'dependencia' => $request->dependencia,
            'siglas' => $request->siglas,
            'activo' => $request->has('activo'),
        ]);

        return redirect()->route('dependencias.index')->with('success', 'Dependencia actualizada exitosamente.');
    }

    public function destroy(Dependencia $dependencia)
    {
        $dependencia->delete();
        return redirect()->route('dependencias.index')->with('success', 'Dependencia eliminada exitosamente.');
    }
}
