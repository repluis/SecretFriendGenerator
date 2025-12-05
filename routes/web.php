<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SecretFriendViewController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Ruta para ver el amigo secreto usando la URL única del jugador
Route::get('/secret-friend/{url}', [SecretFriendViewController::class, 'show'])->name('secret-friend.show');

// Ruta de administración
Route::get('/admin', [AdminController::class, 'index'])->name('admin');
