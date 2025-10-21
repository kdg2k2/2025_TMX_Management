<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BiddingController;
use App\Http\Controllers\Api\BuildSoftwareController;
use App\Http\Controllers\Api\ContractAdvancePaymentController;
use App\Http\Controllers\Api\ContractAppendixController;
use App\Http\Controllers\Api\ContractBillController;
use App\Http\Controllers\Api\ContractController;
use App\Http\Controllers\Api\ContractFileController;
use App\Http\Controllers\Api\ContractFileTypeController;
use App\Http\Controllers\Api\ContractFinanceController;
use App\Http\Controllers\Api\ContractInvestorController;
use App\Http\Controllers\Api\ContractorExperienceController;
use App\Http\Controllers\Api\ContractPaymentController;
use App\Http\Controllers\Api\ContractScanFileController;
use App\Http\Controllers\Api\ContractScanFileTypeController;
use App\Http\Controllers\Api\ContractTypeController;
use App\Http\Controllers\Api\ContractUnitController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

// Auth routes
Route::prefix('auth')->controller(AuthController::class)->group(function () {
    Route::post('login', 'login')->name('api.auth.login');  // Đăng nhập
    Route::post('refresh', 'refresh')->name('api.auth.refresh')->middleware('throttle:5,1');  // Làm mới token
    Route::post('logout', 'logout')->name('api.auth.logout');  // Đăng xuất
    Route::post('force-logout-user/{user_id}', 'api.auth.forceLogoutUser')->middleware('auth.any');  // Ngắt token của user
});

Route::middleware(['web', 'auth.any', 'LogAccess'])->group(function () {
    Route::prefix('contract')->group(function () {
        Route::prefix('type')->controller(ContractTypeController::class)->group(function () {
            Route::get('list', 'list')->name('api.contract.type.list');
            Route::post('store', 'store')->name('api.contract.type.store');
            Route::patch('update', 'update')->name('api.contract.type.update');
        });

        Route::prefix('investor')->controller(ContractInvestorController::class)->group(function () {
            Route::get('list', 'list')->name('api.contract.investor.list');
            Route::post('store', 'store')->name('api.contract.investor.store');
            Route::patch('update', 'update')->name('api.contract.investor.update');
        });

        Route::controller(ContractController::class)->group(function () {
            Route::get('list', 'list')->name('api.contract.list');
            Route::get('show', 'show')->name('api.contract.show');
            Route::post('store', 'store')->name('api.contract.store');
            Route::patch('update', 'update')->name('api.contract.update');
        });

        Route::prefix('file')->group(function () {
            Route::controller(ContractFileController::class)->group(function () {
                Route::get('list', 'list')->name('api.contract.file.list');
                Route::post('store', 'store')->name('api.contract.file.store');
                Route::delete('delete', 'delete')->name('api.contract.file.delete');
            });

            Route::prefix('type')->controller(ContractFileTypeController::class)->group(function () {
                Route::get('list', 'list')->name('api.contract.file.type.list');
                Route::post('store', 'store')->name('api.contract.file.type.store');
                Route::patch('update', 'update')->name('api.contract.file.type.update');
                Route::delete('delete', 'delete')->name('api.contract.file.type.delete');
            });
        });

        Route::prefix('scan-file')->group(function () {
            Route::controller(ContractScanFileController::class)->group(function () {
                Route::get('list', 'list')->name('api.contract.scan-file.list');
                Route::post('store', 'store')->name('api.contract.scan-file.store');
                Route::patch('update', 'update')->name('api.contract.scan-file.update');
                Route::delete('delete', 'delete')->name('api.contract.scan-file.delete');
            });

            Route::prefix('type')->controller(ContractScanFileTypeController::class)->group(function () {
                Route::get('list', 'list')->name('api.contract.scan-file.type.list');
                Route::post('store', 'store')->name('api.contract.scan-file.type.store');
                Route::patch('update', 'update')->name('api.contract.scan-file.type.update');
                Route::delete('delete', 'delete')->name('api.contract.scan-file.type.delete');
            });
        });

        Route::prefix('bill')->controller(ContractBillController::class)->group(function () {
            Route::get('list', 'list')->name('api.contract.bill.list');
            Route::post('store', 'store')->name('api.contract.bill.store');
            Route::patch('update', 'update')->name('api.contract.bill.update');
        });

        Route::prefix('appendix')->controller(ContractAppendixController::class)->group(function () {
            Route::get('list', 'list')->name('api.contract.appendix.list');
            Route::post('store', 'store')->name('api.contract.appendix.store');
            Route::patch('update', 'update')->name('api.contract.appendix.update');
        });

        Route::prefix('unit')->controller(ContractUnitController::class)->group(function () {
            Route::get('list', 'list')->name('api.contract.unit.list');
            Route::post('store', 'store')->name('api.contract.unit.store');
            Route::patch('update', 'update')->name('api.contract.unit.update');
            Route::delete('delete', 'delete')->name('api.contract.unit.delete');
        });

        Route::prefix('finance')->group(function () {
            Route::controller(ContractFinanceController::class)->group(function () {
                Route::get('list', 'list')->name('api.contract.finance.list');
                Route::post('store', 'store')->name('api.contract.finance.store');
                Route::patch('update', 'update')->name('api.contract.finance.update');
                Route::delete('delete', 'delete')->name('api.contract.finance.delete');
            });

            Route::prefix('advance-payment')->controller(ContractAdvancePaymentController::class)->group(function () {
                Route::post('store', 'store')->name('api.contract.finance.advance-payment.store');
                Route::patch('update', 'update')->name('api.contract.finance.advance-payment.update');
            });

            Route::prefix('payment')->controller(ContractPaymentController::class)->group(function () {
                Route::post('store', 'store')->name('api.contract.finance.payment.store');
                Route::patch('update', 'update')->name('api.contract.finance.payment.update');
            });
        });
    });

    Route::prefix('user')->controller(UserController::class)->group(function () {
        Route::get('list', 'list')->name('api.user.list');
        Route::post('store', 'store')->name('api.user.store');
        Route::patch('update', 'update')->name('api.user.update');
    });

    Route::prefix('build-software')->controller(BuildSoftwareController::class)->group(function () {
        Route::get('list', 'list')->name('api.build-software.list');
        Route::post('store', 'store')->name('api.build-software.store');
        Route::patch('update', 'update')->name('api.build-software.update');
    });

    Route::prefix('bidding')->group(function () {
        Route::controller(BiddingController::class)->group(function () {
            Route::get('list', 'list')->name('api.bidding.list');
            Route::post('store', 'store')->name('api.bidding.store');
            Route::patch('update', 'update')->name('api.bidding.update');
        });
    });

    Route::prefix('contractor_experiences')->controller(ContractorExperienceController::class)->group(function () {
        Route::get('list', 'list')->name('api.contractor_experiences.list');
        Route::post('store', 'store')->name('api.contractor_experiences.store');
        Route::patch('update', 'update')->name('api.contractor_experiences.update');
    });
});
