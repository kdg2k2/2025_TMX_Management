<?php

use App\Http\Controllers\Admin\ContractAppendixController;
use App\Http\Controllers\Admin\ContractBillController;
use App\Http\Controllers\Admin\ContractController;
use App\Http\Controllers\Admin\ContractFileController;
use App\Http\Controllers\Admin\ContractFileTypeController;
use App\Http\Controllers\Admin\ContractInvestorController;
use App\Http\Controllers\Admin\ContractTypeController;
use App\Http\Controllers\Admin\ContractUnitController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
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

        Route::prefix('file')->group(function () {
            Route::controller(ContractFileController::class)->group(function () {
                Route::delete('delete', 'delete')->name('contract.file.delete');
            });

            Route::prefix('type')->controller(ContractFileTypeController::class)->group(function () {
                Route::get('index', 'index')->name('contract.file.type.index');
                Route::get('create', 'create')->name('contract.file.type.create');
                Route::get('edit', 'edit')->name('contract.file.type.edit');
                Route::delete('delete', 'delete')->name('contract.file.type.delete');
            });
        });

        Route::prefix('bill')->controller(ContractBillController::class)->group(function () {
            Route::delete('delete', 'delete')->name('contract.bill.delete');
        });

        Route::prefix('appendix')->controller(ContractAppendixController::class)->group(function () {
            Route::delete('delete', 'delete')->name('contract.appendix.delete');
        });

        Route::prefix('unit')->controller(ContractUnitController::class)->group(function () {
            Route::get('index', 'index')->name('contract.unit.index');
            Route::get('create', 'create')->name('contract.unit.create');
            Route::get('edit', 'edit')->name('contract.unit.edit');
            Route::delete('delete', 'delete')->name('contract.unit.delete');
        });
    });

    Route::prefix('user')->controller(UserController::class)->group(function () {
        Route::get('index', 'index')->name('user.index');
        Route::get('create', 'create')->name('user.create');
        Route::get('edit', 'edit')->name('user.edit');
        Route::delete('delete', 'delete')->name('user.delete');
    });
});
