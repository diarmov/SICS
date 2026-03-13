@extends('layouts.admin')

@section('title', 'Editar Comité - SICS')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-tinto text-white">
                    <h4 class="mb-0">Editar Comité de Vigilancia</h4>
                </div>
                <div class="card-body">
                    <!-- ALERTA DE COMITÉ VALIDADO -->
                    <!-- MOSTRAR MENSAJES DE SESIÓN -->
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        {{ session('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif
                    <!-- ALERTA SI EL COMITÉ ESTÁ VALIDADO Y EL USUARIO ES ADMIN -->
                    @if($comite->estaValidado() && Auth::user()->hasRole(['SuperUsuario', 'AdministradorCS']))
                    <div class="alert alert-warning">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Este comité está validado</strong> - Los cambios que realice quitarán la
                                validación automáticamente.
                            </div>
                            <form action="{{ route('comites.invalidar', $comite) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-warning btn-sm"
                                    onclick="return confirm('¿Invalidar este comité? Podrá volver a validarlo después.')">
                                    <i class="fas fa-unlock me-1"></i> Invalidar explícitamente
                                </button>
                            </form>
                        </div>
                    </div>
                    @endif

                    <!-- FORMULARIO PRINCIPAL DE EDICIÓN -->
                    <form action="{{ route('comites.update', $comite) }}" method="POST" enctype="multipart/form-data"
                        id="form-editar-comite">
                        @csrf
                        @method('PUT')

                        <!-- Campos básicos -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre del Comité</label>
                                    <input type="text" class="form-control @error('nombre') is-invalid @enderror"
                                        id="nombre" name="nombre" value="{{ old('nombre', $comite->nombre) }}" required>
                                    @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="dependencia_id" class="form-label">Dependencia</label>
                                    <select class="form-select @error('dependencia_id') is-invalid @enderror"
                                        id="dependencia_id" name="dependencia_id" required>
                                        <option value="">Seleccionar dependencia</option>
                                        @foreach($dependencias as $dependencia)
                                        <option value="{{ $dependencia->id }}" {{ old('dependencia_id', $comite->
                                            dependencia_id) == $dependencia->id ? 'selected' : '' }}>
                                            {{ $dependencia->dependencia }} ({{ $dependencia->siglas }})
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('dependencia_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="programa_id" class="form-label">Programa</label>
                                    <select class="form-select @error('programa_id') is-invalid @enderror"
                                        id="programa_id" name="programa_id" required>
                                        <option value="">Seleccionar programa</option>
                                        @foreach($programas as $programa)
                                        <option value="{{ $programa->id }}" {{ old('programa_id', $comite->programa_id)
                                            == $programa->id ? 'selected' : '' }}>
                                            {{ $programa->nombre }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('programa_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Selectores de ubicación -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="id_estado" class="form-label">Estado</label>
                                    <select class="form-select @error('id_estado') is-invalid @enderror" id="id_estado"
                                        name="id_estado" required>
                                        <option value="">Seleccionar Estado</option>
                                        @foreach($estados as $estado)
                                        <option value="{{ $estado->id_estado }}" {{ old('id_estado', $comite->id_estado)
                                            == $estado->id_estado ? 'selected' : '' }}>
                                            {{ $estado->nombre }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('id_estado')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="id_municipio" class="form-label">Municipio</label>
                                    <select class="form-select @error('id_municipio') is-invalid @enderror"
                                        id="id_municipio" name="id_municipio" required>
                                        <option value="">Cargando municipios...</option>
                                    </select>
                                    @error('id_municipio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="id_localidad" class="form-label">Localidad</label>
                                    <select class="form-select @error('id_localidad') is-invalid @enderror"
                                        id="id_localidad" name="id_localidad" required>
                                        <option value="">Cargando localidades...</option>
                                    </select>
                                    @error('id_localidad')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3 form-check mt-4">
                                    <input type="checkbox" class="form-check-input" id="activo" name="activo" value="1"
                                        {{ old('activo', $comite->activo) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="activo">Activo</label>
                                </div>
                            </div>
                        </div>

                        <!-- SECCIÓN DE ARCHIVOS -->
                        <h5 class="mt-4 text-tinto">Documentos del Comité</h5>

                        <!-- Minuta -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="archivo_minuta" class="form-label">Minuta / Acta constitutiva
                                        (PDF)</label>

                                    @if($comite->archivo_minuta)
                                    <div class="mb-2 p-2 bg-light rounded">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-file-pdf text-danger fa-lg me-2"></i>
                                            <span class="flex-grow-1">
                                                <a href="{{ Storage::url($comite->archivo_minuta) }}" target="_blank"
                                                    class="text-decoration-none">
                                                    {{ basename($comite->archivo_minuta) }}
                                                </a>
                                            </span>
                                            <a href="{{ Storage::url($comite->archivo_minuta) }}" download
                                                class="btn btn-sm btn-outline-primary me-2">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <!-- NOTA: No incluimos botón de eliminar aquí porque se reemplaza al subir uno nuevo -->
                                        </div>
                                    </div>
                                    @endif

                                    <input type="file"
                                        class="form-control @error('archivo_minuta') is-invalid @enderror"
                                        id="archivo_minuta" name="archivo_minuta" accept=".pdf">
                                    <small class="text-muted">
                                        @if($comite->archivo_minuta)
                                        Suba un nuevo archivo para reemplazar la minuta actual (máx. 5MB)
                                        @else
                                        Suba el archivo PDF de la minuta (máx. 5MB)
                                        @endif
                                    </small>
                                    @error('archivo_minuta')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Lista de Asistencia -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="lista_asistencia" class="form-label">Lista de Asistencia</label>

                                    @if($comite->lista_asistencia)
                                    <div class="mb-2 p-2 bg-light rounded">
                                        <div class="d-flex align-items-center">
                                            @php
                                            $extensionLista = pathinfo($comite->lista_asistencia, PATHINFO_EXTENSION);
                                            $iconoLista = in_array(strtolower($extensionLista), ['pdf']) ? 'file-pdf
                                            text-danger' :
                                            (in_array(strtolower($extensionLista), ['doc', 'docx']) ? 'file-word
                                            text-primary' :
                                            'file-excel text-success');
                                            @endphp
                                            <i
                                                class="fas fa-{{ explode(' ', $iconoLista)[0] }} {{ explode(' ', $iconoLista)[1] }} fa-lg me-2"></i>
                                            <span class="flex-grow-1">
                                                <a href="{{ Storage::url($comite->lista_asistencia) }}" target="_blank"
                                                    class="text-decoration-none">
                                                    {{ basename($comite->lista_asistencia) }}
                                                </a>
                                            </span>
                                            <a href="{{ Storage::url($comite->lista_asistencia) }}" download
                                                class="btn btn-sm btn-outline-primary me-2">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger eliminar-lista"
                                                data-ruta="{{ $comite->lista_asistencia }}"
                                                data-comite="{{ $comite->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    @endif

                                    <input type="file"
                                        class="form-control @error('lista_asistencia') is-invalid @enderror"
                                        id="lista_asistencia" name="lista_asistencia"
                                        accept=".pdf,.doc,.docx,.xls,.xlsx">
                                    <small class="text-muted">
                                        @if($comite->lista_asistencia)
                                        Suba un nuevo archivo para reemplazar la lista actual (PDF, Word, Excel, máx.
                                        5MB)
                                        @else
                                        Suba el archivo de lista de asistencia (PDF, Word, Excel, máx. 5MB)
                                        @endif
                                    </small>
                                    @error('lista_asistencia')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Material de Difusión -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="material_difusion" class="form-label">Material de Difusión</label>

                                    @if(count($comite->material_difusion) > 0)
                                    <div class="mb-3">
                                        <h6 class="text-muted">Archivos actuales:</h6>
                                        <div class="list-group" id="material-list">
                                            @foreach($comite->material_difusion as $index => $material)
                                            <div class="list-group-item d-flex justify-content-between align-items-center"
                                                id="material-{{ $index }}">
                                                <div>
                                                    @php
                                                    $extension = pathinfo($material, PATHINFO_EXTENSION);
                                                    $icono = in_array(strtolower($extension), ['pdf']) ? 'file-pdf
                                                    text-danger' :
                                                    (in_array(strtolower($extension), ['doc', 'docx']) ? 'file-word
                                                    text-primary' :
                                                    (in_array(strtolower($extension), ['xls', 'xlsx']) ? 'file-excel
                                                    text-success' :
                                                    'file-image text-info'));
                                                    @endphp
                                                    <i
                                                        class="fas fa-{{ explode(' ', $icono)[0] }} {{ explode(' ', $icono)[1] }} me-2"></i>
                                                    <a href="{{ Storage::url($material) }}" target="_blank"
                                                        class="text-decoration-none">
                                                        {{ basename($material) }}
                                                    </a>
                                                </div>
                                                <div>
                                                    <a href="{{ Storage::url($material) }}" download
                                                        class="btn btn-sm btn-outline-primary me-1">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-danger eliminar-material"
                                                        data-ruta="{{ $material }}" data-comite="{{ $comite->id }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif

                                    <input type="file"
                                        class="form-control @error('material_difusion.*') is-invalid @enderror"
                                        id="material_difusion" name="material_difusion[]"
                                        accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" multiple>
                                    <small class="text-muted">
                                        Puede agregar más archivos (PDF, Word, Excel, JPG, PNG, máx. 5MB cada uno)
                                    </small>
                                    @error('material_difusion.*')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Fotografías -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="fotografias" class="form-label">Fotografías de la Reunión</label>

                                    @if(count($comite->fotografias_reunion) > 0)
                                    <div class="mb-3">
                                        <h6 class="text-muted">Fotografías actuales:</h6>
                                        <div class="row" id="fotos-list">
                                            @foreach($comite->fotografias_reunion as $index => $foto)
                                            <div class="col-md-4 mb-3" id="foto-{{ $index }}">
                                                <div class="card">
                                                    <a href="{{ Storage::url($foto) }}" target="_blank">
                                                        <img src="{{ Storage::url($foto) }}" class="card-img-top"
                                                            alt="Foto {{ $index + 1 }}"
                                                            style="height: 120px; object-fit: cover;">
                                                    </a>
                                                    <div class="card-body p-2">
                                                        <div class="d-flex justify-content-center">
                                                            <a href="{{ Storage::url($foto) }}" download
                                                                class="btn btn-sm btn-outline-primary me-1">
                                                                <i class="fas fa-download"></i>
                                                            </a>
                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-danger eliminar-foto"
                                                                data-ruta="{{ $foto }}" data-comite="{{ $comite->id }}">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif

                                    <input type="file" class="form-control @error('fotografias.*') is-invalid @enderror"
                                        id="fotografias" name="fotografias[]" accept=".jpg,.jpeg" multiple>
                                    <small class="text-muted">
                                        Puede agregar más fotografías (solo JPG, máx. 2MB cada una)
                                    </small>
                                    @error('fotografias.*')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Botones de acción del formulario principal -->
                        <div class="mt-4">
                            <button type="submit" class="btn btn-tinto" id="btn-actualizar">
                                <i class="fas fa-save me-2"></i>Actualizar Comité
                            </button>
                            <a href="{{ route('comites.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                        </div>
                    </form>
                    <!-- FIN FORMULARIO PRINCIPAL -->

                    <!-- SECCIÓN DE ELEMENTOS (FUERA DEL FORMULARIO PRINCIPAL) -->
                    <hr class="my-4">
                    <h5 class="text-tinto">Elementos del Comité</h5>

                    @if($comite->elementos->count() > 0)
                    <div class="table-responsive mt-3">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nombre Completo</th>
                                    <th>Tipo</th>
                                    <th>INE</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($comite->elementos as $elemento)
                                <tr>
                                    <td>{{ $elemento->nombre_completo }}</td>
                                    <td><span class="badge bg-info">{{ $elemento->tipo_elemento }}</span></td>
                                    <td>
                                        @if($elemento->archivo_ine)
                                        @php
                                        $extension = pathinfo($elemento->archivo_ine, PATHINFO_EXTENSION);
                                        $isPdf = strtolower($extension) === 'pdf';
                                        $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png']);
                                        @endphp

                                        @if($isPdf)
                                        <a href="{{ Storage::url($elemento->archivo_ine) }}" target="_blank"
                                            class="btn btn-sm btn-outline-danger" title="Ver INE (PDF)">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                        @elseif($isImage)
                                        <a href="{{ Storage::url($elemento->archivo_ine) }}" target="_blank"
                                            class="btn btn-sm btn-outline-info" title="Ver INE (Imagen)">
                                            <i class="fas fa-image"></i>
                                        </a>
                                        @endif
                                        <a href="{{ Storage::url($elemento->archivo_ine) }}" download
                                            class="btn btn-sm btn-outline-success" title="Descargar">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        @else
                                        <span class="text-muted">
                                            <i class="fas fa-times-circle"></i> Sin INE
                                        </span>
                                        @endif
                                    </td>
                                    <td>
                                        <form action="{{ route('comites.remove-elemento', $elemento) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('¿Eliminar este elemento?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>No hay elementos en este comité.
                    </div>
                    @endif

                    <!-- FORMULARIO PARA AGREGAR ELEMENTOS (SEPARADO) -->
                    <div class="card mt-4 border-success">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Agregar Nuevo Elemento</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('comites.add-elemento', $comite) }}" method="POST"
                                enctype="multipart/form-data" id="form-agregar-elemento">
                                @csrf
                                <div class="row align-items-end">
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Nombre Completo</label>
                                        <input type="text" class="form-control" name="nombre_completo"
                                            placeholder="Nombre completo" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Tipo</label>
                                        <input type="text" class="form-control" name="tipo_elemento"
                                            placeholder="Presidente, Vocal, etc." required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">INE (opcional)</label>
                                        <input type="file" class="form-control" name="archivo_ine"
                                            accept=".pdf,.jpg,.jpeg,.png">
                                        <small class="text-muted">PDF, JPG, PNG (máx. 2MB)</small>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="fas fa-plus"></i> Agregar
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- SCRIPTS -->
<script>
    console.log('=== ESTADO DEL COMITÉ ===');
    console.log('ID:', {{ $comite->id }});
    console.log('¿Está validado?', {{ $comite->estaValidado() ? 'true' : 'false' }});
    console.log('Validado (campo):', {{ $comite->validado ? 'true' : 'false' }});
    console.log('Validado por:', {{ $comite->validado_por ?? 'null' }});
    console.log('Fecha validación:', '{{ $comite->fecha_validacion ? $comite->fecha_validacion->format('Y-m-d H:i:s') : 'null' }}');
    console.log('Rol Admin:', {{ Auth::user()->hasRole(['SuperUsuario', 'AdministradorCS']) ? 'true' : 'false' }});
    console.log('Rol Coordinador:', {{ Auth::user()->hasRole('CoordinadorEnlaces') ? 'true' : 'false' }});

    document.addEventListener('DOMContentLoaded', function() {

        const form = document.getElementById('form-editar-comite');

        // Verificar que el formulario existe
        if (form) {
            console.log('✅ Formulario encontrado');
            console.log('Action:', form.action);
            console.log('Method:', form.method);

            // Agregar evento submit para depuración
            form.addEventListener('submit', function(e) {
                console.log('📤 Formulario enviado');
            });
        } else {
            console.error('❌ Formulario NO encontrado');
        }
        // ===== CONFIGURACIÓN INICIAL =====
        const estadoSelect = document.getElementById('id_estado');
        const municipioSelect = document.getElementById('id_municipio');
        const localidadSelect = document.getElementById('id_localidad');

        const comiteEstadoId = {{ $comite->id_estado ?? 'null' }};
        const comiteMunicipioId = {{ $comite->id_municipio ?? 'null' }};
        const comiteLocalidadId = {{ $comite->id_localidad ?? 'null' }};

        // ===== FUNCIÓN PARA OBTENER CSRF TOKEN =====
        function getCsrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.content ||
                   document.querySelector('input[name="_token"]')?.value ||
                   (document.cookie.match(/XSRF-TOKEN=([^;]+)/) ?
                    decodeURIComponent(document.cookie.match(/XSRF-TOKEN=([^;]+)/)[1]) : null);
        }

        // ===== CARGA DE MUNICIPIOS Y LOCALIDADES =====
        function cargarMunicipios(estadoId, municipioSeleccionado = null) {
            if (!estadoId) {
                municipioSelect.innerHTML = '<option value="">Seleccione un estado primero</option>';
                municipioSelect.disabled = true;
                return;
            }

            municipioSelect.innerHTML = '<option value="">Cargando municipios...</option>';
            municipioSelect.disabled = true;
            localidadSelect.innerHTML = '<option value="">Seleccione un municipio primero</option>';
            localidadSelect.disabled = true;

            fetch(`/api/municipios/${estadoId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) throw new Error('Error al cargar municipios');
                return response.json();
            })
            .then(data => {
                municipioSelect.innerHTML = '<option value="">Seleccionar Municipio</option>';
                if (Array.isArray(data) && data.length > 0) {
                    data.forEach(municipio => {
                        const selected = municipioSeleccionado && municipio.id == municipioSeleccionado ? 'selected' : '';
                        municipioSelect.innerHTML += `<option value="${municipio.id}" ${selected}>${municipio.nombre}</option>`;
                    });
                    municipioSelect.disabled = false;

                    if (municipioSeleccionado) {
                        cargarLocalidades(municipioSeleccionado, comiteLocalidadId);
                    }
                } else {
                    municipioSelect.innerHTML = '<option value="">No hay municipios disponibles</option>';
                    municipioSelect.disabled = true;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                municipioSelect.innerHTML = '<option value="">Error al cargar municipios</option>';
                municipioSelect.disabled = true;
            });
        }

        function cargarLocalidades(municipioId, localidadSeleccionada = null) {
            if (!municipioId) {
                localidadSelect.innerHTML = '<option value="">Seleccione un municipio primero</option>';
                localidadSelect.disabled = true;
                return;
            }

            localidadSelect.innerHTML = '<option value="">Cargando localidades...</option>';
            localidadSelect.disabled = true;

            fetch(`/api/localidades/${municipioId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) throw new Error('Error al cargar localidades');
                return response.json();
            })
            .then(data => {
                localidadSelect.innerHTML = '<option value="">Seleccionar Localidad</option>';
                if (Array.isArray(data) && data.length > 0) {
                    data.forEach(localidad => {
                        const selected = localidadSeleccionada && localidad.id == localidadSeleccionada ? 'selected' : '';
                        localidadSelect.innerHTML += `<option value="${localidad.id}" ${selected}>${localidad.nombre}</option>`;
                    });
                    localidadSelect.disabled = false;
                } else {
                    localidadSelect.innerHTML = '<option value="">No hay localidades disponibles</option>';
                    localidadSelect.disabled = true;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                localidadSelect.innerHTML = '<option value="">Error al cargar localidades</option>';
                localidadSelect.disabled = true;
            });
        }

        // Precargar datos si existen
        if (comiteEstadoId) {
    // Usar un pequeño timeout asegura que el DOM esté listo y los eventos vinculados
    setTimeout(() => {
        cargarMunicipios(comiteEstadoId, comiteMunicipioId);
    }, 100);
}

        // Event listeners
        estadoSelect.addEventListener('change', function() {
            cargarMunicipios(this.value);
        });

        municipioSelect.addEventListener('change', function() {
            cargarLocalidades(this.value);
        });

        // ===== ELIMINACIÓN DE ARCHIVOS (AJAX) =====
        const csrfToken = getCsrfToken();

        document.querySelectorAll('.eliminar-lista').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();
        const ruta = this.dataset.ruta;
        const comiteId = this.dataset.comite;

        if (confirm('¿Eliminar esta lista de asistencia?')) {
            fetch(`/comites/${comiteId}/eliminar-lista`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ archivo: ruta })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Eliminar el elemento del DOM (el div contenedor)
                    const elemento = this.closest('.mb-2.p-2.bg-light.rounded');
                    if (elemento) elemento.remove();

                    alert('Lista de asistencia eliminada correctamente');
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al eliminar la lista de asistencia');
            });
        }
    });
});



        // Eliminar material de difusión
        document.querySelectorAll('.eliminar-material').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const ruta = this.dataset.ruta;
                const comiteId = this.dataset.comite;

                if (confirm('¿Eliminar este archivo?')) {
                    fetch(`/comites/${comiteId}/eliminar-material`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ archivo: ruta })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const elemento = this.closest('.list-group-item');
                            if (elemento) elemento.remove();

                            const materialList = document.getElementById('material-list');
                            if (materialList && materialList.children.length === 0) {
                                materialList.innerHTML = '<div class="alert alert-info">No hay materiales de difusión</div>';
                            }

                            alert('Archivo eliminado correctamente');
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error al eliminar el archivo');
                    });
                }
            });
        });

        // Eliminar fotografías
        document.querySelectorAll('.eliminar-foto').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const ruta = this.dataset.ruta;
                const comiteId = this.dataset.comite;

                if (confirm('¿Eliminar esta fotografía?')) {
                    fetch(`/comites/${comiteId}/eliminar-foto`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ archivo: ruta })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const elemento = this.closest('.col-md-4, .col-md-3');
                            if (elemento) elemento.remove();

                            const fotosList = document.getElementById('fotos-list');
                            if (fotosList && fotosList.children.length === 0) {
                                fotosList.innerHTML = '<div class="alert alert-info">No hay fotografías</div>';
                            }

                            alert('Fotografía eliminada correctamente');
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error al eliminar la fotografía');
                    });
                }
            });
        });

        // ===== VISTAS PREVIAS DE ARCHIVOS =====
        function crearVistaPrevia(input, container, esImagen = false) {
            if (!input || !container) return;

            container.innerHTML = '';

            if (input.files.length > 0) {
                const titulo = document.createElement('h6');
                titulo.className = 'text-tinto mt-2';
                titulo.textContent = esImagen ? 'Fotografías nuevas:' : 'Archivos nuevos:';
                container.appendChild(titulo);

                if (esImagen) {
                    const row = document.createElement('div');
                    row.className = 'row';

                    Array.from(input.files).forEach((file, i) => {
                        const col = document.createElement('div');
                        col.className = 'col-md-3 mb-3';

                        const card = document.createElement('div');
                        card.className = 'card';

                        const img = document.createElement('img');
                        img.className = 'card-img-top';
                        img.style.height = '100px';
                        img.style.objectFit = 'cover';

                        const reader = new FileReader();
                        reader.onload = e => img.src = e.target.result;
                        reader.readAsDataURL(file);

                        card.appendChild(img);

                        const body = document.createElement('div');
                        body.className = 'card-body p-2';
                        body.innerHTML = `<small class="text-muted d-block text-truncate">${file.name}</small>`;

                        card.appendChild(body);
                        col.appendChild(card);
                        row.appendChild(col);
                    });

                    container.appendChild(row);
                } else {
                    const lista = document.createElement('div');
                    lista.className = 'list-group';

                    Array.from(input.files).forEach((file, i) => {
                        const item = document.createElement('div');
                        item.className = 'list-group-item d-flex justify-content-between align-items-center';
                        item.innerHTML = `
                            <span>${i + 1}. ${file.name}</span>
                            <span class="badge bg-info">${(file.size / 1024 / 1024).toFixed(2)} MB</span>
                        `;
                        lista.appendChild(item);
                    });

                    container.appendChild(lista);
                }
            }
        }

        // Configurar vistas previas
        const materialInput = document.getElementById('material_difusion');
        const fotosInput = document.getElementById('fotografias');

        if (materialInput) {
            const previewContainer = document.createElement('div');
            previewContainer.className = 'mt-2';
            materialInput.parentNode.appendChild(previewContainer);

            materialInput.addEventListener('change', function() {
                crearVistaPrevia(this, previewContainer, false);
            });
        }

        if (fotosInput) {
            const previewContainer = document.createElement('div');
            previewContainer.className = 'mt-2';
            fotosInput.parentNode.appendChild(previewContainer);

            fotosInput.addEventListener('change', function() {
                crearVistaPrevia(this, previewContainer, true);
            });
        }

    });

</script>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endsection