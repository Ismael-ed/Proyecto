<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use Exception;
use Illuminate\Http\Request;

class CitaController extends Controller
{
    public function index()
    {
        return Cita::all();
    }

    public function store(Request $request)
    {
        try {

            $request->validate([
                'nombre' => 'required',
                'telefono' => 'required',
                'correo' => 'required|email',
                'numMatricula' => 'required',
                'numIdC' => 'required',
                'tipoConsulta' => 'sometimes|in:cambio de liquidos,cambio de ruedas,revision,otros',
                'informacionAdicional' => 'nullable',
                'idCliente' => 'nullable|exists:users,id'
            ]);

            $cita = new Cita();

            $cita->nombre = $request->nombre;
            $cita->idCliente = $request->idCliente;
            $cita->telefono = $request->telefono;
            $cita->correo = $request->correo;
            $cita->numMatricula = $request->numMatricula;
            $cita->numIdC = $request->numIdC;
            $cita->informacionAdicional = $request->informacionAdicional;
            $cita->tipoConsulta = $request->tipoConsulta;

            if ($cita->save()) {
                return $cita;
            }

            throw new Exception('Error al crear la cita');

        } catch (\Throwable $th) {

            return response(['mensaje' => $th->getMessage()], 500);
        }
    }

    public function show(Cita $cita)
    {
        return $cita;
    }

    public function update(Request $request, Cita $cita)
    {
        try {

            $request->validate([
                'nombre' => 'sometimes|required',
                'telefono' => 'sometimes|required',
                'correo' => 'sometimes|required|email',
                'numMatricula' => 'sometimes|required',
                'numIdC' => 'sometimes|required',
                'tipoConsulta' => 'sometimes|in:cambio de liquidos,cambio de ruedas,revision,otros',
                'informacionAdicional' => 'nullable',
                'pendiente' => 'boolean'
            ]);

            if ($request->has('nombre')){
                $cita->nombre = $request->nombre;
            }

            if ($request->has('telefono')){
                $cita->telefono = $request->telefono;
            }
                
            if ($request->has('correo')){
                $cita->correo = $request->correo;
            } 

            if ($request->has('numMatricula')){
                $cita->numMatricula = $request->numMatricula;
            }

            if ($request->has('numIdC')){
                $cita->numIdC = $request->numIdC;
            }   

            if ($request->has('tipoConsulta')){
                $cita->tipoConsulta = $request->tipoConsulta;
            }

            if ($request->has('informacionAdicional')){
                $cita->informacionAdicional = $request->informacionAdicional;
            }
                
            if ($request->has('pendiente')){
                $cita->pendiente = $request->pendiente;
            }   

            if ($cita->save()) {
                return $cita;
            }else{
                throw new Exception('Error al actualizar la cita');
            }   

        } catch (\Throwable $th) {
            return response([ 'mensaje' => $th->getMessage()], 500);
        }
    }

    public function destroy(Cita $cita)
    {
        try {
            $cita->delete();
            return true;
        } catch (\Throwable $th) {
            return response(['mensaje' => $th->getMessage()], 500);
        }
    }
}
