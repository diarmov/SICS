@extends('layouts.admin')

@section('title', 'Ver Comité - SICS')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-tinto text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ $comite->nombre }}</h4>
                    <div>
                        <a href="{{ route('comites.edit', $comite) }}" class="btn btn-light btn-sm">Editar</a>
                        <a href="{{ route('comites.index') }}" class="btn btn-secondary btn-sm">Volver</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="text-tinto">Información del Comité</h5>
                            <p><strong>Dependencia:</strong> {{ $comite->dependencia->dependencia }} ({{
                                $comite->dependencia->siglas }})</p>
                            <p><strong>Programa:</strong> {{ $comite->programa->nombre }}</p>
                            <p><strong>Estado:</strong>
                                <span class="badge {{ $comite->activo ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $comite->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </p>
                            <p><strong>Minuta:</strong>
                                @if($comite->archivo_minuta)
                                <a href="{{ Storage::url($comite->archivo_minuta) }}" target="_blank"
                                    class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-file-pdf"></i> Ver Minuta
                                </a>
                                <a href="{{ Storage::url($comite->archivo_minuta) }}" download
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-download"></i> Descargar
                                </a>
                                @else
                                <span class="text-muted">No hay minuta cargada</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-tinto">Información del Programa</h5>
                            <p><strong>Periodo:</strong> {{ $comite->programa->periodo }}</p>
                            <p><strong>Vigencia:</strong>
                                {{ \Carbon\Carbon::parse($comite->programa->fecha_inicio)->format('d/m/Y') }} -
                                {{ \Carbon\Carbon::parse($comite->programa->fecha_termino)->format('d/m/Y') }}
                            </p>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h5 class="text-tinto">Elementos del Comité</h5>

                    @if($comite->elementos->count() > 0)
                    <div class="table-responsive">
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
                                    <td>
                                        <span class="badge bg-tinto">{{ $elemento->tipo_elemento }}</span>
                                    </td>
                                    <td>
                                        @if($elemento->archivo_ine)
                                        @if(pathinfo($elemento->archivo_ine, PATHINFO_EXTENSION) === 'pdf')
                                        <a href="{{ Storage::url($elemento->archivo_ine) }}" target="_blank"
                                            class="btn btn-info btn-sm">
                                            <i class="fas fa-file-pdf"></i> Ver INE
                                        </a>
                                        @else
                                        <a href="{{ Storage::url($elemento->archivo_ine) }}" target="_blank"
                                            class="btn btn-info btn-sm">
                                            <i class="fas fa-image"></i> Ver INE
                                        </a>
                                        @endif
                                        @else
                                        <span class="text-muted">Sin INE</span>
                                        @endif
                                    </td>
                                    <td>
                                        <form action="{{ route('comites.remove-elemento', $elemento) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('¿Estás seguro?')">Eliminar</button>
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
                                <button type="submit" class="btn btn-success">Agregar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection