@extends('layouts.admin')

@section('title', 'Dashboard - SICS')

@section('content')
<!-- Welcome Message -->
<div class="card mb-4">
    <div class="card-body">
        <h4 class="card-title text-tinto">Bienvenido, {{ auth()->user()->nombre }}</h4>
        <p class="card-text">
            Sistema Informático de Contraloría Social - SICS
        </p>
        <p class="card-text">
            <small class="text-muted">
                Rol: <strong>{{ auth()->user()->getRoleNames()->first() }}</strong> |
                Dependencia: <strong>{{ auth()->user()->dependencia->dependencia }}</strong>
            </small>
        </p>
    </div>
</div>
@if(auth()->user()->hasRole(['SuperUsuario', 'AdministradorCS']))
<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-tinto">
            <div class="card-body">
                <h5 class="card-title">Usuarios</h5>
                <h2 class="card-text">{{ \App\User::count() }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-secondary">
            <div class="card-body">
                <h5 class="card-title">Programas</h5>
                <h2 class="card-text">{{ \App\Programa::where('activo', true)->count() }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h5 class="card-title">Comités</h5>
                <h2 class="card-text">{{ \App\ComiteVigilancia::where('activo', true)->count() }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-info">
            <div class="card-body">
                <h5 class="card-title">Dependencias</h5>
                <h2 class="card-text">{{ \App\Dependencia::where('activo', true)->count() }}</h2>
            </div>
        </div>
    </div>
</div>

<!-- Actividad Reciente -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Actividad Reciente</h5>
    </div>
    <div class="card-body">
        @if(isset($bitacoras) && $bitacoras->count() > 0)
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Acción</th>
                        <th>Módulo</th>
                        <th>IP</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bitacoras as $bitacora)
                    <tr>
                        <td>{{ $bitacora->user->nombre }}</td>
                        <td>
                            <span class="badge
                                @if($bitacora->accion == 'Creación') bg-success
                                @elseif($bitacora->accion == 'Actualización') bg-warning
                                @elseif($bitacora->accion == 'Eliminación') bg-danger
                                @else bg-info @endif">
                                {{ $bitacora->accion }}
                            </span>
                        </td>
                        <td>{{ $bitacora->modulo }}</td>
                        <td><small>{{ $bitacora->ip_address }}</small></td>
                        <td>{{ $bitacora->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="alert alert-info">
            No hay actividad reciente para mostrar.
        </div>
        @endif
    </div>
</div>
@endif
@endsection