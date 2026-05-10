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
                'precioTotal' => 'required|numeric|min:0',
                'idCompra' => 'nullable|exists:compras,idCompra',
                'idAlquiler' => 'nullable|exists:alquileres,idAlquiler',
                'metodoPago' => 'required|string|max:50',
                'tipoCompra' => 'required|in:compra,alquiler',
                'detalles' => 'nullable|string|max:255'
            ]);

            if (empty($request->idCompra) && empty($request->idAlquiler)) {
                throw new Exception('Debe proporcionar idCompra o idAlquiler');
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

    public function update(Request $request, Factura $factura)
    {
        try {
            $request->validate([
                'fechaCreacion' => 'sometimes|date',
                'precioTotal' => 'sometimes|numeric|min:0',
                'idCompra' => 'sometimes|exists:compras,idCompra',
                'idAlquiler' => 'sometimes|exists:alquileres,idAlquiler',
                'metodoPago' => 'sometimes|string|max:50',
                'tipoCompra' => 'sometimes|in:compra,alquiler',
                'detalles' => 'sometimes|string|max:255'
            ]);

            if ($request->has('fechaCreacion')) $factura->fechaCreacion = $request->fechaCreacion;
            if ($request->has('precioTotal')) $factura->precioTotal = $request->precioTotal;
            if ($request->has('idCompra')) $factura->idCompra = $request->idCompra;
            if ($request->has('idAlquiler')) $factura->idAlquiler = $request->idAlquiler;
            if ($request->has('metodoPago')) $factura->metodoPago = $request->metodoPago;
            if ($request->has('tipoCompra')) $factura->tipoCompra = $request->tipoCompra;
            if ($request->has('detalles')) $factura->detalles = $request->detalles;

            if ($factura->save()) {
                return $factura;
            } else {
                throw new Exception('Error al actualizar la factura');
            }
        } catch (\Throwable $th) {
            return response(['mensaje' => $th->getMessage()], 500);
        }
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
