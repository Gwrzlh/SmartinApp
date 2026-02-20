<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\authController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;

// =========================================
// PUBLIC ROUTES (Tidak perlu login)
// =========================================
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('login', [authController::class, 'index'])->name('login');
    Route::post('login', [authController::class, 'doLogin'])
        ->name('login.post')
        ->middleware('throttle:5,1'); 
});

Route::middleware('auth')->group(function () {

    Route::post('logout', [authController::class, 'logout'])
        ->name('logout');

    Route::get('dashboard', [RoleController::class, 'dashboard'])
        ->name('dashboard');

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {

        Route::get('dashboard', [RoleController::class, 'dashboard'])->name('dashboard');

        // Route::prefix('users')->name('users.')->group(function() {
        //     Route::get('/', [UserController::class, 'index'])->name('index'); // admin.users.index
        //     Route::get('/create', [UserController::class, 'create'])->name('create');
        //     Route::post('/store', [UserController::class, 'store'])->name('store');
        //     Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        // });

        Route::resource('users', UserController::class)->except(['show']);

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


    