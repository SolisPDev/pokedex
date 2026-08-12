<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PokemonController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\IaController;

Route::prefix('v1')->group(function () {
    // Public routes
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::get('/pokemon', [PokemonController::class, 'index']);
    Route::get('/pokemon/{nameOrId}', [PokemonController::class, 'show']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        
        Route::get('/collection', [CollectionController::class, 'index']);
        Route::post('/collection', [CollectionController::class, 'store']);
        Route::put('/collection/{id}', [CollectionController::class, 'update']);
        Route::delete('/collection/{id}', [CollectionController::class, 'destroy']);
        
        Route::post('/ia/identify-pokemon', [IaController::class, 'identify']);
        Route::post('/ia/chat-insights', [IaController::class, 'insights']);
    });
});
