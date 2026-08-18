@extends('layouts.admin')

@section('title', 'Editar Usuario - SICS')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-tinto text-white">
                    <h4 class="mb-0">Editar Usuario</h4>
                </div>
                <div class="card-body">
                    <!-- Mensajes de éxito -->
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    @if(session('password_success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i> {{ session('password_success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <!-- Formulario de información del usuario -->
                    <form method="POST" action="{{ route('users.update', $user) }}">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre(s)</label>
                                    <input id="nombre" type="text"
                                        class="form-control @error('nombre') is-invalid @enderror" name="nombre"
                                        value="{{ old('nombre', $user->nombre) }}" required autocomplete="nombre"
                                        autofocus>
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
                                        name="apellido_paterno"
                                        value="{{ old('apellido_paterno', $user->apellido_paterno) }}" required
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
                                        name="apellido_materno"
                                        value="{{ old('apellido_materno', $user->apellido_materno) }}" required
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
                                        value="{{ old('email', $user->email) }}" required autocomplete="email">
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
                                        <option value="{{ $dependencia->id }}" {{ old('dependencia_id', $user->
                                            dependencia_id) == $dependencia->id ? 'selected' : '' }}>{{
                                            $dependencia->dependencia }} ({{ $dependencia->siglas }})</option>
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
                                    <label for="rol" class="form-label">Rol</label>
                                    <select id="rol" class="form-control @error('rol') is-invalid @enderror" name="rol"
                                        required>
                                        <option value="">Seleccionar Rol</option>
                                        @foreach($roles as $rol)
                                        <option value="{{ $rol->name }}" {{ old('rol', $user->getRoleNames()->first())
                                            == $rol->name ? 'selected' : '' }}>{{ $rol->name }}</option>
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
                                        <option value="">Sin programa asignado</option>
                                        @foreach($programas as $programa)
                                        <option value="{{ $programa->id }}" {{ old('programa_id', $user->programa_id) ==
                                            $programa->id ? 'selected' : '' }}>
                                            {{ $programa->nombre }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('programa_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Solo se muestran los programas de la dependencia actual del usuario.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3 form-check mt-4">
                                    <input type="checkbox" class="form-check-input" id="activo" name="activo" value="1"
                                        {{ old('activo', $user->activo) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="activo">Usuario Activo</label>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-tinto">
                                <i class="fas fa-save me-2"></i> Actualizar Usuario
                            </button>
                        </div>
                    </form>

                    <!-- SECCIÓN DE CAMBIO DE CONTRASEÑA -->
                    <hr class="my-4">
                    <h5 class="text-tinto">
                        <i class="fas fa-key me-2"></i> Cambiar Contraseña
                    </h5>
                    <p class="text-muted small">Esta sección solo está disponible para SuperUsuario y
                        Organo_Estatal_de_Control.</p>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Nota:</strong> Cambiar la contraseña de un usuario la restablecerá de inmediato.
                        El usuario deberá usar la nueva contraseña para iniciar sesión.
                    </div>

                    <form method="POST" action="{{ route('users.update-password', $user) }}" id="changePasswordForm">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">Nueva Contraseña</label>
                                    <div class="password-toggle-wrapper">
                                        <input id="new_password" type="password"
                                            class="form-control @error('new_password') is-invalid @enderror"
                                            name="new_password" required autocomplete="new-password"
                                            placeholder="Ingresa la nueva contraseña">
                                        <span class="password-toggle-icon" id="toggleNewPassword"
                                            title="Mostrar/Ocultar contraseña">
                                            <i class="fas fa-eye"></i>
                                        </span>
                                    </div>
                                    <div class="form-text">
                                        La contraseña debe tener al menos 8 caracteres.
                                        <div id="password-strength" class="mt-2"></div>
                                    </div>
                                    @error('new_password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="new_password_confirmation" class="form-label">Confirmar Nueva
                                        Contraseña</label>
                                    <div class="password-toggle-wrapper">
                                        <input id="new_password_confirmation" type="password"
                                            class="form-control @error('new_password_confirmation') is-invalid @enderror"
                                            name="new_password_confirmation" required autocomplete="new-password"
                                            placeholder="Confirma la nueva contraseña">
                                        <span class="password-toggle-icon" id="toggleConfirmPassword"
                                            title="Mostrar/Ocultar contraseña">
                                            <i class="fas fa-eye"></i>
                                        </span>
                                    </div>
                                    @error('new_password_confirmation')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-key me-2"></i> Cambiar Contraseña
                            </button>
                        </div>
                    </form>

                    <div class="mt-3">
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Volver a Usuarios
                        </a>
                    </div>
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

    #password-strength {
        font-size: 0.9rem;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ===== TOGGLES DE CONTRASEÑA =====
        function setupPasswordToggle(toggleId, inputId) {
            const toggle = document.getElementById(toggleId);
            const input = document.getElementById(inputId);

            if (toggle && input) {
                toggle.addEventListener('click', function() {
                    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                    input.setAttribute('type', type);
                    const icon = this.querySelector('i');
                    icon.classList.toggle('fa-eye');
                    icon.classList.toggle('fa-eye-slash');
                });
            }
        }

        setupPasswordToggle('toggleNewPassword', 'new_password');
        setupPasswordToggle('toggleConfirmPassword', 'new_password_confirmation');

        // ===== INDICADOR DE FORTALEZA =====
        const newPassword = document.getElementById('new_password');
        const passwordStrength = document.getElementById('password-strength');

        if (newPassword) {
            newPassword.addEventListener('input', function() {
                const password = this.value;
                let strength = '';
                let color = '';
                let icon = '';

                if (password.length === 0) {
                    strength = '';
                    color = '';
                    icon = '';
                } else if (password.length < 6) {
                    strength = 'Débil';
                    color = 'text-danger';
                    icon = 'fa-circle';
                } else if (password.length < 8) {
                    strength = 'Media';
                    color = 'text-warning';
                    icon = 'fa-exclamation-circle';
                } else {
                    strength = 'Fuerte';
                    color = 'text-success';
                    icon = 'fa-check-circle';
                }

                if (passwordStrength) {
                    passwordStrength.innerHTML = strength ?
                        `<span class="${color}"><i class="fas ${icon} me-1"></i> Fortaleza: ${strength}</span>` :
                        '';
                }
            });
        }

        // ===== VALIDACIÓN DE COINCIDENCIA =====
        const confirmPassword = document.getElementById('new_password_confirmation');

        if (confirmPassword) {
            confirmPassword.addEventListener('input', function() {
                const password = document.getElementById('new_password').value;
                const confirm = this.value;

                const feedback = this.parentElement;
                const existingFeedback = feedback.querySelector('.password-match-feedback');

                if (existingFeedback) {
                    existingFeedback.remove();
                }

                if (confirm.length > 0 && password !== confirm) {
                    const div = document.createElement('div');
                    div.className = 'password-match-feedback text-danger mt-1';
                    div.innerHTML = '<i class="fas fa-times-circle me-1"></i> Las contraseñas no coinciden';
                    feedback.appendChild(div);
                    this.classList.add('is-invalid');
                    this.classList.remove('is-valid');
                } else if (confirm.length > 0 && password === confirm) {
                    const div = document.createElement('div');
                    div.className = 'password-match-feedback text-success mt-1';
                    div.innerHTML = '<i class="fas fa-check-circle me-1"></i> Las contraseñas coinciden';
                    feedback.appendChild(div);
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } else {
                    this.classList.remove('is-invalid');
                    this.classList.remove('is-valid');
                }
            });
        }

        // ===== VALIDACIÓN EN ENVÍO =====
        const form = document.getElementById('changePasswordForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                const password = document.getElementById('new_password').value;
                const confirm = document.getElementById('new_password_confirmation').value;

                if (password !== confirm) {
                    e.preventDefault();
                    alert('Las contraseñas no coinciden. Por favor, verifícalas.');
                } else if (password.length < 8) {
                    e.preventDefault();
                    alert('La contraseña debe tener al menos 8 caracteres.');
                }
            });
        }

        // ===== CARGA DE PROGRAMAS POR DEPENDENCIA =====
        const dependenciaSelect = document.getElementById('dependencia_id');
        const programaSelect = document.getElementById('programa_id');

        if (dependenciaSelect) {
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
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Error al cargar programas');
                        }
                        return response.json();
                    })
                    .then(data => {
                        programaSelect.innerHTML = '<option value="">Sin programa asignado</option>';
                        if (Array.isArray(data) && data.length > 0) {
                            data.forEach(programa => {
                                const option = document.createElement('option');
                                option.value = programa.id;
                                option.textContent = programa.nombre;

                                if (programa.id == {{ $user->programa_id ?? 'null' }}) {
                                    option.selected = true;
                                }

                                programaSelect.appendChild(option);
                            });
                        } else {
                            programaSelect.innerHTML = '<option value="">No hay programas disponibles para esta dependencia</option>';
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
    });
</script>
@endsection