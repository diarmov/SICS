@extends('layouts.admin')

@section('title', 'Editar Tipo de Apoyo')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0 text-gray-800">Editar Tipo de Apoyo</h1>
                <a href="{{ route('tipos-apoyo.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver al listado
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">Editar Datos del Tipo de Apoyo</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('tipos-apoyo.update', $tipoApoyo) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <!-- IMPORTANTE: Esto es para Laravel -->

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="nombre" class="form-label">Nombre del Tipo de Apoyo *</label>
                                    <input type="text" class="form-control @error('nombre') is-invalid @enderror"
                                        id="nombre" name="nombre" value="{{ old('nombre', $tipoApoyo->nombre) }}"
                                        placeholder="Ej: Apoyo Alimentario" required>
                                    @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Nombre descriptivo del tipo de apoyo.
                                    </small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="fecha_alta" class="form-label">Fecha de Alta *</label>
                                    <input type="date" class="form-control @error('fecha_alta') is-invalid @enderror"
                                        id="fecha_alta" name="fecha_alta"
                                        value="{{ old('fecha_alta', $tipoApoyo->fecha_alta->format('Y-m-d')) }}"
                                        required>
                                    @error('fecha_alta')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Fecha de alta del tipo de apoyo.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <!-- Campo oculto para enviar valor 0 cuando el checkbox no está marcado -->
                            <input type="hidden" name="activo" value="0">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="activo" name="activo" value="1" {{
                                    old('activo', $tipoApoyo->activo) ? 'checked' : '' }}>
                                <label class="form-check-label" for="activo">
                                    <strong>Activo</strong>
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                Los tipos de apoyo inactivos no estarán disponibles para nuevos programas.
                            </small>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Cambios
                            </button>
                            <button type="reset" class="btn btn-secondary">
                                <i class="fas fa-undo"></i> Restaurar
                            </button>
                            <a href="{{ route('tipos-apoyo.show', $tipoApoyo) }}" class="btn btn-info">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Información Actual</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th>Nombre actual:</th>
                            <td>{{ $tipoApoyo->nombre }}</td>
                        </tr>
                        <tr>
                            <th>Fecha alta:</th>
                            <td>{{ $tipoApoyo->fecha_alta->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <th>Estado:</th>
                            <td>
                                <span class="badge badge-{{ $tipoApoyo->activo ? 'success' : 'danger' }}">
                                    {{ $tipoApoyo->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Programas:</th>
                            <td>
                                <span class="badge badge-info">{{ $tipoApoyo->programas->count() }}</span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card shadow">
                <div class="card-header bg-danger text-white">
                    <h6 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Zona de Peligro</h6>
                </div>
                <div class="card-body">
                    @if($tipoApoyo->programas->count() == 0)
                    <form action="{{ route('tipos-apoyo.destroy', $tipoApoyo) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block"
                            onclick="return confirm('¿Está completamente seguro de eliminar este tipo de apoyo?\n\n⚠️ Esta acción NO se puede deshacer.')">
                            <i class="fas fa-trash"></i> Eliminar Tipo de Apoyo
                        </button>
                    </form>
                    @else
                    <div class="alert alert-warning mb-0">
                        <i class="fas fa-ban"></i>
                        <strong>No se puede eliminar:</strong> Este tipo de apoyo tiene
                        {{ $tipoApoyo->programas->count() }} programa(s) asociado(s).
                        Para deshabilitarlo, desactívalo desde la opción "Desactivar".
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection