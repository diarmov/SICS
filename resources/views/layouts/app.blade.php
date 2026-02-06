<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SICS - Sistema Informático de Contraloría Social')</title>

    <!-- Bootstrap CSS -->
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

        .navbar-brand {
            font-weight: bold;
        }

        .footer {
            background-color: var(--color-gris-claro);
        }

        .sidebar {
            min-height: calc(100vh - 56px);
            background-color: var(--color-gris-claro);
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-tinto">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">SICS</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('contacto') }}">Contacto</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('comites.public') }}">Comités de Vigilancia</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('programas.public') }}">Programas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dependencias.public') }}">Dependencias Ejecutoras</a>
                    </li>
                </ul>

                <ul class="navbar-nav">
                    @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown">
                            {{ auth()->user()->nombre }}
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Cerrar Sesión</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                    @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Iniciar Sesión</a>
                    </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer class="footer mt-5 py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p>&copy; 2024 SICS - Sistema Informático de Contraloría Social</p>
                </div>
                <div class="col-md-6 text-end">
                    <p>Desarrollado por la Dirección de Innovación Administrativa</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    @yield('scripts')
</body>

</html>