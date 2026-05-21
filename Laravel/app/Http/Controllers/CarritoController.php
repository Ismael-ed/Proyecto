<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CarritoController extends Controller
{
    public function finalizar(Request $request)
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'No auth'], 401);
            }

            $carrito = $request->input('carrito');
            $metodoPago = $request->input('metodoPago', 'efectivo');

            $itemsCompra = array_filter($carrito, fn($i) => $i['tipo'] === 'compra');
            $itemsAlquiler = array_filter($carrito, fn($i) => $i['tipo'] === 'alquiler');

            if (!empty($itemsCompra)) {
                $idCompra = DB::table('compras')->insertGetId([
                    'idCliente' => $user->id,
                    'created_at' => now()
                ]);

                foreach ($itemsCompra as $item) {
                    DB::table('compra_objeto')->insert([
                        'idCompra' => $idCompra,
                        'idObjeto' => $item['idObjeto'],
                        'cantidad' => $item['cantidad']
                    ]);
                }

                $subtotalCompra = array_reduce($itemsCompra, fn($acc, $item) => $acc + ($item['precio'] * $item['cantidad']), 0);
                
                DB::table('facturas')->insert([
                    'fechaCreacion' => now()->format('Y-m-d'),
                    'precioTotal'   => $subtotalCompra,
                    'idCompra'      => $idCompra,
                    'idAlquiler'    => null,
                    'metodoPago'    => in_array($metodoPago, ['visa', 'efectivo', 'otro']) ? $metodoPago : 'efectivo',
                    'listaObjetos'  => implode(', ', array_column($itemsCompra, 'nombre')),
                    'created_at'    => now(),
                    'updated_at'    => now()
                ]);
            }

            if (!empty($itemsAlquiler)) {
                $fechasFin = array_column($itemsAlquiler, 'fechaFin');
                $maxFechaFin = !empty($fechasFin) ? max($fechasFin) : now()->addMonth()->format('Y-m-d');

                $idAlquiler = DB::table('alquileres')->insertGetId([
                    'fechaInicio' => now()->format('Y-m-d'),
                    'fechaFin'    => $maxFechaFin,
                    'idCliente'   => $user->id,
                    'created_at'  => now(),
                    'updated_at'  => now()
                ]);

                foreach ($itemsAlquiler as $item) {
                    DB::table('alquiler_objeto')->insert([
                        'idAlquiler' => $idAlquiler,
                        'idObjeto'   => $item['idObjeto'],
                        'cantidad'   => $item['cantidad']
                    ]);
                }

                $totalAlquilerCalculado = array_reduce($itemsAlquiler, fn($acc, $item) => $acc + ($item['precio'] * $item['cantidad']), 0);

                DB::table('facturas')->insert([
                    'fechaCreacion' => now()->format('Y-m-d'),
                    'precioTotal'   => $totalAlquilerCalculado,
                    'idCompra'      => null,
                    'idAlquiler'    => $idAlquiler,
                    'metodoPago'    => in_array($metodoPago, ['visa', 'efectivo', 'otro']) ? $metodoPago : 'efectivo',
                    'listaObjetos'  => implode(', ', array_column($itemsAlquiler, 'nombre')),
                    'created_at'    => now(),
                    'updated_at'    => now()
                ]);
            }

            DB::table('users')->where('id', $user->id)->increment('puntosRacha');

            DB::commit();
            return response()->json(['mensaje' => 'Exito'], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function misFacturas()
    {
        $user = Auth::user();
        if (!$user) return response()->json(['error' => 'No auth'], 401);

        $facturas = DB::table('facturas')
            ->whereIn('idCompra', function($q) use ($user) {
                $q->select('idCompra')->from('compras')->where('idCliente', $user->id);
            })
            ->orWhereIn('idAlquiler', function($q) use ($user) {
                $q->select('idAlquiler')->from('alquileres')->where('idCliente', $user->id);
            })
            ->orderBy('fechaCreacion', 'desc')
            ->get();

        return response()->json($facturas);
    }

public function detalles($id)
{
    $factura = \DB::table('facturas')->where('idFactura', $id)->first();
    
    if (!$factura) return response()->json(['error' => 'No existe'], 404);

    $items = [];
    
    $selectCampos = [
        'objetos.nombre', 
        \DB::raw('objetos.precio as precio_original'),
        \DB::raw('CASE WHEN objetos.descuento > 0 
                 THEN objetos.precio * (1 - (objetos.descuento / 100)) 
                 ELSE objetos.precio END as precio')
    ];

    if ($factura->idCompra) {
        $items = \DB::table('compra_objeto')
            ->join('objetos', 'compra_objeto.idObjeto', '=', 'objetos.id')
            ->where('idCompra', $factura->idCompra)
            ->select(array_merge($selectCampos, ['compra_objeto.cantidad']))
            ->get();
    } else if ($factura->idAlquiler) {
        $items = \DB::table('alquiler_objeto')
            ->join('objetos', 'alquiler_objeto.idObjeto', '=', 'objetos.id')
            ->where('idAlquiler', $factura->idAlquiler)
            ->select(array_merge($selectCampos, ['alquiler_objeto.cantidad']))
            ->get();
    }

    return response()->json(['factura' => $factura, 'items' => $items]);
}

    public function index() { return response()->json([]); }
    public function store(Request $request) { return response()->json([]); }
    public function show($id) { return response()->json([]); }
    public function update(Request $request, $id) { return response()->json([]); }
    public function destroy($id) { return response()->json([]); }
}