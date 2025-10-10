<?php

use App\Http\Controllers\Admin\ContractController;
use App\Http\Controllers\Admin\ContractInvestorController;
use App\Http\Controllers\Admin\ContractTypeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::get('/', 'getLogin')->name('login');
    Route::post('login', 'postLogin');
    Route::post('logout', 'logout')->name('logout');
});

Route::middleware(['isLogin'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('contract')->group(function () {
        Route::prefix('type')->controller(ContractTypeController::class)->group(function () {
            Route::get('index', 'index')->name('contract.type.index');
            Route::get('create', 'create')->name('contract.type.create');
            Route::get('edit', 'edit')->name('contract.type.edit');
            Route::delete('delete', 'delete')->name('contract.type.delete');
        });

        Route::prefix('investor')->controller(ContractInvestorController::class)->group(function () {
            Route::get('index', 'index')->name('contract.investor.index');
            Route::get('create', 'create')->name('contract.investor.create');
            Route::get('edit', 'edit')->name('contract.investor.edit');
            Route::delete('delete', 'delete')->name('contract.investor.delete');
        });

        Route::controller(ContractController::class)->group(function () {
            Route::get('index', 'index')->name('contract.index');
            Route::get('create', 'create')->name('contract.create');
            Route::get('edit', 'edit')->name('contract.edit');
            Route::delete('delete', 'delete')->name('contract.delete');
        });
    });
});
