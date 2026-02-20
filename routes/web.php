<?php

use App\Modules\SecretSanta\Presentation\Controllers\HomeController;
use App\Modules\SecretSanta\Presentation\Controllers\SecretFriendViewController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/juego', [HomeController::class, 'game'])->name('juego');
Route::get('/configuracion', [HomeController::class, 'configuracion'])->name('configuracion');

// Ruta para ver el amigo secreto usando la URL única del jugador
Route::get('/secret-friend/{url}', [SecretFriendViewController::class, 'show'])->name('secret-friend.show');

// Rutas de Fundraising
Route::prefix('fundraising')->group(function () {
    Route::get('/navidad', [App\Modules\Fundraising\Presentation\Controllers\FinanceController::class, 'navidad'])->name('fundraising.navidad');
    Route::get('/recaudaciones', [App\Modules\Fundraising\Presentation\Controllers\FinanceController::class, 'recaudaciones'])->name('fundraising.recaudaciones');
    Route::get('/recaudaciones/{userId}/cargos', [App\Modules\Fundraising\Presentation\Controllers\FinanceController::class, 'cargosUsuario'])->name('fundraising.cargos-usuario');
});
