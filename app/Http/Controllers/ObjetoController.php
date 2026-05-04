<?php

namespace App\Http\Controllers;

use App\Models\Objeto;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ObjetoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Objeto::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required',
                'precio' => 'required',
                'calificacion' => 'nullable',
                'comentarios' => 'nullable',
                'descuento' => 'nullable'
            ]);

            $objeto = new Objeto();
            $objeto->nombre = $request->nombre;
            $objeto->precio = $request->precio;
            $objeto->calificacion = $request->calificacion;
            $objeto->comentarios = $request->comentarios;
            $objeto->descuento = $request->descuento ?? 0;

            if ($objeto->save()) {
                return $objeto;
            } else {
                throw new Exception('Error al crear el objeto');
            }
        } catch (\Throwable $th) {
            return response(['mensaje' => $th->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Objeto $objeto)
    {
        return $objeto;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Objeto $objeto)
    {
        try {
            $request->validate([
                'nombre' => 'sometimes',
                'precio' => 'sometimes',
                'calificacion' => 'nullable',
                'comentarios' => 'nullable',
                'descuento' => 'nullable'
            ]);

            if ($request->has('nombre')) {
                $objeto->nombre = $request->nombre;
            }
            if ($request->has('precio')) {
                $objeto->precio = $request->precio;
            }
            if ($request->has('calificacion')) {
                $objeto->calificacion = $request->calificacion;
            }
            if ($request->has('comentarios')) {
                $objeto->comentarios = $request->comentarios;
            }
            if ($request->has('descuento')) {
                $objeto->descuento = $request->descuento;
            }

            if ($objeto->save()) {
                return $objeto;
            } else {
                throw new Exception('Error al actualizar el objeto');
            }
        } catch (\Throwable $th) {
            return response(['mensaje' => $th->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Objeto $objeto)
    {
        try {
            $objeto->delete();
            return true;
        } catch (\Throwable $th) {
            return response(['mensaje' => $th->getMessage()], 500);
        }
    }
}
