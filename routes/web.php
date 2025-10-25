<?php

use App\Http\Controllers\Admin\BiddingController;
use App\Http\Controllers\Admin\BuildSoftwareController;
use App\Http\Controllers\Admin\ContractAdvancePaymentController;
use App\Http\Controllers\Admin\ContractAppendixController;
use App\Http\Controllers\Admin\ContractBillController;
use App\Http\Controllers\Admin\ContractController;
use App\Http\Controllers\Admin\ContractFileController;
use App\Http\Controllers\Admin\ContractFileTypeController;
use App\Http\Controllers\Admin\ContractFinanceController;
use App\Http\Controllers\Admin\ContractInvestorController;
use App\Http\Controllers\Admin\ContractPaymentController;
use App\Http\Controllers\Admin\ContractScanFileController;
use App\Http\Controllers\Admin\ContractScanFileTypeController;
use App\Http\Controllers\Admin\ContractTypeController;
use App\Http\Controllers\Admin\ContractUnitController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EligibilityController;
use App\Http\Controllers\Admin\GoogleDriveController;
use App\Http\Controllers\Admin\PersonnelController;
use App\Http\Controllers\Admin\PersonnelCustomFieldController;
use App\Http\Controllers\Admin\PersonnelFileController;
use App\Http\Controllers\Admin\PersonnelFileTypeController;
use App\Http\Controllers\Admin\PersonnelUnitController;
use App\Http\Controllers\Admin\ProofContractController;
use App\Http\Controllers\Admin\SoftwareOwnershipController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::get('/', 'getLogin')->name('login');
    Route::post('login', 'postLogin');
    Route::post('logout', 'logout')->name('logout');
});

Route::middleware(['isLogin', 'LogAccess'])->group(function () {
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

        Route::prefix('scan-file')->group(function () {
            Route::controller(ContractScanFileController::class)->group(function () {
                Route::delete('delete', 'delete')->name('contract.scan-file.delete');
            });

            Route::prefix('type')->controller(ContractScanFileTypeController::class)->group(function () {
                Route::get('index', 'index')->name('contract.scan-file.type.index');
                Route::get('create', 'create')->name('contract.scan-file.type.create');
                Route::get('edit', 'edit')->name('contract.scan-file.type.edit');
                Route::delete('delete', 'delete')->name('contract.scan-file.type.delete');
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

        Route::prefix('finance')->group(function () {
            Route::controller(ContractFinanceController::class)->group(function () {
                Route::delete('delete', 'delete')->name('contract.finance.delete');
            });

            Route::prefix('advance-payment')->controller(ContractAdvancePaymentController::class)->group(function () {
                Route::delete('delete', 'delete')->name('contract.finance.advance-payment.delete');
            });

            Route::prefix('payment')->controller(ContractPaymentController::class)->group(function () {
                Route::delete('delete', 'delete')->name('contract.finance.payment.delete');
            });
        });
    });

    Route::prefix('user')->controller(UserController::class)->group(function () {
        Route::get('index', 'index')->name('user.index');
        Route::get('create', 'create')->name('user.create');
        Route::get('edit', 'edit')->name('user.edit');
        Route::delete('delete', 'delete')->name('user.delete');
    });

    Route::prefix('build-software')->controller(BuildSoftwareController::class)->group(function () {
        Route::get('index', 'index')->name('build-software.index');
        Route::get('create', 'create')->name('build-software.create');
        Route::get('edit', 'edit')->name('build-software.edit');
        Route::delete('delete', 'delete')->name('build-software.delete');
        Route::post('accept', 'accept')->name('build-software.accept');
        Route::post('reject', 'reject')->name('build-software.reject');
        Route::post('update-state', 'updateState')->name('build-software.update-state');
    });

    Route::prefix('bidding')->group(function () {
        Route::controller(BiddingController::class)->group(function () {
            Route::get('index', 'index')->name('bidding.index');
            Route::get('create', 'create')->name('bidding.create');
            Route::get('edit', 'edit')->name('bidding.edit');
            Route::delete('delete', 'delete')->name('bidding.delete');
            Route::get('show', 'show')->name('bidding.show');
        });
    });

    Route::prefix('eligibilities')->controller(EligibilityController::class)->group(function () {
        Route::get('index', 'index')->name('eligibilities.index');
        Route::get('create', 'create')->name('eligibilities.create');
        Route::get('edit', 'edit')->name('eligibilities.edit');
        Route::delete('delete', 'delete')->name('eligibilities.delete');
    });

    Route::prefix('proof_contracts')->controller(ProofContractController::class)->group(function () {
        Route::get('index', 'index')->name('proof_contracts.index');
        Route::get('create', 'create')->name('proof_contracts.create');
        Route::get('edit', 'edit')->name('proof_contracts.edit');
        Route::delete('delete', 'delete')->name('proof_contracts.delete');
    });

    Route::prefix('software_ownerships')->controller(SoftwareOwnershipController::class)->group(function () {
        Route::get('index', 'index')->name('software_ownerships.index');
        Route::get('create', 'create')->name('software_ownerships.create');
        Route::get('edit', 'edit')->name('software_ownerships.edit');
        Route::delete('delete', 'delete')->name('software_ownerships.delete');
    });

    Route::prefix('personnels')->group(function () {
        Route::controller(PersonnelController::class)->group(function () {
            Route::get('index', 'index')->name('personnels.index');
            Route::get('create', 'create')->name('personnels.create');
            Route::get('edit', 'edit')->name('personnels.edit');
            Route::delete('delete', 'delete')->name('personnels.delete');
        });

        Route::prefix('custom-field')->controller(PersonnelCustomFieldController::class)->group(function () {
            Route::get('index', 'index')->name('personnels.custom-field.index');
            Route::get('create', 'create')->name('personnels.custom-field.create');
            Route::get('edit', 'edit')->name('personnels.custom-field.edit');
            Route::delete('delete', 'delete')->name('personnels.custom-field.delete');
        });

        Route::prefix('units')->controller(PersonnelUnitController::class)->group(function () {
            Route::get('index', 'index')->name('personnels.units.index');
            Route::get('create', 'create')->name('personnels.units.create');
            Route::get('edit', 'edit')->name('personnels.units.edit');
            Route::delete('delete', 'delete')->name('personnels.units.delete');
        });

        Route::prefix('file')->group(function () {
            Route::controller(PersonnelFileController::class)->group(function () {
                Route::get('index', 'index')->name('personnels.file.index');
                Route::get('create', 'create')->name('personnels.file.create');
                Route::get('edit', 'edit')->name('personnels.file.edit');
                Route::delete('delete', 'delete')->name('personnels.file.delete');
            });

            Route::prefix('type')->controller(PersonnelFileTypeController::class)->group(function () {
                Route::get('index', 'index')->name('personnels.file.type.index');
                Route::get('create', 'create')->name('personnels.file.type.create');
                Route::get('edit', 'edit')->name('personnels.file.type.edit');
                Route::delete('delete', 'delete')->name('personnels.file.type.delete');
            });
        });
    });

    Route::prefix('google')->group(function () {
        Route::prefix('drive')->controller(GoogleDriveController::class)->group(function () {
            Route::get('auth', 'auth')->name('google.drive.auth');
            Route::get('callback', 'callback')->name('google.drive.callback');
            Route::get('init-folders', 'initFolders')->name('google.drive.init-folders');

            // Route::post('create-folder', 'createFolder')->name('google.drive.create-folder');
            // Route::post('upload-file', 'uploadFile')->name('google.drive.upload-file');
            // Route::get('list-folders', 'listFolders')->name('google.drive.list-folders');
            // Route::get('get-folder', 'getFolder')->name('google.drive.get-folder');
            // Route::put('update-folder', 'updateFolder')->name('google.drive.update-folder');
            // Route::put('move-folder', 'moveFolder')->name('google.drive.move-folder');
            // Route::delete('delete-folder', 'deleteFolder')->name('google.drive.delete-folder');
            // Route::get('search-folder', 'searchFolder')->name('google.drive.search-folder');
        });
    });
});

