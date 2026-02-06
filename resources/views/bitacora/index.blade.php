@extends('layouts.admin')

@section('title', 'Bitácora - SICS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-tinto">Bitácora del Sistema</h2>
    <div class="text-muted">
        Mostrando {{ $bitacoras->firstItem() }} - {{ $bitacoras->lastItem() }} de {{ $bitacoras->total() }} registros
    </div>
</div>

<div class="card">
    <div class="card-body">
        <!-- Filtros -->
        <form method="GET" action="{{ route('bitacora.filter') }}" class="mb-4">
            <div class="row">
                <div class="col-md-3">
                    <label for="user_id" class="form-label">Usuario</label>
                    <select name="user_id" id="user_id" class="form-select">
                        <option value="">Todos los usuarios</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id')==$user->id ? 'selected' : '' }}>
                            {{ $user->nombre }} {{ $user->apellido_paterno }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="modulo" class="form-label">Módulo</label>
                    <input type="text" name="modulo" id="modulo" class="form-control" placeholder="Módulo..."
                        value="{{ request('modulo') }}">
                </div>
                <div class="col-md-2">
                    <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control"
                        value="{{ request('fecha_inicio') }}">
                </div>
                <div class="col-md-2">
                    <label for="fecha_fin" class="form-label">Fecha Fin</label>
                    <input type="date" name="fecha_fin" id="fecha_fin" class="form-control"
                        value="{{ request('fecha_fin') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <div class="d-grid gap-2 w-100">
                        <button type="submit" class="btn btn-tinto">Filtrar</button>
                        @if(request()->hasAny(['user_id', 'modulo', 'fecha_inicio', 'fecha_fin']))
                        <a href="{{ route('bitacora.index') }}" class="btn btn-secondary">Limpiar</a>
                        @endif
                    </div>
                </div>
            </div>
        </form>

        <!-- Tabla de bitácora -->
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Usuario</th>
                        <th>Acción</th>
                        <th>Módulo</th>
                        <th>Detalles</th>
                        <th>IP</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bitacoras as $bitacora)
                    <tr>
                        <td>
                            <strong>{{ $bitacora->user->nombre }} {{ $bitacora->user->apellido_paterno }}</strong>
                            <br>
                            <small class="text-muted">{{ $bitacora->user->email }}</small>
                        </td>
                        <td>
                            <span class="badge
                                @if($bitacora->accion == 'Creación') bg-success
                                @elseif($bitacora->accion == 'Actualización') bg-warning
                                @elseif($bitacora->accion == 'Eliminación') bg-danger
                                @elseif($bitacora->accion == 'Inicio de sesión') bg-info
                                @elseif($bitacora->accion == 'Cierre de sesión') bg-secondary
                                @elseif($bitacora->accion == 'Carga de archivo') bg-primary
                                @else bg-dark @endif">
                                {{ $bitacora->accion }}
                            </span>
                        </td>
                        <td>{{ $bitacora->modulo }}</td>
                        <td>
                            <span title="{{ $bitacora->detalles }}">
                                {{ Str::limit($bitacora->detalles, 50) }}
                            </span>
                        </td>
                        <td><small class="text-muted">{{ $bitacora->ip_address }}</small></td>
                        <td>
                            <small>
                                {{ $bitacora->created_at->format('d/m/Y') }}<br>
                                {{ $bitacora->created_at->format('H:i:s') }}
                            </small>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Paginación Simple -->
        @if($bitacoras->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="text-muted">
                Página {{ $bitacoras->currentPage() }} de {{ $bitacoras->lastPage() }}
            </div>

            <div>
                {{ $bitacoras->appends(request()->query())->links() }}
            </div>

            <div class="text-muted">
                {{ $bitacoras->firstItem() }}-{{ $bitacoras->lastItem() }} de {{ $bitacoras->total() }}
            </div>
        </div>
        @endif

        @if($bitacoras->isEmpty())
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle"></i> No hay registros en la bitácora para los filtros seleccionados.
        </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    .table-hover tbody tr:hover {
        background-color: rgba(124, 10, 2, 0.05);
    }

    .page-link {
        color: #7c0a02;
    }

    .page-item.active .page-link {
        background-color: #7c0a02;
        border-color: #7c0a02;
    }

    .page-link:hover {
        color: #5a0802;
    }
</style>
@endpush