<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function registro(Request $r)
    {
        try {
            $r->validate([
                'nombre' => 'required',
                'email' => 'required|unique:users,email',
                'ps1' => 'required',
                'ps2' => 'required|same:ps1',
                'telefono' => 'required'
            ]);

            $us = new User();
            $us->nombre = $r->nombre;
            $us->email = $r->email;
            $us->password = Hash::make($r->ps1);
            $us->telefono = $r->telefono;
            $us->save();

            return response()->json($us, 201);
        } catch (\Throwable $th) {
            return response()->json(['mensaje' => $th->getMessage()], 500);
        }
    }

    public function login(Request $r)
    {
        try {
            $r->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if (Auth::attempt(['email' => $r->email, 'password' => $r->password])) {
                $usuario = Auth::user();
                $token = $usuario->createToken('auth_token')->plainTextToken;
                return response()->json(['token' => $token, 'usuario' => $usuario], 200);
            }

            return response()->json(['mensaje' => 'Credenciales incorrectas'], 401);
        } catch (\Throwable $th) {
            return response()->json(['mensaje' => $th->getMessage()], 500);
        }
    }

    public function salir(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['status' => true]);
    }
}