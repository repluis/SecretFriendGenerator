<?php

use App\Http\Controllers\Api\GameConfigurationController;
use App\Http\Controllers\Api\PlayerController;
use Illuminate\Support\Facades\Route;

Route::prefix('players')->group(function () {
    // Ver jugadores - Las rutas específicas deben ir ANTES de las rutas con parámetros
    Route::get('/', [PlayerController::class, 'index']);
    Route::get('/with-friends', [PlayerController::class, 'indexWithFriends']);
    Route::get('/urls', [PlayerController::class, 'getAllUrls']);
    Route::get('/validate-assignments', [PlayerController::class, 'validateAssignments']);
    Route::get('/{id}', [PlayerController::class, 'show']);
    
    // Crear jugadores
    Route::post('/', [PlayerController::class, 'store']);
    Route::post('/many', [PlayerController::class, 'storeMany']);
    
    // Actualizar jugadores
    Route::put('/{id}/url', [PlayerController::class, 'updateUrl']);
    Route::put('/{id}/friend', [PlayerController::class, 'updateFriend']);
    Route::post('/assign-friends', [PlayerController::class, 'assignRandomFriends']);
    Route::post('/generate-urls', [PlayerController::class, 'generateUrls']);
    Route::post('/assign-urls-to-all', [PlayerController::class, 'assignUrlsToAll']);
    Route::post('/sync-urls-and-assign-names', [PlayerController::class, 'syncUrlsAndAssignNames']);
    
    // Eliminar jugadores
    Route::delete('/all', [PlayerController::class, 'destroyAll']);
    Route::delete('/by-name', [PlayerController::class, 'destroyByName']);
    
    // Eliminar URLs
    Route::delete('/urls/all', [PlayerController::class, 'destroyAllUrls']);
    
    // Actualizar player_id de una URL
    Route::put('/urls/{urlId}/player', [PlayerController::class, 'updateUrlPlayer']);
});

// Rutas de configuración del juego
Route::prefix('game-config')->group(function () {
    Route::get('/', [GameConfigurationController::class, 'show']);
    Route::put('/', [GameConfigurationController::class, 'update']);
});

