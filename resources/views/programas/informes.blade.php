@extends('layouts.admin')

@section('title', 'Gestión de Informes - SICS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-tinto">Gestión de Informes - {{ $programa->nombre }}</h2>
    <div>
        <a href="{{ route('programas.index') }}" class="btn btn-secondary">Volver a Programas</a>
    </div>
</div>

<!-- Información del programa -->
<div class="card mb-4">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0">Información del Programa</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <p><strong>Dependencia:</strong> {{ $programa->dependencia->dependencia }}</p>
                <p><strong>Periodo:</strong> {{ $programa->periodo }}</p>
            </div>
            <div class="col-md-4">
                <p><strong>Fecha Inicio:</strong> {{ $programa->fecha_inicio->format('d/m/Y') }}</p>
                <p><strong>Fecha Término:</strong> {{ $programa->fecha_termino->format('d/m/Y') }}</p>
            </div>
            <div class="col-md-4">
                <p><strong>Informes:</strong> {{ $programa->informes->count() }}/{{ $programa->numero_informes }}</p>
                <p><strong>Estado:</strong>
                    <span class="badge {{ $programa->esta_activo ? 'bg-success' : 'bg-secondary' }}">
                        {{ $programa->esta_activo ? 'Dentro del Periodo' : 'Fuera del Periodo' }}
                    </span>
                </p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Formulario para agregar informe -->
    @if($programa->puede_agregar_informes)
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-tinto text-white">
                <h5 class="mb-0">Agregar Informe</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('programas.store-informe', $programa) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="numero_informe" class="form-label">Número de Informe</label>
                        <select class="form-control" id="numero_informe" name="numero_informe" required>
                            <option value="">Seleccionar número</option>
                            @for($i = 1; $i <= $programa->numero_informes; $i++)
                                @if(!$programa->informes->contains('numero_informe', $i))
                                <option value="{{ $i }}">Informe {{ $i }}</option>
                                @endif
                                @endfor
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre del Informe</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>

                    <div class="mb-3">
                        <label for="fecha_entrega" class="form-label">Fecha de Entrega</label>
                        <input type="date" class="form-control" id="fecha_entrega" name="fecha_entrega" required>
                    </div>

                    <div class="mb-3">
                        <label for="archivo" class="form-label">Archivo del Informe</label>
                        <input type="file" class="form-control" id="archivo" name="archivo"
                            accept=".doc,.docx,.xls,.xlsx,.pdf" required>
                        <small class="form-text text-muted">Formatos aceptados: Word, Excel, PDF. Tamaño máximo:
                            10MB.</small>
                    </div>

                    <div class="mb-3">
                        <label for="observaciones" class="form-label">Observaciones</label>
                        <textarea class="form-control" id="observaciones" name="observaciones" rows="3"></textarea>
                    </div>

                    <button type="submit" class="btn btn-tinto w-100">Agregar Informe</button>
                </form>
            </div>
        </div>
    </div>
    @else
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">Información</h5>
            </div>
            <div class="card-body">
                @if(!$programa->esta_activo)
                <div class="alert alert-warning">
                    El programa está fuera de su periodo de vigencia. No se pueden agregar informes.
                </div>
                @elseif($programa->informes->count() >= $programa->numero_informes)
                <div class="alert alert-info">
                    Se ha alcanzado el número máximo de informes para este programa.
                </div>
                @endif
                <p><strong>Informes pendientes:</strong> {{ $programa->informes_pendientes }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Lista de informes existentes -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Informes Registrados</h5>
            </div>
            <div class="card-body">
                @if($programa->informes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre</th>
                                <th>Archivo</th>
                                <th>Fecha Entrega</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($programa->informes as $informe)
                            <tr>
                                <td>{{ $informe->numero_informe }}</td>
                                <td>{{ $informe->nombre }}</td>
                                <td>
                                    @if($informe->archivo)
                                    <a href="{{ asset('storage/' . $informe->archivo) }}" target="_blank"
                                        class="btn btn-outline-primary btn-sm">
                                        Descargar
                                    </a>
                                    @else
                                    <span class="text-muted">Sin archivo</span>
                                    @endif
                                </td>
                                <td>{{ $informe->fecha_entrega->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge {{ $informe->entregado ? 'bg-success' : 'bg-warning' }}">
                                        {{ $informe->entregado ? 'Entregado' : 'Pendiente' }}
                                    </span>
                                </td>
                                <td>
                                    <form action="{{ route('programas.destroy-informe', $informe) }}" method="POST"
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
                    No hay informes registrados para este programa.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection