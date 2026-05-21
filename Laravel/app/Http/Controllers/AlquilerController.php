<?php

namespace App\Http\Controllers;

use App\Models\Alquilere;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlquilerController extends Controller
{
    public function index()
    {
        return Alquilere::all();
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'fechaInicio' => 'required|date',
                'fechaFin' => 'required|date|after:fechaInicio',
                'estado' => 'required|in:disponible,mantenimiento,ocupado',
                'idCliente' => 'required',
                'costoTotal' => 'required',
                'metodoPagoAl' => 'required|in:visa,efectivo,otro'
            ]);

            $alquiler = new Alquilere();
            $alquiler->fechaInicio = $request->fechaInicio;
            $alquiler->fechaFin = $request->fechaFin;
            $alquiler->estado = $request->estado;
            $alquiler->idCliente = $request->idCliente;
            $alquiler->costoTotal = $request->costoTotal;
            $alquiler->metodoPagoAl = $request->metodoPagoAl;

            if ($alquiler->save()) {
                return $alquiler;
            } else {
                throw new Exception('Error al crear el alquiler');
            }
        } catch (\Throwable $th) {
            return response(['mensaje' => $th->getMessage()], 500);
        }
    }

    public function show(Alquilere $alquiler)
    {
        return $alquiler;
    }

    public function destroy(Alquilere $alquiler)
    {
        try {
            $alquiler->delete();
            return true;
        } catch (\Throwable $th) {
            return response(['mensaje' => $th->getMessage()], 500);
        }
    }
}
