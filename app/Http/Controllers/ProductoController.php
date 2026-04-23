<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Contracts\Service\Attribute\Required;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Producto::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            //code...
            $request->validate([
                'nombre'=>'required',
                'precio'=>'required'
            ]);

            $p = new Producto();
            $p->nombre=$request->nombre;
            $p->precio=$request->precio;
            if($p->save()){
                return $p;
            }
            else{
                throw new Exception('Error al crear el producto');
            }
        } catch (\Throwable $th) {
            return response(['mensaje'=>$th->getMessage()],500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Producto $producto)
    {
        return $producto;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Producto $producto)
    {
        try {
            //code...
            if($request->nombre == null and $request->precio==null){
                throw new Exception('Falta nombre o precio a modificar');
            }
            if($request->nombre!=null){
                $producto->nombre=$request->nombre;
            }
            if($request->precio!=null){
                $producto->precio=$request->precio;
            }
            if($producto->save()){
                return $producto;
            }
            else{
                throw new Exception('Error al crear el producto');
            }
        } catch (\Throwable $th) {
            return response(['mensaje'=>$th->getMessage()],500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Producto $producto)
    {
        try {
            //bORRAR EL producto solamente si no se han creado pedidos para él
            //Obtener detalle
            $detalle = 
            DB::select('select * from detalles where producto_id = ?', [$producto->id]);
            if(sizeof($detalle)>0){
                throw new Exception('No se puede borrar el producto porque ya se ha pedido');
            }
            $producto->delete();
            return true;
        } catch (\Throwable $th) {
             return response(['mensaje'=>$th->getMessage()],500);
        }
    }
}
