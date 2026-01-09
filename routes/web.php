<?php

use App\Http\Controllers\Admin\AirlineController;
use App\Http\Controllers\Admin\AirportController;
use App\Http\Controllers\Admin\BiddingController;
use App\Http\Controllers\Admin\BoardMeetingMinuteController;
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
use App\Http\Controllers\Admin\DeviceController;
use App\Http\Controllers\Admin\DeviceFixController;
use App\Http\Controllers\Admin\DeviceImageController;
use App\Http\Controllers\Admin\DeviceLoanController;
use App\Http\Controllers\Admin\DeviceStatisticController;
use App\Http\Controllers\Admin\DeviceTypeController;
use App\Http\Controllers\Admin\DossierHandoverController;
use App\Http\Controllers\Admin\DossierMinuteController;
use App\Http\Controllers\Admin\DossierPlanController;
use App\Http\Controllers\Admin\DossierSyntheticController;
use App\Http\Controllers\Admin\DossierTypeController;
use App\Http\Controllers\Admin\DossierUsageRegisterController;
use App\Http\Controllers\Admin\EligibilityController;
use App\Http\Controllers\Admin\EmploymentContractPersonnelController;
use App\Http\Controllers\Admin\EmploymentContractPersonnelCustomFieldController;
use App\Http\Controllers\Admin\GoogleDriveController;
use App\Http\Controllers\Admin\IncomingOfficialDocumentController;
use App\Http\Controllers\Admin\InternalBulletinController;
use App\Http\Controllers\Admin\InternalMeetingMinuteController;
use App\Http\Controllers\Admin\LeaveRequestController;
use App\Http\Controllers\Admin\OfficialDocumentController;
use App\Http\Controllers\Admin\OfficialDocumentSectorController;
use App\Http\Controllers\Admin\OfficialDocumentTypeController;
use App\Http\Controllers\Admin\PayrollController;
use App\Http\Controllers\Admin\PersonnelController;
use App\Http\Controllers\Admin\PersonnelCustomFieldController;
use App\Http\Controllers\Admin\PersonnelFileController;
use App\Http\Controllers\Admin\PersonnelFileTypeController;
use App\Http\Controllers\Admin\PersonnelUnitController;
use App\Http\Controllers\Admin\PlaneTicketClassController;
use App\Http\Controllers\Admin\PlaneTicketController;
use App\Http\Controllers\Admin\PlaneTicketDetailController;
use App\Http\Controllers\Admin\ProfessionalRecordHandoverController;
use App\Http\Controllers\Admin\ProfessionalRecordMinuteController;
use App\Http\Controllers\Admin\ProfessionalRecordPlanController;
use App\Http\Controllers\Admin\ProfessionalRecordSyntheticController;
use App\Http\Controllers\Admin\ProfessionalRecordTypeController;
use App\Http\Controllers\Admin\ProfessionalRecordUsageRegisterController;
use App\Http\Controllers\Admin\ProofContractController;
use App\Http\Controllers\Admin\ShareholderMeetingMinuteController;
use App\Http\Controllers\Admin\SoftwareOwnershipController;
use App\Http\Controllers\Admin\TaskScheduleController;
use App\Http\Controllers\Admin\TrainAndBusTicketController;
use App\Http\Controllers\Admin\TrainAndBusTicketDetailController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UserSubEmailController;
use App\Http\Controllers\Admin\UserTimeTableController;
use App\Http\Controllers\Admin\VehicleController;
use App\Http\Controllers\Admin\VehicleLoanController;
use App\Http\Controllers\Admin\VehicleStatisticController;
use App\Http\Controllers\Admin\WorkScheduleController;
use App\Http\Controllers\Admin\WorkTimesheetController;
use App\Http\Controllers\Admin\WorkTimesheetOvertimeController;
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
            Route::get('export', 'export')->name('contract.export');
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

    Route::prefix('user')->group(function () {
        Route::controller(UserController::class)->group(function () {
            Route::get('index', 'index')->name('user.index');
            Route::get('create', 'create')->name('user.create');
            Route::get('edit', 'edit')->name('user.edit');
            Route::delete('delete', 'delete')->name('user.delete');
        });

        Route::prefix('sub-email')->controller(UserSubEmailController::class)->group(function () {
            Route::get('index', 'index')->name('user.sub-email.index');
            Route::get('create', 'create')->name('user.sub-email.create');
            Route::get('edit', 'edit')->name('user.sub-email.edit');
            Route::delete('delete', 'delete')->name('user.sub-email.delete');
        });

        Route::prefix('timetable')->controller(UserTimeTableController::class)->group(function () {
            Route::get('index', 'index')->name('user.timetable.index');
        });
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
        });
    });

    Route::prefix('work-schedule')->controller(WorkScheduleController::class)->group(function () {
        Route::get('index', 'index')->name('work-schedule.index');
        Route::get('create', 'create')->name('work-schedule.create');
    });

    Route::prefix('leave-request')->controller(LeaveRequestController::class)->group(function () {
        Route::get('index', 'index')->name('leave-request.index');
        Route::get('create', 'create')->name('leave-request.create');
    });

    Route::prefix('work-timesheet')->group(function () {
        Route::controller(WorkTimesheetController::class)->group(function () {
            Route::get('index', 'index')->name('work-timesheet.index');
        });

        Route::prefix('overtime')->controller(WorkTimesheetOvertimeController::class)->group(function () {
            Route::get('index', 'index')->name('work-timesheet.overtime.index');
        });

        Route::prefix('payroll')->controller(PayrollController::class)->group(function () {
            Route::get('index', 'index')->name('payroll.index');
        });
    });

    Route::prefix('task-schedule')->controller(TaskScheduleController::class)->group(function () {
        Route::get('index', 'index')->name('task-schedule.index');
        Route::get('edit', 'edit')->name('task-schedule.edit');
    });

    Route::prefix('employment-contract-personnel')->group(function () {
        Route::controller(EmploymentContractPersonnelController::class)->group(function () {
            Route::get('index', 'index')->name('employment-contract-personnel.index');
            Route::get('create', 'create')->name('employment-contract-personnel.create');
            Route::get('edit', 'edit')->name('employment-contract-personnel.edit');
            Route::delete('delete', 'delete')->name('employment-contract-personnel.delete');
        });

        Route::prefix('custom-field')->controller(EmploymentContractPersonnelCustomFieldController::class)->group(function () {
            Route::get('index', 'index')->name('employment-contract-personnel.custom-field.index');
            Route::get('create', 'create')->name('employment-contract-personnel.custom-field.create');
            Route::get('edit', 'edit')->name('employment-contract-personnel.custom-field.edit');
            Route::delete('delete', 'delete')->name('employment-contract-personnel.custom-field.delete');
        });
    });

    Route::prefix('internal-meeting-minute')->controller(InternalMeetingMinuteController::class)->group(function () {
        Route::get('index', 'index')->name('internal-meeting-minute.index');
        Route::get('create', 'create')->name('internal-meeting-minute.create');
        Route::get('edit', 'edit')->name('internal-meeting-minute.edit');
        Route::delete('delete', 'delete')->name('internal-meeting-minute.delete');
    });

    Route::prefix('board-meeting-minute')->controller(BoardMeetingMinuteController::class)->group(function () {
        Route::get('index', 'index')->name('board-meeting-minute.index');
        Route::get('create', 'create')->name('board-meeting-minute.create');
        Route::get('edit', 'edit')->name('board-meeting-minute.edit');
        Route::delete('delete', 'delete')->name('board-meeting-minute.delete');
    });

    Route::prefix('shareholder-meeting-minute')->controller(ShareholderMeetingMinuteController::class)->group(function () {
        Route::get('index', 'index')->name('shareholder-meeting-minute.index');
        Route::get('create', 'create')->name('shareholder-meeting-minute.create');
        Route::get('edit', 'edit')->name('shareholder-meeting-minute.edit');
        Route::delete('delete', 'delete')->name('shareholder-meeting-minute.delete');
    });

    Route::prefix('internal-bulletin')->controller(InternalBulletinController::class)->group(function () {
        Route::get('index', 'index')->name('internal-bulletin.index');
        Route::get('create', 'create')->name('internal-bulletin.create');
        Route::get('edit', 'edit')->name('internal-bulletin.edit');
        Route::delete('delete', 'delete')->name('internal-bulletin.delete');
    });

    // Các đơn vị thuộc tỉnh
    Route::prefix('unit')->controller(UnitController::class)->group(function () {
        Route::get('index', 'index')->name('unit.index');
        Route::get('create', 'create')->name('unit.create');
        Route::get('edit', 'edit')->name('unit.edit');
        Route::delete('delete', 'delete')->name('unit.delete');
    });

    // hồ sơ ngoại nghiệp
    Route::prefix('dossier')->group(function () {
        Route::prefix('type')->controller(DossierTypeController::class)->group(function () {
            Route::get('index', 'index')->name('dossier.type.index');
            Route::get('edit', 'edit')->name('dossier.type.edit');
            Route::delete('delete', 'delete')->name('dossier.type.delete');
            Route::get('export', 'export')->name('dossier.type.export');
            Route::post('import', 'import')->name('dossier.type.import');
            Route::get('create', 'create')->name('dossier.type.create');
        });

        Route::prefix('plan')->controller(DossierPlanController::class)->group(function () {
            Route::get('index', 'index')->name('dossier.plan.index');
        });

        Route::prefix('handover')->controller(DossierHandoverController::class)->group(function () {
            Route::get('index', 'index')->name('dossier.handover.index');
        });

        Route::prefix('usage_register')->controller(DossierUsageRegisterController::class)->group(function () {
            Route::get('index', 'index')->name('dossier.usage_register.index');
        });

        Route::prefix('minute')->controller(DossierMinuteController::class)->group(function () {
            Route::get('index', 'index')->name('dossier.minute.index');
        });

        Route::prefix('synthetic')->controller(DossierSyntheticController::class)->group(function () {
            Route::get('index', 'index')->name('dossier.synthetic.index');
        });
    });

    // hồ sơ chuyên môn
    Route::prefix('professional-record')->group(function () {
        Route::prefix('type')->controller(ProfessionalRecordTypeController::class)->group(function () {
            Route::get('index', 'index')->name('professional-record.type.index');
            Route::get('edit', 'edit')->name('professional-record.type.edit');
            Route::delete('delete', 'delete')->name('professional-record.type.delete');
            Route::get('export', 'export')->name('professional-record.type.export');
            Route::post('import', 'import')->name('professional-record.type.import');
            Route::get('create', 'create')->name('professional-record.type.create');
        });

        Route::prefix('plan')->controller(ProfessionalRecordPlanController::class)->group(function () {
            Route::get('index', 'index')->name('professional-record.plan.index');
        });

        Route::prefix('handover')->controller(ProfessionalRecordHandoverController::class)->group(function () {
            Route::get('index', 'index')->name('professional-record.handover.index');
        });

        Route::prefix('usage_register')->controller(ProfessionalRecordUsageRegisterController::class)->group(function () {
            Route::get('index', 'index')->name('professional-record.usage_register.index');
        });

        Route::prefix('minute')->controller(ProfessionalRecordMinuteController::class)->group(function () {
            Route::get('index', 'index')->name('professional-record.minute.index');
        });

        Route::prefix('synthetic')->controller(ProfessionalRecordSyntheticController::class)->group(function () {
            Route::get('index', 'index')->name('professional-record.synthetic.index');
        });
    });

    // vé tàu xe
    Route::prefix('train-and-bus-ticket')->group(function () {
        Route::controller(TrainAndBusTicketController::class)->group(function () {
            Route::get('index', 'index')->name('train-and-bus-ticket.index');
            Route::get('create', 'create')->name('train-and-bus-ticket.create');
            Route::post('approve', 'approve')->name('train-and-bus-ticket.approve');
            Route::post('reject', 'reject')->name('train-and-bus-ticket.reject');
        });

        Route::prefix('detail')->controller(TrainAndBusTicketDetailController::class)->group(function () {
            Route::get('index', 'index')->name('train-and-bus-ticket.detail.index');
            Route::get('edit', 'edit')->name('train-and-bus-ticket.detail.edit');
        });
    });

    // Sân bay
    Route::prefix('airport')->controller(AirportController::class)->group(function () {
        Route::get('index', 'index')->name('airport.index');
        Route::get('create', 'create')->name('airport.create');
        Route::get('edit', 'edit')->name('airport.edit');
        Route::delete('delete', 'delete')->name('airport.delete');
    });

    // Hãng bay
    Route::prefix('airline')->controller(AirlineController::class)->group(function () {
        Route::get('index', 'index')->name('airline.index');
        Route::get('create', 'create')->name('airline.create');
        Route::get('edit', 'edit')->name('airline.edit');
        Route::delete('delete', 'delete')->name('airline.delete');
    });

    // Hạng vé bay
    Route::prefix('plane-ticket-class')->controller(PlaneTicketClassController::class)->group(function () {
        Route::get('index', 'index')->name('plane-ticket-class.index');
        Route::get('create', 'create')->name('plane-ticket-class.create');
        Route::get('edit', 'edit')->name('plane-ticket-class.edit');
        Route::delete('delete', 'delete')->name('plane-ticket-class.delete');
    });

    // vé máy bay
    Route::prefix('plane-ticket')->group(function () {
        Route::controller(PlaneTicketController::class)->group(function () {
            Route::get('index', 'index')->name('plane-ticket.index');
            Route::get('create', 'create')->name('plane-ticket.create');
            Route::post('approve', 'approve')->name('plane-ticket.approve');
            Route::post('reject', 'reject')->name('plane-ticket.reject');
        });

        Route::prefix('detail')->controller(PlaneTicketDetailController::class)->group(function () {
            Route::get('index', 'index')->name('plane-ticket.detail.index');
            Route::get('edit', 'edit')->name('plane-ticket.detail.edit');
        });
    });

    // thiết bị
    Route::prefix('device')->group(function () {
        Route::prefix('type')->controller(DeviceTypeController::class)->group(function () {
            Route::get('index', 'index')->name('device.type.index');
            Route::get('create', 'create')->name('device.type.create');
            Route::get('edit', 'edit')->name('device.type.edit');
            Route::delete('delete', 'delete')->name('device.type.delete');
        });

        Route::controller(DeviceController::class)->group(function () {
            Route::get('index', 'index')->name('device.index');
            Route::get('create', 'create')->name('device.create');
            Route::get('edit', 'edit')->name('device.edit');
            Route::delete('delete', 'delete')->name('device.delete');
        });

        Route::prefix('image')->controller(DeviceImageController::class)->group(function () {
            Route::get('index', 'index')->name('device.image.index');
            Route::delete('delete', 'delete')->name('device.image.delete');
        });

        Route::prefix('loan')->controller(DeviceLoanController::class)->group(function () {
            Route::get('index', 'index')->name('device.loan.index');
            Route::get('create', 'create')->name('device.loan.create');
            Route::post('approve', 'approve')->name('device.loan.approve');
            Route::post('reject', 'reject')->name('device.loan.reject');
        });

        Route::prefix('fix')->controller(DeviceFixController::class)->group(function () {
            Route::get('index', 'index')->name('device.fix.index');
            Route::get('create', 'create')->name('device.fix.create');
            Route::post('approve', 'approve')->name('device.fix.approve');
            Route::post('reject', 'reject')->name('device.fix.reject');
        });

        Route::get('statistic/index', [DeviceStatisticController::class, 'index'])->name('device.statistic.index');
    });

    // Xe cộ
    Route::prefix('vehicle')->group(function () {
        Route::controller(VehicleController::class)->group(function () {
            Route::get('index', 'index')->name('vehicle.index');
            Route::get('create', 'create')->name('vehicle.create');
            Route::get('edit', 'edit')->name('vehicle.edit');
            Route::delete('delete', 'delete')->name('vehicle.delete');
        });

        Route::prefix('loan')->controller(VehicleLoanController::class)->group(function () {
            Route::get('index', 'index')->name('vehicle.loan.index');
            Route::get('create', 'create')->name('vehicle.loan.create');
            Route::post('approve', 'approve')->name('vehicle.loan.approve');
            Route::post('reject', 'reject')->name('vehicle.loan.reject');
        });

        Route::get('statistic/index', [VehicleStatisticController::class, 'index'])->name('vehicle.statistic.index');
    });

    // công văn quyết định
    Route::prefix('official-document')->group(function () {
        Route::prefix('type')->controller(OfficialDocumentTypeController::class)->group(function () {
            Route::get('index', 'index')->name('official-document.type.index');
            Route::get('create', 'create')->name('official-document.type.create');
            Route::get('edit', 'edit')->name('official-document.type.edit');
            Route::delete('delete', 'delete')->name('official-document.type.delete');
        });

        Route::prefix('sector')->controller(OfficialDocumentSectorController::class)->group(function () {
            Route::get('index', 'index')->name('official-document.sector.index');
            Route::get('create', 'create')->name('official-document.sector.create');
            Route::get('edit', 'edit')->name('official-document.sector.edit');
            Route::delete('delete', 'delete')->name('official-document.sector.delete');
        });

        Route::prefix('incoming')->controller(IncomingOfficialDocumentController::class)->group(function () {
            Route::get('index', 'index')->name('official-document.incoming.index');
            Route::get('create', 'create')->name('official-document.incoming.create');
            Route::get('edit', 'edit')->name('official-document.incoming.edit');
            Route::delete('delete', 'delete')->name('official-document.incoming.delete');
            Route::post('assign', 'assign')->name('official-document.incoming.assign');
            Route::post('complete', 'complete')->name('official-document.incoming.complete');
        });

        Route::controller(OfficialDocumentController::class)->group(function () {
            Route::get('index', 'index')->name('official-document.index');
            Route::get('create', 'create')->name('official-document.create');
            Route::get('edit', 'edit')->name('official-document.edit');
            Route::delete('delete', 'delete')->name('official-document.delete');
            Route::post('review-approve', 'reviewApprove')->name('official-document.review-approve');
            Route::post('review-reject', 'reviewReject')->name('official-document.review-reject');
            Route::post('approve', 'approve')->name('official-document.approve');
            Route::post('reject', 'reject')->name('official-document.reject');
            Route::post('release', 'release')->name('official-document.release');
        });
    });
});
