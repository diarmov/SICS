<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Programa;
use App\ComiteVigilancia;
use App\Dependencia;
use App\Bitacora;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function index()
    {
        $programas = Programa::where('activo', true)->take(6)->get();
        $comites = ComiteVigilancia::where('activo', true)->take(6)->get();
        $dependencias = Dependencia::where('activo', true)->get();

        return view('home', compact('programas', 'comites', 'dependencias'));
    }

    public function contacto()
    {
        return view('contacto');
    }

    public function comites()
    {
        $comites = ComiteVigilancia::where('activo', true)->get();
        return view('comites', compact('comites'));
    }

    public function programas()
    {
        $programas = Programa::where('activo', true)->get();
        return view('programas', compact('programas'));
    }

    public function dependencias()
    {
        $dependencias = Dependencia::where('activo', true)->get();
        return view('dependencias', compact('dependencias'));
    }

    // Método para el dashboard
    public function dashboard()
    {
        // Obtener actividad reciente de la bitácora (solo para SuperUsuario y AdministradorCS)
        if (Auth::user()->hasRole(['SuperUsuario', 'AdministradorCS'])) {
            try {
                if (Schema::hasTable('bitacoras')) {
                    $bitacoras = Bitacora::with('user')->latest()->paginate(15); // 10 para dashboard
                } else {
                    $bitacoras = collect();
                }
            } catch (\Exception $e) {
                $bitacoras = collect();
            }
        } else {
            $bitacoras = collect();
        }

        return view('dashboard', compact('bitacoras'));
    }
}
