<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ObjetoController;
use App\Http\Controllers\AlquilerController;
use App\Http\Controllers\ComprasController;
use App\Http\Controllers\FacturaController;

Route::controller(LoginController::class)->group(function () {
    Route::post('registro', 'registro');
    Route::post('login', 'login');
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('salir', [LoginController::class, 'salir'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('objetos', ObjetoController::class);
    Route::apiResource('alquileres', AlquilerController::class);
    Route::apiResource('compras', ComprasController::class);
    Route::apiResource('facturas', FacturaController::class);
});