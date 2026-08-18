<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Dependencia;
use App\Programa;
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
        if (!auth()->user()->hasRole(['SuperUsuario', 'Organo_Estatal_de_Control', 'Instancia_Normativa'])) {
            abort(403, 'No autorizado para acceder a la gestión de usuarios.');
        }

        if (auth()->user()->hasRole('Instancia_Normativa')) {
            $users = User::where('dependencia_id', auth()->user()->dependencia_id)
                ->with(['dependencia', 'programa'])
                ->get();
        } else {
            $users = User::with(['dependencia', 'programa'])->get();
        }

        return view('users.index', compact('users'));
    }

    public function create()
    {
        if (!auth()->user()->hasRole(['SuperUsuario', 'Organo_Estatal_de_Control', 'Instancia_Normativa'])) {
            abort(403, 'No autorizado para crear usuarios.');
        }

        if (auth()->user()->hasRole('Instancia_Normativa')) {
            // Solo puede asignar rol de Instancia_Ejecutora
            $roles = Role::where('name', 'Instancia_Ejecutora')->get();

            // Solo puede crear usuarios para su propia dependencia
            $dependencias = Dependencia::where('id', auth()->user()->dependencia_id)
                ->where('activo', true)
                ->get();

            // Obtener programas de su dependencia para vincular
            $programas = Programa::where('dependencia_id', auth()->user()->dependencia_id)
                ->where('activo', true)
                ->get();
        } else {
            $roles = Role::all();
            $dependencias = Dependencia::where('activo', true)->get();
            $programas = Programa::where('activo', true)->get();
        }

        return view('users.create', compact('dependencias', 'roles', 'programas'));
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

        // Validaciones adicionales para Instancia_Normativa
        if (auth()->user()->hasRole('Instancia_Normativa')) {
            // Verificar que solo asigne rol Instancia_Ejecutora
            if ($request->rol !== 'Instancia_Ejecutora') {
                return redirect()->back()
                    ->with('error', 'Solo puedes crear usuarios con rol Instancia_Ejecutora.')
                    ->withInput();
            }

            // Verificar que solo cree usuarios para su dependencia
            if ($request->dependencia_id != auth()->user()->dependencia_id) {
                return redirect()->back()
                    ->with('error', 'Solo puedes crear usuarios para tu propia dependencia.')
                    ->withInput();
            }

            // Verificar que el programa pertenezca a su dependencia
            if ($request->filled('programa_id')) {
                $programa = Programa::where('id', $request->programa_id)
                    ->where('dependencia_id', auth()->user()->dependencia_id)
                    ->first();

                if (!$programa) {
                    return redirect()->back()
                        ->with('error', 'El programa seleccionado no pertenece a tu dependencia.')
                        ->withInput();
                }
            }
        }

        $userData = [
            'name' => $request->nombre,
            'nombre' => $request->nombre,
            'apellido_paterno' => $request->apellido_paterno,
            'apellido_materno' => $request->apellido_materno,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'dependencia_id' => $request->dependencia_id,
            'programa_id' => $request->programa_id, // Agregar programa_id
            'activo' => $request->has('activo'),
        ];

        $user = User::create($userData);
        $user->assignRole($request->rol);

        return redirect()->route('users.index')->with('success', 'Usuario creado exitosamente.');
    }


    public function edit(User $user)
    {
        // Verificar permisos
        if (!auth()->user()->hasRole(['SuperUsuario', 'Organo_Estatal_de_Control'])) {
            abort(403, 'No autorizado para editar usuarios.');
        }

        $dependencias = Dependencia::where('activo', true)->get();
        $roles = Role::all();

        // Obtener solo los programas de la dependencia del usuario
        $programas = Programa::where('dependencia_id', $user->dependencia_id)
            ->where('activo', true)
            ->get();

        return view('users.edit', compact('user', 'dependencias', 'roles', 'programas'));
    }

    public function update(Request $request, User $user)
    {
        // Verificar permisos - Solo SuperUsuario y Organo_Estatal_de_Control pueden editar
        if (!auth()->user()->hasRole(['SuperUsuario', 'Organo_Estatal_de_Control'])) {
            abort(403, 'No autorizado para actualizar usuarios.');
        }

        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido_paterno' => 'required|string|max:255',
            'apellido_materno' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'dependencia_id' => 'required|exists:dependencias,id',
            'rol' => 'required|exists:roles,name',
            'programa_id' => 'nullable|exists:programas,id',
        ]);

        $user->update([
            'name' => $request->nombre,
            'nombre' => $request->nombre,
            'apellido_paterno' => $request->apellido_paterno,
            'apellido_materno' => $request->apellido_materno,
            'email' => $request->email,
            'dependencia_id' => $request->dependencia_id,
            'programa_id' => $request->programa_id,
            'activo' => $request->has('activo'),
        ]);

        $user->syncRoles([$request->rol]);

        return redirect()->route('users.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    public function updatePassword(Request $request, User $user)
    {
        // Verificar permisos
        if (!auth()->user()->hasRole(['SuperUsuario', 'Organo_Estatal_de_Control'])) {
            abort(403, 'No autorizado para cambiar contraseñas de otros usuarios.');
        }

        $request->validate([
            'new_password' => 'required|string|min:8|confirmed',
            'new_password_confirmation' => 'required|string|min:8',
        ]);

        // Cambiar la contraseña
        $user->password = Hash::make($request->new_password);
        $user->save();

        // Registrar en bitácora
        $this->registrarBitacora(
            'Cambio de contraseña forzado',
            'Usuarios',
            "El usuario " . auth()->user()->nombre . " cambió la contraseña del usuario {$user->nombre} ({$user->email})",
            auth()->user()
        );

        return redirect()->route('users.edit', $user)
            ->with('password_success', "Contraseña del usuario {$user->nombre} actualizada exitosamente.");
    }

    public function destroy(User $user)
    {
        if (!auth()->user()->hasRole(['SuperUsuario', 'Organo_Estatal_de_Control'])) {
            abort(403, 'No autorizado para eliminar usuarios.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Usuario eliminado exitosamente.');
    }
}
