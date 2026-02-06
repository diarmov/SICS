@extends('layouts.admin')

@section('title', 'Editar Dependencia - SICS')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-tinto text-white">
                    <h4 class="mb-0">Editar Dependencia</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('dependencias.update', $dependencia) }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="dependencia" class="form-label">Nombre de la Dependencia</label>
                            <input id="dependencia" type="text"
                                class="form-control @error('dependencia') is-invalid @enderror" name="dependencia"
                                value="{{ old('dependencia', $dependencia->dependencia) }}" required
                                autocomplete="dependencia" autofocus>
                            @error('dependencia')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="siglas" class="form-label">Siglas</label>
                            <input id="siglas" type="text" class="form-control @error('siglas') is-invalid @enderror"
                                name="siglas" value="{{ old('siglas', $dependencia->siglas) }}" required
                                autocomplete="siglas">
                            @error('siglas')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="activo" name="activo" value="1" {{
                                old('activo', $dependencia->activo) ? 'checked' : '' }}>
                            <label class="form-check-label" for="activo">Dependencia Activa</label>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-tinto">Actualizar Dependencia</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection