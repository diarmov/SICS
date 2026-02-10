<?php

namespace App\Http\Controllers;

use App\Estado;
use App\Bitacora;
use App\Programa;
use App\Dependencia;
use App\ElementoComite;
use App\ComiteVigilancia;
use Illuminate\Http\Request;
use App\Traits\RegistraBitacora;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
            'lista_asistencia' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:5120', // 5MB
            'material_difusion.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:5120',
            'fotografias.*' => 'nullable|image|mimes:jpg,jpeg|max:2048', // 2MB cada foto
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
            // Guardar lista de asistencia si se subió
            if ($request->hasFile('lista_asistencia')) {
                try {
                    $listaAsistencia = $request->file('lista_asistencia');
                    $nombreLista = 'lista_asistencia_' . $comite->id . '_' . time() . '.' . $listaAsistencia->getClientOriginalExtension();

                    Log::info("Subiendo lista de asistencia: {$nombreLista}");

                    // Crear directorio si no existe
                    $directorioListas = 'listas_asistencia';
                    if (!Storage::disk('public')->exists($directorioListas)) {
                        Storage::disk('public')->makeDirectory($directorioListas);
                    }

                    // Guardar archivo
                    $rutaLista = $listaAsistencia->storeAs('public/' . $directorioListas, $nombreLista);
                    $rutaListaDB = str_replace('public/', '', $rutaLista);

                    // Actualizar comité con la ruta
                    $comite->update(['lista_asistencia' => $rutaListaDB]);

                    Log::info("Lista de asistencia guardada en: " . $rutaListaDB);
                } catch (\Exception $e) {
                    Log::error("Error al guardar lista de asistencia: " . $e->getMessage());
                }
            }

            // Guardar material de difusión (múltiples archivos)
            if ($request->hasFile('material_difusion')) {
                try {
                    $materiales = [];
                    foreach ($request->file('material_difusion') as $archivo) {
                        $nombreMaterial = 'material_difusion_' . $comite->id . '_' . uniqid() . '.' . $archivo->getClientOriginalExtension();

                        Log::info("Subiendo material de difusión: {$nombreMaterial}");

                        // Crear directorio si no existe
                        $directorioMateriales = 'material_difusion/' . $comite->id;
                        if (!Storage::disk('public')->exists($directorioMateriales)) {
                            Storage::disk('public')->makeDirectory($directorioMateriales);
                        }

                        // Guardar archivo
                        $rutaMaterial = $archivo->storeAs('public/' . $directorioMateriales, $nombreMaterial);
                        $rutaMaterialDB = str_replace('public/', '', $rutaMaterial);

                        $materiales[] = $rutaMaterialDB;
                    }

                    // Guardar rutas como JSON en la base de datos
                    $comite->update(['material_difusion' => json_encode($materiales)]);

                    Log::info("Material de difusión guardado: " . count($materiales) . " archivos");
                } catch (\Exception $e) {
                    Log::error("Error al guardar material de difusión: " . $e->getMessage());
                }
            }

            // Guardar fotografías de la reunión
            if ($request->hasFile('fotografias')) {
                try {
                    $fotografias = [];
                    foreach ($request->file('fotografias') as $index => $foto) {
                        $nombreFoto = 'foto_' . $comite->id . '_' . ($index + 1) . '_' . time() . '.jpg';

                        Log::info("Subiendo fotografía: {$nombreFoto}");

                        // Crear directorio si no existe
                        $directorioFotos = 'fotografias_reunion/' . $comite->id;
                        if (!Storage::disk('public')->exists($directorioFotos)) {
                            Storage::disk('public')->makeDirectory($directorioFotos);
                        }

                        // Guardar archivo
                        $rutaFoto = $foto->storeAs('public/' . $directorioFotos, $nombreFoto);
                        $rutaFotoDB = str_replace('public/', '', $rutaFoto);

                        $fotografias[] = $rutaFotoDB;
                    }

                    // Guardar rutas como JSON en la base de datos
                    $comite->update(['fotografias_reunion' => json_encode($fotografias)]);

                    Log::info("Fotografías guardadas: " . count($fotografias) . " archivos");
                } catch (\Exception $e) {
                    Log::error("Error al guardar fotografías: " . $e->getMessage());
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
            'archivo_minuta' => 'nullable|file|mimes:pdf|max:5120',
            'lista_asistencia' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:5120',
            'material_difusion.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:5120',
            'fotografias.*' => 'nullable|image|mimes:jpg,jpeg|max:2048',
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
        // Guardar lista de asistencia si se subió
        if ($request->hasFile('lista_asistencia')) {
            try {
                // Eliminar lista anterior si existe
                if ($comite->lista_asistencia && Storage::disk('public')->exists($comite->lista_asistencia)) {
                    Storage::disk('public')->delete($comite->lista_asistencia);
                }

                $listaAsistencia = $request->file('lista_asistencia');
                $nombreLista = 'lista_asistencia_' . $comite->id . '_' . time() . '.' . $listaAsistencia->getClientOriginalExtension();

                Log::info("Actualizando lista de asistencia: {$nombreLista}");

                // Crear directorio si no existe
                $directorioListas = 'listas_asistencia';
                if (!Storage::disk('public')->exists($directorioListas)) {
                    Storage::disk('public')->makeDirectory($directorioListas);
                }

                // Guardar archivo
                $rutaLista = $listaAsistencia->storeAs('public/' . $directorioListas, $nombreLista);
                $rutaListaDB = str_replace('public/', '', $rutaLista);

                // Agregar ruta a los datos de actualización
                $updateData['lista_asistencia'] = $rutaListaDB;

                Log::info("Lista de asistencia actualizada en: " . $rutaListaDB);
            } catch (\Exception $e) {
                Log::error("Error al actualizar lista de asistencia: " . $e->getMessage());
            }
        }

        // Guardar material de difusión (múltiples archivos)
        if ($request->hasFile('material_difusion')) {
            try {
                $materiales = $comite->material_difusion ?? []; // Mantener archivos existentes

                foreach ($request->file('material_difusion') as $archivo) {
                    $nombreMaterial = 'material_difusion_' . $comite->id . '_' . uniqid() . '.' . $archivo->getClientOriginalExtension();

                    Log::info("Agregando material de difusión: {$nombreMaterial}");

                    // Crear directorio si no existe
                    $directorioMateriales = 'material_difusion/' . $comite->id;
                    if (!Storage::disk('public')->exists($directorioMateriales)) {
                        Storage::disk('public')->makeDirectory($directorioMateriales);
                    }

                    // Guardar archivo
                    $rutaMaterial = $archivo->storeAs('public/' . $directorioMateriales, $nombreMaterial);
                    $rutaMaterialDB = str_replace('public/', '', $rutaMaterial);

                    $materiales[] = $rutaMaterialDB;
                }

                // Agregar rutas a los datos de actualización
                $updateData['material_difusion'] = json_encode($materiales);

                Log::info("Material de difusión actualizado. Total: " . count($materiales) . " archivos");
            } catch (\Exception $e) {
                Log::error("Error al actualizar material de difusión: " . $e->getMessage());
            }
        }

        // Guardar fotografías de la reunión
        if ($request->hasFile('fotografias')) {
            try {
                $fotografias = $comite->fotografias_reunion ?? []; // Mantener fotos existentes

                foreach ($request->file('fotografias') as $index => $foto) {
                    $nombreFoto = 'foto_' . $comite->id . '_' . (count($fotografias) + $index + 1) . '_' . time() . '.jpg';

                    Log::info("Agregando fotografía: {$nombreFoto}");

                    // Crear directorio si no existe
                    $directorioFotos = 'fotografias_reunion/' . $comite->id;
                    if (!Storage::disk('public')->exists($directorioFotos)) {
                        Storage::disk('public')->makeDirectory($directorioFotos);
                    }

                    // Guardar archivo
                    $rutaFoto = $foto->storeAs('public/' . $directorioFotos, $nombreFoto);
                    $rutaFotoDB = str_replace('public/', '', $rutaFoto);

                    $fotografias[] = $rutaFotoDB;
                }

                // Agregar rutas a los datos de actualización
                $updateData['fotografias_reunion'] = json_encode($fotografias);

                Log::info("Fotografías actualizadas. Total: " . count($fotografias) . " fotos");
            } catch (\Exception $e) {
                Log::error("Error al actualizar fotografías: " . $e->getMessage());
            }
        }

        $comite->update($updateData);

        return redirect()->route('comites.index')->with('success', 'Comité de vigilancia actualizado exitosamente.');
    }
    /**
     * Eliminar archivo de material de difusión
     */
    public function eliminarMaterialDifusion(Request $request, ComiteVigilancia $comite)
    {
        if (!Auth::user()->hasRole(['SuperUsuario', 'AdministradorCS']) && $comite->dependencia_id != Auth::user()->dependencia_id) {
            abort(403, 'No autorizado para eliminar este archivo.');
        }

        $request->validate([
            'archivo' => 'required|string'
        ]);

        $archivoRuta = $request->archivo;

        try {
            // Obtener array actual
            $materiales = $comite->material_difusion;

            // Buscar y eliminar el archivo del array
            $indice = array_search($archivoRuta, $materiales);
            if ($indice !== false) {
                // Eliminar archivo físico
                if (Storage::disk('public')->exists($archivoRuta)) {
                    Storage::disk('public')->delete($archivoRuta);
                }

                // Eliminar del array
                unset($materiales[$indice]);
                $materiales = array_values($materiales); // Reindexar

                // Actualizar en base de datos
                $comite->update(['material_difusion' => json_encode($materiales)]);

                return response()->json([
                    'success' => true,
                    'message' => 'Archivo eliminado correctamente',
                    'count' => count($materiales)
                ]);
            }

            return response()->json(['success' => false, 'message' => 'Archivo no encontrado'], 404);
        } catch (\Exception $e) {
            Log::error("Error al eliminar material de difusión: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al eliminar archivo'], 500);
        }
    }

    /**
     * Eliminar fotografía de la reunión
     */
    public function eliminarFotografia(Request $request, ComiteVigilancia $comite)
    {
        if (!Auth::user()->hasRole(['SuperUsuario', 'AdministradorCS']) && $comite->dependencia_id != Auth::user()->dependencia_id) {
            abort(403, 'No autorizado para eliminar esta foto.');
        }

        $request->validate([
            'archivo' => 'required|string'
        ]);

        $fotoRuta = $request->archivo;

        try {
            // Obtener array actual
            $fotografias = $comite->fotografias_reunion;

            // Buscar y eliminar la foto del array
            $indice = array_search($fotoRuta, $fotografias);
            if ($indice !== false) {
                // Eliminar archivo físico
                if (Storage::disk('public')->exists($fotoRuta)) {
                    Storage::disk('public')->delete($fotoRuta);
                }

                // Eliminar del array
                unset($fotografias[$indice]);
                $fotografias = array_values($fotografias); // Reindexar

                // Actualizar en base de datos
                $comite->update(['fotografias_reunion' => json_encode($fotografias)]);

                return response()->json([
                    'success' => true,
                    'message' => 'Fotografía eliminada correctamente',
                    'count' => count($fotografias)
                ]);
            }

            return response()->json(['success' => false, 'message' => 'Fotografía no encontrada'], 404);
        } catch (\Exception $e) {
            Log::error("Error al eliminar fotografía: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al eliminar fotografía'], 500);
        }
    }

    /**
     * Eliminar lista de asistencia
     */
    public function eliminarListaAsistencia(ComiteVigilancia $comite)
    {
        if (!Auth::user()->hasRole(['SuperUsuario', 'AdministradorCS']) && $comite->dependencia_id != Auth::user()->dependencia_id) {
            abort(403, 'No autorizado para eliminar este archivo.');
        }

        try {
            if ($comite->lista_asistencia && Storage::disk('public')->exists($comite->lista_asistencia)) {
                Storage::disk('public')->delete($comite->lista_asistencia);
                $comite->update(['lista_asistencia' => null]);

                return back()->with('success', 'Lista de asistencia eliminada correctamente');
            }

            return back()->with('error', 'No se encontró la lista de asistencia');
        } catch (\Exception $e) {
            Log::error("Error al eliminar lista de asistencia: " . $e->getMessage());
            return back()->with('error', 'Error al eliminar lista de asistencia');
        }
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

    /**
     * Validar un comité
     */
    public function validar(Request $request, ComiteVigilancia $comite)
    {
        if (!Auth::user()->hasRole(['SuperUsuario', 'AdministradorCS'])) {
            abort(403, 'No autorizado para validar comités.');
        }

        $comite->validar(Auth::id());

        // Registrar en bitácora
        if (auth()->check()) {
            Bitacora::registrar(
                'Validación',
                'Comités de Vigilancia',
                "Comité validado: " . $comite->getNombreParaBitacora()
            );
        }

        return redirect()->route('comites.show', $comite)
            ->with('success', 'Comité validado exitosamente.');
    }

    /**
     * Invalidar un comité
     */
    public function invalidar(Request $request, ComiteVigilancia $comite)
    {
        if (!Auth::user()->hasRole(['SuperUsuario', 'AdministradorCS'])) {
            abort(403, 'No autorizado para invalidar comités.');
        }

        $comite->invalidar();

        // Registrar en bitácora
        if (auth()->check()) {
            Bitacora::registrar(
                'Invalidación',
                'Comités de Vigilancia',
                "Comité invalidado: " . $comite->getNombreParaBitacora()
            );
        }

        return redirect()->route('comites.show', $comite)
            ->with('warning', 'Comité invalidado.');
    }

    /**
     * Listar comités pendientes de validación
     */
    public function pendientes()
    {
        if (!Auth::user()->hasRole(['SuperUsuario', 'AdministradorCS'])) {
            abort(403, 'No autorizado para ver comités pendientes.');
        }

        $comites = ComiteVigilancia::pendientes()
            ->with(['dependencia', 'programa', 'elementos', 'estado', 'municipio', 'localidad'])
            ->get();

        return view('comites.pendientes', compact('comites'));
    }
}
