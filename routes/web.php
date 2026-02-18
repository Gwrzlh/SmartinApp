<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\authController;
use App\Http\Controllers\RoleController;

// =========================================
// PUBLIC ROUTES (Tidak perlu login)
// =========================================
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('login', [authController::class, 'index'])
    ->name('login')
    ->middleware('guest'); 

Route::post('login', [authController::class, 'doLogin'])
    ->name('login.post')
    ->middleware(['guest', 'throttle:5,1']); 

Route::middleware('auth')->group(function () {

    Route::post('logout', [authController::class, 'logout'])
        ->name('logout');

    Route::get('dashboard', [RoleController::class, 'dashboard'])
        ->name('dashboard');

    Route::middleware('role:admin')->group(function () {
        Route::get('admin', [RoleController::class, 'dashboard'])
            ->name('admin.dashboard');
    });

    Route::middleware('role:owner')->group(function () {
        Route::get('owner', [RoleController::class, 'dashboard'])
            ->name('owner.dashboard');
    });

    Route::middleware('role:kasir')->group(function () {
        Route::get('kasir', [RoleController::class, 'dashboard'])
            ->name('kasir.dashboard');
    });
});


    