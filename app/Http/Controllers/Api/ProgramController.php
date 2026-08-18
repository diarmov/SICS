<?php
// app/Http/Controllers/Api/ProgramaController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Programa;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function getByDependencia($dependenciaId)
    {
        $programas = Programa::where('dependencia_id', $dependenciaId)
            ->where('activo', true)
            ->select('id', 'nombre')
            ->orderBy('nombre')
            ->get();

        return response()->json($programas);
    }
}
