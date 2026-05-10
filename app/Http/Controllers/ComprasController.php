<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use Exception;
use Illuminate\Http\Request;

class ComprasController extends Controller
{
    public function index()
    {
        return Compra::all();
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'fechaCompra' => 'required|date',
                'precioTotal' => 'required',
                'idCliente' => 'required',
                'metodoPagoCO' => 'required|in:visa,efectivo,otro'
            ]);

            $compra = new Compra();
            $compra->fechaCompra = $request->fechaCompra;
            $compra->precioTotal = $request->precioTotal;
            $compra->idCliente = $request->idCliente;
            $compra->metodoPagoCO = $request->metodoPagoCO;

            if ($compra->save()) {
                return $compra;
            } else {
                throw new Exception('Error al crear la compra');
            }
        } catch (\Throwable $th) {
            return response(['mensaje' => $th->getMessage()], 500);
        }
    }

    public function show(Compra $compra)
    {
        return $compra;
    }

    public function update(Request $request, Compra $compra)
    {
        try {
            $request->validate([
                'fechaCompra' => 'sometimes|date',
                'precioTotal' => 'sometimes|numeric|min:0',
                'idCliente' => 'sometimes|integer|exists:clientes,id',
                'metodoPagoOC' => 'sometimes|string|max:50'
            ]);

            if ($request->has('fechaCompra')) {
                $compra->fechaCompra = $request->fechaCompra;
            }
            if ($request->has('precioTotal')) {
                $compra->precioTotal = $request->precioTotal;
            }
            if ($request->has('idCliente')) {
                $compra->idCliente = $request->idCliente;
            }
            if ($request->has('metodoPagoOC')) {
                $compra->metodoPagoOC = $request->metodoPagoOC;
            }

            if ($compra->save()) {
                return $compra;
            } else {
                throw new Exception('Error al actualizar la compra');
            }
        } catch (\Throwable $th) {
            return response(['mensaje' => $th->getMessage()], 500);
        }
    }

    public function destroy(Compra $compra)
    {
        try {
            $compra->delete();
            return true;
        } catch (\Throwable $th) {
            return response(['mensaje' => $th->getMessage()], 500);
        }
    }
}
