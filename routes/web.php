<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\authController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\DashboardController;
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
            Route::get('/kasir', [DashboardController::class, 'index'])
                ->name('kasir.dashboard');
            Route::get('/transaction', [transactionController::class, 'index'])
                ->name('kasir.transaction');

            Route::get('/siswa-manage', [transactionController::class, 'siswaManage'])
                ->name('kasir.siswa.index');
            Route::get('/riwayat-transaksi', [transactionController::class, 'riwayatTransaksi'])
                ->name('kasir.riwayat.index');

            Route::prefix('siswa')->group(function(){
                Route::post('/store', [transactionController::class, 'storeSiswa'])->name('simpanSiswa');
                Route::patch('/{student}', [transactionController::class, 'updateSiswa'])->name('updateSiswa');
                Route::post('/delete/{student}', [transactionController::class, 'destroySiswa'])->name('hapusSiswa');

            });
            Route::post('/siswa/select', [transactionController::class,'selectStudent'])
                ->name('kasir.selectStudent');
            Route::post('/cart/add', [transactionController::class,'addToCart'])
                ->name('kasir.cart.add');
            Route::post('/cart/remove/{items}', [transactionController::class, 'removeItem'])
                ->name('kasir.cart.remove');
            Route::post('/transaction/checkout',[transactionController::class,'checkout'])
                ->name('kasir.checkout');
            Route::get('/invoice/{id}', [transactionController::class, 'generateInvoice'])->name('kasir.invoice');
            
            Route::get('/transaction/{transaction_id}/schedules', [\App\Http\Controllers\SchedulePlacementController::class, 'showPlacementUI'])
                ->name('kasir.transaction.schedules');
            Route::post('/transaction/{transaction_id}/schedules', [\App\Http\Controllers\SchedulePlacementController::class, 'storeAssignments'])
                ->name('kasir.transaction.saveSchedules');

            Route::get('/schedules-manage', [\App\Http\Controllers\SchedulePlacementController::class, 'index'])
                ->name('kasir.schedules.index');
            Route::get('/schedules-manage/{id}', [\App\Http\Controllers\SchedulePlacementController::class, 'show'])
                ->name('kasir.schedules.show')
                ->where('id', '[0-9]+');
            Route::get('/api/schedules/available/{subject_id}', [\App\Http\Controllers\SchedulePlacementController::class, 'getAvailableSchedules'])
                ->name('kasir.schedules.available');
            Route::post('/schedules-manage/reschedule', [\App\Http\Controllers\SchedulePlacementController::class, 'reschedule'])
                ->name('kasir.schedules.reschedule');

        });
    });


    