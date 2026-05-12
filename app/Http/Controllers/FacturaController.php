<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use Exception;
use Illuminate\Http\Request;

class FacturaController extends Controller
{
    public function index()
    {
        return Factura::all();
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'fechaCreacion' => 'required|date',
                'precioTotal' => 'required',
                'idCompra' => 'nullable',
                'idAlquiler' => 'nullable',
                'metodoPago' => 'required',
                'tipoCompra' => 'required|in:compra,alquiler',
                'detalles' => 'nullable'
            ]);

            if (empty($request->idCompra) && empty($request->idAlquiler)) {
                throw new Exception('No se recibio ningun id de Compra o Alquiler');
            }

            $factura = new Factura();
            $factura->fechaCreacion = $request->fechaCreacion;
            $factura->precioTotal = $request->precioTotal;
            $factura->idCompra = $request->idCompra;
            $factura->idAlquiler = $request->idAlquiler;
            $factura->metodoPago = $request->metodoPago;
            $factura->tipoCompra = $request->tipoCompra;
            $factura->detalles = $request->detalles;

            if ($factura->save()) {
                return $factura;
            } else {
                throw new Exception('Error al crear la factura');
            }
        } catch (\Throwable $th) {
            return response(['mensaje' => $th->getMessage()], 500);
        }
    }

    public function show(Factura $factura)
    {
        return $factura;
    }

    public function destroy(Factura $factura)
    {
        try {
            $factura->delete();
            return true;
        } catch (\Throwable $th) {
            return response(['mensaje' => $th->getMessage()], 500);
        }
    }
}
