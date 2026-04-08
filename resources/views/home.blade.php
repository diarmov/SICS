@extends('layouts.app')

@section('title', 'Inicio - SICS')

@section('content')
<div class="container mt-4">


    <div class="row align-items-center mb-5">
        <div class="col-md-4 text-center">
            <img src="{{ asset('storage/imgs/GOBZAC.png') }}" alt="SICS" class="img-fluid rounded"
                style="width: 80%; height: auto;">
        </div>
        <div class="col-md-4 text-center">
            <img src="{{ asset('storage/imgs/pROGRESO.png') }}" alt="SICS" class="img-fluid rounded"
                style="width: 80%; height: auto;">
        </div>
        <div class="col-md-4 text-center">
            <img src="{{ asset('storage/imgs/SFP.png') }}" alt="SICS" class="img-fluid rounded"
                style="width: 80%; height: auto;">
        </div>
    </div>
    <div class="row align-items-center mb-5">
        <div class="col-md-9 text-center">
            <h1 class="display-4 text-tinto text-shadow"><b>Sistema Informático de Contraloría Social</b></h1>
            <p class="lead">
            <h4>Plataforma para la gestión y seguimiento de programas sociales y comités de
                vigilancia.</h4>
            </p>
        </div>
        <div class="col-md-3 text-left">
            <img src="{{ asset('storage/imgs/SICS.png') }}" alt="SICS" class="img-fluid rounded"
                style="width: 50%; height: auto;">
        </div>
    </div>

    <!-- Carrusel de Imágenes -->
    @if($carruseles->isNotEmpty())
    <div class="row mb-5">
        <div class="col-12">
            <div id="mainCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    @foreach($carruseles as $index => $item)
                    <button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="{{ $index }}"
                        class="{{ $index == 0 ? 'active' : '' }}" aria-current="{{ $index == 0 ? 'true' : 'false' }}"
                        aria-label="Slide {{ $index + 1 }}"></button>
                    @endforeach
                </div>
                <div class="carousel-inner">
                    @foreach($carruseles as $index => $item)
                    <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                        @if($item->url)
                        <a href="{{ $item->url }}" target="_blank" class="carousel-link">
                            @endif
                            <img src="{{ Storage::url($item->imagen) }}" class="d-block w-100" alt="{{ $item->titulo }}"
                                style="max-height: 500px; object-fit: cover;">
                            @if($item->url)
                        </a>
                        @endif
                        <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded p-3">
                            <h5 class="text-white">{{ $item->titulo }}</h5>
                        </div>
                    </div>
                    @endforeach
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Anterior</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#mainCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Siguiente</span>
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection