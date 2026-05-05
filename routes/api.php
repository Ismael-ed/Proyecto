<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ObjetoController;

//Route::get('/index', function () {
//    return view('index');
//});

Route::controller(LoginController::class)->group(function () {
    Route::post('registro', 'registro');
    Route::post('login', 'login');
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('salir', [LoginController::class, 'salir'])->middleware('auth:sanctum');

Route::apiResource('objetos', ObjetoController::class);
