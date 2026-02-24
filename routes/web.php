<?php

use App\Modules\Auth\Presentation\Controllers\AuthController;
use App\Modules\Auth\Infrastructure\Middleware\AuthenticateUser;
use App\Modules\Home\Presentation\Controllers\HomeController;
use App\Modules\Dashboard\Presentation\Controllers\DashboardController;
use App\Modules\SecretSanta\Presentation\Controllers\GameController;
use App\Modules\User\Presentation\Controllers\ProfileController;
use App\Modules\User\Presentation\Controllers\UserWebController;
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

// Hashea con bcrypt el valor recibido por parámetro
Route::get('/generate-password/{value}', fn (string $value) => response()->json([
    'password' => bcrypt($value),
]))->name('generate-password');

// ─── Protected routes ────────────────────────────────────────

Route::middleware(AuthenticateUser::class)->group(function () {

    // Dashboard administrativo completo
    Route::get('/home', [DashboardController::class, 'index'])->name('home');

    // Configuración del juego (requiere login)
    Route::get('/configuracion', [GameController::class, 'configuracion'])->name('configuracion');

    // Usuarios
    Route::get('/usuarios', [UserWebController::class, 'index'])->name('usuarios');

    // Perfil
    Route::get('/perfil', [ProfileController::class, 'show'])->name('perfil');
    Route::post('/perfil/nombre', [ProfileController::class, 'updateName'])->name('perfil.nombre');
    Route::post('/perfil/contrasena', [ProfileController::class, 'updatePassword'])->name('perfil.contrasena');
    Route::post('/perfil/identificacion', [ProfileController::class, 'updateIdentification'])->name('perfil.identificacion');

    // Fundraising (todas protegidas)
    Route::prefix('fundraising')->group(function () {
        Route::get('/recaudaciones', [App\Modules\Fundraising\Presentation\Controllers\FinanceController::class, 'recaudaciones'])->name('fundraising.recaudaciones');
        Route::get('/pagos', [App\Modules\Fundraising\Presentation\Controllers\FinanceController::class, 'pagos'])->name('fundraising.pagos');
        Route::get('/recaudaciones/{userId}/cargos', [App\Modules\Fundraising\Presentation\Controllers\FinanceController::class, 'cargosUsuario'])->name('fundraising.cargos-usuario');
    });
});
