<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Bitacora;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/dashboard';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    protected function authenticated(Request $request, $user)
    {
        // Registrar en bitácora el inicio de sesión
        Bitacora::registrar(
            'Inicio de sesión',
            'Autenticación',
            "El usuario {$user->nombre} inició sesión",
            $user
        );

        return redirect()->intended($this->redirectPath());
    }

    public function logout(Request $request)
    {
        // Registrar en bitácora el cierre de sesión
        if (auth()->check()) {
            $user = auth()->user();
            Bitacora::registrar(
                'Cierre de sesión',
                'Autenticación',
                "El usuario {$user->nombre} cerró sesión",
                $user
            );
        }

        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return $this->loggedOut($request) ?: redirect('/');
    }
}
