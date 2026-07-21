<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Traits\RegistraBitacora;

class ChangePasswordController extends Controller
{
    use RegistraBitacora;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function showChangePasswordForm()
    {
        return view('auth.change-password');
    }

    public function changePassword(Request $request)
    {
        // Validar los datos
        $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
            'new_password_confirmation' => ['required', 'string', 'min:8'],
        ]);

        // Verificar que la contraseña actual sea correcta
        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return back()->withErrors([
                'current_password' => 'La contraseña actual no es correcta.'
            ])->withInput();
        }

        // Verificar que la nueva contraseña no sea igual a la actual
        if (Hash::check($request->new_password, auth()->user()->password)) {
            return back()->withErrors([
                'new_password' => 'La nueva contraseña no puede ser igual a la actual.'
            ])->withInput();
        }

        // Actualizar la contraseña
        $user = auth()->user();
        $user->password = Hash::make($request->new_password);
        $user->save();

        // Registrar en bitácora
        $this->registrarBitacora(
            'Cambio de contraseña',
            'Autenticación',
            "El usuario {$user->nombre} cambió su contraseña",
            $user
        );

        return redirect()->back()->with('success', '¡Contraseña actualizada exitosamente!');
    }
}
