<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BiddingContractorExperienceController;
use App\Http\Controllers\Api\BiddingController;
use App\Http\Controllers\Api\BiddingEligibilityController;
use App\Http\Controllers\Api\BiddingImplementationPersonnelController;
use App\Http\Controllers\Api\BiddingOrtherFileController;
use App\Http\Controllers\Api\BiddingProofContractController;
use App\Http\Controllers\Api\BiddingSoftwareOwnershipController;
use App\Http\Controllers\Api\BuildSoftwareController;
use App\Http\Controllers\Api\ContractAdvancePaymentController;
use App\Http\Controllers\Api\ContractAppendixController;
use App\Http\Controllers\Api\ContractBillController;
use App\Http\Controllers\Api\ContractController;
use App\Http\Controllers\Api\ContractFileController;
use App\Http\Controllers\Api\ContractFileTypeController;
use App\Http\Controllers\Api\ContractFinanceController;
use App\Http\Controllers\Api\ContractInvestorController;
use App\Http\Controllers\Api\ContractPaymentController;
use App\Http\Controllers\Api\ContractScanFileController;
use App\Http\Controllers\Api\ContractScanFileTypeController;
use App\Http\Controllers\Api\ContractTypeController;
use App\Http\Controllers\Api\ContractUnitController;
use App\Http\Controllers\Api\EligibilityController;
use App\Http\Controllers\Api\LeaveRequestController;
use App\Http\Controllers\Api\PersonnelController;
use App\Http\Controllers\Api\PersonnelCustomFieldController;
use App\Http\Controllers\Api\PersonnelFileController;
use App\Http\Controllers\Api\PersonnelFileTypeController;
use App\Http\Controllers\Api\PersonnelUnitController;
use App\Http\Controllers\Api\ProofContractController;
use App\Http\Controllers\Api\SoftwareOwnershipController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserSubEmailController;
use App\Http\Controllers\Api\UserTimeTableController;
use App\Http\Controllers\Api\UserWarningController;
use App\Http\Controllers\Api\WorkScheduleController;
use App\Http\Controllers\Api\WorkTimesheetController;
use App\Http\Controllers\Api\WorkTimesheetOvertimeController;
use App\Http\Controllers\Api\WorkTimesheetOvertimeDetailController;
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

    Route::prefix('user')->group(function () {
        Route::controller(UserController::class)->group(function () {
            Route::get('list', 'list')->name('api.user.list');
            Route::post('store', 'store')->name('api.user.store');
            Route::patch('update', 'update')->name('api.user.update');
        });

        Route::prefix('sub-email')->controller(UserSubEmailController::class)->group(function () {
            Route::get('list', 'list')->name('api.user.sub-email.list');
            Route::post('store', 'store')->name('api.user.sub-email.store');
            Route::patch('update', 'update')->name('api.user.sub-email.update');
            Route::delete('delete', 'delete')->name('api.user.sub-email.delete');
        });

        Route::prefix('timetable')->controller(UserTimeTableController::class)->group(function () {
            Route::get('list', 'list')->name('api.user.timetable.list');
            Route::get('get-weeks', 'getWeeks')->name('api.user.timetable.get-weeks');
        });

        Route::prefix('warning')->controller(UserWarningController::class)->group(function () {
            Route::post('store', 'store')->name('api.user.warning.store');
        });
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
            Route::get('download-built-result', 'downloadBuiltResult')->name('api.bidding.download-built-result');
        });

        Route::prefix('contractor-experience')->controller(BiddingContractorExperienceController::class)->group(function () {
            Route::get('list', 'list')->name('api.bidding.contractor-experience.list');
            Route::post('store', 'store')->name('api.bidding.contractor-experience.store');
            Route::delete('delete', 'delete')->name('api.bidding.contractor-experience.delete');
            Route::delete('delete-by-contract-id', 'deleteByContractId')->name('api.bidding.contractor-experience.delete-by-contract-id');
        });

        Route::prefix('eligibility')->controller(BiddingEligibilityController::class)->group(function () {
            Route::get('list', 'list')->name('api.bidding.eligibility.list');
            Route::post('store', 'store')->name('api.bidding.eligibility.store');
            Route::delete('delete', 'delete')->name('api.bidding.eligibility.delete');
            Route::delete('delete-by-eligibility-id', 'deleteByEligibilityIdRequest')->name('api.bidding.eligibility.delete-by-eligibility-id');
        });

        Route::prefix('software-ownership')->controller(BiddingSoftwareOwnershipController::class)->group(function () {
            Route::get('list', 'list')->name('api.bidding.software-ownership.list');
            Route::post('store', 'store')->name('api.bidding.software-ownership.store');
            Route::delete('delete', 'delete')->name('api.bidding.software-ownership.delete');
            Route::delete('delete-by-software-ownership-id', 'deleteBySoftwareOwnershipId')->name('api.bidding.software-ownership.delete-by-software-ownership-id');
        });

        Route::prefix('proof-contract')->controller(BiddingProofContractController::class)->group(function () {
            Route::get('list', 'list')->name('api.bidding.proof-contract.list');
            Route::post('store', 'store')->name('api.bidding.proof-contract.store');
            Route::delete('delete', 'delete')->name('api.bidding.proof-contract.delete');
            Route::delete('delete-by-proof-contract-id', 'deleteByProofContractId')->name('api.bidding.proof-contract.delete-by-proof-contract-id');
        });

        Route::prefix('implementation-personnel')->controller(BiddingImplementationPersonnelController::class)->group(function () {
            Route::get('list', 'list')->name('api.bidding.implementation-personnel.list');
            Route::post('store', 'store')->name('api.bidding.implementation-personnel.store');
            Route::delete('delete', 'delete')->name('api.bidding.implementation-personnel.delete');
        });

        Route::prefix('orther-file')->controller(BiddingOrtherFileController::class)->group(function () {
            Route::get('list', 'list')->name('api.bidding.orther-file.list');
            Route::post('store', 'store')->name('api.bidding.orther-file.store');
            Route::delete('delete', 'delete')->name('api.bidding.orther-file.delete');
        });
    });

    Route::prefix('eligibilities')->controller(EligibilityController::class)->group(function () {
        Route::get('list', 'list')->name('api.eligibilities.list');
        Route::post('store', 'store')->name('api.eligibilities.store');
        Route::patch('update', 'update')->name('api.eligibilities.update');
    });

    Route::prefix('proof_contracts')->controller(ProofContractController::class)->group(function () {
        Route::get('list', 'list')->name('api.proof_contracts.list');
        Route::post('store', 'store')->name('api.proof_contracts.store');
        Route::patch('update', 'update')->name('api.proof_contracts.update');
    });

    Route::prefix('software_ownerships')->controller(SoftwareOwnershipController::class)->group(function () {
        Route::get('list', 'list')->name('api.software_ownerships.list');
        Route::post('store', 'store')->name('api.software_ownerships.store');
        Route::patch('update', 'update')->name('api.software_ownerships.update');
    });

    Route::prefix('personnels')->group(function () {
        Route::controller(PersonnelController::class)->group(function () {
            Route::get('list', 'list')->name('api.personnels.list');
            Route::get('syncthetic-excel', 'synctheticExcel')->name('api.personnels.syncthetic-excel');
            Route::post('store', 'store')->name('api.personnels.store');
            Route::patch('update', 'update')->name('api.personnels.update');
        });

        Route::prefix('custom-field')->controller(PersonnelCustomFieldController::class)->group(function () {
            Route::get('list', 'list')->name('api.personnels.custom-field.list');
            Route::post('store', 'store')->name('api.personnels.custom-field.store');
            Route::patch('update', 'update')->name('api.personnels.custom-field.update');
        });

        Route::prefix('units')->controller(PersonnelUnitController::class)->group(function () {
            Route::get('list', 'list')->name('api.personnels.units.list');
            Route::post('store', 'store')->name('api.personnels.units.store');
            Route::patch('update', 'update')->name('api.personnels.units.update');
        });

        Route::prefix('file')->group(function () {
            Route::controller(PersonnelFileController::class)->group(function () {
                Route::get('list', 'list')->name('api.personnels.file.list');
                Route::post('store', 'store')->name('api.personnels.file.store');
                Route::patch('update', 'update')->name('api.personnels.file.update');
            });

            Route::prefix('type')->controller(PersonnelFileTypeController::class)->group(function () {
                Route::get('list', 'list')->name('api.personnels.file.type.list');
                Route::post('store', 'store')->name('api.personnels.file.type.store');
                Route::patch('update', 'update')->name('api.personnels.file.type.update');
                Route::delete('delete', 'delete')->name('api.personnels.file.type.delete');
            });
        });
    });

    Route::prefix('work-schedule')->controller(WorkScheduleController::class)->group(function () {
        Route::get('list', 'list')->name('api.work-schedule.list');
        Route::post('store', 'store')->name('api.work-schedule.store');
        Route::post('approve', 'approve')->name('api.work-schedule.approve');
        Route::post('reject', 'reject')->name('api.work-schedule.reject');
        Route::post('return', 'return')->name('api.work-schedule.return');
        Route::post('return-approve', 'returnApprove')->name('api.work-schedule.return-approve');
        Route::post('return-reject', 'returnReject')->name('api.work-schedule.return-reject');
    });

    Route::prefix('leave-request')->controller(LeaveRequestController::class)->group(function () {
        Route::get('list', 'list')->name('api.leave-request.list');
        Route::post('get-total-leave-days', 'getTotalLeaveDays')->name('api.leave-request.get-total-leave-days');
        Route::post('store', 'store')->name('api.leave-request.store');
        Route::post('approve', 'approve')->name('api.leave-request.approve');
        Route::post('reject', 'reject')->name('api.leave-request.reject');
        Route::post('adjust', 'adjust')->name('api.leave-request.adjust');
        Route::post('adjust-approve', 'adjustApprove')->name('api.leave-request.adjust-approve');
        Route::post('adjust-reject', 'adjustReject')->name('api.leave-request.adjust-reject');
    });

    Route::prefix('work-timesheet')->group(function () {
        Route::controller(WorkTimesheetController::class)->group(function () {
            Route::get('data', 'data')->name('api.work-timesheet.data');
            Route::post('import', 'import')->name('api.work-timesheet.import');
            Route::patch('update', 'update')->name('api.work-timesheet.update');
        });
        Route::prefix('overtime')->group(function () {
            Route::controller(WorkTimesheetOvertimeController::class)->group(function () {
                Route::get('list', 'list')->name('api.work-timesheet.overtime.list');
                Route::get('template', 'template')->name('api.work-timesheet.overtime.template');
                Route::post('upload', 'upload')->name('api.work-timesheet.overtime.upload');
            });
            Route::prefix('detail')->controller(WorkTimesheetOvertimeDetailController::class)->group(function () {
                Route::get('list', 'list')->name('api.work-timesheet.overtime.detail.list');
            });
        });
    });
});
