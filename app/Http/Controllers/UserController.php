<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Dependencia;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use App\Traits\RegistraBitacora;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    use RegistraBitacora;

    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
        // Verificar permisos
        if (!auth()->user()->hasRole(['SuperUsuario', 'AdministradorCS', 'CoordinadorEnlaces'])) {
            abort(403, 'No autorizado para acceder a la gestión de usuarios.');
        }

        // Si es CoordinadorEnlaces, solo muestra usuarios de su dependencia
        if (auth()->user()->hasRole('CoordinadorEnlaces')) {
            $users = User::where('dependencia_id', auth()->user()->dependencia_id)
                ->with('dependencia')
                ->get();
        } else {
            $users = User::with('dependencia')->get();
        }

        return view('users.index', compact('users'));
    }

    public function create()
    {
        // Verificar permisos
        if (!auth()->user()->hasRole(['SuperUsuario', 'AdministradorCS', 'CoordinadorEnlaces'])) {
            abort(403, 'No autorizado para crear usuarios.');
        }

        // Obtener roles disponibles según el usuario actual
        if (auth()->user()->hasRole('CoordinadorEnlaces')) {
            // CoordinadorEnlaces solo puede asignar rol de EnlacePrograma
            $roles = Role::where('name', 'EnlacePrograma')->get();
            // Solo puede crear usuarios para su propia dependencia
            $dependencias = Dependencia::where('id', auth()->user()->dependencia_id)
                ->where('activo', true)
                ->get();
        } else {
            // SuperUsuario y AdministradorCS pueden asignar cualquier rol
            $roles = Role::all();
            $dependencias = Dependencia::where('activo', true)->get();
        }

        return view('users.create', compact('dependencias', 'roles'));
    }

    public function store(Request $request)
    {
        // Validaciones base
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido_paterno' => 'required|string|max:255',
            'apellido_materno' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'dependencia_id' => 'required|exists:dependencias,id',
            'rol' => 'required|exists:roles,name',
        ]);

        // Validaciones adicionales para CoordinadorEnlaces
        if (auth()->user()->hasRole('CoordinadorEnlaces')) {
            // Verificar que solo asigne rol EnlacePrograma
            if ($request->rol !== 'EnlacePrograma') {
                return redirect()->back()
                    ->with('error', 'Solo puedes crear usuarios con rol EnlacePrograma.')
                    ->withInput();
            }

            // Verificar que solo cree usuarios para su dependencia
            if ($request->dependencia_id != auth()->user()->dependencia_id) {
                return redirect()->back()
                    ->with('error', 'Solo puedes crear usuarios para tu propia dependencia.')
                    ->withInput();
            }
        }

        $user = User::create([
            'name' => $request->nombre,
            'nombre' => $request->nombre,
            'apellido_paterno' => $request->apellido_paterno,
            'apellido_materno' => $request->apellido_materno,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'dependencia_id' => $request->dependencia_id,
            'activo' => $request->has('activo'),
        ]);

        $user->assignRole($request->rol);

        return redirect()->route('users.index')->with('success', 'Usuario creado exitosamente.');
    }

    public function edit(User $user)
    {
        // Verificar permisos
        if (!auth()->user()->hasRole(['SuperUsuario', 'AdministradorCS'])) {
            abort(403, 'No autorizado para editar usuarios.');
        }

        // CoordinadorEnlaces no puede editar usuarios
        $dependencias = Dependencia::where('activo', true)->get();
        $roles = Role::all();

        return view('users.edit', compact('user', 'dependencias', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        // Verificar permisos - Solo SuperUsuario y AdministradorCS pueden editar
        if (!auth()->user()->hasRole(['SuperUsuario', 'AdministradorCS'])) {
            abort(403, 'No autorizado para actualizar usuarios.');
        }

        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido_paterno' => 'required|string|max:255',
            'apellido_materno' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'dependencia_id' => 'required|exists:dependencias,id',
            'rol' => 'required|exists:roles,name',
        ]);

        $user->update([
            'name' => $request->nombre,
            'nombre' => $request->nombre,
            'apellido_paterno' => $request->apellido_paterno,
            'apellido_materno' => $request->apellido_materno,
            'email' => $request->email,
            'dependencia_id' => $request->dependencia_id,
            'activo' => $request->has('activo'),
        ]);

        $user->syncRoles([$request->rol]);

        return redirect()->route('users.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy(User $user)
    {
        // Verificar permisos - Solo SuperUsuario y AdministradorCS pueden eliminar
        if (!auth()->user()->hasRole(['SuperUsuario', 'AdministradorCS'])) {
            abort(403, 'No autorizado para eliminar usuarios.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Usuario eliminado exitosamente.');
    }
}
