<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BasCamionController;
use App\Http\Controllers\Api\BasPieController;
use App\Http\Controllers\Api\OrdenIngresoMpController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas públicas (sin autenticación)
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

/*
|--------------------------------------------------------------------------
| Rutas protegidas (requieren autenticación)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // Auth / Sesión
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout-all', [AuthController::class, 'logoutAll']);
        Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword'])
            ->middleware('role:admin');
    });

    // Usuario autenticado
    Route::get('/user', [AuthController::class, 'me']);
    Route::put('/user/profile', [AuthController::class, 'updateProfile']);
    Route::get('/user/login-history', [AuthController::class, 'myLoginHistory']);

    // Admin: historial de todos los logins
    Route::get('/admin/login-history', [AuthController::class, 'allLoginHistory'])
        ->middleware('role:admin');

    // Báscula / Camiones
    Route::get('/bas-camion', [BasCamionController::class, 'index']);
    Route::get('/bas-camion/{id}', [BasCamionController::class, 'show']);
    Route::post('/bas-camion', [BasCamionController::class, 'store']);

    // Báscula / Pesaje en Pie
    Route::get('/bas-pie', [BasPieController::class, 'index']);
    Route::get('/bas-pie/{id}', [BasPieController::class, 'show']);
    Route::post('/bas-pie', [BasPieController::class, 'store']);

    // Orden de Ingreso Materia Prima
    Route::get('/orden-ingreso-mp', [OrdenIngresoMpController::class, 'index']);
    Route::get('/orden-ingreso-mp/{id}', [OrdenIngresoMpController::class, 'show']);
    Route::post('/orden-ingreso-mp', [OrdenIngresoMpController::class, 'store']);
    Route::patch('/orden-ingreso-mp/{id}/estado', [OrdenIngresoMpController::class, 'updateEstado']);
});
