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
                    <form action="{{ route('comites.update', $comite) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre del Comité</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre"
                                        value="{{ old('nombre', $comite->nombre) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="dependencia_id" class="form-label">Dependencia</label>
                                    <select class="form-select" id="dependencia_id" name="dependencia_id" required>
                                        <option value="">Seleccionar dependencia</option>
                                        @foreach($dependencias as $dependencia)
                                        <option value="{{ $dependencia->id }}" {{ old('dependencia_id', $comite->
                                            dependencia_id) == $dependencia->id ? 'selected' : '' }}>
                                            {{ $dependencia->dependencia }} ({{ $dependencia->siglas }})
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="programa_id" class="form-label">Programa</label>
                                    <select class="form-select" id="programa_id" name="programa_id" required>
                                        <option value="">Seleccionar programa</option>
                                        @foreach($programas as $programa)
                                        <option value="{{ $programa->id }}" {{ old('programa_id', $comite->programa_id)
                                            == $programa->id ? 'selected' : '' }}>
                                            {{ $programa->nombre }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="id_estado" class="form-label">Estado</label>
                                    <select class="form-select" id="id_estado" name="id_estado" required>
                                        <option value="">Seleccionar Estado</option>
                                        @foreach($estados as $estado)
                                        <option value="{{ $estado->id_estado }}" {{ old('id_estado', $comite->id_estado)
                                            == $estado->id_estado ?
                                            'selected' : '' }}>
                                            {{ $estado->nombre }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="id_municipio" class="form-label">Municipio</label>
                                    <select class="form-select" id="id_municipio" name="id_municipio" required>
                                        <option value="">Cargando municipios...</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="id_localidad" class="form-label">Localidad</label>
                                    <select class="form-select" id="id_localidad" name="id_localidad" required>
                                        <option value="">Cargando localidades...</option>
                                    </select>
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

                        <h5 class="mt-4 text-tinto">Minuta del Comité</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="archivo_minuta" class="form-label">Archivo de Minuta (PDF)</label>

                                    <!-- Mostrar archivo actual si existe -->
                                    @if($comite->archivo_minuta)
                                    <div class="mb-2">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-file-pdf text-danger me-2"></i>
                                            <span>Minuta actual:
                                                <a href="{{ Storage::url($comite->archivo_minuta) }}" target="_blank"
                                                    class="text-decoration-none">
                                                    {{ basename($comite->archivo_minuta) }}
                                                </a>
                                            </span>
                                            <a href="{{ Storage::url($comite->archivo_minuta) }}" download
                                                class="btn btn-sm btn-outline-primary ms-2">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </div>
                                    </div>
                                    @endif

                                    <input type="file" class="form-control" id="archivo_minuta" name="archivo_minuta"
                                        accept=".pdf">
                                    <small class="text-muted">
                                        @if($comite->archivo_minuta)
                                        Suba un nuevo archivo para reemplazar la minuta actual (máx. 5MB)
                                        @else
                                        Suba el archivo PDF de la minuta de constitución del comité (máx. 5MB)
                                        @endif
                                    </small>
                                    @error('archivo_minuta')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <h5 class="mt-4 text-tinto">Lista de Asistencia</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="lista_asistencia" class="form-label">Archivo de Lista de
                                        Asistencia</label>

                                    <!-- Mostrar archivo actual si existe -->
                                    @if($comite->lista_asistencia)
                                    <div class="mb-2">
                                        <div class="d-flex align-items-center">
                                            @php
                                            $extensionLista = pathinfo($comite->lista_asistencia, PATHINFO_EXTENSION);
                                            $iconoLista = in_array(strtolower($extensionLista), ['pdf']) ? 'file-pdf' :
                                            (in_array(strtolower($extensionLista), ['doc', 'docx']) ? 'file-word' :
                                            'file-excel');
                                            @endphp
                                            <i class="fas fa-{{ $iconoLista }} text-primary me-2"></i>
                                            <span>Lista actual:
                                                <a href="{{ Storage::url($comite->lista_asistencia) }}" target="_blank"
                                                    class="text-decoration-none">
                                                    {{ basename($comite->lista_asistencia) }}
                                                </a>
                                            </span>
                                            <a href="{{ Storage::url($comite->lista_asistencia) }}" download
                                                class="btn btn-sm btn-outline-primary ms-2">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <form action="{{ route('comites.eliminar-lista', $comite) }}" method="POST"
                                                class="d-inline ms-2">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('¿Eliminar esta lista de asistencia?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    @endif

                                    <input type="file" class="form-control" id="lista_asistencia"
                                        name="lista_asistencia" accept=".pdf,.doc,.docx,.xls,.xlsx">
                                    <small class="text-muted">
                                        @if($comite->lista_asistencia)
                                        Suba un nuevo archivo para reemplazar la lista actual (PDF, Word o Excel, máx.
                                        5MB)
                                        @else
                                        Suba el archivo de lista de asistencia (PDF, Word o Excel, máx. 5MB)
                                        @endif
                                    </small>
                                    @error('lista_asistencia')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <h5 class="mt-4 text-tinto">Material de Difusión</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="material_difusion" class="form-label">Archivos de Material de
                                        Difusión</label>

                                    <!-- Mostrar archivos existentes -->
                                    @if(count($comite->material_difusion) > 0)
                                    <div class="mb-3">
                                        <h6>Archivos actuales:</h6>
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
                                                        class="fas fa-{{ str_replace('file-', '', $icono) }} {{ str_replace('text-', 'text-', $icono) }} me-2"></i>
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

                                    <input type="file" class="form-control" id="material_difusion"
                                        name="material_difusion[]" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
                                        multiple>
                                    <small class="text-muted">
                                        Puede agregar más archivos (PDF, Word, Excel, JPG o PNG, máx. 5MB cada uno)
                                    </small>
                                    @error('material_difusion.*')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <h5 class="mt-4 text-tinto">Fotografías de la Reunión</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="fotografias" class="form-label">Fotografías de la Reunión</label>

                                    <!-- Mostrar fotografías existentes -->
                                    @if(count($comite->fotografias_reunion) > 0)
                                    <div class="mb-3">
                                        <h6>Fotografías actuales:</h6>
                                        <div class="row" id="fotos-list">
                                            @foreach($comite->fotografias_reunion as $index => $foto)
                                            <div class="col-md-3 mb-3" id="foto-{{ $index }}">
                                                <div class="card">
                                                    <a href="{{ Storage::url($foto) }}" target="_blank">
                                                        <img src="{{ Storage::url($foto) }}" class="card-img-top"
                                                            alt="Foto {{ $index + 1 }}"
                                                            style="height: 150px; object-fit: cover;">
                                                    </a>
                                                    <div class="card-body p-2">
                                                        <div class="d-flex justify-content-between">
                                                            <a href="{{ Storage::url($foto) }}" download
                                                                class="btn btn-sm btn-outline-primary">
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

                                    <input type="file" class="form-control" id="fotografias" name="fotografias[]"
                                        accept=".jpg,.jpeg" multiple>
                                    <small class="text-muted">
                                        Puede agregar más fotografías (solo JPG, máx. 2MB cada una)
                                    </small>
                                    @error('fotografias.*')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-tinto">Actualizar Comité</button>
                            <a href="{{ route('comites.index') }}" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>

                    <!-- Gestión de elementos existentes -->
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
                                    <td>{{ $elemento->tipo_elemento }}</td>
                                    <td>
                                        @if($elemento->archivo_ine)
                                        @php
                                        $extension = pathinfo($elemento->archivo_ine, PATHINFO_EXTENSION);
                                        $isPdf = in_array(strtolower($extension), ['pdf']);
                                        $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']);
                                        @endphp

                                        @if($isPdf)
                                        <a href="{{ Storage::url($elemento->archivo_ine) }}" target="_blank"
                                            class="btn btn-info btn-sm" title="Ver INE (PDF)">
                                            <i class="fas fa-file-pdf"></i> Ver INE
                                        </a>
                                        <a href="{{ Storage::url($elemento->archivo_ine) }}" download
                                            class="btn btn-outline-info btn-sm" title="Descargar INE">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        @elseif($isImage)
                                        <a href="{{ Storage::url($elemento->archivo_ine) }}" target="_blank"
                                            class="btn btn-info btn-sm" title="Ver INE (Imagen)">
                                            <i class="fas fa-image"></i> Ver INE
                                        </a>
                                        <a href="{{ Storage::url($elemento->archivo_ine) }}" download
                                            class="btn btn-outline-info btn-sm" title="Descargar INE">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        @else
                                        <a href="{{ Storage::url($elemento->archivo_ine) }}" target="_blank"
                                            class="btn btn-secondary btn-sm" title="Ver Archivo">
                                            <i class="fas fa-file"></i> Ver Archivo
                                        </a>
                                        <a href="{{ Storage::url($elemento->archivo_ine) }}" download
                                            class="btn btn-outline-secondary btn-sm" title="Descargar Archivo">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        @endif
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
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('¿Estás seguro?')">
                                                <i class="fas fa-trash"></i> Eliminar
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
                        No hay elementos en este comité.
                    </div>
                    @endif

                    <!-- Formulario para agregar nuevo elemento -->
                    <form action="{{ route('comites.add-elemento', $comite) }}" method="POST" class="mt-4"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="nombre_completo"
                                    placeholder="Nombre completo" required>
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control" name="tipo_elemento"
                                    placeholder="Tipo (Presidente, Vocal, etc.)" required>
                            </div>
                            <div class="col-md-3">
                                <input type="file" class="form-control" name="archivo_ine"
                                    accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted">PDF, JPG o PNG (max 2MB)</small>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-success btn-sm">Agregar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cargar municipios y localidades al editar
        const estadoSelect = document.getElementById('id_estado');
        const municipioSelect = document.getElementById('id_municipio');
        const localidadSelect = document.getElementById('id_localidad');

        // Datos del comité para precargar
        const comiteEstadoId = {{ $comite->id_estado ?? 'null' }};
        const comiteMunicipioId = {{ $comite->id_municipio ?? 'null' }};
        const comiteLocalidadId = {{ $comite->id_localidad ?? 'null' }};

        // Función para obtener el token CSRF de manera segura
        function getCsrfToken() {
            // Intentar obtener del meta tag
            const metaTag = document.querySelector('meta[name="csrf-token"]');
            if (metaTag) {
                return metaTag.content;
            }

            // Buscar en inputs ocultos
            const csrfInput = document.querySelector('input[name="_token"]');
            if (csrfInput) {
                return csrfInput.value;
            }

            // Buscar en cualquier elemento con name="_token"
            const tokenElements = document.querySelectorAll('[name="_token"]');
            if (tokenElements.length > 0) {
                return tokenElements[0].value;
            }

            // Si Laravel está usando cookies, intentar obtener de la cookie
            const cookieMatch = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
            if (cookieMatch) {
                return decodeURIComponent(cookieMatch[1]);
            }

            console.warn('Token CSRF no encontrado');
            return null;
        }

        // Función para hacer peticiones fetch con headers apropiados
        function apiFetch(url, options = {}) {
            const token = getCsrfToken();
            const defaultHeaders = {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            };

            // Solo agregar token si existe
            if (token) {
                defaultHeaders['X-CSRF-TOKEN'] = token;
            }

            return fetch(url, {
                ...options,
                headers: {
                    ...defaultHeaders,
                    ...options.headers
                }
            });
        }

        // Función para cargar municipios
        function cargarMunicipios(estadoId, municipioIdSeleccionado = null) {
            if (estadoId) {
                municipioSelect.innerHTML = '<option value="">Cargando municipios...</option>';
                municipioSelect.disabled = true;

                apiFetch(`/api/municipios/${estadoId}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`Error ${response.status}: ${response.statusText}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        municipioSelect.innerHTML = '<option value="">Seleccionar Municipio</option>';
                        if (Array.isArray(data) && data.length > 0) {
                            data.forEach(municipio => {
                                const selected = municipioIdSeleccionado && municipio.id == municipioIdSeleccionado ? 'selected' : '';
                                municipioSelect.innerHTML += `<option value="${municipio.id}" ${selected}>${municipio.nombre}</option>`;
                            });
                        } else {
                            municipioSelect.innerHTML = '<option value="">No hay municipios disponibles</option>';
                        }
                        municipioSelect.disabled = false;

                        // Si hay municipio seleccionado, cargar localidades
                        if (municipioIdSeleccionado) {
                            cargarLocalidades(municipioIdSeleccionado, comiteLocalidadId);
                        }
                    })
                    .catch(error => {
                        console.error('Error cargando municipios:', error);
                        municipioSelect.innerHTML = `<option value="">Error: ${error.message}</option>`;
                        municipioSelect.disabled = false;
                    });
            }
        }

        // Función para cargar localidades
        function cargarLocalidades(municipioId, localidadIdSeleccionado = null) {
            if (municipioId) {
                localidadSelect.innerHTML = '<option value="">Cargando localidades...</option>';
                localidadSelect.disabled = true;

                apiFetch(`/api/localidades/${municipioId}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`Error ${response.status}: ${response.statusText}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        localidadSelect.innerHTML = '<option value="">Seleccionar Localidad</option>';
                        if (Array.isArray(data) && data.length > 0) {
                            data.forEach(localidad => {
                                const selected = localidadIdSeleccionado && localidad.id == localidadIdSeleccionado ? 'selected' : '';
                                localidadSelect.innerHTML += `<option value="${localidad.id}" ${selected}>${localidad.nombre}</option>`;
                            });
                        } else {
                            localidadSelect.innerHTML = '<option value="">No hay localidades disponibles</option>';
                        }
                        localidadSelect.disabled = false;
                    })
                    .catch(error => {
                        console.error('Error cargando localidades:', error);
                        localidadSelect.innerHTML = `<option value="">Error: ${error.message}</option>`;
                        localidadSelect.disabled = false;
                    });
            }
        }

        // Precargar municipios si hay estado seleccionado
        if (comiteEstadoId) {
            cargarMunicipios(comiteEstadoId, comiteMunicipioId);
        }

        // Event listeners para cambios
        estadoSelect.addEventListener('change', function() {
            const estadoId = this.value;

            // Reset municipio y localidad
            municipioSelect.innerHTML = '<option value="">Cargando municipios...</option>';
            municipioSelect.disabled = true;

            localidadSelect.innerHTML = '<option value="">Selecciona un municipio primero</option>';
            localidadSelect.disabled = true;

            if (estadoId) {
                cargarMunicipios(estadoId);
            }
        });

        municipioSelect.addEventListener('change', function() {
            const municipioId = this.value;

            // Reset localidad
            localidadSelect.innerHTML = '<option value="">Cargando localidades...</option>';
            localidadSelect.disabled = true;

            if (municipioId) {
                cargarLocalidades(municipioId);
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        // Manejar eliminación de material de difusión
        document.addEventListener('click', function(e) {
            if (e.target && (e.target.classList.contains('eliminar-material') ||
                e.target.closest('.eliminar-material'))) {
                const button = e.target.classList.contains('eliminar-material') ?
                              e.target : e.target.closest('.eliminar-material');

                const ruta = button.dataset.ruta;
                const comiteId = button.dataset.comite;

                if (confirm('¿Está seguro de eliminar este archivo?')) {
                    fetch(`/comites/${comiteId}/eliminar-material`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ archivo: ruta })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Eliminar elemento de la lista
                            button.closest('.list-group-item').remove();
                            alert(data.message);
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error al eliminar el archivo');
                    });
                }
            }

            // Manejar eliminación de fotografías
            if (e.target && (e.target.classList.contains('eliminar-foto') ||
                e.target.closest('.eliminar-foto'))) {
                const button = e.target.classList.contains('eliminar-foto') ?
                              e.target : e.target.closest('.eliminar-foto');

                const ruta = button.dataset.ruta;
                const comiteId = button.dataset.comite;

                if (confirm('¿Está seguro de eliminar esta fotografía?')) {
                    fetch(`/comites/${comiteId}/eliminar-foto`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ archivo: ruta })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Eliminar elemento de la lista
                            button.closest('.col-md-3').remove();
                            alert(data.message);
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error al eliminar la fotografía');
                    });
                }
            }
        });

        // Vista previa para nuevos archivos
        const materialDifusionInput = document.getElementById('material_difusion');
        const fotografiasInput = document.getElementById('fotografias');

        // Función para mostrar vista previa
        function mostrarVistaPrevia(input, contenedorId, esImagen = false) {
            const contenedor = document.getElementById(contenedorId);

            if (input.files.length > 0) {
                const previewTitle = document.createElement('h6');
                previewTitle.textContent = 'Archivos nuevos:';
                contenedor.appendChild(previewTitle);

                const fileList = document.createElement('div');
                fileList.className = esImagen ? 'row mt-2' : 'list-group mt-2';

                for (let i = 0; i < input.files.length; i++) {
                    const file = input.files[i];

                    if (esImagen) {
                        // Vista previa para imágenes
                        const col = document.createElement('div');
                        col.className = 'col-md-3 mb-3';

                        const card = document.createElement('div');
                        card.className = 'card';

                        const img = document.createElement('img');
                        img.className = 'card-img-top';
                        img.style.height = '150px';
                        img.style.objectFit = 'cover';

                        const reader = new FileReader();
                        reader.onload = function(e) {
                            img.src = e.target.result;
                        };
                        reader.readAsDataURL(file);

                        card.appendChild(img);

                        const cardBody = document.createElement('div');
                        cardBody.className = 'card-body p-2';

                        const fileName = document.createElement('small');
                        fileName.className = 'text-muted d-block text-truncate';
                        fileName.textContent = file.name;

                        cardBody.appendChild(fileName);
                        card.appendChild(cardBody);
                        col.appendChild(card);
                        fileList.appendChild(col);
                    } else {
                        // Vista previa para documentos
                        const listItem = document.createElement('div');
                        listItem.className = 'list-group-item d-flex justify-content-between align-items-center';

                        const nombre = document.createElement('span');
                        nombre.textContent = `${i + 1}. ${file.name}`;

                        const tamano = document.createElement('small');
                        tamano.className = 'text-muted ms-2';
                        tamano.textContent = `(${(file.size / 1024 / 1024).toFixed(2)} MB)`;

                        nombre.appendChild(tamano);
                        listItem.appendChild(nombre);

                        const badge = document.createElement('span');
                        badge.className = 'badge bg-info';
                        badge.textContent = file.type.split('/')[1].toUpperCase();
                        listItem.appendChild(badge);

                        fileList.appendChild(listItem);
                    }
                }

                contenedor.appendChild(fileList);
            }
        }

        // Crear contenedores para vista previa
        const materialPreviewContainer = document.createElement('div');
        materialPreviewContainer.id = 'material-preview';
        materialDifusionInput.parentNode.appendChild(materialPreviewContainer);

        const fotosPreviewContainer = document.createElement('div');
        fotosPreviewContainer.id = 'fotos-preview';
        fotografiasInput.parentNode.appendChild(fotosPreviewContainer);

        materialDifusionInput.addEventListener('change', function() {
            document.getElementById('material-preview').innerHTML = '';
            mostrarVistaPrevia(this, 'material-preview', false);
        });

        fotografiasInput.addEventListener('change', function() {
            document.getElementById('fotos-preview').innerHTML = '';
            mostrarVistaPrevia(this, 'fotos-preview', true);
        });
    });
</script>

<!-- Agregar Font Awesome para los íconos si no los tienes -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endsection