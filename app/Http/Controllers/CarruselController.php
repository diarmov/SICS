<?php
// app/Http/Controllers/CarruselController.php

namespace App\Http\Controllers;

use App\Carrusel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CarruselController extends Controller
{
    public function index()
    {
        $carruseles = Carrusel::orderBy('orden')->orderBy('created_at', 'desc')->get();
        return view('carrusel.index', compact('carruseles'));
    }

    public function create()
    {
        return view('carrusel.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'imagen' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'url' => 'nullable|url|max:500',
            'orden' => 'nullable|integer|min:0'
        ]);

        $path = $request->file('imagen')->store('carrusel', 'public');

        $carrusel = Carrusel::create([
            'titulo' => $request->titulo,
            'imagen' => $path,
            'url' => $request->url,
            'orden' => $request->orden ?? 0,
            'activo' => true
        ]);

        // La bitácora se registrará automáticamente en el evento created del modelo
        // No es necesario llamar a registrarBitacora aquí

        return redirect()->route('carrusel.index')
            ->with('success', 'Imagen agregada al carrusel exitosamente.');
    }

    public function edit(Carrusel $carrusel)
    {
        return view('carrusel.edit', compact('carrusel'));
    }

    public function update(Request $request, Carrusel $carrusel)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'url' => 'nullable|url|max:500',
            'orden' => 'nullable|integer|min:0',
            'activo' => 'nullable|boolean'
        ]);

        $data = [
            'titulo' => $request->titulo,
            'url' => $request->url,
            'orden' => $request->orden ?? 0,
            'activo' => $request->has('activo')
        ];

        if ($request->hasFile('imagen')) {
            // Eliminar imagen antigua
            if ($carrusel->imagen && Storage::disk('public')->exists($carrusel->imagen)) {
                Storage::disk('public')->delete($carrusel->imagen);
            }
            $data['imagen'] = $request->file('imagen')->store('carrusel', 'public');
        }

        $carrusel->update($data);

        // La bitácora se registrará automáticamente en el evento updated del modelo

        return redirect()->route('carrusel.index')
            ->with('success', 'Imagen actualizada exitosamente.');
    }

    public function destroy(Carrusel $carrusel)
    {
        // Guardar información antes de eliminar para la bitácora
        $nombreParaBitacora = $carrusel->getNombreParaBitacora();

        // Eliminar el archivo de imagen
        if ($carrusel->imagen && Storage::disk('public')->exists($carrusel->imagen)) {
            Storage::disk('public')->delete($carrusel->imagen);
        }

        $carrusel->delete();

        // La bitácora se registrará automáticamente en el evento deleted del modelo

        return redirect()->route('carrusel.index')
            ->with('success', 'Imagen eliminada del carrusel.');
    }

    public function toggleStatus(Carrusel $carrusel)
    {
        $estadoAnterior = $carrusel->activo ? 'Activo' : 'Inactivo';
        $carrusel->activo = !$carrusel->activo;
        $carrusel->save();

        $estadoNuevo = $carrusel->activo ? 'Activo' : 'Inactivo';

        // Registrar cambio de estado específicamente
        \App\Bitacora::registrar(
            'Cambio de Estado',
            'Carrusel',
            "Estado cambiado para imagen: {$carrusel->getNombreParaBitacora()} | {$estadoAnterior} → {$estadoNuevo}"
        );

        return redirect()->route('carrusel.index')
            ->with('success', 'Estado de la imagen actualizado.');
    }
}
