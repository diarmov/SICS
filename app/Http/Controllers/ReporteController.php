<?php

namespace App\Http\Controllers;

use App\Bitacora;
use App\ComiteVigilancia;
use App\Dependencia;
use App\Programa;
use App\Exports\ReporteExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class ReporteController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Muestra la vista principal de reportes
     */
    public function index()
    {
        if (!auth()->user()->hasRole(['SuperUsuario', 'Organo_Estatal_de_Control'])) {
            abort(403, 'No autorizado para acceder a los reportes.');
        }

        $dependencias = Dependencia::where('activo', true)->orderBy('dependencia')->get();
        $programas = Programa::where('activo', true)->orderBy('nombre')->get();

        return view('reportes.index', compact('dependencias', 'programas'));
    }

    /**
     * Genera reporte en formato PDF
     */
    public function generarPDF(Request $request)
    {
        if (!auth()->user()->hasRole(['SuperUsuario', 'Organo_Estatal_de_Control'])) {
            abort(403, 'No autorizado para generar reportes.');
        }

        $this->validateRequest($request);

        $data = $this->obtenerDatosReporte($request);

        // Registrar en bitácora
        Bitacora::registrar(
            'Generación de Reporte',
            'Reportes',
            "Generó reporte PDF - Filtros: " . $this->getFiltrosTexto($request)
        );

        $pdf = PDF::loadView('reportes.pdf', $data);
        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('reporte_comites_' . date('Y-m-d_Hi') . '.pdf');
    }

    /**
     * Genera reporte en formato Excel
     */
    public function generarExcel(Request $request)
    {
        if (!auth()->user()->hasRole(['SuperUsuario', 'Organo_Estatal_de_Control'])) {
            abort(403, 'No autorizado para generar reportes.');
        }

        $this->validateRequest($request);

        $data = $this->obtenerDatosReporte($request);

        // Registrar en bitácora
        Bitacora::registrar(
            'Generación de Reporte',
            'Reportes',
            "Generó reporte EXCEL - Filtros: " . $this->getFiltrosTexto($request)
        );

        return Excel::download(new ReporteExport($data), 'reporte_comites_' . date('Y-m-d_Hi') . '.xlsx');
    }

    /**
     * Obtiene los datos para el reporte
     */
    private function obtenerDatosReporte(Request $request)
    {
        $query = ComiteVigilancia::with([
            'dependencia',
            'programa',
            'elementos',
            'estado',
            'municipio',
            'localidad',
            'programa.tipoApoyo'
        ]);

        // Aplicar filtros
        if ($request->filled('dependencia_id')) {
            $query->where('dependencia_id', $request->dependencia_id);
        }

        if ($request->filled('programa_id')) {
            $query->where('programa_id', $request->programa_id);
        }

        if ($request->filled('comite_id')) {
            $query->where('id', $request->comite_id);
        }

        if ($request->filled('estado_validacion')) {
            if ($request->estado_validacion === 'validados') {
                $query->where('validado', true);
            } elseif ($request->estado_validacion === 'pendientes') {
                $query->where(function ($q) {
                    $q->where('validado', false)->orWhereNull('validado');
                });
            }
        }

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }

        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }

        $comites = $query->orderBy('created_at', 'desc')->get();

        // Estadísticas generales
        $estadisticas = $this->calcularEstadisticas($comites);

        // Agrupar por tipo de apoyo
        $porTipoApoyo = $this->agruparPorTipoApoyo($comites);

        // Materiales de difusión por comité
        $materialesPorComite = $this->calcularMaterialesPorComite($comites);

        return [
            'comites' => $comites,
            'estadisticas' => $estadisticas,
            'porTipoApoyo' => $porTipoApoyo,
            'materialesPorComite' => $materialesPorComite,
            'filtros' => $request->all(),
            'fecha_generacion' => now(),
            'usuario' => auth()->user()
        ];
    }

    /**
     * Calcula estadísticas generales
     */
    private function calcularEstadisticas($comites)
    {
        $totalComites = $comites->count();
        $totalBeneficiarios = 0;
        $totalMontoVigilado = 0;
        $totalElementos = 0;
        $totalValidados = 0;
        $totalMaterialDifusion = 0;

        foreach ($comites as $comite) {
            if ($comite->programa) {
                $totalBeneficiarios += $comite->programa->numero_beneficiarios ?? 0;
                $totalMontoVigilado += $comite->programa->monto_vigilado ?? 0;
            }

            $totalElementos += $comite->elementos->count();

            if ($comite->estaValidado()) {
                $totalValidados++;
            }

            $materiales = $comite->material_difusion;
            if (is_array($materiales)) {
                foreach ($materiales as $material) {
                    $totalMaterialDifusion += $material['cantidad'] ?? 1;
                }
            }
        }

        return [
            'total_comites' => $totalComites,
            'total_beneficiarios' => $totalBeneficiarios,
            'total_monto_vigilado' => $totalMontoVigilado,
            'total_elementos' => $totalElementos,
            'total_validados' => $totalValidados,
            'total_pendientes' => $totalComites - $totalValidados,
            'total_material_difusion' => $totalMaterialDifusion,
            'promedio_beneficiarios' => $totalComites > 0 ? round($totalBeneficiarios / $totalComites) : 0,
            'promedio_monto' => $totalComites > 0 ? $totalMontoVigilado / $totalComites : 0
        ];
    }

    /**
     * Agrupa estadísticas por tipo de apoyo
     */
    private function agruparPorTipoApoyo($comites)
    {
        $grupos = [];

        foreach ($comites as $comite) {
            if (!$comite->programa || !$comite->programa->tipoApoyo) {
                continue;
            }

            $tipoApoyo = $comite->programa->tipoApoyo;
            $key = $tipoApoyo->id;

            if (!isset($grupos[$key])) {
                $grupos[$key] = [
                    'nombre' => $tipoApoyo->nombre,
                    'total_comites' => 0,
                    'total_beneficiarios' => 0,
                    'total_monto_vigilado' => 0,
                    'total_elementos' => 0
                ];
            }

            $grupos[$key]['total_comites']++;
            $grupos[$key]['total_beneficiarios'] += $comite->programa->numero_beneficiarios ?? 0;
            $grupos[$key]['total_monto_vigilado'] += $comite->programa->monto_vigilado ?? 0;
            $grupos[$key]['total_elementos'] += $comite->elementos->count();
        }

        return array_values($grupos);
    }

    /**
     * Calcula materiales de difusión por comité
     */
    private function calcularMaterialesPorComite($comites)
    {
        $resultado = [];

        foreach ($comites as $comite) {
            $materiales = $comite->material_difusion;
            $totalMateriales = 0;
            $detalleMateriales = [];

            if (is_array($materiales)) {
                foreach ($materiales as $material) {
                    $cantidad = $material['cantidad'] ?? 1;
                    $tipo = $material['tipo'] ?? 'general';
                    $totalMateriales += $cantidad;

                    if (!isset($detalleMateriales[$tipo])) {
                        $detalleMateriales[$tipo] = 0;
                    }
                    $detalleMateriales[$tipo] += $cantidad;
                }
            }

            $resultado[] = [
                'comite_id' => $comite->id,
                'comite_nombre' => $comite->nombre,
                'dependencia' => $comite->dependencia->siglas ?? 'N/A',
                'programa' => $comite->programa->nombre ?? 'N/A',
                'total_materiales' => $totalMateriales,
                'detalle_materiales' => $detalleMateriales,
                'validado' => $comite->estaValidado()
            ];
        }

        return $resultado;
    }

    /**
     * Valida los parámetros del request
     */
    private function validateRequest(Request $request)
    {
        $request->validate([
            'dependencia_id' => 'nullable|exists:dependencias,id',
            'programa_id' => 'nullable|exists:programas,id',
            'comite_id' => 'nullable|exists:comites_vigilancia,id',
            'estado_validacion' => 'nullable|in:todos,validados,pendientes',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        ]);
    }

    /**
     * Obtiene texto descriptivo de los filtros aplicados
     */
    private function getFiltrosTexto(Request $request)
    {
        $filtros = [];

        if ($request->filled('dependencia_id')) {
            $dependencia = Dependencia::find($request->dependencia_id);
            $filtros[] = "Dependencia: " . ($dependencia ? $dependencia->siglas : $request->dependencia_id);
        }

        if ($request->filled('programa_id')) {
            $programa = Programa::find($request->programa_id);
            $filtros[] = "Programa: " . ($programa ? $programa->nombre : $request->programa_id);
        }

        if ($request->filled('comite_id')) {
            $comite = ComiteVigilancia::find($request->comite_id);
            $filtros[] = "Comité: " . ($comite ? $comite->nombre : $request->comite_id);
        }

        if ($request->filled('estado_validacion') && $request->estado_validacion !== 'todos') {
            $filtros[] = "Estado: " . ($request->estado_validacion === 'validados' ? 'Validados' : 'Pendientes');
        }

        if ($request->filled('fecha_inicio')) {
            $filtros[] = "Desde: " . $request->fecha_inicio;
        }

        if ($request->filled('fecha_fin')) {
            $filtros[] = "Hasta: " . $request->fecha_fin;
        }

        return empty($filtros) ? 'Sin filtros (todos los registros)' : implode(' | ', $filtros);
    }

    /**
     * Obtiene comités para filtro dinámico (AJAX)
     */
    public function getComitesPorFiltro(Request $request)
    {
        if (!auth()->user()->hasRole(['SuperUsuario', 'Organo_Estatal_de_Control'])) {
            return response()->json([], 403);
        }

        $query = ComiteVigilancia::query();

        if ($request->filled('dependencia_id')) {
            $query->where('dependencia_id', $request->dependencia_id);
        }

        if ($request->filled('programa_id')) {
            $query->where('programa_id', $request->programa_id);
        }

        $comites = $query->orderBy('nombre')->get(['id', 'nombre']);

        return response()->json($comites);
    }

    /**
     * Vista previa de estadísticas (AJAX)
     */
    public function preview(Request $request)
    {
        if (!auth()->user()->hasRole(['SuperUsuario', 'Organo_Estatal_de_Control'])) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $query = ComiteVigilancia::with(['dependencia', 'programa']);

        if ($request->filled('dependencia_id')) {
            $query->where('dependencia_id', $request->dependencia_id);
        }

        if ($request->filled('programa_id')) {
            $query->where('programa_id', $request->programa_id);
        }

        if ($request->filled('comite_id')) {
            $query->where('id', $request->comite_id);
        }

        if ($request->filled('estado_validacion')) {
            if ($request->estado_validacion === 'validados') {
                $query->where('validado', true);
            } elseif ($request->estado_validacion === 'pendientes') {
                $query->where(function ($q) {
                    $q->where('validado', false)->orWhereNull('validado');
                });
            }
        }

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }

        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }

        $comites = $query->get();
        $estadisticas = $this->calcularEstadisticas($comites);

        return view('reportes.preview', compact('estadisticas'));
    }
}
