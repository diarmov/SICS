@extends('layouts.admin')

@section('title', 'Crear Programa - SICS')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-tinto text-white">
                    <h4 class="mb-0">Crear Nuevo Programa</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('programas.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
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
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre del Programa</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre"
                                        value="{{ old('nombre') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tipo_apoyo_id" class="form-label">Tipo de Apoyo</label>
                                    <select class="form-select" id="tipo_apoyo_id" name="tipo_apoyo_id" required>
                                        <option value="">Seleccionar tipo de apoyo</option>
                                        @foreach($tiposApoyo as $tipo)
                                        <option value="{{ $tipo->id }}" {{ old('tipo_apoyo_id')==$tipo->id ? 'selected'
                                            : '' }}>
                                            {{ $tipo->nombre }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="numero_beneficiarios" class="form-label">Número de Beneficiarios</label>
                                    <input type="number" class="form-control" id="numero_beneficiarios"
                                        name="numero_beneficiarios" value="{{ old('numero_beneficiarios', 0) }}" min="0"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="monto_vigilado" class="form-label">Monto Vigilado ($)</label>
                                    <input type="number" class="form-control" id="monto_vigilado" name="monto_vigilado"
                                        value="{{ old('monto_vigilado', 0) }}" min="0" step="0.01" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="fecha_inicio" class="form-label">Fecha de Inicio de ejecución</label>
                                    <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio"
                                        value="{{ old('fecha_inicio') }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="fecha_termino" class="form-label">Fecha de Término de ejecución</label>
                                    <input type="date" class="form-control" id="fecha_termino" name="fecha_termino"
                                        value="{{ old('fecha_termino') }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="periodo" class="form-label">Periodo (Año)</label>
                                    <input type="number" class="form-control" id="periodo" name="periodo" min="2000"
                                        max="2030" value="{{ old('periodo', date('Y')) }}" required>
                                </div>
                            </div>
                        </div>

                        <!-- Nuevo campo para número de informes -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="numero_informes" class="form-label">Número de Informes a
                                        Entregar</label>
                                    <input type="number" class="form-control" id="numero_informes"
                                        name="numero_informes" min="0" max="12"
                                        value="{{ old('numero_informes', isset($programa) ? $programa->numero_informes : 0) }}"
                                        required>
                                    <small class="form-text text-muted">Número máximo de informes que se deben entregar
                                        para este programa.</small>
                                </div>
                            </div>
                        </div>

                        <!-- Archivos PDF -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="archivo_pdf" class="form-label">Archivo PDF del Programa
                                        (Opcional)</label>
                                    <input type="file" class="form-control" id="archivo_pdf" name="archivo_pdf"
                                        accept=".pdf">
                                    <small class="form-text text-muted">Tamaño máximo: 10MB. Solo archivos PDF.</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="reglas_operacion_pdf" class="form-label">Reglas de Operación
                                        (PDF)</label>
                                    <input type="file" class="form-control" id="reglas_operacion_pdf"
                                        name="reglas_operacion_pdf" accept=".pdf">
                                    <small class="form-text text-muted">Archivo PDF de las reglas de operación. Tamaño
                                        máximo: 10MB.</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="guia_operativa_pdf" class="form-label">Guía Operativa (PDF)</label>
                                    <input type="file" class="form-control" id="guia_operativa_pdf"
                                        name="guia_operativa_pdf" accept=".pdf">
                                    <small class="form-text text-muted">Archivo PDF de la guía operativa. Tamaño máximo:
                                        10MB.</small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="activo" name="activo" value="1" {{
                                old('activo', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="activo">Programa Activo</label>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-tinto">Guardar Programa</button>
                            <a href="{{ route('programas.index') }}" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection