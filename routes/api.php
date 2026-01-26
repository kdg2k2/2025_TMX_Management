<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UnitController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\AirlineController;
use App\Http\Controllers\Api\AirportController;
use App\Http\Controllers\Api\BiddingController;
use App\Http\Controllers\Api\PayrollController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\VehicleController;
use App\Http\Controllers\Api\ContractController;
use App\Http\Controllers\Api\DeviceFixController;
use App\Http\Controllers\Api\PersonnelController;
use App\Http\Controllers\Api\DeviceLoanController;
use App\Http\Controllers\Api\DeviceTypeController;
use App\Http\Controllers\Api\DeviceImageController;
use App\Http\Controllers\Api\DossierPlanController;
use App\Http\Controllers\Api\DossierTypeController;
use App\Http\Controllers\Api\EligibilityController;
use App\Http\Controllers\Api\PlaneTicketController;
use App\Http\Controllers\Api\UserWarningController;
use App\Http\Controllers\Api\VehicleLoanController;
use App\Http\Controllers\Api\ContractBillController;
use App\Http\Controllers\Api\ContractFileController;
use App\Http\Controllers\Api\ContractTypeController;
use App\Http\Controllers\Api\ContractUnitController;
use App\Http\Controllers\Api\LeaveRequestController;
use App\Http\Controllers\Api\TaskScheduleController;
use App\Http\Controllers\Api\UserSubEmailController;
use App\Http\Controllers\Api\WorkScheduleController;
use App\Http\Controllers\Api\BuildSoftwareController;
use App\Http\Controllers\Api\DossierMinuteController;
use App\Http\Controllers\Api\KasperskyCodeController;
use App\Http\Controllers\Api\PersonnelFileController;
use App\Http\Controllers\Api\PersonnelUnitController;
use App\Http\Controllers\Api\ProofContractController;
use App\Http\Controllers\Api\UserTimeTableController;
use App\Http\Controllers\Api\WorkTimesheetController;
use App\Http\Controllers\Api\ContractFinanceController;
use App\Http\Controllers\Api\ContractPaymentController;
use App\Http\Controllers\Api\ContractProductController;
use App\Http\Controllers\Api\DeviceStatisticController;
use App\Http\Controllers\Api\DossierHandoverController;
use App\Http\Controllers\Api\ProfileSubEmailController;
use App\Http\Controllers\Api\ContractAppendixController;
use App\Http\Controllers\Api\ContractFileTypeController;
use App\Http\Controllers\Api\ContractInvestorController;
use App\Http\Controllers\Api\ContractManyYearController;
use App\Http\Controllers\Api\ContractScanFileController;
use App\Http\Controllers\Api\DossierSyntheticController;
use App\Http\Controllers\Api\InternalBulletinController;
use App\Http\Controllers\Api\OfficialDocumentController;
use App\Http\Controllers\Api\PlaneTicketClassController;
use App\Http\Controllers\Api\VehicleStatisticController;
use App\Http\Controllers\Api\BiddingOrtherFileController;
use App\Http\Controllers\Api\ContractPersonnelController;
use App\Http\Controllers\Api\PersonnelFileTypeController;
use App\Http\Controllers\Api\PlaneTicketDetailController;
use App\Http\Controllers\Api\SoftwareOwnershipController;
use App\Http\Controllers\Api\TrainAndBusTicketController;
use App\Http\Controllers\Api\BiddingEligibilityController;
use App\Http\Controllers\Api\BoardMeetingMinuteController;
use App\Http\Controllers\Api\ContractMainProductController;
use App\Http\Controllers\Api\BiddingProofContractController;
use App\Http\Controllers\Api\ContractDisbursementController;
use App\Http\Controllers\Api\ContractProfessionalController;
use App\Http\Controllers\Api\ContractScanFileTypeController;
use App\Http\Controllers\Api\DossierUsageRegisterController;
use App\Http\Controllers\Api\OfficialDocumentTypeController;
use App\Http\Controllers\Api\PersonnelCustomFieldController;
use App\Http\Controllers\Api\ContractProductMinuteController;
use App\Http\Controllers\Api\InternalMeetingMinuteController;
use App\Http\Controllers\Api\WorkTimesheetOvertimeController;
use App\Http\Controllers\Api\ContractAdvancePaymentController;
use App\Http\Controllers\Api\KasperskyCodeStatisticController;
use App\Http\Controllers\Api\OfficialDocumentSectorController;
use App\Http\Controllers\Api\ProfessionalRecordPlanController;
use App\Http\Controllers\Api\ProfessionalRecordTypeController;
use App\Http\Controllers\Api\TrainAndBusTicketDetailController;
use App\Http\Controllers\Api\BiddingSoftwareOwnershipController;
use App\Http\Controllers\Api\IncomingOfficialDocumentController;
use App\Http\Controllers\Api\ProfessionalRecordMinuteController;
use App\Http\Controllers\Api\ShareholderMeetingMinuteController;
use App\Http\Controllers\Api\ContractProductInspectionController;
use App\Http\Controllers\Api\KasperskyCodeRegistrationController;
use App\Http\Controllers\Api\ProfessionalRecordHandoverController;
use App\Http\Controllers\Api\BiddingContractorExperienceController;
use App\Http\Controllers\Api\ContractIntermediateProductController;
use App\Http\Controllers\Api\EmploymentContractPersonnelController;
use App\Http\Controllers\Api\ProfessionalRecordSyntheticController;
use App\Http\Controllers\Api\WorkTimesheetOvertimeDetailController;
use App\Http\Controllers\Api\BiddingImplementationPersonnelController;
use App\Http\Controllers\Api\ContractProductMinuteSignatureController;
use App\Http\Controllers\Api\ProfessionalRecordUsageRegisterController;
use App\Http\Controllers\Api\EmploymentContractPersonnelCustomFieldController;

// Auth routes
Route::prefix('auth')->controller(AuthController::class)->group(function () {
    Route::post('login', 'login')->name('api.auth.login');  // Đăng nhập
    Route::post('refresh', 'refresh')->name('api.auth.refresh')->middleware('throttle:5,1');  // Làm mới token
    Route::post('logout', 'logout')->name('api.auth.logout');  // Đăng xuất
    Route::post('force-logout-user/{user_id}', 'api.auth.forceLogoutUser')->middleware('auth.any');  // Ngắt token của user
});

Route::middleware(['web', 'auth.any', 'LogAccess'])->group(function () {
    // hợp đồng
    Route::prefix('contract')->group(function () {
        // loại hợp đồng
        Route::prefix('type')->controller(ContractTypeController::class)->group(function () {
            Route::get('list', 'list')->name('api.contract.type.list');
            Route::post('store', 'store')->name('api.contract.type.store');
            Route::patch('update', 'update')->name('api.contract.type.update');
        });

        // nhà đầu tư
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

        // tệp
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

        // tệp scan
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

        // hóa đơn
        Route::prefix('bill')->controller(ContractBillController::class)->group(function () {
            Route::get('list', 'list')->name('api.contract.bill.list');
            Route::post('store', 'store')->name('api.contract.bill.store');
            Route::patch('update', 'update')->name('api.contract.bill.update');
        });

        // phụ lục
        Route::prefix('appendix')->controller(ContractAppendixController::class)->group(function () {
            Route::get('list', 'list')->name('api.contract.appendix.list');
            Route::post('store', 'store')->name('api.contract.appendix.store');
            Route::patch('update', 'update')->name('api.contract.appendix.update');
        });

        // đơn vị liên danh
        Route::prefix('unit')->controller(ContractUnitController::class)->group(function () {
            Route::get('list', 'list')->name('api.contract.unit.list');
            Route::post('store', 'store')->name('api.contract.unit.store');
            Route::patch('update', 'update')->name('api.contract.unit.update');
            Route::delete('delete', 'delete')->name('api.contract.unit.delete');
        });

        // tài chính
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

        // nhiều năm
        Route::prefix('many-year')->controller(ContractManyYearController::class)->group(function () {
            Route::get('list', 'list')->name('api.contract.many-year.list');
        });

        // phụ trách chuyên môn
        Route::prefix('professional')->controller(ContractProfessionalController::class)->group(function () {
            Route::get('list', 'list')->name('api.contract.professional.list');
        });

        // phụ trách giải ngân
        Route::prefix('disbursement')->controller(ContractDisbursementController::class)->group(function () {
            Route::get('list', 'list')->name('api.contract.disbursement.list');
        });

        // sản phẩm
        Route::prefix('product')->group(function () {
            Route::controller(ContractProductController::class)->group(function () {
                Route::get('list', 'list')->name('api.contract.product.list');
            });

            // chính
            Route::prefix('main')->controller(ContractMainProductController::class)->group(function () {
                Route::get('list', 'list')->name('api.contract.product.main.list');
                Route::get('export', 'export')->name('api.contract.product.main.export');
                Route::put('import', 'import')->name('api.contract.product.main.import');
            });

            // trung gian
            Route::prefix('intermediate')->controller(ContractIntermediateProductController::class)->group(function () {
                Route::get('list', 'list')->name('api.contract.product.intermediate.list');
                Route::get('export', 'export')->name('api.contract.product.intermediate.export');
                Route::put('import', 'import')->name('api.contract.product.intermediate.import');
            });

            // biên bản
            Route::prefix('minute')->group(function () {
                Route::controller(ContractProductMinuteController::class)->group(function () {
                    Route::get('list', 'list')->name('api.contract.product.minute.list');
                    Route::get('show', 'show')->name('api.contract.product.minute.show');
                    Route::post('create', 'create')->name('api.contract.product.minute.create');
                    Route::patch('replace', 'replace')->name('api.contract.product.minute.replace');
                    Route::patch('signature-request', 'signatureRequest')->name('api.contract.product.minute.signature-request');
                    Route::patch('confirm-issues', 'confirmIssues')->name('api.contract.product.minute.confirm-issues');
                });
                Route::prefix('sign')->controller(ContractProductMinuteSignatureController::class)->group(function () {
                    Route::patch('/', 'sign')->name('contract.product.minute.sign');
                });
            });

            // kiểm tra
            Route::prefix('inspection')->controller(ContractProductInspectionController::class)->group(function () {
                Route::get('list', 'list')->name('api.contract.product.inspection.list');
                Route::post('request', 'request')->name('api.contract.product.inspection.request');
                Route::patch('cancel', 'cancel')->name('api.contract.product.inspection.cancel');
                Route::patch('response', 'response')->name('api.contract.product.inspection.response');
            });
        });

        // nhân sự thực hiện
        Route::prefix('personnel')->controller(ContractPersonnelController::class)->group(function () {
            Route::get('list', 'list')->name('api.contract.personnel.list');
            Route::get('synthetic', 'synthetic')->name('api.contract.personnel.synthetic');
        });
    });

    // Thông tin cá nhân
    Route::prefix('profile')->group(function () {
        Route::controller(ProfileController::class)->group(function () {
            Route::patch('update', 'update')->name('api.profile.update');
        });

        Route::prefix('sub-email')->controller(ProfileSubEmailController::class)->group(function () {
            Route::get('list', 'list')->name('api.profile.sub-email.list');
            Route::post('store', 'store')->name('api.profile.sub-email.store');
            Route::patch('update', 'update')->name('api.profile.sub-email.update');
            Route::delete('delete', 'delete')->name('api.profile.sub-email.delete');
        });
    });

    // tài khoản
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

    // đề xuất xây dựng phần mề
    Route::prefix('build-software')->controller(BuildSoftwareController::class)->group(function () {
        Route::get('list', 'list')->name('api.build-software.list');
        Route::post('store', 'store')->name('api.build-software.store');
        Route::patch('update', 'update')->name('api.build-software.update');
    });

    // đấu thầu
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

    // tư cách hợp lệ
    Route::prefix('eligibilities')->controller(EligibilityController::class)->group(function () {
        Route::get('list', 'list')->name('api.eligibilities.list');
        Route::post('store', 'store')->name('api.eligibilities.store');
        Route::patch('update', 'update')->name('api.eligibilities.update');
    });

    // hợp đồng minh chứng
    Route::prefix('proof_contracts')->controller(ProofContractController::class)->group(function () {
        Route::get('list', 'list')->name('api.proof_contracts.list');
        Route::post('store', 'store')->name('api.proof_contracts.store');
        Route::patch('update', 'update')->name('api.proof_contracts.update');
    });

    // sở hữu phần mềm
    Route::prefix('software_ownerships')->controller(SoftwareOwnershipController::class)->group(function () {
        Route::get('list', 'list')->name('api.software_ownerships.list');
        Route::post('store', 'store')->name('api.software_ownerships.store');
        Route::patch('update', 'update')->name('api.software_ownerships.update');
    });

    // nhân sự
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

    // công tác
    Route::prefix('work-schedule')->controller(WorkScheduleController::class)->group(function () {
        Route::get('list', 'list')->name('api.work-schedule.list');
        Route::post('store', 'store')->name('api.work-schedule.store');
        Route::post('approve', 'approve')->name('api.work-schedule.approve');
        Route::post('reject', 'reject')->name('api.work-schedule.reject');
        Route::post('return', 'return')->name('api.work-schedule.return');
        Route::post('return-approve', 'returnApprove')->name('api.work-schedule.return-approve');
        Route::post('return-reject', 'returnReject')->name('api.work-schedule.return-reject');
    });

    // nghỉ phép
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

    // xuất lưới
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
        Route::prefix('payroll')->controller(PayrollController::class)->group(function () {
            Route::get('data', 'data')->name('api.payroll.data');
            Route::patch('update', 'update')->name('api.payroll.update');
        });
    });

    // task tự động
    Route::prefix('task-schedule')->controller(TaskScheduleController::class)->group(function () {
        Route::get('list', 'list')->name('api.task-schedule.list');
        Route::patch('update', 'update')->name('api.task-schedule.update');
        Route::post('run', 'run')->name('api.task-schedule.run');
    });

    // hồ sơ lao động/ bổ nhiệm
    Route::prefix('employment-contract-personnel')->group(function () {
        Route::controller(EmploymentContractPersonnelController::class)->group(function () {
            Route::get('list', 'list')->name('api.employment-contract-personnel.list');
            Route::get('syncthetic-excel', 'synctheticExcel')->name('api.employment-contract-personnel.syncthetic-excel');
            Route::post('store', 'store')->name('api.employment-contract-personnel.store');
            Route::patch('update', 'update')->name('api.employment-contract-personnel.update');
        });

        Route::prefix('custom-field')->controller(EmploymentContractPersonnelCustomFieldController::class)->group(function () {
            Route::get('list', 'list')->name('api.employment-contract-personnel.custom-field.list');
            Route::post('store', 'store')->name('api.employment-contract-personnel.custom-field.store');
            Route::patch('update', 'update')->name('api.employment-contract-personnel.custom-field.update');
        });
    });

    // giao ban
    Route::prefix('internal-meeting-minute')->controller(InternalMeetingMinuteController::class)->group(function () {
        Route::get('list', 'list')->name('api.internal-meeting-minute.list');
        Route::post('store', 'store')->name('api.internal-meeting-minute.store');
        Route::patch('update', 'update')->name('api.internal-meeting-minute.update');
    });

    // hội đồng quản trị
    Route::prefix('board-meeting-minute')->controller(BoardMeetingMinuteController::class)->group(function () {
        Route::get('list', 'list')->name('api.board-meeting-minute.list');
        Route::post('store', 'store')->name('api.board-meeting-minute.store');
        Route::patch('update', 'update')->name('api.board-meeting-minute.update');
    });

    // cổ đông
    Route::prefix('shareholder-meeting-minute')->controller(ShareholderMeetingMinuteController::class)->group(function () {
        Route::get('list', 'list')->name('api.shareholder-meeting-minute.list');
        Route::post('store', 'store')->name('api.shareholder-meeting-minute.store');
        Route::patch('update', 'update')->name('api.shareholder-meeting-minute.update');
    });

    // bảng tin
    Route::prefix('internal-bulletin')->controller(InternalBulletinController::class)->group(function () {
        Route::get('list', 'list')->name('api.internal-bulletin.list');
        Route::post('store', 'store')->name('api.internal-bulletin.store');
        Route::patch('update', 'update')->name('api.internal-bulletin.update');
    });

    // Các đơn vị thuộc tỉnh
    Route::prefix('unit')->controller(UnitController::class)->group(function () {
        Route::get('list', 'list')->name('api.unit.list');
        Route::post('store', 'store')->name('api.unit.store');
        Route::patch('update', 'update')->name('api.unit.update');
    });

    // hồ sơ ngoại nghiệp
    Route::prefix('dossier')->group(function () {
        Route::prefix('type')->controller(DossierTypeController::class)->group(function () {
            Route::get('list', 'list')->name('api.dossier.type.list');
            Route::post('store', 'store')->name('api.dossier.type.store');
            Route::patch('update', 'update')->name('api.dossier.type.update');
        });

        Route::prefix('plan')->controller(DossierPlanController::class)->group(function () {
            Route::get('find-by-id-contract-and-year', 'findByIdContractAndYear')->name('api.dossier.plan.findByIdContractAndYear');
            Route::get('create-temp-excel', 'createTempExcel')->name('api.dossier.plan.createTempExcel');
            Route::post('upload-excel', 'uploadExcel')->name('api.dossier.plan.uploadExcel');
            Route::post('create-minute', 'createMinute')->name('api.dossier.plan.createMinute');
            Route::post('send-approve-request', 'sendApproveRequest')->name('api.dossier.plan.sendApproveRequest');
        });

        Route::prefix('handover')->controller(DossierHandoverController::class)->group(function () {
            Route::get('find-by-id-contract-and-year', 'findByIdContractAndYear')->name('api.dossier.handover.findByIdContractAndYear');
            Route::get('create-temp-excel', 'createTempExcel')->name('api.dossier.handover.createTempExcel');
            Route::post('upload-excel', 'uploadExcel')->name('api.dossier.handover.uploadExcel');
            Route::get('create-minute', 'createMinute')->name('api.dossier.handover.createMinute');
            Route::post('send-approve-request', 'sendApproveRequest')->name('api.dossier.handover.sendApproveRequest');
        });

        Route::prefix('usage_register')->controller(DossierUsageRegisterController::class)->group(function () {
            Route::get('find-by-id-contract-and-year', 'findByIdContractAndYear')->name('api.dossier.usage_register.findByIdContractAndYear');
            Route::get('create-temp-excel', 'createTempExcel')->name('api.dossier.usage_register.createTempExcel');
            Route::post('upload-excel', 'uploadExcel')->name('api.dossier.usage_register.uploadExcel');
            Route::post('send-approve-request', 'sendApproveRequest')->name('api.dossier.usage_register.sendApproveRequest');
        });

        Route::prefix('minute')->controller(DossierMinuteController::class)->group(function () {
            Route::get('list', 'list')->name('api.dossier.minute.list');
            Route::post('accept', 'accept')->name('api.dossier.minute.accept');
            Route::post('deny', 'deny')->name('api.dossier.minute.deny');
        });

        Route::prefix('synthetic')->controller(DossierSyntheticController::class)->group(function () {
            Route::get('create-synthetic-file', 'createSyntheticFile')->name('api.dossier.synthetic.create-synthetic-file');
        });
    });

    // hồ sơ chuyên môn
    Route::prefix('professional-record')->group(function () {
        Route::prefix('type')->controller(ProfessionalRecordTypeController::class)->group(function () {
            Route::get('list', 'list')->name('api.professional-record.type.list');
            Route::post('store', 'store')->name('api.professional-record.type.store');
            Route::patch('update', 'update')->name('api.professional-record.type.update');
        });

        Route::prefix('plan')->controller(ProfessionalRecordPlanController::class)->group(function () {
            Route::get('find-by-id-contract-and-year', 'findByIdContractAndYear')->name('api.professional-record.plan.findByIdContractAndYear');
            Route::get('create-temp-excel', 'createTempExcel')->name('api.professional-record.plan.createTempExcel');
            Route::post('upload-excel', 'uploadExcel')->name('api.professional-record.plan.uploadExcel');
            Route::post('send-approve-request', 'sendApproveRequest')->name('api.professional-record.plan.sendApproveRequest');
        });

        Route::prefix('handover')->controller(ProfessionalRecordHandoverController::class)->group(function () {
            Route::get('find-by-id-contract-and-year', 'findByIdContractAndYear')->name('api.professional-record.handover.findByIdContractAndYear');
            Route::get('create-temp-excel', 'createTempExcel')->name('api.professional-record.handover.createTempExcel');
            Route::post('upload-excel', 'uploadExcel')->name('api.professional-record.handover.uploadExcel');
            Route::get('create-minute', 'createMinute')->name('api.professional-record.handover.createMinute');
            Route::post('send-approve-request', 'sendApproveRequest')->name('api.professional-record.handover.sendApproveRequest');
        });

        Route::prefix('usage_register')->controller(ProfessionalRecordUsageRegisterController::class)->group(function () {
            Route::get('find-by-id-contract-and-year', 'findByIdContractAndYear')->name('api.professional-record.usage_register.findByIdContractAndYear');
            Route::get('create-temp-excel', 'createTempExcel')->name('api.professional-record.usage_register.createTempExcel');
            Route::post('upload-excel', 'uploadExcel')->name('api.professional-record.usage_register.uploadExcel');
            Route::post('send-approve-request', 'sendApproveRequest')->name('api.professional-record.usage_register.sendApproveRequest');
        });

        Route::prefix('minute')->controller(ProfessionalRecordMinuteController::class)->group(function () {
            Route::get('list', 'list')->name('api.professional-record.minute.list');
            Route::post('accept', 'accept')->name('api.professional-record.minute.accept');
            Route::post('deny', 'deny')->name('api.professional-record.minute.deny');
        });

        Route::prefix('synthetic')->controller(ProfessionalRecordSyntheticController::class)->group(function () {
            Route::get('create-synthetic-file', 'createSyntheticFile')->name('api.professional-record.synthetic.create-synthetic-file');
        });
    });

    // vé tàu xe
    Route::prefix('train-and-bus-ticket')->group(function () {
        Route::controller(TrainAndBusTicketController::class)->group(function () {
            Route::get('list', 'list')->name('api.train-and-bus-ticket.list');
            Route::post('store', 'store')->name('api.train-and-bus-ticket.store');
            Route::patch('update', 'update')->name('api.train-and-bus-ticket.update');
        });

        Route::prefix('detail')->controller(TrainAndBusTicketDetailController::class)->group(function () {
            Route::get('list', 'list')->name('api.train-and-bus-ticket.detail.list');
            Route::patch('update', 'update')->name('api.train-and-bus-ticket.detail.update');
        });
    });

    // Sân bay
    Route::prefix('airport')->controller(AirportController::class)->group(function () {
        Route::get('list', 'list')->name('api.airport.list');
        Route::post('store', 'store')->name('api.airport.store');
        Route::patch('update', 'update')->name('api.airport.update');
    });

    // Hãng bay
    Route::prefix('airline')->controller(AirlineController::class)->group(function () {
        Route::get('list', 'list')->name('api.airline.list');
        Route::post('store', 'store')->name('api.airline.store');
        Route::patch('update', 'update')->name('api.airline.update');
    });

    // Hạng vé bay
    Route::prefix('plane-ticket-class')->controller(PlaneTicketClassController::class)->group(function () {
        Route::get('list', 'list')->name('api.plane-ticket-class.list');
        Route::post('store', 'store')->name('api.plane-ticket-class.store');
        Route::patch('update', 'update')->name('api.plane-ticket-class.update');
    });

    // vé máy bay
    Route::prefix('plane-ticket')->group(function () {
        Route::controller(PlaneTicketController::class)->group(function () {
            Route::get('list', 'list')->name('api.plane-ticket.list');
            Route::post('store', 'store')->name('api.plane-ticket.store');
            Route::patch('update', 'update')->name('api.plane-ticket.update');
        });

        Route::prefix('detail')->controller(PlaneTicketDetailController::class)->group(function () {
            Route::get('list', 'list')->name('api.plane-ticket.detail.list');
            Route::patch('update', 'update')->name('api.plane-ticket.detail.update');
        });
    });

    // thiết bị
    Route::prefix('device')->group(function () {
        Route::prefix('type')->controller(DeviceTypeController::class)->group(function () {
            Route::get('list', 'list')->name('api.device.type.list');
            Route::post('store', 'store')->name('api.device.type.store');
            Route::patch('update', 'update')->name('api.device.type.update');
        });

        Route::controller(DeviceController::class)->group(function () {
            Route::get('list', 'list')->name('api.device.list');
            Route::post('store', 'store')->name('api.device.store');
            Route::patch('update', 'update')->name('api.device.update');
        });

        Route::prefix('image')->controller(DeviceImageController::class)->group(function () {
            Route::get('list', 'list')->name('api.device.image.list');
            Route::post('store', 'store')->name('api.device.image.store');
            Route::patch('update', 'update')->name('api.device.image.update');
        });

        Route::prefix('loan')->controller(DeviceLoanController::class)->group(function () {
            Route::get('list', 'list')->name('api.device.loan.list');
            Route::post('store', 'store')->name('api.device.loan.store');
            Route::post('return', 'return')->name('api.device.loan.return');
        });

        Route::prefix('fix')->controller(DeviceFixController::class)->group(function () {
            Route::get('list', 'list')->name('api.device.fix.list');
            Route::post('store', 'store')->name('api.device.fix.store');
            Route::post('fixed', 'fixed')->name('api.device.fix.fixed');
        });

        Route::get('statistic/data', [DeviceStatisticController::class, 'data'])->name('api.device.statistic.data');
    });

    // xe cộ
    Route::prefix('vehicle')->group(function () {
        Route::controller(VehicleController::class)->group(function () {
            Route::get('list', 'list')->name('api.vehicle.list');
            Route::post('store', 'store')->name('api.vehicle.store');
            Route::patch('update', 'update')->name('api.vehicle.update');
        });

        Route::prefix('loan')->controller(VehicleLoanController::class)->group(function () {
            Route::get('list', 'list')->name('api.vehicle.loan.list');
            Route::post('store', 'store')->name('api.vehicle.loan.store');
            Route::post('return', 'return')->name('api.vehicle.loan.return');
        });

        Route::controller(VehicleStatisticController::class)->group(function () {
            Route::get('statistic/data', 'data')->name('api.vehicle.statistic.data');
            Route::get('vehicle/statistic/detail', 'detail')->name('api.vehicle.statistic.detail');
        });
    });

    // công văn quyết định
    Route::prefix('official-document')->group(function () {
        Route::prefix('type')->controller(OfficialDocumentTypeController::class)->group(function () {
            Route::get('list', 'list')->name('api.official-document.type.list');
            Route::post('store', 'store')->name('api.official-document.type.store');
            Route::patch('update', 'update')->name('api.official-document.type.update');
        });
        Route::prefix('sector')->controller(OfficialDocumentSectorController::class)->group(function () {
            Route::get('list', 'list')->name('api.official-document.sector.list');
            Route::post('store', 'store')->name('api.official-document.sector.store');
            Route::patch('update', 'update')->name('api.official-document.sector.update');
        });

        Route::prefix('incoming')->controller(IncomingOfficialDocumentController::class)->group(function () {
            Route::get('list', 'list')->name('api.official-document.incoming.list');
            Route::post('store', 'store')->name('api.official-document.incoming.store');
            Route::patch('update', 'update')->name('api.official-document.incoming.update');
        });

        Route::controller(OfficialDocumentController::class)->group(function () {
            Route::get('list', 'list')->name('api.official-document.list');
            Route::post('store', 'store')->name('api.official-document.store');
            Route::patch('update', 'update')->name('api.official-document.update');
        });
    });

    // mã kaspersky
    Route::prefix('kaspersky')->group(function () {
        Route::prefix('code')->controller(KasperskyCodeController::class)->group(function () {
            Route::get('list', 'list')->name('api.kaspersky.code.list');
            Route::post('store', 'store')->name('api.kaspersky.code.store');
            Route::patch('update', 'update')->name('api.kaspersky.code.update');
        });

        Route::prefix('registration')->controller(KasperskyCodeRegistrationController::class)->group(function () {
            Route::get('list', 'list')->name('api.kaspersky.registration.list');
            Route::post('store', 'store')->name('api.kaspersky.registration.store');
        });

        Route::get('statistic/data', [KasperskyCodeStatisticController::class, 'data'])->name('api.kaspersky.statistic.data');
    });
});
