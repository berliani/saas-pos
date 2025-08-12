<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\StaffDashboardController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'company.auth'
])->group(function () {

    // --- RUTE KHUSUS OWNER ---
    Route::middleware(['role:owner'])->group(function () {
        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');

        Route::resource('products', ProductController::class);
        Route::resource('users',UserController::class);

    });

    // --- RUTE KHUSUS STAFF ---
    Route::middleware(['role:staff'])->group(function() {
        Route::get('/staff/dashboard', [StaffDashboardController::class, 'index'])->name('staff.dashboard');
    });

    // --- RUTE BERSAMA (OWNER & STAFF) ---
    Route::middleware(['role:owner,staff'])->group(function () {
        Route::get('pos', [PosController::class, 'index'])->name('pos.index');
        Route::post('pos', [PosController::class, 'store'])->name('pos.store');
    });

});
