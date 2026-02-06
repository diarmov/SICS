<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use App\Dependencia;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/dashboard';

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:SuperUsuario|AdministradorCS');
    }

    public function showRegistrationForm()
    {
        $dependencias = Dependencia::where('activo', true)->get();
        $roles = Role::all();
        return view('auth.register', compact('dependencias', 'roles'));
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'nombre' => ['required', 'string', 'max:255'],
            'apellido_paterno' => ['required', 'string', 'max:255'],
            'apellido_materno' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'dependencia_id' => ['required', 'exists:dependencias,id'],
            'rol' => ['required', 'exists:roles,name'],
        ]);
    }

    protected function create(array $data)
    {
        $user = User::create([
            'nombre' => $data['nombre'],
            'apellido_paterno' => $data['apellido_paterno'],
            'apellido_materno' => $data['apellido_materno'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'dependencia_id' => $data['dependencia_id'],
            'activo' => true,
        ]);

        $user->assignRole($data['rol']);

        return $user;
    }
}
