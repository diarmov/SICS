@extends('layouts.admin')

@section('title', 'Tipos de Apoyo')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0 text-gray-800">Tipos de Apoyo</h1>
                @if(auth()->user()->hasRole(['SuperUsuario', 'AdministradorCS']))
                <a href="{{ route('tipos-apoyo.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Tipo de Apoyo
                </a>
                @endif
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nombre</th>
                            <th>Fecha de Alta</th>
                            <th>Estado</th>
                            <th class="text-center">Programas Asociados</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tiposApoyo as $tipo)
                        <tr>
                            <td>{{ $loop->iteration + (($tiposApoyo->currentPage() - 1) * $tiposApoyo->perPage()) }}
                            </td>
                            <td>{{ $tipo->nombre }}</td>
                            <td>{{ $tipo->fecha_alta->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge bg-{{ $tipo->activo ? 'success' : 'danger' }}">
                                    {{ $tipo->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info">{{ $tipo->programas->count() }}</span>
                            </td>

                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('tipos-apoyo.show', $tipo) }}" class="btn btn-info btn-sm"
                                        title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(auth()->user()->hasRole(['SuperUsuario', 'AdministradorCS']))
                                    <a href="{{ route('tipos-apoyo.edit', $tipo) }}" class="btn btn-sm btn-warning"
                                        title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('tipos-apoyo.toggle-status', $tipo) }}" method="POST"
                                        style="display: inline;">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit"
                                            class="btn btn-sm btn-{{ $tipo->activo ? 'secondary' : 'success' }}">
                                            <i class="fas fa-{{ $tipo->activo ? 'ban' : 'check' }}"
                                                title="activar o desactivar"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('tipos-apoyo.destroy', $tipo) }}" method="POST"
                                        style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('¿Está seguro de eliminar este tipo de apoyo?')"
                                            title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No hay tipos de apoyo registrados.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center">
                {{ $tiposApoyo->links() }}
            </div>
        </div>
    </div>
</div>

@endsection