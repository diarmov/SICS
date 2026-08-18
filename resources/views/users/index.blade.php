@extends('layouts.admin')

@section('title', 'Usuarios - SICS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-tinto">Gestión de Usuarios</h2>
    @can('usuarios.create')
    <a href="{{ route('users.create') }}" class="btn btn-tinto">Nuevo Usuario</a>
    @endcan
</div>

@if(auth()->user()->hasRole('Instancia_Normativa'))
<div class="alert alert-info">
    <strong>Información:</strong> Como Coordinador de Enlaces, solo puedes crear usuarios con rol "Instancia_Ejecutora"
    para
    tu dependencia ({{ auth()->user()->dependencia->siglas }}).
</div>
@endif

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Dependencia</th>
                        <th>Programa</th> {{-- Agregar columna --}}
                        <th>Rol</th>
                        <th>Estado</th>
                        @if(auth()->user()->hasRole(['SuperUsuario', 'Organo_Estatal_de_Control']))
                        <th>Acciones</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>{{ $user->nombre_completo }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->dependencia->siglas }}</td>
                        <td>{{ $user->programa ? $user->programa->nombre : 'Sin programa' }}</td>
                        <td>{{ $user->getRoleNames()->first() }}</td>
                        <td>
                            <span class="badge {{ $user->activo ? 'bg-success' : 'bg-secondary' }}">
                                {{ $user->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        @if(auth()->user()->hasRole(['SuperUsuario', 'Organo_Estatal_de_Control']))
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-primary btn-sm">Editar</a>
                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('¿Estás seguro?')">Eliminar</button>
                                </form>
                            </div>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($users->isEmpty())
        <div class="alert alert-info">
            No hay usuarios registrados.
        </div>
        @endif
    </div>
</div>
@endsection