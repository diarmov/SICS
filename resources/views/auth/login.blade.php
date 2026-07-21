<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - SICS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome para los íconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --color-tinto: #7c0a02;
            --color-gris-claro: #f8f9fa;
        }

        .bg-tinto {
            background-color: var(--color-tinto);
        }

        .text-tinto {
            color: var(--color-tinto);
        }

        .btn-tinto {
            background-color: var(--color-tinto);
            color: white;
        }

        .btn-tinto:hover {
            background-color: #5a0802;
            color: white;
        }

        .login-container {
            min-height: 100vh;
            background-color: var(--color-gris-claro);
        }

        /* Estilo para el campo de contraseña con ícono */
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
</head>

<body>
    <div class="login-container d-flex align-items-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow">
                        <div class="card-header bg-tinto text-white text-center py-3">
                            <h4 class="mb-0">SICS</h4>
                            <small>Sistema Informático de Contraloría Social</small>
                        </div>
                        <div class="card-body p-4">
                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="email" class="form-label">Correo Electrónico</label>
                                    <input id="email" type="email"
                                        class="form-control @error('email') is-invalid @enderror" name="email"
                                        value="{{ old('email') }}" required autocomplete="email" autofocus>
                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">Contraseña</label>
                                    <div class="password-toggle-wrapper">
                                        <input id="password" type="password"
                                            class="form-control @error('password') is-invalid @enderror" name="password"
                                            required autocomplete="current-password">
                                        <span class="password-toggle-icon" id="togglePassword"
                                            title="Mostrar/Ocultar contraseña">
                                            <i class="fas fa-eye"></i>
                                        </span>
                                        @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3 form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{
                                        old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="remember">
                                        Recordarme
                                    </label>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-tinto">Iniciar Sesión</button>
                                </div>

                                @if (Route::has('password.request'))
                                <div class="text-center mt-3">
                                    <a class="btn btn-link text-tinto" href="{{ route('password.request') }}">
                                        ¿Olvidaste tu contraseña?
                                    </a>
                                </div>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script para mostrar/ocultar contraseña -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');

            togglePassword.addEventListener('click', function() {
                // Toggle el tipo de input
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

                // Cambiar el ícono
                const icon = this.querySelector('i');
                icon.classList.toggle('fa-eye');
                icon.classList.toggle('fa-eye-slash');
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>