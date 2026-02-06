<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Estado;
use App\Municipio;
use App\Localidad;

class UbicacionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function getEstados()
    {
        $estados = Estado::where('activo', true)
            ->orderBy('nombre')
            ->get(['id_estado as id', 'nombre', 'clave']);

        return response()->json($estados);
    }

    public function getMunicipios($estadoId)
    {
        \Log::info('Solicitando municipios para estado ID: ' . $estadoId);

        try {
            $municipios = Municipio::where('id_estado', $estadoId)
                ->where('activo', true)
                ->orderBy('nombre')
                ->get(['id_municipio as id', 'nombre', 'clave']);

            \Log::info('Municipios encontrados: ' . $municipios->count());

            return response()->json($municipios);
        } catch (\Exception $e) {
            \Log::error('Error en getMunicipios: ' . $e->getMessage());
            return response()->json(['error' => 'Error al cargar municipios', 'message' => $e->getMessage()], 500);
        }
    }

    public function getLocalidades($municipioId)
    {
        try {
            $localidades = Localidad::where('id_municipio', $municipioId)
                ->where('activo', true)
                ->orderBy('nombre')
                ->get(['id_localidad as id', 'nombre', 'clave']);

            return response()->json($localidades);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al cargar localidades'], 500);
        }
    }
}
