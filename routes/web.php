<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransaksiController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    ])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'
    ])->name('dashboard');
    Route::get('/transaksi', [TransaksiController::class, 'index'
    ])->name('transaksi');
    Route::post('/transaksi', [TransaksiController::class, 'store'
    ])->name('transaksi.store');
    Route::put('/transaksi/edit/{id}', [TransaksiController::class, 'update'
    ])->name('transaksi.update');
    Route::delete('/transaksi/{id}', [TransaksiController::class, 'destroy'
    ])->name('transaksi.destroy');
});
