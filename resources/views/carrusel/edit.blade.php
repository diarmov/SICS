@extends('layouts.admin')

@section('title', 'Editar Imagen del Carrusel - SICS')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2>Editar Imagen del Carrusel</h2>
            <p class="text-muted">Modifica los datos de la imagen seleccionada.</p>
        </div>
        <div class="col text-end">
            <a href="{{ route('carrusel.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-tinto text-white">
            <h5 class="mb-0">Editar Imagen</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('carrusel.update', $carrusel) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="titulo" class="form-label">Título *</label>
                    <input type="text" class="form-control @error('titulo') is-invalid @enderror" id="titulo"
                        name="titulo" value="{{ old('titulo', $carrusel->titulo) }}" required>
                    @error('titulo')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="imagen" class="form-label">Imagen Actual</label>
                    <div class="mb-2">
                        <img src="{{ Storage::url($carrusel->imagen) }}" alt="{{ $carrusel->titulo }}"
                            style="max-width: 300px; max-height: 200px; border-radius: 5px;">
                    </div>
                    <label for="imagen" class="form-label">Cambiar Imagen (Opcional)</label>
                    <input type="file" class="form-control @error('imagen') is-invalid @enderror" id="imagen"
                        name="imagen" accept="image/*">
                    <small class="form-text text-muted">Formatos permitidos: JPG, PNG, GIF. Máximo 2MB.</small>
                    @error('imagen')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="url" class="form-label">URL (Opcional)</label>
                    <input type="url" class="form-control @error('url') is-invalid @enderror" id="url" name="url"
                        value="{{ old('url', $carrusel->url) }}" placeholder="https://ejemplo.com">
                    <small class="form-text text-muted">Si se proporciona, al hacer clic en la imagen se abrirá este
                        enlace.</small>
                    @error('url')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="orden" class="form-label">Orden</label>
                    <input type="number" class="form-control @error('orden') is-invalid @enderror" id="orden"
                        name="orden" value="{{ old('orden', $carrusel->orden) }}" min="0">
                    <small class="form-text text-muted">Define el orden de aparición (menor número = más a la
                        izquierda).</small>
                    @error('orden')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="activo" name="activo" value="1" {{
                            old('activo', $carrusel->activo) ? 'checked' : '' }}>
                        <label class="form-check-label" for="activo">Activo</label>
                        <small class="form-text text-muted d-block">Si está activo, aparecerá en el carrusel.</small>
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-tinto">
                        <i class="fas fa-save"></i> Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection