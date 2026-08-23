<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactController;

// 1. Rutas Públicas
Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/refresh', [AuthController::class, 'refresh']);
Route::post('/contact', [ContactController::class, 'store']);

Route::get('projects', [ProjectController::class, 'index']); // Ver todos
Route::get('projects/{project}', [ProjectController::class, 'show']); // Ver uno específico
// Ver las categorías
Route::get('categories', [\App\Http\Controllers\CategoryController::class, 'index']);

// 2. Rutas Protegidas (Requieren Token JWT)
Route::middleware('auth:api')->group(function () {
    
    // Autenticación
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::post('auth/me', [AuthController::class, 'me']);
    
    // Proyectos (CRUD Administrativo)
    Route::post('projects', [ProjectController::class, 'store']);
    // NOTA TÉCNICA: Usamos POST en lugar de PUT/PATCH porque PHP y Postman 
    // tienen un bug histórico manejando 'multipart/form-data' en métodos PUT.
    Route::post('projects/{project}', [ProjectController::class, 'update']); 
    // Ruta específica para borrar una foto o video individual de la galería
    Route::delete('project-media/{id}', [ProjectController::class, 'destroyMedia']);
    // Ruta para borrar un proyecto
    Route::delete('projects/{project}', [ProjectController::class, 'destroy']);

    // Categorías (CRUD Administrativo)
    Route::post('categories', [\App\Http\Controllers\CategoryController::class, 'store']);
    Route::put('categories/{category}', [\App\Http\Controllers\CategoryController::class, 'update']);
    Route::delete('categories/{category}', [\App\Http\Controllers\CategoryController::class, 'destroy']);
    
});