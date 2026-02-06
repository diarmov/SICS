<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Usuario - SICS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
    </style>
</head>

<body>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-tinto text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Registrar Nuevo Usuario</h4>
                        <a href="{{ route('users.index') }}" class="btn btn-light btn-sm">Volver</a>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('register') }}">
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
                                            <option value="{{ $dependencia->id }}" {{
                                                old('dependencia_id')==$dependencia->id ? 'selected' : '' }}>{{
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
                                        <label for="password" class="form-label">Contraseña</label>
                                        <input id="password" type="password"
                                            class="form-control @error('password') is-invalid @enderror" name="password"
                                            required autocomplete="new-password">
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
                                        <input id="password-confirm" type="password" class="form-control"
                                            name="password_confirmation" required autocomplete="new-password">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="rol" class="form-label">Rol</label>
                                        <select id="rol" class="form-control @error('rol') is-invalid @enderror"
                                            name="rol" required>
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
                                    <div class="mb-3 form-check mt-4">
                                        <input type="checkbox" class="form-check-input" id="activo" name="activo"
                                            value="1" checked>
                                        <label class="form-check-label" for="activo">Usuario Activo</label>
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-tinto">Registrar Usuario</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>