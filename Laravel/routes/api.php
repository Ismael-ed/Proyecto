<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ObjetoController;
use App\Http\Controllers\AlquilerController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\ComprasController;
use App\Http\Controllers\UserController;

Route::controller(LoginController::class)->group(function () {
    Route::post('registro', 'registro');
    Route::post('login', 'login');
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('salir', [LoginController::class, 'salir'])->middleware('auth:sanctum');

Route::apiResource('citas', CitaController::class);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('usuario', UserController::class);
    Route::apiResource('objetos', ObjetoController::class);
    Route::apiResource('alquileres', AlquilerController::class);
    Route::apiResource('compras', ComprasController::class);
    Route::apiResource('carrito', CarritoController::class);
    Route::post('/carrito/finalizar', [CarritoController::class, 'finalizar']);
    Route::get('/facturas/mis-facturas', [CarritoController::class, 'misFacturas']);
    Route::get('/facturas/detalles/{id}', [CarritoController::class, 'detalles']);
});