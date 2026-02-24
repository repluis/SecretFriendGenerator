<?php

use App\Modules\Auth\Presentation\Controllers\AuthController;
use App\Modules\Auth\Infrastructure\Middleware\AuthenticateUser;
use App\Modules\Home\Presentation\Controllers\HomeController;
use App\Modules\Dashboard\Presentation\Controllers\DashboardController;
use App\Modules\SecretSanta\Presentation\Controllers\GameController;
use App\Modules\SecretSanta\Presentation\Controllers\SecretFriendViewController;
use Illuminate\Support\Facades\Route;

// ─── Public routes ───────────────────────────────────────────

// Landing pública con tabla de pagos + botón de login
Route::get('/', [HomeController::class, 'index'])->name('landing');

// Auth: login y logout
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Ver URL de amigo secreto (público)
Route::get('/secret-friend/{url}', [SecretFriendViewController::class, 'show'])->name('secret-friend.show');

// Juego (público)
Route::get('/juego', [GameController::class, 'game'])->name('juego');

// ─── Protected routes ────────────────────────────────────────

Route::middleware(AuthenticateUser::class)->group(function () {

    // Dashboard administrativo completo
    Route::get('/home', [DashboardController::class, 'index'])->name('home');

    // Configuración del juego (requiere login)
    Route::get('/configuracion', [GameController::class, 'configuracion'])->name('configuracion');

    // Fundraising (todas protegidas)
    Route::prefix('fundraising')->group(function () {
        Route::get('/navidad', [App\Modules\Fundraising\Presentation\Controllers\FinanceController::class, 'navidad'])->name('fundraising.navidad');
        Route::get('/recaudaciones', [App\Modules\Fundraising\Presentation\Controllers\FinanceController::class, 'recaudaciones'])->name('fundraising.recaudaciones');
        Route::get('/pagos', [App\Modules\Fundraising\Presentation\Controllers\FinanceController::class, 'pagos'])->name('fundraising.pagos');
        Route::get('/recaudaciones/{userId}/cargos', [App\Modules\Fundraising\Presentation\Controllers\FinanceController::class, 'cargosUsuario'])->name('fundraising.cargos-usuario');
    });
});
