<?php

namespace App\Http\Controllers;

use App\Bitacora;
// Ensure the Carrusel model exists in the App namespace
use App\Carrusel;
use App\ComiteVigilancia;
use App\Dependencia;
use App\Programa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function index()
    {
        $programas = Programa::where('activo', true)->take(6)->get();
        $comites = ComiteVigilancia::where('activo', true)->take(6)->get();
        $dependencias = Dependencia::where('activo', true)->get();

        // Obtener imágenes activas del carrusel ordenadas por orden
        $carruseles = Carrusel::where('activo', true)
            ->orderBy('orden')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('home', compact('programas', 'comites', 'dependencias', 'carruseles'));
    }

    public function contacto()
    {
        return view('principales/contacto');
    }

    public function contraloriasocial()
    {
        $comites = ComiteVigilancia::where('activo', true)->get();
        return view('principales/contraloriasocial', compact('comites'));
    }

    public function consulta()
    {
        $programas = Programa::where('activo', true)->take(6)->get();
        $comites = ComiteVigilancia::where('activo', true)->take(6)->get();
        $dependencias = Dependencia::where('activo', true)->get();

        return view('principales/consulta', compact('programas', 'dependencias', 'comites'));
    }

    public function denuncias()
    {
        $dependencias = Dependencia::where('activo', true)->get();
        return view('principales/denuncias', compact('dependencias'));
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
