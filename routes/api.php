<?php

use App\Modules\Fundraising\Presentation\Controllers\FundraisingApiController;
use App\Modules\SecretSanta\Presentation\Controllers\GameConfigApiController;
use App\Modules\SecretSanta\Presentation\Controllers\PlayerApiController;
use App\Modules\Transaction\Presentation\Controllers\TransactionApiController;
use App\Modules\User\Presentation\Controllers\UserApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('players')->group(function () {
    // Ver jugadores - Las rutas específicas deben ir ANTES de las rutas con parámetros
    Route::get('/', [PlayerApiController::class, 'index']);
    Route::get('/with-friends', [PlayerApiController::class, 'indexWithFriends']);
    Route::get('/urls', [PlayerApiController::class, 'getAllUrls']);
    Route::get('/validate-assignments', [PlayerApiController::class, 'validateAssignments']);

    // Habilitar vista
    Route::post('/enable-view', [PlayerApiController::class, 'enableViewForPlayer']);
    Route::post('/enable-view-all', [PlayerApiController::class, 'enableViewForAll']);
    Route::post('/urls/{urlId}/reset-view', [PlayerApiController::class, 'resetUrlView']);

    Route::get('/{id}', [PlayerApiController::class, 'show']);

    // Crear jugadores
    Route::post('/', [PlayerApiController::class, 'store']);
    Route::post('/many', [PlayerApiController::class, 'storeMany']);

    // Actualizar jugadores
    Route::put('/{id}/friend', [PlayerApiController::class, 'updateFriend']);
    Route::post('/assign-friends', [PlayerApiController::class, 'assignRandomFriends']);
    Route::post('/generate-urls', [PlayerApiController::class, 'generateUrls']);
    Route::post('/assign-urls-to-all', [PlayerApiController::class, 'assignUrlsToAll']);
    Route::post('/sync-urls-and-assign-names', [PlayerApiController::class, 'syncUrlsAndAssignNames']);

    // Eliminar jugadores
    Route::delete('/all', [PlayerApiController::class, 'destroyAll']);
    Route::delete('/by-name', [PlayerApiController::class, 'destroyByName']);

    // Eliminar URLs
    Route::delete('/urls/all', [PlayerApiController::class, 'destroyAllUrls']);

    // Actualizar player_id de una URL
    Route::put('/urls/{urlId}/player', [PlayerApiController::class, 'updateUrlPlayer']);
});

// Rutas de configuración del juego
Route::prefix('game-config')->group(function () {
    Route::get('/', [GameConfigApiController::class, 'show']);
    Route::put('/', [GameConfigApiController::class, 'update']);
    Route::post('/reset', [GameConfigApiController::class, 'resetGame']);
});

// Rutas de recaudaciones
Route::prefix('fundraising')->group(function () {
    Route::post('/run-manual', [FundraisingApiController::class, 'runManual']);
    Route::delete('/reset-data', [FundraisingApiController::class, 'resetData']);
    Route::patch('/charges/{chargeId}/penalty', [FundraisingApiController::class, 'updatePenalty']);
});

// Rutas de transacciones
Route::prefix('transactions')->group(function () {
    Route::get('/', [TransactionApiController::class, 'index']);
    Route::post('/', [TransactionApiController::class, 'store']);
    Route::patch('/{id}/toggle-active', [TransactionApiController::class, 'toggleActive']);
});

// Rutas de usuarios
Route::prefix('users')->group(function () {
    Route::get('/', [UserApiController::class, 'index']);
    Route::post('/', [UserApiController::class, 'store']);
    Route::put('/{id}', [UserApiController::class, 'update']);
    Route::patch('/{id}/toggle-active', [UserApiController::class, 'toggleActive']);
    Route::patch('/{id}/identification', [UserApiController::class, 'updateIdentification']);
    Route::patch('/{id}/reset-password', [UserApiController::class, 'resetPassword']);
});
