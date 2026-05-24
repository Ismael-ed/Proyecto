<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CarritoController extends Controller
{
    public function finalizar(Request $request)
    {
        try {

            $request->validate([
                'carrito' => 'required|array',
                'carrito.*.idObjeto' => 'required',
                'carrito.*.cantidad' => 'required',
                'carrito.*.tipo' => 'required|in:compra,alquiler',
                'carrito.*.precio' => 'required',
                'carrito.*.nombre' => 'required',
                'precioTotal' => 'required',
                'metodoPago' => 'required|in:visa,efectivo,otro'
            ]);

            if (Auth::check()) {
                $user = Auth::user();
            } else {
                throw new Exception('Error, usuario no autenticado');
            }

            DB::beginTransaction();

            $carrito = $request->carrito;
            $precioTotal = $request->precioTotal;
            $metodoPago = $request->metodoPago;

            $listaObjetos = '';

            $hayCompras = false;
            $hayAlquileres = false;
            $idCompra = null;
            $idAlquiler = null;

            foreach ($carrito as $item) {

                if ($item['tipo'] == 'compra') {
                    $hayCompras = true;
                }
                if ($item['tipo'] == 'alquiler') {
                    $hayAlquileres = true;
                }
            }

            if ($hayCompras) {

                $idCompra = DB::table('compras')->insertGetId([
                    'idCliente' => $user->id,
                    'created_at' => now()
                ]);

                foreach ($carrito as $item) {

                    if ($item['tipo'] == 'compra') {

                        DB::table('compra_objeto')->insert([
                            'idCompra' => $idCompra,
                            'idObjeto' => $item['idObjeto'],
                            'cantidad' => $item['cantidad']
                        ]);

                        $objeto = DB::table('objetos')
                        ->where('id', $item['idObjeto'])
                        ->first();
    
                        if ($objeto->cantidad < $item['cantidad']) {
                            throw new Exception('No hay suficiente stock de ' . $objeto->nombre);
                        }
    
                        DB::table('objetos')
                            ->where('id', $item['idObjeto'])
                            ->update(['cantidad' => $objeto->cantidad - $item['cantidad']]);
                    }
                }
            }
       
            if ($hayAlquileres) {
    
                $primerAlquiler = null;
    
                foreach ($carrito as $item) {
    
                    if ($item['tipo'] == 'alquiler') {
                        $primerAlquiler = $item;
                        break;
                    }
                }
    
                $idAlquiler = DB::table('alquileres')->insertGetId([
                    'fechaInicio' => $primerAlquiler['fechaInicio'],
                    'fechaFin' => $primerAlquiler['fechaFin'],
                    'idCliente' => $user->id
                ]);
    
                foreach ($carrito as $item) {
    
                    if ($item['tipo'] == 'alquiler') {
    
                        DB::table('alquiler_objeto')->insert([
                            'idAlquiler' => $idAlquiler,
                            'idObjeto' => $item['idObjeto'],
                            'cantidad' => $item['cantidad']
                        ]);

                        DB::table('objetos')
                        ->where('id', $item['idObjeto'])
                        ->update(['estado' => 'ocupado']);
                    }
                }
            }


            foreach ($carrito as $item) {
                $listaObjetos .= $item['nombre'] . ', ';
            }

            $idFactura = DB::table('facturas')->insertGetId([
                'fechaCreacion' => now()->toDateString(),
                'precioTotal' => $precioTotal,
                'idCompra' => $idCompra,
                'idAlquiler' => $idAlquiler,
                'metodoPago' => $metodoPago,
                'listaObjetos' => $listaObjetos,
                'idCliente' => $user->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            if ($user->descuentoActivo == true) {

                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['descuentoActivo' => false]);
            }

            if ($precioTotal > 25) {

                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['puntosRacha' => $user->puntosRacha + 1]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'idFactura' => $idFactura
            ]);

        } catch (Exception $e) {

            DB::rollBack();

            throw new Exception('Ha ocurrido un error');
        }
    }


    public function misFacturas(Request $request)
    {
        try {

            $usuario = auth()->user();

            $facturas = DB::table('facturas')
                ->where('idCliente', $usuario->id)
                ->orderBy('fechaCreacion', 'desc')
                ->get();

            return response()->json($facturas);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'mensaje' => $e->getMessage()
            ], 500);
        }
    }



    public function detalles($id)
    {
        try {

            $factura = DB::table('facturas')
                ->where('idFactura', $id)
                ->first();

            if (!$factura) {
                throw new Exception('Factura no encontrada');
            }

            $items = [];

            if ($factura->idCompra != null) {

                $compras = DB::table('compra_objeto')
                    ->join('objetos', 'compra_objeto.idObjeto', '=', 'objetos.id')
                    ->where('compra_objeto.idCompra', $factura->idCompra)
                    ->select(
                        'objetos.nombre',
                        'compra_objeto.cantidad',
                        'objetos.precio'
                    )
                    ->get();

                foreach ($compras as $item) {

                    $items[] = [
                        'nombre' => $item->nombre,
                        'cantidad' => $item->cantidad,
                        'precioPagado' => $item->precio,
                        'tipo' => 'compra'
                    ];
                }
            }

            if ($factura->idAlquiler != null) {

                $alquiler = DB::table('alquileres')
                    ->where('idAlquiler', $factura->idAlquiler)
                    ->first();

                $alquileres = DB::table('alquiler_objeto')
                    ->join('objetos', 'alquiler_objeto.idObjeto', '=', 'objetos.id')
                    ->where('alquiler_objeto.idAlquiler', $factura->idAlquiler)
                    ->select(
                        'objetos.nombre',
                        'alquiler_objeto.cantidad',
                        'objetos.precio'
                    )
                    ->get();

                foreach ($alquileres as $item) {

                    $items[] = [
                        'nombre' => $item->nombre,
                        'cantidad' => $item->cantidad,
                        'precioPagado' => $item->precio,
                        'tipo' => 'alquiler',
                        'fechaInicio' => $alquiler->fechaInicio,
                        'fechaFin' => $alquiler->fechaFin
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'items' => $items
            ]);

        } catch (\Throwable $th) {

            return response()->json([
                'success' => false,
                'mensaje' => $th->getMessage()
            ], 500);
        }
    }
}
