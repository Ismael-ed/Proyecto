<?php

namespace App\Http\Controllers;

use App\Models\Objeto;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ObjetoController extends Controller
{
    public function index()
    {
        return Objeto::all();
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required',
                'imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'tipo' => 'required',             
                'cantidad' => 'required',
                'estado' => 'nullable',
                'precio' => 'required',
                'calificacion' => 'required',
                'detalles' => 'nullable',
                'descuento' => 'nullable'
            ]);

            $objeto = new Objeto();
            $objeto->nombre = $request->nombre;
            $objeto->imagen = $request->imagen;
            $objeto->tipo = $request->tipo;
            $objeto->cantidad = $request->cantidad;
            $objeto->estado = $request->estado;
            $objeto->precio = $request->precio;
            $objeto->calificacion = $request->calificacion;
            $objeto->detalles = $request->detalles;
            $objeto->descuento = $request->descuento;

            if ($request->hasFile('imagen')) {

                $fichImgs = $request->file('imagen')->store('imgs', 'public');

                $objeto->imagen = $fichImgs;
            }
            
            if ($objeto->save()) {
                return $objeto;
            } else {
                throw new Exception('Error al crear el objeto');
            }
        } catch (\Throwable $th) {
            return response(['mensaje' => $th->getMessage()], 500);
        }
    }

    public function show(Objeto $objeto)
    {
        return $objeto;
    }

    public function update(Request $request, Objeto $objeto)
    {
        try {
            $request->validate([
                'nombre' => 'required',
                'imagen' => 'nullable',
                'tipo' => 'required',             
                'cantidad' => 'required',
                'estado' => 'nullable',
                'precio' => 'required',
                'calificacion' => 'required',
                'detalles' => 'nullable',
                'descuento' => 'nullable'
            ]);

            if ($request->has('nombre')) $objeto->nombre = $request->nombre;
            if ($request->hasFile('imagen')) {

                if ($objeto->imagen && Storage::disk('public')->exists($objeto->imagen)) {
                    Storage::disk('public')->delete($objeto->imagen);
                }

                $ruta = $request->file('imagen')->store('objetos', 'public');

                $objeto->imagen = $ruta;
            }
            if ($request->has('tipo')) $objeto->tipo = $request->tipo;
            if ($request->has('cantidad')) $objeto->cantidad = $request->cantidad;
            if ($request->has('estado')) $objeto->estado = $request->estado;
            if ($request->has('precio')) $objeto->precio = $request->precio;
            if ($request->has('calificacion')) $objeto->calificacion = $request->calificacion;
            if ($request->has('detalles')) $objeto->detalles = $request->detalles;
            if ($request->has('descuento')) $objeto->descuento = $request->descuento;
            
            if ($objeto->save()) {
                return $objeto;
            } else {
                throw new Exception('Error al actualizar el objeto');
            }
        } catch (\Throwable $th) {
            return response(['mensaje' => $th->getMessage()], 500);
        }
    }

    public function destroy(Objeto $objeto)
    {
        try {
            if ($objeto->imagen && Storage::disk('public')->exists($objeto->imagen)) {
                Storage::disk('public')->delete($objeto->imagen);
            }

            $objeto->delete();
            return true;
        } catch (\Throwable $th) {
            return response(['mensaje' => $th->getMessage()], 500);
        }
    }
}