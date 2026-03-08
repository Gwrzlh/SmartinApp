<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\authController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\Kasir\DashboardController as KasirDashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SubjectsController;
use App\Http\Controllers\mentorController;
use App\Http\Controllers\bundlingController;
use App\Http\Controllers\scheduleController;
use App\Http\Controllers\transactionController;

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

        Route::resource('users', UserController::class);
        Route::get('users/search', [UserController::class, 'search'])->name('users.search');
        Route::resource('category', CategoryController::class);
        Route::resource('subjects', SubjectsController::class);
        Route::resource('mentor', mentorController::class);
        Route::resource('bundling', bundlingController::class);
        Route::resource('schedules', scheduleController::class);
        Route::get('get-subjects/{mentorId}', [scheduleController::class, 'getSubjectsByMentor'])->name('getSubjects');
    });

    Route::middleware('role:owner')->group(function () {
        Route::get('owner', [RoleController::class, 'dashboard'])
            ->name('owner.dashboard');

    });

    Route::middleware('role:kasir')->group(function () {
        Route::get('kasir', [KasirDashboardController::class, 'index'])
            ->name('kasir.dashboard');
        Route::get('transaction', [transactionController::class, 'index'])->name('kasir.transaction'); 
        Route::post('storeSiswa', [transactionController::class, 'storeSiswa'])->name('simpanSiswa');
        Route::patch('siswa/{student}', [transactionController::class, 'updateSiswa'])->name('updateSiswa');
        Route::post('siswa/delete{student}', [transactionController::class, 'destroySiswa'])->name('hapusSiswa');
        
        });
});


    