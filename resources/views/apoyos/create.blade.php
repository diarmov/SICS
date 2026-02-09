@extends('layouts.admin')

@section('title', 'Nuevo Tipo de Apoyo')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0 text-gray-800">Nuevo Tipo de Apoyo</h1>
                <a href="{{ route('tipos-apoyo.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver al listado
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Datos del Tipo de Apoyo</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('tipos-apoyo.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="nombre" class="form-label">Nombre del Tipo de Apoyo *</label>
                                    <input type="text" class="form-control @error('nombre') is-invalid @enderror"
                                        id="nombre" name="nombre" value="{{ old('nombre') }}"
                                        placeholder="Ej: Apoyo Alimentario" required>
                                    @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Nombre descriptivo del tipo de apoyo que se brindará.
                                    </small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="fecha_alta" class="form-label">Fecha de Alta *</label>
                                    <input type="date" class="form-control @error('fecha_alta') is-invalid @enderror"
                                        id="fecha_alta" name="fecha_alta" value="{{ old('fecha_alta', date('Y-m-d')) }}"
                                        required>
                                    @error('fecha_alta')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Fecha en que se da de alta este tipo de apoyo en el sistema.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <!-- Campo oculto para enviar valor 0 cuando el checkbox no está marcado -->
                            <input type="hidden" name="activo" value="0">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="activo" name="activo" value="1" {{
                                    old('activo', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="activo">Activo</label>
                            </div>
                            <small class="form-text text-muted">
                                Los tipos de apoyo inactivos no aparecerán en las listas desplegables al crear
                                programas.
                            </small>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar
                            </button>
                            <a href="{{ route('tipos-apoyo.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Información Importante</h5>
                </div>
                <div class="card-body">
                    <h6><i class="fas fa-info-circle text-info"></i> Consideraciones:</h6>
                    <ul class="small">
                        <li>El nombre debe ser claro y descriptivo.</li>
                        <li>Asegúrate de que el tipo de apoyo no esté duplicado.</li>
                        <li>Los tipos inactivos no estarán disponibles para nuevos programas.</li>
                        <li>Este catálogo es utilizado en la creación y edición de programas.</li>
                    </ul>

                    <hr>

                    <h6><i class="fas fa-exclamation-triangle text-warning"></i> Notas:</h6>
                    <ul class="small">
                        <li>No se puede eliminar un tipo de apoyo si tiene programas asociados.</li>
                        <li>Para deshabilitar temporalmente un tipo de apoyo, usa el botón "Desactivar".</li>
                        <li>Todos los cambios son registrados en la bitácora del sistema.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@if($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'error',
            title: 'Error de Validación',
            html: `@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach`,
            showConfirmButton: true
        });
    });
</script>
@endif
@endsection