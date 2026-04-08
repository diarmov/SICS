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
        try {
            \Log::info('Iniciando creación de programa', $request->all());

            // Validación básica
            $request->validate([
                'dependencia_id' => 'required|exists:dependencias,id',
                'nombre' => 'required|string|max:255',
                'fecha_inicio' => 'required|date',
                'fecha_termino' => 'required|date|after:fecha_inicio',
                'periodo' => 'required|integer|min:2000|max:2030',
                'numero_informes' => 'required|integer|min:0|max:12',
                'tipo_apoyo_id' => 'required|exists:tipos_apoyo,id',
                'numero_beneficiarios' => 'required|integer|min:0',
                'monto_vigilado' => 'required|numeric|min:0',
            ]);

            // Validación específica de archivos
            $archivosValidacion = $this->validarArchivos($request);

            if (!$archivosValidacion['success']) {
                return redirect()->back()
                    ->withErrors($archivosValidacion['errors'])
                    ->withInput();
            }

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

            // 🔥 IMPORTANTE: El programa comienza inactivo hasta que se valide la guía operativa
            $programa->activo = false;
            $programa->guia_operativa_validada = false;

            // Guardar archivos
            $archivos = [
                'archivo_pdf' => 'programas_pdf',
                'reglas_operacion_pdf' => 'reglas_operacion',
                'guia_operativa_pdf' => 'guias_operativas'
            ];

            $erroresArchivos = [];

            foreach ($archivos as $campo => $directorio) {
                if ($request->hasFile($campo)) {
                    $archivo = $request->file($campo);

                    // Validación extra por si falla la validación de Laravel
                    if (!$archivo->isValid()) {
                        $nombreAmigable = str_replace('_pdf', '', $campo);
                        $erroresArchivos[] = "El archivo {$nombreAmigable} no es válido o está corrupto.";
                        continue;
                    }

                    try {
                        $ruta = $archivo->store($directorio, 'public');
                        $programa->$campo = $ruta;
                        \Log::info("Archivo {$campo} guardado en: {$ruta}");
                    } catch (\Exception $e) {
                        $nombreAmigable = str_replace('_pdf', '', $campo);
                        $erroresArchivos[] = "Error al guardar el archivo {$nombreAmigable}: " . $e->getMessage();
                    }
                }
            }

            if (!empty($erroresArchivos)) {
                return redirect()->back()
                    ->withErrors($erroresArchivos)
                    ->withInput();
            }

            $programa->save();

            return redirect()->route('programas.index')
                ->with('success', 'Programa creado exitosamente. La guía operativa quedará pendiente de validación para activar el programa.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Error de validación: ' . json_encode($e->errors()));
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            \Log::error('Error al guardar programa: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            return redirect()->back()
                ->with('error', 'Error al guardar el programa: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Validación detallada de archivos
     */
    private function validarArchivos(Request $request)
    {
        $errors = [];
        $success = true;

        $archivos = [
            'archivo_pdf' => 'Archivo PDF del Programa',
            'reglas_operacion_pdf' => 'Reglas de Operación',
            'guia_operativa_pdf' => 'Guía Operativa'
        ];

        foreach ($archivos as $campo => $nombreAmigable) {
            if ($request->hasFile($campo)) {
                $archivo = $request->file($campo);

                // Validar tipo de archivo
                $extension = strtolower($archivo->getClientOriginalExtension());
                $mimeType = $archivo->getMimeType();
                $validExtensions = ['pdf'];
                $validMimeTypes = ['application/pdf', 'application/x-pdf'];

                if (!in_array($extension, $validExtensions)) {
                    $errors[] = "❌ {$nombreAmigable}: El archivo debe ser PDF. Extensión detectada: .{$extension}";
                    $success = false;
                } elseif (!in_array($mimeType, $validMimeTypes)) {
                    $errors[] = "❌ {$nombreAmigable}: El archivo no es un PDF válido. Tipo MIME detectado: {$mimeType}";
                    $success = false;
                }

                // Validar tamaño (10MB = 10485760 bytes)
                $maxSize = 10 * 1024 * 1024; // 10MB en bytes
                $fileSize = $archivo->getSize();

                if ($fileSize > $maxSize) {
                    $sizeMB = round($fileSize / (1024 * 1024), 2);
                    $errors[] = "❌ {$nombreAmigable}: El archivo excede el tamaño máximo permitido de 10MB. Tamaño actual: {$sizeMB}MB";
                    $success = false;
                }

                // Validar que el archivo no esté corrupto
                if (!$archivo->isValid()) {
                    $errors[] = "❌ {$nombreAmigable}: El archivo está corrupto o no es válido.";
                    $success = false;
                }
            } else {
                // Si no es requerido, no hay error
                continue;
            }
        }

        return [
            'success' => $success,
            'errors' => $errors
        ];
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
        try {
            if (!Auth::user()->hasRole(['SuperUsuario', 'AdministradorCS']) && $programa->dependencia_id != Auth::user()->dependencia_id) {
                abort(403, 'No autorizado para actualizar este programa.');
            }

            // Validación básica
            $request->validate([
                'dependencia_id' => 'required|exists:dependencias,id',
                'nombre' => 'required|string|max:255',
                'fecha_inicio' => 'required|date',
                'fecha_termino' => 'required|date|after:fecha_inicio',
                'periodo' => 'required|integer|min:2000|max:2030',
                'numero_informes' => 'required|integer|min:0|max:12',
                'tipo_apoyo_id' => 'required|exists:tipos_apoyo,id',
                'numero_beneficiarios' => 'required|integer|min:0',
                'monto_vigilado' => 'required|numeric|min:0',
            ]);

            // Validación específica de archivos solo si se suben nuevos
            $archivosValidacion = $this->validarArchivosUpdate($request);

            if (!$archivosValidacion['success']) {
                return redirect()->back()
                    ->withErrors($archivosValidacion['errors'])
                    ->withInput();
            }

            $programa->dependencia_id = $request->dependencia_id;
            $programa->tipo_apoyo_id = $request->tipo_apoyo_id;
            $programa->nombre = $request->nombre;
            $programa->fecha_inicio = $request->fecha_inicio;
            $programa->fecha_termino = $request->fecha_termino;
            $programa->periodo = $request->periodo;
            $programa->numero_informes = $request->numero_informes;
            $programa->numero_beneficiarios = $request->numero_beneficiarios;
            $programa->monto_vigilado = $request->monto_vigilado;

            // 🔥 IMPORTANTE: Solo los administradores pueden activar manualmente el programa
            // pero si la guía no está validada, no se puede activar
            if (Auth::user()->hasRole(['SuperUsuario', 'AdministradorCS'])) {
                if ($request->has('activo') && $request->activo == 1) {
                    // Verificar que la guía esté validada antes de activar
                    if ($programa->guia_operativa_validada) {
                        $programa->activo = true;
                    } else {
                        // No permitir activar si la guía no está validada
                        return redirect()->back()
                            ->with('error', 'No se puede activar el programa porque la guía operativa no ha sido validada.');
                    }
                } else {
                    $programa->activo = false;
                }
            }

            // Actualizar archivos
            $archivos = [
                'archivo_pdf' => 'programas_pdf',
                'reglas_operacion_pdf' => 'reglas_operacion',
                'guia_operativa_pdf' => 'guias_operativas'
            ];

            $guiaOperativaActualizada = false;
            $erroresArchivos = [];

            foreach ($archivos as $campo => $directorio) {
                if ($request->hasFile($campo)) {
                    $archivo = $request->file($campo);

                    // Validaciones adicionales
                    if (!$archivo->isValid()) {
                        $erroresArchivos[] = "El archivo para " . str_replace('_pdf', '', $campo) . " no es válido.";
                        continue;
                    }

                    try {
                        // Eliminar archivo anterior si existe
                        if ($programa->$campo) {
                            Storage::disk('public')->delete($programa->$campo);
                        }

                        $ruta = $archivo->store($directorio, 'public');
                        $programa->$campo = $ruta;

                        if ($campo === 'guia_operativa_pdf') {
                            $guiaOperativaActualizada = true;
                        }
                    } catch (\Exception $e) {
                        $erroresArchivos[] = "Error al guardar el archivo: " . $e->getMessage();
                    }
                }
            }

            if (!empty($erroresArchivos)) {
                return redirect()->back()
                    ->withErrors($erroresArchivos)
                    ->withInput();
            }

            // Resetear validación si se actualizó la guía operativa
            if ($guiaOperativaActualizada) {
                $programa->guia_operativa_validada = false;
                $programa->guia_operativa_observaciones = null;
                $programa->guia_operativa_validada_por = null;
                $programa->guia_operativa_fecha_validacion = null;
                // 🔥 Desactivar el programa hasta nueva validación
                $programa->activo = false;
            }

            $programa->save();

            $mensaje = 'Programa actualizado exitosamente.';
            if ($guiaOperativaActualizada) {
                $mensaje .= ' La guía operativa requiere nueva validación y el programa ha sido desactivado.';
            }

            return redirect()->route('programas.index')->with('success', $mensaje);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Validación detallada de archivos para actualización
     */
    private function validarArchivosUpdate(Request $request)
    {
        $errors = [];
        $success = true;

        $archivos = [
            'archivo_pdf' => 'Archivo PDF del Programa',
            'reglas_operacion_pdf' => 'Reglas de Operación',
            'guia_operativa_pdf' => 'Guía Operativa'
        ];

        foreach ($archivos as $campo => $nombreAmigable) {
            if ($request->hasFile($campo)) {
                $archivo = $request->file($campo);

                // Validar tipo de archivo
                $extension = strtolower($archivo->getClientOriginalExtension());
                $mimeType = $archivo->getMimeType();
                $validExtensions = ['pdf'];
                $validMimeTypes = ['application/pdf', 'application/x-pdf'];

                if (!in_array($extension, $validExtensions)) {
                    $errors[] = "❌ {$nombreAmigable}: El archivo debe ser PDF. Extensión detectada: .{$extension}";
                    $success = false;
                } elseif (!in_array($mimeType, $validMimeTypes)) {
                    $errors[] = "❌ {$nombreAmigable}: El archivo no es un PDF válido. Tipo MIME detectado: {$mimeType}";
                    $success = false;
                }

                // Validar tamaño
                $maxSize = 10 * 1024 * 1024; // 10MB
                $fileSize = $archivo->getSize();

                if ($fileSize > $maxSize) {
                    $sizeMB = round($fileSize / (1024 * 1024), 2);
                    $errors[] = "❌ {$nombreAmigable}: El archivo excede el tamaño máximo permitido de 10MB. Tamaño actual: {$sizeMB}MB";
                    $success = false;
                }

                if (!$archivo->isValid()) {
                    $errors[] = "❌ {$nombreAmigable}: El archivo está corrupto o no es válido.";
                    $success = false;
                }
            }
        }

        return [
            'success' => $success,
            'errors' => $errors
        ];
    }

    public function destroy(Programa $programa)
    {
        if (!Auth::user()->hasRole(['SuperUsuario', 'AdministradorCS']) && $programa->dependencia_id != Auth::user()->dependencia_id) {
            abort(403, 'No autorizado para eliminar este programa.');
        }

        try {
            // Verificar si el programa tiene comités de vigilancia asociados
            if ($programa->comitesVigilancia()->count() > 0) {
                $cantidadComites = $programa->comitesVigilancia()->count();
                $comitesList = $programa->comitesVigilancia->pluck('nombre')->implode(', ');

                return redirect()->route('programas.index')
                    ->with('error', 'No se puede eliminar el programa "' . $programa->nombre . '" porque tiene ' .
                        $cantidadComites . ' comité(s) de vigilancia asociado(s): ' . $comitesList);
            }

            // Verificar si el programa tiene informes asociados
            if ($programa->informes()->count() > 0) {
                $cantidadInformes = $programa->informes()->count();

                return redirect()->route('programas.index')
                    ->with('error', 'No se puede eliminar el programa "' . $programa->nombre . '" porque tiene ' .
                        $cantidadInformes . ' informe(s) asociado(s).');
            }

            // Si pasa todas las validaciones, proceder con la eliminación
            $programaNombre = $programa->nombre;

            // Eliminar todos los archivos relacionados
            $archivos = ['archivo_pdf', 'reglas_operacion_pdf', 'guia_operativa_pdf'];

            foreach ($archivos as $archivo) {
                if ($programa->$archivo) {
                    Storage::disk('public')->delete($programa->$archivo);
                }
            }

            $programa->delete();

            return redirect()->route('programas.index')
                ->with('success', 'Programa ' . $programaNombre . ' eliminado exitosamente.');
        } catch (\Illuminate\Database\QueryException $e) {
            // Como respaldo, capturar cualquier error de base de datos
            if ($e->getCode() == 23000) {
                return redirect()->route('programas.index')
                    ->with('error', 'No se puede eliminar el programa ' . $programa->nombre .
                        ' porque tiene elementos asociados (comités de vigilancia, informes, etc.).');
            }

            return redirect()->route('programas.index')
                ->with('error', 'Error de base de datos: ' . $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->route('programas.index')
                ->with('error', 'Error inesperado: ' . $e->getMessage());
        }
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

    // Método para validar la guía operativa
    public function validarGuiaOperativa(Request $request, Programa $programa)
    {
        // Verificar permisos
        if (!Auth::user()->hasRole(['SuperUsuario', 'AdministradorCS'])) {
            abort(403, 'No autorizado para validar guías operativas.');
        }

        // Verificar que el programa tenga una guía operativa cargada
        if (!$programa->guia_operativa_pdf) {
            return redirect()->back()->with('error', 'El programa no tiene una guía operativa cargada para validar.');
        }

        $request->validate([
            'validar' => 'required|boolean',
            'observaciones' => 'nullable|string|max:500'
        ]);

        if ($request->validar) {
            // Validar la guía operativa
            $programa->guia_operativa_validada = true;
            $programa->guia_operativa_observaciones = null;

            // 🔥 IMPORTANTE: Activar el programa automáticamente
            $programa->activo = true;

            $mensaje = 'Guía operativa validada exitosamente. El programa ha sido activado automáticamente.';

            // Registrar en bitácora
            $this->registrarBitacora(
                'Validación',
                'Programas',
                "Guía operativa validada y programa activado para: {$programa->nombre} por: " . Auth::user()->name
            );
        } else {
            // Rechazar con observaciones
            if (empty($request->observaciones)) {
                return redirect()->back()->with('error', 'Debe proporcionar observaciones al rechazar la guía operativa.');
            }

            $programa->guia_operativa_validada = false;
            $programa->guia_operativa_observaciones = $request->observaciones;

            // 🔥 Asegurar que el programa quede inactivo
            $programa->activo = false;

            $mensaje = 'Guía operativa rechazada. Se han enviado observaciones para su corrección. El programa permanece inactivo.';

            // Registrar en bitácora
            $this->registrarBitacora(
                'Rechazo',
                'Programas',
                "Guía operativa rechazada para programa: {$programa->nombre}. Motivo: {$request->observaciones}"
            );
        }

        $programa->guia_operativa_validada_por = Auth::id();
        $programa->guia_operativa_fecha_validacion = now();
        $programa->save();

        return redirect()->back()->with('success', $mensaje);
    }

    // Método para que el creador pueda editar la guía operativa cuando es rechazada
    public function editarGuiaOperativa(Request $request, Programa $programa)
    {
        // Verificar que el usuario sea el creador o tenga permisos
        if (
            !Auth::user()->hasRole(['SuperUsuario', 'AdministradorCS']) &&
            $programa->dependencia_id != Auth::user()->dependencia_id
        ) {
            abort(403, 'No autorizado para editar este programa.');
        }

        // Verificar que la guía esté en estado rechazado
        if ($programa->guia_operativa_validada) {
            return redirect()->back()->with('error', 'No se puede editar una guía operativa que ya ha sido validada.');
        }

        $request->validate([
            'guia_operativa_pdf' => 'required|file|mimes:pdf|max:10240',
            'guia_operativa_observaciones' => 'nullable|string'
        ]);

        // Eliminar archivo anterior si existe
        if ($programa->guia_operativa_pdf) {
            Storage::disk('public')->delete($programa->guia_operativa_pdf);
        }

        // Guardar nuevo archivo
        $archivo = $request->file('guia_operativa_pdf');
        $ruta = $archivo->store('guias_operativas', 'public');
        $programa->guia_operativa_pdf = $ruta;

        // Resetear estado de validación y asegurar que el programa queda inactivo
        $programa->guia_operativa_validada = false;
        $programa->guia_operativa_observaciones = $request->guia_operativa_observaciones;
        $programa->guia_operativa_validada_por = null;
        $programa->guia_operativa_fecha_validacion = null;

        // 🔥 IMPORTANTE: Desactivar el programa hasta nueva validación
        $programa->activo = false;

        $programa->save();

        // Registrar en bitácora
        $this->registrarBitacora(
            'Edición',
            'Programas',
            "Guía operativa reemplazada para programa: {$programa->nombre} después de observaciones. Programa desactivado hasta nueva validación."
        );

        return redirect()->back()->with('success', 'Guía operativa actualizada. Queda pendiente de nueva validación y el programa ha sido desactivado.');
    }
}
