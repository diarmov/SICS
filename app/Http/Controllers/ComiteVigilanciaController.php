<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ComiteVigilancia;
use App\Dependencia;
use App\Programa;
use App\ElementoComite;
use App\Estado;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Traits\RegistraBitacora;

class ComiteVigilanciaController extends Controller
{
    use RegistraBitacora;

    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
        if (Auth::user()->hasRole(['SuperUsuario', 'AdministradorCS'])) {
            $comites = ComiteVigilancia::with(['dependencia', 'programa', 'elementos', 'estado', 'municipio', 'localidad'])->get();
        } else {
            $comites = ComiteVigilancia::where('dependencia_id', Auth::user()->dependencia_id)
                ->with(['dependencia', 'programa', 'elementos', 'estado', 'municipio', 'localidad'])
                ->get();
        }

        return view('comites.index', compact('comites'));
    }

    public function create()
    {
        if (Auth::user()->hasRole(['SuperUsuario', 'AdministradorCS'])) {
            $dependencias = Dependencia::where('activo', true)->get();
            $programas = Programa::where('activo', true)->get();
        } else {
            $dependencias = Dependencia::where('id', Auth::user()->dependencia_id)->where('activo', true)->get();
            $programas = Programa::where('dependencia_id', Auth::user()->dependencia_id)->where('activo', true)->get();
        }

        $estados = Estado::where('activo', true)->orderBy('nombre')->get();

        return view('comites.create', compact('dependencias', 'programas', 'estados'));
    }

    public function store(Request $request)
    {
        Log::info('=== INICIANDO STORE COMITÉ ===');
        Log::info('Datos recibidos:', $request->all());

        // Validaciones
        $request->validate([
            'dependencia_id' => 'required|exists:dependencias,id',
            'programa_id' => 'required|exists:programas,id',
            'nombre' => 'required|string|max:255',
            'id_estado' => 'required|exists:estados,id_estado',
            'id_municipio' => 'required|exists:municipios,id_municipio',
            'id_localidad' => 'required|exists:localidades,id_localidad',
            'archivo_minuta' => 'nullable|file|mimes:pdf|max:5120', // 5MB para minuta
            'elementos' => 'required|array|min:1',
            'elementos.*.nombre_completo' => 'required|string|max:255',
            'elementos.*.tipo_elemento' => 'required|string|max:100',
            'elementos.*.archivo_ine' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        Log::info('Validaciones pasadas correctamente');

        try {
            // Datos básicos del comité
            $comiteData = [
                'dependencia_id' => $request->dependencia_id,
                'programa_id' => $request->programa_id,
                'nombre' => $request->nombre,
                'id_estado' => $request->id_estado,
                'id_municipio' => $request->id_municipio,
                'id_localidad' => $request->id_localidad,
                'activo' => $request->has('activo'),
            ];

            // Crear el comité primero
            $comite = ComiteVigilancia::create($comiteData);
            Log::info('Comité creado ID: ' . $comite->id);

            // Guardar archivo de minuta si se subió
            if ($request->hasFile('archivo_minuta')) {
                try {
                    $minuta = $request->file('archivo_minuta');
                    $nombreMinuta = 'minuta_' . $comite->id . '_' . time() . '.pdf';

                    Log::info("Subiendo minuta: {$nombreMinuta}");

                    // Crear directorio si no existe
                    $directorioMinutas = 'minutas_comites';
                    if (!Storage::disk('public')->exists($directorioMinutas)) {
                        Storage::disk('public')->makeDirectory($directorioMinutas);
                    }

                    // Guardar archivo
                    $rutaMinuta = $minuta->storeAs('public/' . $directorioMinutas, $nombreMinuta);
                    $rutaMinutaDB = str_replace('public/', '', $rutaMinuta);

                    // Actualizar comité con la ruta de la minuta
                    $comite->update(['archivo_minuta' => $rutaMinutaDB]);

                    Log::info("Minuta guardada en: " . $rutaMinutaDB);
                } catch (\Exception $e) {
                    Log::error("Error al guardar minuta: " . $e->getMessage());
                }
            }

            // Crear elementos
            foreach ($request->elementos as $index => $elementoData) {
                Log::info("Procesando elemento {$index}: ", [
                    'nombre' => $elementoData['nombre_completo'],
                    'tipo' => $elementoData['tipo_elemento'],
                    'tiene_archivo' => isset($elementoData['archivo_ine'])
                ]);

                $elemento = ElementoComite::create([
                    'comite_vigilancia_id' => $comite->id,
                    'nombre_completo' => $elementoData['nombre_completo'],
                    'tipo_elemento' => $elementoData['tipo_elemento'],
                ]);

                Log::info("Elemento creado ID: " . $elemento->id);

                // Guardar archivo INE si se subió
                if (isset($elementoData['archivo_ine']) && $elementoData['archivo_ine']) {
                    try {
                        $archivo = $elementoData['archivo_ine'];
                        $extension = $archivo->getClientOriginalExtension();
                        $nombreArchivo = 'ine_' . $comite->id . '_' . $elemento->id . '_' . time() . '.' . $extension;

                        Log::info("Subiendo archivo INE: {$nombreArchivo}");

                        // Crear directorio si no existe
                        $directorioINE = 'ine_comites/' . $comite->id;
                        if (!Storage::disk('public')->exists($directorioINE)) {
                            Storage::disk('public')->makeDirectory($directorioINE);
                        }

                        // Guardar archivo
                        $rutaINE = $archivo->storeAs('public/' . $directorioINE, $nombreArchivo);
                        $rutaParaDB = str_replace('public/', '', $rutaINE);

                        // Actualizar elemento con la ruta
                        $elemento->update(['archivo_ine' => $rutaParaDB]);

                        Log::info("INE guardado en: " . $rutaParaDB);
                    } catch (\Exception $e) {
                        Log::error("Error al guardar archivo INE: " . $e->getMessage());
                    }
                }
            }

            Log::info('=== FIN STORE COMITÉ - ÉXITO ===');
            return redirect()->route('comites.index')->with('success', 'Comité de vigilancia creado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error en store comité: ' . $e->getMessage());
            Log::error('Trace: ' . $e->getTraceAsString());

            return back()->withInput()->withErrors([
                'error' => 'Error al crear el comité: ' . $e->getMessage()
            ]);
        }
    }

    public function show(ComiteVigilancia $comite)
    {
        if (!Auth::user()->hasRole(['SuperUsuario', 'AdministradorCS']) && $comite->dependencia_id != Auth::user()->dependencia_id) {
            abort(403, 'No autorizado para ver este comité.');
        }

        $comite->load(['dependencia', 'programa', 'elementos', 'estado', 'municipio', 'localidad']);

        return view('comites.show', compact('comite'));
    }

    public function edit(ComiteVigilancia $comite)
    {
        if (!Auth::user()->hasRole(['SuperUsuario', 'AdministradorCS']) && $comite->dependencia_id != Auth::user()->dependencia_id) {
            abort(403, 'No autorizado para editar este comité.');
        }

        if (Auth::user()->hasRole(['SuperUsuario', 'AdministradorCS'])) {
            $dependencias = Dependencia::where('activo', true)->get();
            $programas = Programa::where('activo', true)->get();
        } else {
            $dependencias = Dependencia::where('id', Auth::user()->dependencia_id)->where('activo', true)->get();
            $programas = Programa::where('dependencia_id', Auth::user()->dependencia_id)->where('activo', true)->get();
        }

        $estados = Estado::where('activo', true)->orderBy('nombre')->get();

        $comite->load(['elementos', 'estado', 'municipio', 'localidad']);

        return view('comites.edit', compact('comite', 'dependencias', 'programas', 'estados'));
    }

    public function update(Request $request, ComiteVigilancia $comite)
    {
        if (!Auth::user()->hasRole(['SuperUsuario', 'AdministradorCS']) && $comite->dependencia_id != Auth::user()->dependencia_id) {
            abort(403, 'No autorizado para actualizar este comité.');
        }

        $request->validate([
            'dependencia_id' => 'required|exists:dependencias,id',
            'programa_id' => 'required|exists:programas,id',
            'nombre' => 'required|string|max:255',
            'id_estado' => 'required|exists:estados,id_estado',
            'id_municipio' => 'required|exists:municipios,id_municipio',
            'id_localidad' => 'required|exists:localidades,id_localidad',
            'archivo_minuta' => 'nullable|file|mimes:pdf|max:5120', // Agregar validación para minuta
        ]);

        Log::info('Datos recibidos en update para comite ID ' . $comite->id . ':', [
            'id_estado' => $request->id_estado,
            'id_municipio' => $request->id_municipio,
            'id_localidad' => $request->id_localidad,
            'tiene_minuta' => $request->hasFile('archivo_minuta')
        ]);

        // Datos de actualización
        $updateData = [
            'dependencia_id' => $request->dependencia_id,
            'programa_id' => $request->programa_id,
            'nombre' => $request->nombre,
            'id_estado' => $request->id_estado,
            'id_municipio' => $request->id_municipio,
            'id_localidad' => $request->id_localidad,
            'activo' => $request->has('activo'),
        ];

        // Guardar archivo de minuta si se subió
        if ($request->hasFile('archivo_minuta')) {
            try {
                // Eliminar minuta anterior si existe
                if ($comite->archivo_minuta && Storage::disk('public')->exists($comite->archivo_minuta)) {
                    Storage::disk('public')->delete($comite->archivo_minuta);
                }

                $minuta = $request->file('archivo_minuta');
                $nombreMinuta = 'minuta_' . $comite->id . '_' . time() . '.pdf';

                Log::info("Actualizando minuta: {$nombreMinuta}");

                // Crear directorio si no existe
                $directorioMinutas = 'minutas_comites';
                if (!Storage::disk('public')->exists($directorioMinutas)) {
                    Storage::disk('public')->makeDirectory($directorioMinutas);
                }

                // Guardar archivo
                $rutaMinuta = $minuta->storeAs('public/' . $directorioMinutas, $nombreMinuta);
                $rutaMinutaDB = str_replace('public/', '', $rutaMinuta);

                // Agregar ruta de minuta a los datos de actualización
                $updateData['archivo_minuta'] = $rutaMinutaDB;

                Log::info("Minuta actualizada en: " . $rutaMinutaDB);
            } catch (\Exception $e) {
                Log::error("Error al actualizar minuta: " . $e->getMessage());
            }
        }

        $comite->update($updateData);

        return redirect()->route('comites.index')->with('success', 'Comité de vigilancia actualizado exitosamente.');
    }

    public function destroy(ComiteVigilancia $comite)
    {
        if (!Auth::user()->hasRole(['SuperUsuario', 'AdministradorCS']) && $comite->dependencia_id != Auth::user()->dependencia_id) {
            abort(403, 'No autorizado para eliminar este comité.');
        }

        $comite->elementos()->delete();
        $comite->delete();

        return redirect()->route('comites.index')->with('success', 'Comité de vigilancia eliminado exitosamente.');
    }

    public function addElemento(Request $request, ComiteVigilancia $comite)
    {
        Log::info('=== AGREGANDO ELEMENTO A COMITÉ ===');
        Log::info('Comité ID: ' . $comite->id);
        Log::info('Datos recibidos:', $request->all());
        Log::info('Archivo INE:', $request->hasFile('archivo_ine') ? 'Sí' : 'No');

        $request->validate([
            'nombre_completo' => 'required|string|max:255',
            'tipo_elemento' => 'required|string|max:100',
            'archivo_ine' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        try {
            $elemento = ElementoComite::create([
                'comite_vigilancia_id' => $comite->id,
                'nombre_completo' => $request->nombre_completo,
                'tipo_elemento' => $request->tipo_elemento,
            ]);

            Log::info('Elemento creado ID: ' . $elemento->id);

            // Guardar archivo INE si se subió
            if ($request->hasFile('archivo_ine')) {
                try {
                    $archivo = $request->file('archivo_ine');
                    $extension = $archivo->getClientOriginalExtension();
                    $nombreArchivo = 'ine_' . $comite->id . '_' . $elemento->id . '_' . time() . '.' . $extension;

                    Log::info("Subiendo archivo: {$nombreArchivo}");

                    // Crear directorio si no existe
                    $directorio = 'ine_comites/' . $comite->id;
                    if (!Storage::disk('public')->exists($directorio)) {
                        Storage::disk('public')->makeDirectory($directorio);
                    }

                    // Guardar archivo
                    $ruta = $archivo->storeAs('public/' . $directorio, $nombreArchivo);
                    $rutaParaDB = str_replace('public/', '', $ruta);

                    // Actualizar elemento con la ruta
                    $elemento->update([
                        'archivo_ine' => $rutaParaDB
                    ]);

                    Log::info("Archivo guardado en: " . $rutaParaDB);
                } catch (\Exception $e) {
                    Log::error("Error al guardar archivo INE: " . $e->getMessage());
                }
            }

            Log::info('=== ELEMENTO AGREGADO CON ÉXITO ===');
            return redirect()->route('comites.show', $comite)->with('success', 'Elemento agregado al comité.');
        } catch (\Exception $e) {
            Log::error('Error al agregar elemento: ' . $e->getMessage());
            Log::error('Trace: ' . $e->getTraceAsString());

            return back()->withInput()->withErrors([
                'error' => 'Error al agregar el elemento: ' . $e->getMessage()
            ]);
        }
    }

    public function removeElemento(ElementoComite $elemento)
    {
        $comiteId = $elemento->comite_vigilancia_id;
        $elemento->delete();

        return redirect()->route('comites.show', $comiteId)->with('success', 'Elemento eliminado del comité.');
    }
}
