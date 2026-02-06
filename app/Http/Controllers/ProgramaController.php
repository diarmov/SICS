<?php

namespace App\Http\Controllers;

use App\Informe;
use App\Programa;
use App\Dependencia;
use App\TipoApoyo;
use Illuminate\Http\Request;
use App\Traits\RegistraBitacora;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProgramaController extends Controller
{
    use RegistraBitacora;
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
        if (Auth::user()->hasRole(['SuperUsuario', 'AdministradorCS'])) {
            $programas = Programa::with('dependencia')->get();
        } else {
            $programas = Programa::where('dependencia_id', Auth::user()->dependencia_id)->get();
        }

        return view('programas.index', compact('programas'));
    }

    public function create()
    {
        if (Auth::user()->hasRole(['SuperUsuario', 'AdministradorCS'])) {
            $dependencias = Dependencia::where('activo', true)->get();
        } else {
            $dependencias = Dependencia::where('id', Auth::user()->dependencia_id)->where('activo', true)->get();
        }

        // Agregar esta línea para obtener los tipos de apoyo
        $tiposApoyo = TipoApoyo::where('activo', true)->orderBy('nombre')->get();

        return view('programas.create', compact('dependencias', 'tiposApoyo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'dependencia_id' => 'required|exists:dependencias,id',
            'nombre' => 'required|string|max:255',
            'archivo_pdf' => 'nullable|file|mimes:pdf|max:10240',
            'reglas_operacion_pdf' => 'nullable|file|mimes:pdf|max:10240',
            'guia_operativa_pdf' => 'nullable|file|mimes:pdf|max:10240',
            'fecha_inicio' => 'required|date',
            'fecha_termino' => 'required|date|after:fecha_inicio',
            'periodo' => 'required|integer|min:2000|max:2030',
            'numero_informes' => 'required|integer|min:0|max:12',
            'tipo_apoyo_id' => 'required|exists:tipos_apoyo,id',
            'numero_beneficiarios' => 'required|integer|min:0',
            'monto_vigilado' => 'required|numeric|min:0',
        ]);

        $programa = new Programa();
        $programa->dependencia_id = $request->dependencia_id;
        $programa->tipo_apoyo_id = $request->tipo_apoyo_id;
        $programa->nombre = $request->nombre;
        $programa->fecha_inicio = $request->fecha_inicio;
        $programa->fecha_termino = $request->fecha_termino;
        $programa->periodo = $request->periodo;
        $programa->numero_informes = $request->numero_informes;
        $programa->numero_beneficiarios = $request->numero_beneficiarios;
        $programa->monto_vigilado = $request->monto_vigilado;
        $programa->activo = $request->has('activo');

        // Guardar archivos
        $archivos = [
            'archivo_pdf' => 'programas_pdf',
            'reglas_operacion_pdf' => 'reglas_operacion',
            'guia_operativa_pdf' => 'guias_operativas'
        ];

        foreach ($archivos as $campo => $directorio) {
            if ($request->hasFile($campo)) {
                $archivo = $request->file($campo);
                $ruta = $archivo->store($directorio, 'public');
                $programa->$campo = $ruta;
            }
        }

        $programa->save();

        return redirect()->route('programas.index')->with('success', 'Programa creado exitosamente.');
    }

    public function show(Programa $programa)
    {
        if (!Auth::user()->hasRole(['SuperUsuario', 'AdministradorCS']) && $programa->dependencia_id != Auth::user()->dependencia_id) {
            abort(403, 'No autorizado para ver este programa.');
        }

        return view('programas.show', compact('programa'));
    }

    public function edit(Programa $programa)
    {
        if (!Auth::user()->hasRole(['SuperUsuario', 'AdministradorCS']) && $programa->dependencia_id != Auth::user()->dependencia_id) {
            abort(403, 'No autorizado para editar este programa.');
        }

        if (Auth::user()->hasRole(['SuperUsuario', 'AdministradorCS'])) {
            $dependencias = Dependencia::where('activo', true)->get();
        } else {
            $dependencias = Dependencia::where('id', Auth::user()->dependencia_id)->where('activo', true)->get();
        }

        // Agregar esta línea para obtener los tipos de apoyo
        $tiposApoyo = TipoApoyo::where('activo', true)->orderBy('nombre')->get();

        return view('programas.edit', compact('programa', 'dependencias', 'tiposApoyo'));
    }

    public function update(Request $request, Programa $programa)
    {
        if (!Auth::user()->hasRole(['SuperUsuario', 'AdministradorCS']) && $programa->dependencia_id != Auth::user()->dependencia_id) {
            abort(403, 'No autorizado para actualizar este programa.');
        }

        $request->validate([
            'dependencia_id' => 'required|exists:dependencias,id',
            'nombre' => 'required|string|max:255',
            'archivo_pdf' => 'nullable|file|mimes:pdf|max:10240',
            'reglas_operacion_pdf' => 'nullable|file|mimes:pdf|max:10240',
            'guia_operativa_pdf' => 'nullable|file|mimes:pdf|max:10240',
            'fecha_inicio' => 'required|date',
            'fecha_termino' => 'required|date|after:fecha_inicio',
            'periodo' => 'required|integer|min:2000|max:2030',
            'numero_informes' => 'required|integer|min:0|max:12',
            'tipo_apoyo_id' => 'required|exists:tipos_apoyo,id',
            'numero_beneficiarios' => 'required|integer|min:0',
            'monto_vigilado' => 'required|numeric|min:0',
        ]);

        $programa->dependencia_id = $request->dependencia_id;
        $programa->tipo_apoyo_id = $request->tipo_apoyo_id;
        $programa->nombre = $request->nombre;
        $programa->fecha_inicio = $request->fecha_inicio;
        $programa->fecha_termino = $request->fecha_termino;
        $programa->periodo = $request->periodo;
        $programa->numero_informes = $request->numero_informes;
        $programa->numero_beneficiarios = $request->numero_beneficiarios;
        $programa->monto_vigilado = $request->monto_vigilado;
        $programa->activo = $request->has('activo');

        // Actualizar archivos
        $archivos = [
            'archivo_pdf' => 'programas_pdf',
            'reglas_operacion_pdf' => 'reglas_operacion',
            'guia_operativa_pdf' => 'guias_operativas'
        ];

        foreach ($archivos as $campo => $directorio) {
            if ($request->hasFile($campo)) {
                // Eliminar archivo anterior si existe
                if ($programa->$campo) {
                    Storage::disk('public')->delete($programa->$campo);
                }

                $archivo = $request->file($campo);
                $ruta = $archivo->store($directorio, 'public');
                $programa->$campo = $ruta;
            }
        }

        $programa->save();

        return redirect()->route('programas.index')->with('success', 'Programa actualizado exitosamente.');
    }

    public function destroy(Programa $programa)
    {
        if (!Auth::user()->hasRole(['SuperUsuario', 'AdministradorCS']) && $programa->dependencia_id != Auth::user()->dependencia_id) {
            abort(403, 'No autorizado para eliminar este programa.');
        }

        // Eliminar todos los archivos relacionados
        $archivos = ['archivo_pdf', 'reglas_operacion_pdf', 'guia_operativa_pdf'];

        foreach ($archivos as $archivo) {
            if ($programa->$archivo) {
                Storage::disk('public')->delete($programa->$archivo);
            }
        }
        $programa->delete();

        return redirect()->route('programas.index')->with('success', 'Programa eliminado exitosamente.');
    }

    public function uploadBeneficiarios(Request $request, Programa $programa)
    {
        $request->validate([
            'archivo_beneficiarios' => 'required|file|mimes:xlsx,xls,csv|max:10240'
        ]);

        if ($request->hasFile('archivo_beneficiarios')) {
            $archivo = $request->file('archivo_beneficiarios');
            $ruta = $archivo->store("beneficiarios/{$programa->id}", 'public');

            // Registrar en bitácora la carga de beneficiarios
            $this->registrarBitacora(
                'Carga de archivo',
                'Programas',
                "Archivo de beneficiarios cargado para programa: {$programa->nombre} - Ruta: {$ruta}"
            );

            return redirect()->route('programas.show', $programa)->with('success', 'Archivo de beneficiarios cargado exitosamente.');
        }

        return redirect()->route('programas.show', $programa)->with('error', 'Error al cargar el archivo.');
    }
    // Nuevo método para gestionar informes
    public function informes(Programa $programa)
    {
        if (!Auth::user()->hasRole(['SuperUsuario', 'AdministradorCS']) && $programa->dependencia_id != Auth::user()->dependencia_id) {
            abort(403, 'No autorizado para ver los informes de este programa.');
        }

        $programa->load('informes');
        return view('programas.informes', compact('programa'));
    }

    // Método para agregar/editar informe
    public function storeInforme(Request $request, Programa $programa)
    {
        if (!Auth::user()->hasRole(['SuperUsuario', 'AdministradorCS']) && $programa->dependencia_id != Auth::user()->dependencia_id) {
            abort(403, 'No autorizado para agregar informes a este programa.');
        }

        // Verificar que el programa esté activo
        if (!$programa->esta_activo) {
            return redirect()->back()->with('error', 'No se pueden agregar informes a un programa fuera de su periodo de vigencia.');
        }

        // Verificar que no se exceda el número de informes
        if ($programa->informes->count() >= $programa->numero_informes) {
            return redirect()->back()->with('error', 'Se ha alcanzado el número máximo de informes para este programa.');
        }

        $request->validate([
            'numero_informe' => 'required|integer|min:1|max:' . $programa->numero_informes,
            'nombre' => 'required|string|max:255',
            'archivo' => 'required|file|mimes:doc,docx,xls,xlsx,pdf|max:10240',
            'observaciones' => 'nullable|string',
            'fecha_entrega' => 'required|date',
        ]);

        // Verificar que el número de informe no esté duplicado
        $informeExistente = Informe::where('programa_id', $programa->id)
            ->where('numero_informe', $request->numero_informe)
            ->first();

        if ($informeExistente) {
            return redirect()->back()->with('error', 'Ya existe un informe con ese número para este programa.');
        }

        $informe = new Informe();
        $informe->programa_id = $programa->id;
        $informe->numero_informe = $request->numero_informe;
        $informe->nombre = $request->nombre;
        $informe->fecha_entrega = $request->fecha_entrega;
        $informe->observaciones = $request->observaciones;
        $informe->entregado = true;

        if ($request->hasFile('archivo')) {
            $archivo = $request->file('archivo');
            $ruta = $archivo->store("informes/programa_{$programa->id}", 'public');
            $informe->archivo = $ruta;
        }

        $informe->save();

        return redirect()->route('programas.informes', $programa)->with('success', 'Informe agregado exitosamente.');
    }

    // Método para eliminar informe
    public function destroyInforme(Informe $informe)
    {
        $programa = $informe->programa;

        if (!Auth::user()->hasRole(['SuperUsuario', 'AdministradorCS']) && $programa->dependencia_id != Auth::user()->dependencia_id) {
            abort(403, 'No autorizado para eliminar este informe.');
        }

        // Eliminar archivo físico
        if ($informe->archivo) {
            Storage::disk('public')->delete($informe->archivo);
        }

        $informe->delete();

        return redirect()->route('programas.informes', $programa)->with('success', 'Informe eliminado exitosamente.');
    }
}
