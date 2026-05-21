<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
                'correo' => 'required',
                'numMatricula' => 'required',
                'numIdC' => 'required',
                'tipoConsulta' => 'required|in:cambio de liquidos,cambio de ruedas,revision,otros',
                'informacionAdicional' => 'nullable',
                'idCliente' => 'nullable|exists:users,id'
            ]);

            $cita = new Cita();
            $cita->nombre = $request->nombre;
            if (Auth::check()) {
                $cita->idCliente = Auth::id();
            } 
            $cita->telefono = $request->telefono;
            $cita->correo = $request->correo;
            $cita->numMatricula = $request->numMatricula;
            $cita->numIdC = $request->numIdC;
            $cita->tipoConsulta = $request->tipoConsulta;
            $cita->informacionAd = $request->informacionAdicional;
            
            $cita->idCliente = $request->idCliente;

            if ($cita->save()) {
                return $cita;
            }
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

            $request->validate(['pendiente' => 'required|boolean']);

            $cita->pendiente = $request->pendiente;

            if ($request->pendiente == false && $cita->idCliente != null) {

                $cliente = User::find($cita->idCliente);

                if ($cliente) {

                    $cliente->puntosRacha += 1;

                    if ($cliente->puntosRacha >= 5) {
                        $cliente->descuentoActivo = true;
                        $cliente->puntosRacha = 0;
                    }

                    $cliente->save();
                }
            }

            if ($cita->save()) {
                return $cita;
            }else{
                throw new Exception('Error al actualizar la cita');
            }

        } catch (\Throwable $th) {
            return response(['mensaje' => $th->getMessage()], 500);
        }
    }

}