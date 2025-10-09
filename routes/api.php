<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

// Auth routes
Route::prefix('auth')->controller(AuthController::class)->group(function () {
    Route::post('login', 'login')->name('api.auth.login');  // Đăng nhập
    Route::post('refresh', 'refresh')->name('api.auth.refresh')->middleware('throttle:5,1');  // Làm mới token
    Route::post('logout', 'logout')->name('api.auth.logout');  // Đăng xuất
    Route::post('force-logout-user/{user_id}', 'api.auth.forceLogoutUser')->middleware('auth.any');  // Ngắt token của user
});
