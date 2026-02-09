<?php

namespace App\Http\Controllers;

use App\TipoApoyo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\RegistraBitacora;

class TipoApoyoController extends Controller
{
    use RegistraBitacora;

    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
        // Solo SuperUsuario y AdministradorCS pueden ver los tipos de apoyo
        if (!auth()->user()->hasRole(['SuperUsuario', 'AdministradorCS'])) {
            abort(403, 'No autorizado para acceder a los tipos de apoyo.');
        }

        $tiposApoyo = TipoApoyo::orderBy('nombre')->paginate(20);
        return view('apoyos.index', compact('tiposApoyo'));
    }

    public function create()
    {
        if (!auth()->user()->hasRole(['SuperUsuario', 'AdministradorCS'])) {
            abort(403, 'No autorizado para crear tipos de apoyo.');
        }

        return view('apoyos.create');
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasRole(['SuperUsuario', 'AdministradorCS'])) {
            abort(403, 'No autorizado para crear tipos de apoyo.');
        }

        $request->validate([
            'nombre' => 'required|string|max:255|unique:tipos_apoyo,nombre',
            'fecha_alta' => 'required|date',
            'activo' => 'boolean',
        ]);

        $tipoApoyo = new TipoApoyo();
        $tipoApoyo->nombre = $request->nombre;
        $tipoApoyo->fecha_alta = $request->fecha_alta;
        $tipoApoyo->activo = $request->has('activo');
        $tipoApoyo->save();

        // Registrar en bitácora
        $this->registrarBitacora(
            'Creación',
            'Tipos_Apoyo',
            "Tipo de apoyo creado: {$tipoApoyo->nombre}"
        );

        return redirect()->route('tipos-apoyo.index')
            ->with('success', 'Tipo de apoyo creado exitosamente.');
    }

    public function show(TipoApoyo $tipoApoyo)
    {
        if (!auth()->user()->hasRole(['SuperUsuario', 'AdministradorCS'])) {
            abort(403, 'No autorizado para ver tipos de apoyo.');
        }

        $tipoApoyo->load('programas');
        return view('apoyos.show', compact('tipoApoyo'));
    }

    public function edit(TipoApoyo $tipoApoyo)
    {
        if (!auth()->user()->hasRole(['SuperUsuario', 'AdministradorCS'])) {
            abort(403, 'No autorizado para editar tipos de apoyo.');
        }

        return view('apoyos.edit', compact('tipoApoyo'));
    }

    public function update(Request $request, TipoApoyo $tipoApoyo)
    {
        if (!auth()->user()->hasRole(['SuperUsuario', 'AdministradorCS'])) {
            abort(403, 'No autorizado para actualizar tipos de apoyo.');
        }

        $request->validate([
            'nombre' => 'required|string|max:255|unique:tipos_apoyo,nombre,' . $tipoApoyo->id,
            'fecha_alta' => 'required|date',
            'activo' => 'boolean',
        ]);

        $oldNombre = $tipoApoyo->nombre;

        $tipoApoyo->nombre = $request->nombre;
        $tipoApoyo->fecha_alta = $request->fecha_alta;
        $tipoApoyo->activo = $request->has('activo');
        $tipoApoyo->save();

        // Registrar en bitácora
        $this->registrarBitacora(
            'Actualización',
            'Tipos_Apoyo',
            "Tipo de apoyo actualizado: {$oldNombre} -> {$tipoApoyo->nombre}"
        );

        return redirect()->route('tipos-apoyo.index')
            ->with('success', 'Tipo de apoyo actualizado exitosamente.');
    }

    public function destroy(TipoApoyo $tipoApoyo)
    {
        if (!auth()->user()->hasRole(['SuperUsuario', 'AdministradorCS'])) {
            abort(403, 'No autorizado para eliminar tipos de apoyo.');
        }

        // Verificar si hay programas usando este tipo de apoyo
        if ($tipoApoyo->programas()->count() > 0) {
            return redirect()->route('tipos-apoyo.index')
                ->with('error', 'No se puede eliminar el tipo de apoyo porque está siendo utilizado por uno o más programas.');
        }

        $nombre = $tipoApoyo->nombre;
        $tipoApoyo->delete();

        // Registrar en bitácora
        $this->registrarBitacora(
            'Eliminación',
            'Tipos_Apoyo',
            "Tipo de apoyo eliminado: {$nombre}"
        );

        return redirect()->route('tipos-apoyo.index')
            ->with('success', 'Tipo de apoyo eliminado exitosamente.');
    }

    public function toggleStatus(TipoApoyo $tipoApoyo)
    {
        if (!auth()->user()->hasRole(['SuperUsuario', 'AdministradorCS'])) {
            abort(403, 'No autorizado para cambiar el estado de tipos de apoyo.');
        }

        $tipoApoyo->activo = !$tipoApoyo->activo;
        $tipoApoyo->save();

        $estado = $tipoApoyo->activo ? 'activado' : 'desactivado';

        // Registrar en bitácora
        $this->registrarBitacora(
            'Actualización',
            'Tipos_Apoyo',
            "Tipo de apoyo {$estado}: {$tipoApoyo->nombre}"
        );

        return redirect()->route('tipos-apoyo.index')
            ->with('success', "Tipo de apoyo {$estado} exitosamente.");
    }
}
