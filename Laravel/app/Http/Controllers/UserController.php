<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Exception;

class UserController extends Controller
{
    public function index()
    {
        return User::all();
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required',
                'tipoUsuario' => 'required'
            ]);

            $user = new User();
            $user->nombre = $request->nombre;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->telefono = $request->telefono;
            $user->tipoUsuario = $request->tipoUsuario;

            if ($user->save()) {
                return $user;
            } else {
                throw new Exception('Error al crear el usuario');
            }
        } catch (\Throwable $th) {
            return response(['mensaje' => $th->getMessage()], 500);
        }
    }

    public function show(User $user)
    {
        return $user;
    }

    
    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $request->validate([
                'nombre' => 'required|string|max:100',
                'telefono' => 'required|string|max:20',
                'tipoUsuario' => 'required'
            ]);

            $user->nombre = $request->nombre;
            $user->telefono = $request->telefono;
            $user->tipoUsuario = $request->tipoUsuario;

            if ($user->save()) {
                return response()->json($user, 200);
            }
        } catch (\Throwable $th) {
            return response()->json(['mensaje' => 'Error: ' . $th->getMessage()], 500);
        }
    }

    public function destroy(User $user)
    {
        try {
            if ($user->delete()) {
                return ["mensaje" => "Usuario eliminado"];
            } else {
                throw new Exception('Error al eliminar');
            }
        } catch (\Throwable $th) {
            return response(['mensaje' => $th->getMessage()], 500);
        }
    }
}