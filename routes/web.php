<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BitacoraController;
use App\Http\Controllers\ProgramaController;
use App\Http\Controllers\TipoApoyoController;
use App\Http\Controllers\UbicacionController;
use App\Http\Controllers\DependenciaController;
use App\Http\Controllers\ComiteVigilanciaController;

// Rutas públicas
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/contacto', [HomeController::class, 'contacto'])->name('contacto');
Route::get('/comites-vigilancia', [HomeController::class, 'comites'])->name('comites.public');
Route::get('/programas-list', [HomeController::class, 'programas'])->name('programas.public');
Route::get('/dependencias-list', [HomeController::class, 'dependencias'])->name('dependencias.public');

// Rutas de autenticación
Auth::routes(['register' => false]);

// Rutas protegidas
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');

    // Usuarios
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Dependencias
    Route::get('/dependencias', [DependenciaController::class, 'index'])->name('dependencias.index');
    Route::get('/dependencias/create', [DependenciaController::class, 'create'])->name('dependencias.create');
    Route::post('/dependencias', [DependenciaController::class, 'store'])->name('dependencias.store');
    Route::get('/dependencias/{dependencia}/edit', [DependenciaController::class, 'edit'])->name('dependencias.edit');
    Route::put('/dependencias/{dependencia}', [DependenciaController::class, 'update'])->name('dependencias.update');
    Route::delete('/dependencias/{dependencia}', [DependenciaController::class, 'destroy'])->name('dependencias.destroy');

    // Programas
    Route::get('/programas', [ProgramaController::class, 'index'])->name('programas.index');
    Route::get('/programas/create', [ProgramaController::class, 'create'])->name('programas.create');
    Route::post('/programas', [ProgramaController::class, 'store'])->name('programas.store');
    Route::get('/programas/{programa}', [ProgramaController::class, 'show'])->name('programas.show');
    Route::get('/programas/{programa}/edit', [ProgramaController::class, 'edit'])->name('programas.edit');
    Route::put('/programas/{programa}', [ProgramaController::class, 'update'])->name('programas.update');
    Route::delete('/programas/{programa}', [ProgramaController::class, 'destroy'])->name('programas.destroy');
    Route::post('/programas/{programa}/beneficiarios', [ProgramaController::class, 'uploadBeneficiarios'])->name('programas.upload-beneficiarios');
    Route::get('/programas/{programa}/informes', [ProgramaController::class, 'informes'])->name('programas.informes');
    Route::post('/programas/{programa}/informes', [ProgramaController::class, 'storeInforme'])->name('programas.store-informe');
    Route::delete('/informes/{informe}', [ProgramaController::class, 'destroyInforme'])->name('programas.destroy-informe');

    // Comités de Vigilancia
    Route::get('/comites', [ComiteVigilanciaController::class, 'index'])->name('comites.index');
    Route::get('/comites/create', [ComiteVigilanciaController::class, 'create'])->name('comites.create');
    Route::post('/comites', [ComiteVigilanciaController::class, 'store'])->name('comites.store');
    Route::get('/comites/{comite}', [ComiteVigilanciaController::class, 'show'])->name('comites.show');
    Route::get('/comites/{comite}/edit', [ComiteVigilanciaController::class, 'edit'])->name('comites.edit');
    Route::put('/comites/{comite}', [ComiteVigilanciaController::class, 'update'])->name('comites.update');
    Route::delete('/comites/{comite}', [ComiteVigilanciaController::class, 'destroy'])->name('comites.destroy');
    Route::post('/comites/{comite}/elementos', [ComiteVigilanciaController::class, 'addElemento'])->name('comites.add-elemento');
    Route::delete('/elementos/{elemento}', [ComiteVigilanciaController::class, 'removeElemento'])->name('comites.remove-elemento');

    // Tipos de Apoyo (dentro del middleware auth)
    Route::prefix('tipos-apoyo')->name('tipos-apoyo.')->group(function () {
        Route::get('/', [TipoApoyoController::class, 'index'])->name('index');
        Route::get('/create', [TipoApoyoController::class, 'create'])->name('create');
        Route::post('/', [TipoApoyoController::class, 'store'])->name('store');
        Route::get('/{tipoApoyo}', [TipoApoyoController::class, 'show'])->name('show');
        Route::get('/{tipoApoyo}/edit', [TipoApoyoController::class, 'edit'])->name('edit');
        Route::put('/{tipoApoyo}', [TipoApoyoController::class, 'update'])->name('update');
        Route::put('/{tipoApoyo}/toggle-status', [TipoApoyoController::class, 'toggleStatus'])->name('toggle-status');
        Route::delete('/{tipoApoyo}', [TipoApoyoController::class, 'destroy'])->name('destroy');
    });

    // Rutas para selects dependientes
    Route::get('/api/estados', [UbicacionController::class, 'getEstados'])->name('api.estados');
    Route::get('/api/municipios/{estadoId}', [UbicacionController::class, 'getMunicipios'])->name('api.municipios');
    Route::get('/api/localidades/{municipioId}', [UbicacionController::class, 'getLocalidades'])->name('api.localidades');


    // Bitácora
    Route::get('/bitacora', [BitacoraController::class, 'index'])->name('bitacora.index');
    Route::get('/bitacora/filter', [BitacoraController::class, 'filter'])->name('bitacora.filter');
});
