<?php


namespace App\Http\Controllers;


use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;


class UserController extends Controller
{
    public function index()
    {
        return User::all();
    }


    public function show(User $user)
    {
        return $user;
    }

    public function update(Request $request, User $user)
    {
        try {
            $request->validate([
                'nombre' => 'required',
                'email' => 'required',
                'password' => 'nullable',
                'telefono' => 'nullable',
                'descuentoactivo' => 'nullable'
            ]);


            if ($request->has('nombre')){
                $user->nombre = $request->nombre;
            } 
            if ($request->has('email')){
                $user->email = $request->email;
            } 
            if ($request->has('password') && $request->password != '') {
                $user->password = Hash::make($request->password);
            }
            if ($request->has('telefono')){
                $user->telefono = $request->telefono;
            } 
            if ($request->has('tipoUsuario')){
                $user->tipoUsuario = $request->tipoUsuario;
            } 
            if ($request->has('descuentoactivo')){
                $user->descuentoactivo = $request->descuentoactivo;
            } 


            if ($user->save()) {
                return $user;
            } else {
                throw new Exception('Error al actualizar el usuario');
            }
        } catch (\Throwable $th) {
            return response(['mensaje' => $th->getMessage()], 500);
        }
    }

}
