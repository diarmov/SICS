@extends('layouts.admin')

@section('title', 'Cambiar Contraseña - SICS')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-tinto text-white">
                <h5 class="mb-0">Cambiar Contraseña</h5>
            </div>
            <div class="card-body">
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <form method="POST" action="{{ route('password.change') }}" id="changePasswordForm">
                    @csrf

                    <div class="mb-4">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Para tu seguridad, debes confirmar tu contraseña actual antes de cambiarla.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="current_password" class="form-label">Contraseña Actual</label>
                        <div class="password-toggle-wrapper">
                            <input id="current_password" type="password"
                                class="form-control @error('current_password') is-invalid @enderror"
                                name="current_password" required autocomplete="current-password"
                                placeholder="Ingresa tu contraseña actual">
                            <span class="password-toggle-icon" id="toggleCurrentPassword"
                                title="Mostrar/Ocultar contraseña">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                        @error('current_password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="new_password" class="form-label">Nueva Contraseña</label>
                        <div class="password-toggle-wrapper">
                            <input id="new_password" type="password"
                                class="form-control @error('new_password') is-invalid @enderror" name="new_password"
                                required autocomplete="new-password" placeholder="Ingresa tu nueva contraseña">
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

                    <div class="mb-3">
                        <label for="new_password_confirmation" class="form-label">Confirmar Nueva Contraseña</label>
                        <div class="password-toggle-wrapper">
                            <input id="new_password_confirmation" type="password"
                                class="form-control @error('new_password_confirmation') is-invalid @enderror"
                                name="new_password_confirmation" required autocomplete="new-password"
                                placeholder="Confirma tu nueva contraseña">
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

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-tinto">
                            <i class="fas fa-key me-2"></i> Cambiar Contraseña
                        </button>
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Consejos de seguridad -->
        <div class="card mt-3">
            <div class="card-header bg-secondary text-white">
                <h6 class="mb-0"><i class="fas fa-shield-alt me-2"></i> Consejos de Seguridad</h6>
            </div>
            <div class="card-body">
                <ul class="mb-0">
                    <li>Usa una contraseña única que no uses en otros sitios y de al menos 8 caracteres.</li>
                    <li>Combina letras mayúsculas, minúsculas, números y símbolos.</li>
                    <li>Evita usar información personal como fechas de nacimiento o nombres.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
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
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Función para toggle de contraseña
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

        // Configurar todos los toggles
        setupPasswordToggle('toggleCurrentPassword', 'current_password');
        setupPasswordToggle('toggleNewPassword', 'new_password');
        setupPasswordToggle('toggleConfirmPassword', 'new_password_confirmation');

        // Validación en tiempo real de la contraseña
        const newPassword = document.getElementById('new_password');
        const confirmPassword = document.getElementById('new_password_confirmation');
        const passwordStrength = document.getElementById('password-strength');

        if (newPassword) {
            newPassword.addEventListener('input', function() {
                const password = this.value;
                let strength = '';
                let color = '';

                if (password.length === 0) {
                    strength = '';
                    color = '';
                } else if (password.length < 6) {
                    strength = 'Débil';
                    color = 'text-danger';
                } else if (password.length < 8) {
                    strength = 'Media';
                    color = 'text-warning';
                } else {
                    strength = 'Fuerte';
                    color = 'text-success';
                }

                if (passwordStrength) {
                    passwordStrength.innerHTML = strength ?
                        `<span class="${color}"><i class="fas fa-circle me-1" style="font-size: 0.6rem;"></i> Fortaleza: ${strength}</span>` :
                        '';
                }
            });
        }

        // Validación de coincidencia de contraseñas
        if (confirmPassword) {
            confirmPassword.addEventListener('input', function() {
                const password = document.getElementById('new_password').value;
                const confirm = this.value;

                const feedback = this.parentElement;
                const existingFeedback = feedback.querySelector('.password-match-feedback');

                // Eliminar feedback existente
                if (existingFeedback) {
                    existingFeedback.remove();
                }

                if (confirm.length > 0 && password !== confirm) {
                    const div = document.createElement('div');
                    div.className = 'password-match-feedback text-danger mt-1';
                    div.innerHTML = '<i class="fas fa-times-circle me-1"></i> Las contraseñas no coinciden';
                    feedback.appendChild(div);
                    this.classList.add('is-invalid');
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

        // Prevenir envío si las contraseñas no coinciden
        const form = document.getElementById('changePasswordForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                const password = document.getElementById('new_password').value;
                const confirm = document.getElementById('new_password_confirmation').value;

                if (password !== confirm) {
                    e.preventDefault();
                    alert('Las contraseñas no coinciden. Por favor, verifícalas.');
                }
            });
        }
    });
</script>
@endpush