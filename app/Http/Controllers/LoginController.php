<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    //
    function registro(Request $r)
    {
        try {
            //Validar campos del formulario
            $r->validate([
                'nombre' => 'required',
                'email' => 'required|unique:App\Models\User|email:rfc,dns', //No se puede repetir
                'ps1' => 'required',
                'ps2' => 'required|same:ps1'
            ]);
            $us = new User();
            $us->name = $r->nombre;
            $us->email = $r->email;
            $us->password = Hash::make($r->ps1);
            $us->save();
            return $us;
        } catch (\Throwable $th) {
            return response(['mensaje' => $th->getMessage()], 500);
        }
    }
    public function login(Request $r)
    {
        try {
            //Comporbar credenciales y en caso correcto
            //redirigir a inicio
            $r->validate([
                'email' => 'required|email:rfc,dns', 
                'ps' => 'required'
            ]);
            if (Auth::attempt(['email' => $r->email, 'password' => $r->ps], true)) {
                $usuario = User::find(Auth::user()->id);
                $token = $usuario->createToken('auth_token')->plainTextToken;
               return response(['token'=>$token,'usuario'=>$usuario],200);
            } else {
               throw new Exception('Login incorrecto');
            }
        } catch (\Throwable $th) {
            return response(['mensaje' => $th->getMessage()], 500);
        }
    }
    public function salir(Request $request){
        $request->user()->tokens()->delete(); //borrar todos los tokens del us
        return true;
    }
}
