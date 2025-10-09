<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::get('/', 'getLogin')->name('login');
    Route::post('login', 'postLogin')->name('login');
});

Route::middleware(['isLogin'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
});
