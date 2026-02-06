@extends('layouts.admin')

@section('title', 'Ver Programa - SICS')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-tinto text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ $programa->nombre }}</h4>
                    <div>
                        <a href="{{ route('programas.edit', $programa) }}" class="btn btn-light btn-sm">Editar</a>
                        <a href="{{ route('programas.index') }}" class="btn btn-secondary btn-sm">Volver</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="text-tinto">Información del Programa</h5>
                            <p><strong>Dependencia:</strong> {{ $programa->dependencia->dependencia }} ({{
                                $programa->dependencia->siglas }})</p>
                            <p><strong>Periodo:</strong> {{ $programa->periodo }}</p>
                            <p><strong>Fecha de Inicio de ejecución:</strong> {{
                                \Carbon\Carbon::parse($programa->fecha_inicio)->format('d/m/Y') }}</p>
                            <p><strong>Fecha de Término de ejecución:</strong> {{
                                \Carbon\Carbon::parse($programa->fecha_termino)->format('d/m/Y') }}</p>
                            <p><strong>Estado:</strong>
                                <span class="badge {{ $programa->activo ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $programa->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-tinto">Archivos</h5>
                            @if($programa->archivo_pdf)
                            <p>
                                <strong>Programa PDF:</strong>
                                <a href="{{ asset('storage/' . $programa->archivo_pdf) }}" target="_blank"
                                    class="btn btn-outline-tinto btn-sm">
                                    Ver Programa
                                </a>
                            </p>
                            @else
                            <p class="text-muted">No hay archivo PDF cargado para este programa.</p>
                            @endif
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Sección para cargar archivo de beneficiarios -->
                    <h5 class="text-tinto">Cargar Archivo de Beneficiarios</h5>
                    <form action="{{ route('programas.upload-beneficiarios', $programa) }}" method="POST"
                        enctype="multipart/form-data" class="mb-4">
                        @csrf
                        <div class="row">
                            <div class="col-md-8">
                                <input type="file" class="form-control" name="archivo_beneficiarios"
                                    accept=".xlsx,.xls,.csv" required>
                                <small class="form-text text-muted">Formatos aceptados: Excel (.xlsx, .xls) o CSV.
                                    Tamaño máximo: 10MB.</small>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-tinto">Cargar Beneficiarios</button>
                            </div>
                        </div>
                    </form>

                    <!-- Información adicional del programa -->
                    <div class="mt-4">
                        <h5 class="text-tinto">Comités de Vigilancia Asociados</h5>
                        @if($programa->comitesVigilancia->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Nombre del Comité</th>
                                        <th>Elementos</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($programa->comitesVigilancia as $comite)
                                    <tr>
                                        <td>{{ $comite->nombre }}</td>
                                        <td>{{ $comite->elementos->count() }}</td>
                                        <td>
                                            <span class="badge {{ $comite->activo ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $comite->activo ? 'Activo' : 'Inactivo' }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="alert alert-info">
                            No hay comités de vigilancia asociados a este programa.
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection