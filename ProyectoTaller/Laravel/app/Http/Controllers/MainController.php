<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class MainController extends Controller
{
    public function login()
    {
        return view('login');
    }

    public function postLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'ps' => 'required|min:3'
        ]);

        if ($request->email == 'ejemplo@gmail.com' && $request->ps == '123') {

            Session::put('us', (object)[
                'nombre' => 'Ejemplo',
                'perfil' => 'U',
                'id' => 1
            ]);

            Session::put('estadistica', [
                'numLibrosVenta' => 8,
                'numLibrosVendidos' => 3,
                'saldoTotal' => 245.75
            ]);

            return redirect()->route('index')->with('mensaje', '¡Bienvenido!');
        }

        return redirect()->route('login')->with('error', 'Email o contraseña incorrectos');
    }

    public function index()
    {
        if (!Session::has('us')) {
            return redirect()->route('login');
        }

        $anuncios = [
            (object)['getTitulo' => 'Libro PHP Avanzado', 'getPrecio' => '25€', 'getDescripcion' => 'Aprende Laravel'],
            (object)['getTitulo' => 'CSS Moderno', 'getPrecio' => '18€', 'getDescripcion' => 'Flexbox y Grid'],
            (object)['getTitulo' => 'JS React', 'getPrecio' => '35€', 'getDescripcion' => 'Hooks y Context']
        ];

        $user = Session::get('us');
        $estadistica = Session::get('estadistica');

        return view('index', compact('anuncios', 'user', 'estadistica'));
    }
}
