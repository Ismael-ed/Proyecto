<?php

use App\Http\Controllers\MainController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('index');
});

Route::controller(MainController::class)->group(function () {
    // Index 
    Route::get('/index', 'index')->name('index');
    
    // Login
    Route::get('/login', 'login')->name('login');
    Route::post('/login', 'postLogin')->name('login.post');
    
    Route::post('/cerrar', 'cerrarSesion')->name('cerrar');
});
