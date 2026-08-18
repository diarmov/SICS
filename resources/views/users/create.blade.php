@extends('layouts.admin')

@section('title', 'Crear Usuario - SICS')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-tinto text-white">
                    <h4 class="mb-0">Crear Nuevo Usuario</h4>
                </div>
                <div class="card-body">
                    @if(auth()->user()->hasRole('Instancia_Normativa'))
                    <div class="alert alert-info">
                        <strong>Información:</strong> Como Instancia Normativa, solo puedes crear usuarios con rol
                        <strong>"Instancia Ejecutora"</strong> para tu dependencia y vincularlos a un programa
                        específico.
                    </div>
                    @endif

                    <form method="POST" action="{{ route('users.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre(s)</label>
                                    <input id="nombre" type="text"
                                        class="form-control @error('nombre') is-invalid @enderror" name="nombre"
                                        value="{{ old('nombre') }}" required autocomplete="nombre" autofocus>
                                    @error('nombre')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="apellido_paterno" class="form-label">Apellido Paterno</label>
                                    <input id="apellido_paterno" type="text"
                                        class="form-control @error('apellido_paterno') is-invalid @enderror"
                                        name="apellido_paterno" value="{{ old('apellido_paterno') }}" required
                                        autocomplete="apellido_paterno">
                                    @error('apellido_paterno')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="apellido_materno" class="form-label">Apellido Materno</label>
                                    <input id="apellido_materno" type="text"
                                        class="form-control @error('apellido_materno') is-invalid @enderror"
                                        name="apellido_materno" value="{{ old('apellido_materno') }}" required
                                        autocomplete="apellido_materno">
                                    @error('apellido_materno')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Correo Electrónico</label>
                                    <input id="email" type="email"
                                        class="form-control @error('email') is-invalid @enderror" name="email"
                                        value="{{ old('email') }}" required autocomplete="email">
                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="dependencia_id" class="form-label">Dependencia</label>
                                    <select id="dependencia_id"
                                        class="form-control @error('dependencia_id') is-invalid @enderror"
                                        name="dependencia_id" required>
                                        <option value="">Seleccionar Dependencia</option>
                                        @foreach($dependencias as $dependencia)
                                        <option value="{{ $dependencia->id }}" {{ old('dependencia_id')==$dependencia->
                                            id ? 'selected' : '' }}>{{ $dependencia->dependencia }} ({{
                                            $dependencia->siglas }})</option>
                                        @endforeach
                                    </select>
                                    @error('dependencia_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Contraseña</label>
                                    <div class="password-toggle-wrapper">
                                        <input id="password" type="password"
                                            class="form-control @error('password') is-invalid @enderror" name="password"
                                            required autocomplete="new-password">
                                        <span class="password-toggle-icon" id="togglePassword"
                                            title="Mostrar/Ocultar contraseña">
                                            <i class="fas fa-eye"></i>
                                        </span>
                                    </div>
                                    @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password-confirm" class="form-label">Confirmar Contraseña</label>
                                    <div class="password-toggle-wrapper">
                                        <input id="password-confirm" type="password" class="form-control"
                                            name="password_confirmation" required autocomplete="new-password">
                                        <span class="password-toggle-icon" id="togglePasswordConfirm"
                                            title="Mostrar/Ocultar contraseña">
                                            <i class="fas fa-eye"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="alert-info">La contraseña debe contener al menos 8 caracteres, para mayor
                                seguridad combinar entre mayúsculas, minúscilas, números o caracteres especiales.</div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="rol" class="form-label">Rol</label>
                                    <select id="rol" class="form-control @error('rol') is-invalid @enderror" name="rol"
                                        required>
                                        <option value="">Seleccionar Rol</option>
                                        @foreach($roles as $rol)
                                        <option value="{{ $rol->name }}" {{ old('rol')==$rol->name ? 'selected' : ''
                                            }}>{{ $rol->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('rol')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="programa_id" class="form-label">Programa Asociado</label>
                                    <select id="programa_id"
                                        class="form-control @error('programa_id') is-invalid @enderror"
                                        name="programa_id">
                                        <option value="">Seleccionar Programa</option>
                                        @if(auth()->user()->hasRole('Instancia_Normativa'))
                                        @foreach($programas as $programa)
                                        <option value="{{ $programa->id }}" {{ old('programa_id')==$programa->id ?
                                            'selected' : '' }}>
                                            {{ $programa->nombre }}
                                        </option>
                                        @endforeach
                                        @else
                                        {{-- Para SuperUsuario y Organo_Estatal_de_Control, mostrar todos --}}
                                        @foreach($programas as $programa)
                                        <option value="{{ $programa->id }}" {{ old('programa_id')==$programa->id ?
                                            'selected' : '' }}>
                                            {{ $programa->nombre }}
                                        </option>
                                        @endforeach
                                        @endif
                                    </select>
                                    @error('programa_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        @if(auth()->user()->hasRole('Instancia_Normativa'))
                                        Solo se muestran los programas de tu dependencia.
                                        @else
                                        Selecciona el programa al que estará vinculado este usuario.
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3 form-check mt-4">
                                    <input type="checkbox" class="form-check-input" id="activo" name="activo" value="1"
                                        {{ old('activo', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="activo">Usuario Activo</label>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-tinto">Guardar Usuario</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .password-toggle-wrapper {
        position: relative;
    }

    .password-toggle-wrapper .form-control {
        padding-right: 45px;
    }

    .password-toggle-icon {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #6c757d;
        z-index: 10;
    }

    .password-toggle-icon:hover {
        color: var(--color-tinto);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dependenciaSelect = document.getElementById('dependencia_id');
        const programaSelect = document.getElementById('programa_id');

        // Solo si el usuario es SuperUsuario o Organo_Estatal_de_Control
        @if(auth()->user()->hasRole(['SuperUsuario', 'Organo_Estatal_de_Control']))
        if (dependenciaSelect && programaSelect) {
            dependenciaSelect.addEventListener('change', function() {
                const dependenciaId = this.value;

                programaSelect.innerHTML = '<option value="">Cargando programas...</option>';

                if (dependenciaId) {
                    const token = document.querySelector('meta[name="csrf-token"]')?.content;

                    fetch(`/api/programas-por-dependencia/${dependenciaId}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': token
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        programaSelect.innerHTML = '<option value="">Seleccionar Programa</option>';
                        if (Array.isArray(data) && data.length > 0) {
                            data.forEach(programa => {
                                const option = document.createElement('option');
                                option.value = programa.id;
                                option.textContent = programa.nombre;
                                programaSelect.appendChild(option);
                            });
                        } else {
                            programaSelect.innerHTML = '<option value="">No hay programas disponibles</option>';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        programaSelect.innerHTML = '<option value="">Error al cargar programas</option>';
                    });
                } else {
                    programaSelect.innerHTML = '<option value="">Selecciona una dependencia primero</option>';
                }
            });
        }
        @endif
    });
</script>
@endsection