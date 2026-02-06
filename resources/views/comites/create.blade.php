@extends('layouts.admin')

@section('title', 'Crear Comité - SICS')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-tinto text-white">
                    <h4 class="mb-0">Crear Nuevo Comité de Vigilancia</h4>
                </div>
                <div class="card-body">
                    <!-- AGREGAR enctype="multipart/form-data" -->
                    <form action="{{ route('comites.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre del Comité</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre"
                                        value="{{ old('nombre') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="dependencia_id" class="form-label">Dependencia</label>
                                    <select class="form-select" id="dependencia_id" name="dependencia_id" required>
                                        <option value="">Seleccionar dependencia</option>
                                        @foreach($dependencias as $dependencia)
                                        <option value="{{ $dependencia->id }}" {{ old('dependencia_id')==$dependencia->
                                            id ? 'selected' : '' }}>
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
                                        <option value="{{ $programa->id }}" {{ old('programa_id')==$programa->id ?
                                            'selected' : '' }}>
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
                                        <option value="{{ $estado->id_estado }}" {{ old('id_estado')==$estado->id_estado
                                            ?
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
                                    <select class="form-select" id="id_municipio" name="id_municipio" required disabled>
                                        <option value="">Primero selecciona un Estado</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="id_localidad" class="form-label">Localidad</label>
                                    <select class="form-select" id="id_localidad" name="id_localidad" required disabled>
                                        <option value="">Primero selecciona un Municipio</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3 form-check mt-4">
                                    <input type="checkbox" class="form-check-input" id="activo" name="activo" value="1"
                                        {{ old('activo', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="activo">Activo</label>
                                </div>
                            </div>
                        </div>

                        <!-- NUEVA SECCIÓN: Archivo de Minuta -->
                        <h5 class="mt-4 text-tinto">Minuta del Comité</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="archivo_minuta" class="form-label">Archivo de Minuta (PDF)</label>
                                    <input type="file" class="form-control" id="archivo_minuta" name="archivo_minuta"
                                        accept=".pdf">
                                    <small class="text-muted">Suba el archivo PDF de la minuta de constitución del
                                        comité (máx. 5MB)</small>
                                    @error('archivo_minuta')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <h5 class="mt-4 text-tinto">Elementos del Comité</h5>

                        <div id="elementos-container">
                            <div class="elemento-row row mb-3">
                                <div class="col-md-4">
                                    <input type="text" class="form-control" name="elementos[0][nombre_completo]"
                                        placeholder="Nombre completo" required>
                                </div>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" name="elementos[0][tipo_elemento]"
                                        placeholder="Tipo (Presidente, Vocal, etc.)" required>
                                </div>
                                <div class="col-md-3">
                                    <input type="file" class="form-control" name="elementos[0][archivo_ine]"
                                        accept=".pdf,.jpg,.jpeg,.png">
                                    <small class="text-muted">INE (PDF, JPG o PNG, máx. 2MB)</small>
                                </div>
                                <div class="col-md-2">
                                    <button type="button"
                                        class="btn btn-danger btn-sm remove-elemento">Eliminar</button>
                                </div>
                            </div>
                        </div>

                        <button type="button" id="add-elemento" class="btn btn-secondary btn-sm mb-3">Agregar
                            Elemento</button>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-tinto">Guardar Comité</button>
                            <a href="{{ route('comites.index') }}" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Elementos del comité
        let elementoCount = 1;
        document.getElementById('add-elemento').addEventListener('click', function() {
            const container = document.getElementById('elementos-container');
            const newRow = document.createElement('div');
            newRow.className = 'elemento-row row mb-3';
            newRow.innerHTML = `
                <div class="col-md-4">
                    <input type="text" class="form-control" name="elementos[${elementoCount}][nombre_completo]" placeholder="Nombre completo" required>
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control" name="elementos[${elementoCount}][tipo_elemento]" placeholder="Tipo (Presidente, Vocal, etc.)" required>
                </div>
                <div class="col-md-3">
                    <input type="file" class="form-control" name="elementos[${elementoCount}][archivo_ine]" accept=".pdf,.jpg,.jpeg,.png">
                    <small class="text-muted">INE (PDF, JPG o PNG, máx. 2MB)</small>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm remove-elemento">Eliminar</button>
                </div>
            `;
            container.appendChild(newRow);
            elementoCount++;
        });

        document.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('remove-elemento')) {
                if (document.querySelectorAll('.elemento-row').length > 1) {
                    e.target.closest('.elemento-row').remove();
                } else {
                    alert('Debe haber al menos un elemento en el comité.');
                }
            }
        });

        // Selects dependientes (tu código existente)
        const estadoSelect = document.getElementById('id_estado');
        const municipioSelect = document.getElementById('id_municipio');
        const localidadSelect = document.getElementById('id_localidad');

        // Obtener token CSRF de una forma alternativa
        function getCsrfToken() {
            const metaTag = document.querySelector('meta[name="csrf-token"]');
            if (metaTag) {
                return metaTag.getAttribute('content');
            }
            const csrfInput = document.querySelector('input[name="_token"]');
            if (csrfInput) {
                return csrfInput.value;
            }
            const tokenInputs = document.querySelectorAll('[name="_token"]');
            if (tokenInputs.length > 0) {
                return tokenInputs[0].value;
            }
            console.warn('Token CSRF no encontrado');
            return null;
        }

        estadoSelect.addEventListener('change', function() {
            const estadoId = this.value;
            municipioSelect.innerHTML = '<option value="">Cargando municipios...</option>';
            municipioSelect.disabled = true;
            localidadSelect.innerHTML = '<option value="">Selecciona un municipio primero</option>';
            localidadSelect.disabled = true;

            if (estadoId) {
                const token = getCsrfToken();
                const headers = {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                };
                if (token) {
                    headers['X-CSRF-TOKEN'] = token;
                }

                fetch(`/api/municipios/${estadoId}`, {
                    headers: headers
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la respuesta del servidor: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    municipioSelect.innerHTML = '<option value="">Seleccionar Municipio</option>';
                    if (Array.isArray(data) && data.length > 0) {
                        data.forEach(municipio => {
                            municipioSelect.innerHTML += `<option value="${municipio.id}">${municipio.nombre}</option>`;
                        });
                    } else {
                        municipioSelect.innerHTML = '<option value="">No hay municipios disponibles</option>';
                    }
                    municipioSelect.disabled = false;
                })
                .catch(error => {
                    console.error('Error:', error);
                    municipioSelect.innerHTML = '<option value="">Error al cargar municipios: ' + error.message + '</option>';
                    municipioSelect.disabled = false;
                });
            } else {
                municipioSelect.innerHTML = '<option value="">Primero selecciona un Estado</option>';
                municipioSelect.disabled = true;
            }
        });

        municipioSelect.addEventListener('change', function() {
            const municipioId = this.value;
            localidadSelect.innerHTML = '<option value="">Cargando localidades...</option>';
            localidadSelect.disabled = true;

            if (municipioId) {
                const token = getCsrfToken();
                const headers = {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                };
                if (token) {
                    headers['X-CSRF-TOKEN'] = token;
                }

                fetch(`/api/localidades/${municipioId}`, {
                    headers: headers
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la respuesta del servidor: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    localidadSelect.innerHTML = '<option value="">Seleccionar Localidad</option>';
                    if (Array.isArray(data) && data.length > 0) {
                        data.forEach(localidad => {
                            localidadSelect.innerHTML += `<option value="${localidad.id}">${localidad.nombre}</option>`;
                        });
                    } else {
                        localidadSelect.innerHTML = '<option value="">No hay localidades disponibles</option>';
                    }
                    localidadSelect.disabled = false;
                })
                .catch(error => {
                    console.error('Error:', error);
                    localidadSelect.innerHTML = '<option value="">Error al cargar localidades: ' + error.message + '</option>';
                    localidadSelect.disabled = false;
                });
            } else {
                localidadSelect.innerHTML = '<option value="">Primero selecciona un Municipio</option>';
                localidadSelect.disabled = true;
            }
        });
    });
</script>
@endsection