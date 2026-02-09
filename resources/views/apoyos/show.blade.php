@extends('layouts.admin')

@section('title', 'Detalles del Tipo de Apoyo')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0 text-gray-800">Detalles del Tipo de Apoyo</h1>
                <div>
                    <a href="{{ route('tipos-apoyo.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                    @if(auth()->user()->hasRole(['SuperUsuario', 'AdministradorCS']))
                    <a href="{{ route('tipos-apoyo.edit', $tipoApoyo) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Información General</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%" class="bg-light">Nombre:</th>
                            <td>{{ $tipoApoyo->nombre }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Fecha de Alta:</th>
                            <td>{{ $tipoApoyo->fecha_alta->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Estado:</th>
                            <td>
                                <span class="badge badge-{{ $tipoApoyo->activo ? 'success' : 'danger' }}">
                                    {{ $tipoApoyo->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="bg-light">Creado:</th>
                            <td>{{ $tipoApoyo->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Actualizado:</th>
                            <td>{{ $tipoApoyo->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>

                    @if(auth()->user()->hasRole(['SuperUsuario', 'AdministradorCS']))
                    <div class="mt-4">
                        <form action="{{ route('tipos-apoyo.toggle-status', $tipoApoyo) }}" method="POST"
                            class="d-inline">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-{{ $tipoApoyo->activo ? 'secondary' : 'success' }}">
                                <i class="fas fa-{{ $tipoApoyo->activo ? 'ban' : 'check' }}"></i>
                                {{ $tipoApoyo->activo ? 'Desactivar' : 'Activar' }}
                            </button>
                        </form>

                        <form action="{{ route('tipos-apoyo.destroy', $tipoApoyo) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger"
                                onclick="return confirm('¿Está seguro de eliminar este tipo de apoyo?\n\n⚠️ Esta acción no se puede deshacer.')">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-list"></i> Programas Asociados</h5>
                    <span class="badge badge-light">{{ $tipoApoyo->programas->count() }}</span>
                </div>
                <div class="card-body">
                    @if($tipoApoyo->programas->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Programa</th>
                                    <th>Dependencia</th>
                                    <th>Periodo</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tipoApoyo->programas as $programa)
                                <tr>
                                    <td>
                                        <a href="{{ route('programas.show', $programa) }}" class="text-primary">
                                            <i class="fas fa-external-link-alt fa-xs"></i>
                                            {{ $programa->nombre }}
                                        </a>
                                    </td>
                                    <td>{{ $programa->dependencia->siglas }}</td>
                                    <td>{{ $programa->periodo }}</td>
                                    <td>
                                        <span class="badge badge-{{ $programa->activo ? 'success' : 'danger' }}">
                                            {{ $programa->activo ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle"></i>
                        No hay programas asociados a este tipo de apoyo.
                    </div>
                    @endif
                </div>
            </div>

            @if($tipoApoyo->programas->count() > 0)
            <div class="card shadow">
                <div class="card-header bg-warning text-white">
                    <h6 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Advertencia</h6>
                </div>
                <div class="card-body">
                    <p class="mb-0 small">
                        <strong>Nota:</strong> Este tipo de apoyo no puede ser eliminado porque tiene
                        {{ $tipoApoyo->programas->count() }} programa(s) asociado(s).
                        Si deseas deshabilitarlo, usa el botón "Desactivar" en lugar de "Eliminar".
                    </p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection