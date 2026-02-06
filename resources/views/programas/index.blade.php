@extends('layouts.admin')

@section('title', 'Programas - SICS')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="text-tinto">Gestión de Programas</h2>
                    <a href="{{ route('programas.create') }}" class="btn btn-tinto">Nuevo Programa</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <!-- En la tabla, agregar nueva columna -->
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Dependencia</th>
                                    <th>Periodo</th>
                                    <th>Tipo de Apoyo</th>
                                    <th>Beneficiarios</th>
                                    <th>Monto Vigilado</th>
                                    <th>Informes</th>
                                    <th>Inicio de ejecución</th>
                                    <th>Término de ejecución</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($programas as $programa)
                                <tr>
                                    <td>{{ $programa->nombre }}</td>
                                    <td>{{ $programa->dependencia->siglas }}</td>
                                    <td>{{ $programa->periodo }}</td>
                                    <td>{{ $programa->tipoApoyo->nombre ?? 'N/A' }}</td>
                                    <td>{{ number_format($programa->numero_beneficiarios) }}</td>
                                    <td>${{ number_format($programa->monto_vigilado, 2) }}</td>
                                    <td>
                                        <span
                                            class="badge {{ $programa->informes->count() > 0 ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $programa->informes->count() }}/{{ $programa->numero_informes }}
                                        </span>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($programa->fecha_inicio)->format('d/m/Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($programa->fecha_termino)->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge {{ $programa->activo ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $programa->activo ? 'Activo' : 'Inactivo' }}
                                        </span>
                                        @if($programa->esta_activo)
                                        <span class="badge bg-info">En Periodo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('programas.show', $programa) }}"
                                                class="btn btn-info btn-sm">Ver</a>
                                            <a href="{{ route('programas.informes', $programa) }}"
                                                class="btn btn-warning btn-sm">Informes</a>
                                            <a href="{{ route('programas.edit', $programa) }}"
                                                class="btn btn-primary btn-sm">Editar</a>
                                            <form action="{{ route('programas.destroy', $programa) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('¿Estás seguro?')">Eliminar</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($programas->isEmpty())
                    <div class="alert alert-info">
                        No hay programas registrados.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection