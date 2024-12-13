<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\usuariosController;
use App\Http\Controllers\comidaController;


// Obtener todas las comidas
Route::get('/comidas', [comidaController::class, 'index']);

// Obtener una comida específica por ID
Route::get('/comidas/{id}', [comidaController::class, 'show']);

// Crear una nueva comida
Route::post('/comidas', [comidaController::class, 'store']);

// Actualizar completamente una comida
Route::put('/comidas/{id}', [comidaController::class, 'update']);

// Actualizar parcialmente una comida
Route::patch('/comidas/{id}', [comidaController::class, 'updateParcial']);

// Eliminar una comida
Route::delete('/comidas/{id}', [comidaController::class, 'destroy']);

// Obtener todos los usuarios
Route::get('/usuarios', [usuariosController::class, 'index']);

// Obtener un usuario específico por ID
Route::get('/usuarios/{id}', [usuariosController::class, 'show']);

// Crear un nuevo usuario
Route::post('/usuarios', [usuariosController::class, 'store']);

// Actualizar completamente un usuario
Route::put('/usuarios/{id}', [usuariosController::class, 'update']);

// Actualizar parcialmente un usuario
Route::patch('/usuarios/{id}', [usuariosController::class, 'updateParcial']);

// Eliminar un usuario
Route::delete('/usuarios/{id}', [usuariosController::class, 'destroy']);



