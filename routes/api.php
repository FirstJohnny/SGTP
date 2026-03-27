<?php
// routes/api.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\GpsController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Bilhetica\BilheteController;
use App\Http\Controllers\Ocorrencia\OcorrenciaController;
use App\Http\Controllers\Publico\PublicController;

/*
|--------------------------------------------------------------------------
| API Routes V1
|--------------------------------------------------------------------------
*/
Route::prefix('v1')->group(function () {

    // Rotas públicas
    Route::prefix('public')->group(function () {
        Route::get('/rotas', [PublicController::class, 'rotas']);
        Route::get('/horarios', [PublicController::class, 'horarios']);
        Route::get('/tarifas', [PublicController::class, 'tarifas']);
        Route::post('/feedback', [PublicController::class, 'feedback']);
        Route::post('/planejar-viagem', [PublicController::class, 'planejarViagem']);
    });

    // Autenticação API
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

    // Rotas protegidas por token
    Route::middleware('auth:sanctum')->group(function () {

        // GPS Tracking
        Route::post('/gps/update', [GpsController::class, 'update']);
        Route::get('/gps/vehicles', [GpsController::class, 'vehicles']);
        Route::get('/gps/vehicle/{id}', [GpsController::class, 'vehicle']);
        Route::get('/gps/historico', [GpsController::class, 'historico']);

        // Bilhética API
        Route::post('/bilhetes/vender', [BilheteController::class, 'vender']);
        Route::post('/bilhetes/validar', [BilheteController::class, 'validar']);
        Route::get('/bilhetes/{codigo}', [BilheteController::class, 'consultar']);

        // Ocorrências API
        Route::post('/ocorrencias', [OcorrenciaController::class, 'store']);
        Route::get('/ocorrencias', [OcorrenciaController::class, 'index']);

        // Dados do usuário
        Route::get('/me', [AuthController::class, 'me']);
        Route::put('/me', [AuthController::class, 'updateProfile']);
    });
});
