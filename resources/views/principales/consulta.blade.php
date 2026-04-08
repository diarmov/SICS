@extends('layouts.app')

@section('title', 'Consulta - SICS')

@section('content')
<div class="container  mt-4">
    <div class="text-center">
        <H1>CONSULTA</H1>
    </div>
    <div class="row">
        <!-- Programas Destacados -->
        <section class="mb-5">
            <h2 class="text-tinto mb-4">Programas Activos</h2>
            <h4 class="mb-4">Total de Programas activos {{ count($programas) }}</h4>
            <div class="row">
                @foreach($programas as $programa)
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body shadow">
                            <h5 class="card-title">{{ $programa->nombre }}</h5>
                            <p class="card-text">
                                <strong>Dependencia:</strong> {{ $programa->dependencia->siglas }}<br>
                                <strong>Periodo:</strong> {{ $programa->periodo }}<br>
                                <strong>Vigencia:</strong> {{ $programa->fecha_inicio->format('d/m/Y') }} - {{
                                $programa->fecha_termino->format('d/m/Y') }}
                            </p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </section>


        <!-- Comités de Vigilancia -->
        <section class="mb-5">
            <h2 class="text-tinto mb-4">Comités de Vigilancia</h2>
            <h4 class="mb-4">Total de Comités {{ count($comites) }}</h4>
            <div class="row">
                @foreach($comites as $comite)
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">{{ $comite->nombre }}</h5>
                            <p class="card-text">
                                <strong>Programa:</strong> {{ $comite->programa->nombre }}<br>
                                <strong>Dependencia:</strong> {{ $comite->dependencia->siglas }}<br>
                                <strong>Miembros:</strong> {{ $comite->elementos->count() }}
                            </p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </section>

        <!-- Dependencias -->
        <section>
            <h2 class="text-tinto mb-4">Dependencias Ejecutoras</h2>
            <h4 class="mb-4">Total de Dependencias {{ count($dependencias) }}</h4>
            <div class="row">
                @foreach($dependencias as $dependencia)
                <div class="col-md-3 mb-3">
                    <div class="card text-center zoom-card">
                        <div class="card-body">
                            <h6 class="card-title">{{ $dependencia->siglas }}</h6>
                            <p class="card-text small">{{ $dependencia->dependencia }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </section>
    </div>
</div>
@endsection

@section('scripts')

@endsection