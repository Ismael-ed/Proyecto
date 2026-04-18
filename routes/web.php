<?php

use App\Http\Controllers\AlumnoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('alumnos');
});
/* ***FORMA INCIAL - MÁS FÁCIL DE ENTENDER ***
//Rutas para hacer un CRUD de Alumnos
//Ruta para consultar todos los alumnos
Route::get('/alumnos',[AlumnoController::class,'obtenerAlumnos'])->name('alumnos');
//Ruta para consultar un único alumno
Route::get('/alumnos/{idAlumno}',[AlumnoController::class,'verAlumno'])->name('alumno');
//Ruta para insertar un alumno
Route::post('/alumnos',[AlumnoController::class,'insertarAlumno'])->name('insertar');
//Ruta para modificar un alumno
Route::put('/alumnos',[AlumnoController::class,'modificarAlumno'])->name('modificar');
//Ruta para borrar un alumno
Route::delete('/alumnos',[AlumnoController::class,'borrarAlumno'])->name('borrar');
**********       */
Route::controller(AlumnoController::class)->group(function(){
    //Rutas para hacer un CRUD de Alumnos
    //Ruta para consultar todos los alumnos
    Route::get('/alumnos','obtenerAlumnos')->name('alumnos');
    //Ruta para consultar un único alumno
    Route::get('/alumnos/{idAlumno}','verAlumno')->name('alumno');
    //Ruta para insertar un alumno
    Route::post('/alumnos','insertarAlumno')->name('insertar');
    //Ruta para modificar un alumno
    Route::put('/alumnos/{idAlumno}','modificarAlumno')->name('modificar');
    //Ruta para borrar un alumno
    Route::delete('/alumnos','borrarAlumno')->name('borrar');
});
