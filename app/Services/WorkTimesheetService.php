<?php

namespace App\Services;

use App\Models\WorkTimesheet;
use App\Models\WorkTimesheetDetail;
use App\Repositories\WorkTimesheetRepository;
use Arr;
use Exception;

class WorkTimesheetService extends BaseService
{
    public function __construct(
        private DateService $dateService,
        private HandlerUploadFileService $handlerUploadFileService,
        private ExcelService $excelService,
        private UserService $userService,
        private WorkScheduleService $workScheduleService,
        private LeaveRequestService $leaveRequestService,
        private WorkTimesheetDetailService $workTimesheetDetailService
    ) {
        $this->repository = app(WorkTimesheetRepository::class);
    }

    public function baseIndexData()
    {
        $nowMonthYear = date('Y-n', strtotime('first day of last month'));
        $currentYear = (int) date('Y', strtotime($nowMonthYear));
        $currentMonth = (int) date('n', strtotime($nowMonthYear));

        $days = $this->dateService->getDaysInMonth($currentMonth, $currentYear, [0])->toArray();

        return [
            'years' => $this->dateService->getYearOptions(2024),
            'months' => $this->dateService->getMonthOptions(),
            'days' => array_combine($days, array_map(fn($d) => $this->formatDateForPreview($d), $days)),
            'currentYear' => $currentYear,
            'currentMonth' => $currentMonth,
        ];
    }

    public function data(array $request = [])
    {
        return $this->tryThrow(function () use ($request) {
            $data = optional($this->repository->findByMonthYear(
                $request['month'],
                $request['year']
            ))->toArray();

            return $data ? $this->formatRecord($data) : null;
        });
    }

    public function formatRecord(array $array)
    {
        $array = parent::formatRecord($array);
        if (isset($array['original_path']))
            $array['original_path'] = $this->getAssetUrl($array['original_path']);
        if (isset($array['calculated_path']))
            $array['calculated_path'] = $this->getAssetUrl($array['calculated_path']);
        return $array;
    }

    public function import(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            // tìm và xóa xuất lưới cũ cùng tháng
            $old = $this->repository->findByMonthYear(
                $request['month'],
                $request['year']
            );
            $oldOriginalPath = $old['original_path'] ?? null;
            $oldCalculatedPath = $old['calculated_path'] ?? null;
            if ($old)
                $old->delete();

            // tạo record xuất lưới mới
            $record = $this->repository->store([
                'month' => $request['month'],
                'year' => $request['year'],
                'total_holiday_days' => count($request['holiday_days'] ?? []),
                'total_power_outage_days' => count($request['power_outage_days'] ?? []),
                'total_compensated_days' => count($request['compensated_days'] ?? []),
                'days_details' => json_encode([
                    'holiday_days' => $request['holiday_days'] ?? [],
                    'power_outage_days' => $request['power_outage_days'] ?? [],
                    'compensated_days' => $request['compensated_days'] ?? [],
                ]),
                'original_path' => $this->handlerUploadFileService->storeAndRemoveOld($request['file'], "work-timesheets/{$request['year']}", $request['month']),
            ]);

            // đọc vã tính toán số liệu excel
            $this->readExcel($record);

            // load các số liệu dã được tính toán
            $record->load($this->repository->relations);

            // tạo file excel hiển thị kết quả tính
            $this->createCaculatedExcel($record);

            $this->handlerUploadFileService->removeFiles([$oldOriginalPath, $oldCalculatedPath]);
        }, true);
    }

    // tìm thông tin tài khoản theo tên
    private function getUserInfoByName(string $name, int $month, int $year)
    {
        // ngày đầu và ngày cuối của tháng
        $firstDay = $this->dateService->getFirstDayOfMonth($month, $year);
        $lastDay = $this->dateService->getLastDayOfMonth($month, $year);

        return optional($this->userService->findByKey($name, 'name', true, true, [
            'workSchedules' => fn($q) => $this->workScheduleService->getBaseQueryForDateRange($firstDay, $lastDay, ['approved'], $q),
            'leaveRequest' => fn($q) => $this->leaveRequestService->getBaseQueryForDateRange($firstDay, $lastDay, ['approved'], $q),
            'warning' => fn($q) => $q->where('warning_date', 'like', '%' . sprintf('%s-%02d', $year, $month) . '%'),
        ]))->toArray();
    }

    // tính công bộ phận đề xuất: các ngày ko tính chủ nhật - nghỉ lễ - mất điện + làm bù + công tác
    private function calculateProposedWorkDays(int $month, int $year, int $totalHolidayDays = 0, int $totalPowerOutageDays = 0, int $totalCompensatedDays = 0, int $totalBusinessTripDays = 0)
    {
        return count($this->dateService->getDaysInMonth($month, $year, [0])) - $totalHolidayDays - $totalPowerOutageDays + $totalCompensatedDays + $totalBusinessTripDays;
    }

    // các ngày đi công tác trong tháng ko tính chủ nhật
    private function businessTripDays(array $workSchedules, int $month, int $year)
    {
        return array_unique(Arr::flatten(collect($workSchedules)->map(fn($i) => $this->dateService->getDatesInRange($i['from_date'], $i['to_date'], [0], 'Y-m-d', $month, $year))->toArray()));
    }

    // gom các ngày chấm công vs ngày làm bù
    private function getRealTimeSheet(array $rawCompensatedDays, array $timeSheets)
    {
        // lấy ra mảng chấm công các ngày làm bù
        $compensatedDays = collect($timeSheets)->filter(fn($i) => in_array($i[3], $rawCompensatedDays))->toArray();

        // gom lại và lọc unique các ngày cần tính đi muộn về sớm
        return array_map('unserialize', array_unique(array_map('serialize', array_values(array_merge($timeSheets, $compensatedDays)))));
    }

    // tính trung bình muộn
    private function calculateAvgLateMinute(array $lateEarly, int $validAttendanceCount, int $businessTripDayCount, bool $isChildcareMode)
    {
        $avgLateMinutes = 0;
        $isLatestArrival = false;
        $note = null;

        // tính trung bình muộn: round(tổng 4 tiêu chí chấm muộn / (công hợp lệ + công tác), 2)
        $avgLateMinutes = round(collect($lateEarly)->except('validAttendanceCount')->sum() / ($validAttendanceCount + $businessTripDayCount), 2);

        // nếu có con nhỏ cho đi muộn về sớm 30p
        if ($isChildcareMode == 1)
            $avgLateMinutes -= 60;

        // nếu quá trung bình muộn quá 15p đánh giá top muộn
        if ($avgLateMinutes > 15) {
            $isLatestArrival = true;
            $note = 'Muộn quá 15 phút';
        }

        return [
            'avgLateMinutes' => $avgLateMinutes,
            'isLatestArrival' => $isLatestArrival,
            'note' => $note,
        ];
    }

    private function checkLateEarlyMultipleDays(array $times, bool $lambu)
    {
        $final = [
            'validAttendanceCount' => 0,
            'lateMorningCount' => 0,
            'earlyMorningCount' => 0,
            'lateAfternoonCount' => 0,
            'earlyAfternoonCount' => 0,
        ];

        for ($i = 0; $i < count($times); $i++) {
            if ($this->dateService->isSunday($times[$i][3]))
                continue;

            $res = $this->checkLateEarlyOneDay(
                $times[$i][4],
                $times[$i][5],
                $times[$i][6],
                $times[$i][7],
                $this->dateService->isSaturday($times[$i][3]),
                $lambu
            );

            $final['validAttendanceCount'] += $res['validAttendanceCount'];
            $final['lateMorningCount'] += $res['lateMorningCount'];
            $final['earlyMorningCount'] += $res['earlyMorningCount'];
            $final['lateAfternoonCount'] += $res['lateAfternoonCount'];
            $final['earlyAfternoonCount'] += $res['earlyAfternoonCount'];
        }

        return $final;
    }

    private function checkLateEarlyOneDay(
        string $morningCheckInRaw = null,
        string $morningCheckOutRaw = null,
        string $afternoonCheckInRaw = null,
        string $afternoonCheckOutRaw = null,
        bool $isSaturday,
        bool $isMakeUpWorkingDay
    ) {
        $standardTimes = [
            'morningCheckIn' => $this->dateService->stringToDateTime('07:30'),
            'morningCheckOut' => $this->dateService->stringToDateTime('12:00'),
            'afternoonCheckIn' => $this->dateService->stringToDateTime('13:30'),
            'afternoonCheckOut' => $this->dateService->stringToDateTime('17:00'),
        ];

        $standardTimesSaturday = [
            'morningCheckIn' => $this->dateService->stringToDateTime('08:00'),
            'morningCheckOut' => $this->dateService->stringToDateTime('11:30'),
        ];

        $userTimes = [
            'morningCheckIn' => $this->dateService->stringToDateTime($morningCheckInRaw),
            'morningCheckOut' => $this->dateService->stringToDateTime($morningCheckOutRaw),
            'afternoonCheckIn' => $this->dateService->stringToDateTime($afternoonCheckInRaw),
            'afternoonCheckOut' => $this->dateService->stringToDateTime($afternoonCheckOutRaw),
        ];

        $result = [
            'validAttendanceCount' => 0,
            'lateMorningCount' => 0,
            'earlyMorningCount' => 0,
            'lateAfternoonCount' => 0,
            'earlyAfternoonCount' => 0,
        ];

        if ($isSaturday == false) {  // nếu là ngày thứ thì nửa ngày 1 công hợp lệ
            if ($userTimes['morningCheckIn'])
                $result['lateMorningCount'] = max(0, $this->dateService->getTimeDiffInMinutes($userTimes['morningCheckIn'], $standardTimes['morningCheckIn']));
            if ($userTimes['morningCheckOut'])
                $result['earlyMorningCount'] = max(0, $this->dateService->getTimeDiffInMinutes($standardTimes['morningCheckOut'], $userTimes['morningCheckOut']));
            if ($userTimes['afternoonCheckIn'])
                $result['lateAfternoonCount'] = max(0, $this->dateService->getTimeDiffInMinutes($userTimes['afternoonCheckIn'], $standardTimes['afternoonCheckIn']));
            if ($userTimes['afternoonCheckOut'])
                $result['earlyAfternoonCount'] = max(0, $this->dateService->getTimeDiffInMinutes($standardTimes['afternoonCheckOut'], $userTimes['afternoonCheckOut']));
            if ($userTimes['morningCheckIn'] && $userTimes['morningCheckIn'])
                $result['validAttendanceCount'] += 1;
            if ($userTimes['afternoonCheckIn'] && $userTimes['afternoonCheckIn'])
                $result['validAttendanceCount'] += 1;
        } else {
            if ($isMakeUpWorkingDay != false)  // nếu là ngày làm bù thì tính giờ giấc như ngày thứ, nếu là thứ 7 thì tính theo giờ t7
            {
                $standardTimes = [
                    'morningCheckIn' => $this->dateService->stringToDateTime('08:00'),
                    'morningCheckOut' => $this->dateService->stringToDateTime('11:30'),
                ];

                if ($userTimes['morningCheckIn'])
                    $result['lateMorningCount'] = max(0, $this->dateService->getTimeDiffInMinutes($userTimes['morningCheckIn'], $standardTimesSaturday['morningCheckIn']));
                if ($userTimes['morningCheckOut'])
                    $result['earlyMorningCount'] = max(0, $this->dateService->getTimeDiffInMinutes($standardTimes['morningCheckOut'], $standardTimesSaturday['morningCheckOut']));
                $result['lateAfternoonCount'] = null;
                $result['earlyAfternoonCount'] = null;
            }

            // nếu là thứ 7 thì nửa ngày 2 công hợp lệ
            if ($userTimes['morningCheckIn'] && $userTimes['morningCheckIn'])
                $result['validAttendanceCount'] += 2;
            if ($isMakeUpWorkingDay == true)  // nếu làm bù thì mới tính công chiều t7
                if ($userTimes['afternoonCheckIn'] && $userTimes['afternoonCheckIn'])
                    $result['validAttendanceCount'] += 2;
        }

        return $result;
    }

    // tính lại số ngày nghỉ thực tế khi có nghỉ lễ
    private function calculateLeaveRequest(array $workTimeSheet, array $userInfo)
    {
        // tính lại số ngày nghỉ thực tế khi có nghỉ lễ
        $compensatedDays = json_decode($workTimeSheet['days_details'], true)['holiday_days'];
        $leaveDays = $userInfo['leave_request'] ?? [];
        if (count($compensatedDays) > 0)
            $leaveDays = array_map(function ($i) use ($workTimeSheet, $compensatedDays) {
                $i['range'] = $this->dateService->getDatesInRange($i['from_date'], $i['to_date'], [0], 'Y-m-d', $workTimeSheet['month'], $workTimeSheet['year'])->toArray();
                $intersect = array_intersect($i['range'], $compensatedDays);
                if (!empty($intersect)) {
                    $minusValue = in_array($i['type'], ['morning', 'afternoon']) ? 0.5 : 1;  // nghỉ nửa ngày thì trừ 0.5
                    $i['total_leave_days'] = max(0, $i['total_leave_days'] - $minusValue);  // nếu có ngày nghỉ trùng ngày lễ thì bỏ qua
                }
                return $i;
            }, $leaveDays);

        // tổng số ngày nghỉ phép
        $leaveDaysWithPermission = array_sum(array_column(array_filter(
            $leaveDays, fn($i) => !in_array($i, $compensatedDays)
        ), 'total_leave_days'));
        // tổng số ngày đã nghỉ trong năm
        $totalLeaveDaysInYear = $this->workTimesheetDetailService->getTotalLeaveDaysWithPermission($userInfo['id'], $workTimeSheet['year']) + $leaveDaysWithPermission;
        // số ngày nghỉ phép có lương trong năm còn lại
        $maxPaidLeaveDaysPerYear = $userInfo['position_id'] == 6 ? max(12 - $totalLeaveDaysInYear - $leaveDaysWithPermission, 0) : 0;

        return [
            'leaveDays' => Arr::flatten(collect($leaveDays)->map(fn($i) => $i['range'])->toArray()),
            'leaveDaysWithPermission' => $leaveDaysWithPermission,
            'totalLeaveDaysInYear' => $totalLeaveDaysInYear,
            'maxPaidLeaveDaysPerYear' => $maxPaidLeaveDaysPerYear,
        ];
    }

    // tính lương
    private function calculateSalary(WorkTimesheetDetail $detail)
    {
        // max công bpdx
        $maxProposedWorkDays = $this->workTimesheetDetailService->getMaxProposedWorkDayInMonth($detail['user_id'], $detail['workTimeSheet']['month'], $detail['workTimeSheet']['year']);

        // mức lương ngoài giờ
        $overtimeSalaryRate = round(($detail['salary_level'] / $maxProposedWorkDays) / 2, 0);
        // tổng nhận lương ngoài giờ
        $overtimeTotalCount = $detail['overtime_total_count'] * $overtimeSalaryRate;

        // số công
        $numberOfJobs = min($maxProposedWorkDays, $maxProposedWorkDays + $detail['max_paid_leave_days_per_year'] - $detail['leave_days_with_permission'] - $detail['leave_days_without_permission']);

        // tổng lương: tổng phụ cấp + (mức lương/max công bpdx) - tổng trừ tiêu chí + tổng công thêm
        $totalReceivedSalary = $detail['allowance_contact'] + $detail['allowance_meal'] + $detail['allowance_position'] + $detail['allowance_fuel'] + $detail['allowance_transport'] + (($detail['salary_level'] / $maxProposedWorkDays) * $numberOfJobs) - $detail['deduction_amount'] + $overtimeTotalCount;
        if ($detail['position_id'] != 6)
            $totalReceivedSalary -= (($detail['salary_level'] + $detail['allowance_position']) * 0.105);
        $totalReceivedSalary = round($totalReceivedSalary, 0);

        $detail->update([
            'overtime_salary_rate' => $overtimeSalaryRate,
            'overtime_total_count' => $overtimeTotalCount,
            'total_received_salary' => $totalReceivedSalary,
        ]);
    }

    private function readExcel(WorkTimesheet $record)
    {
        $dataExcel = $this->excelService->readExcel($record['original_path']);
        $sheetData = $dataExcel['Sheet1'] ?? [];
        if (empty($sheetData))
            throw new Exception("Không tìm thấy data trong 'Sheet1'");

        // cắt 2 dòng header của file excel
        array_splice($sheetData, 0, 2);

        $details = collect($sheetData)->groupBy(1)->map(function ($value, $key) use ($record) {
            // tìm thông tin tài khoản
            $userInfo = $this->getUserInfoByName($key, $record['month'], $record['year']);

            // báo lỗi nếu ko tìm thấy user
            if (!$userInfo)
                throw new Exception("Không tìm thấy dữ liệu tài khoản của $key");

            // ko bật tính lương thì cút
            if ($userInfo['is_salary_counted'] == 0)
                return null;

            // các ngày đi công tác trong tháng ko tính chủ nhật
            $businessTripDays = $this->businessTripDays($userInfo['work_schedules'], $record['month'], $record['year']);
            $businessTripDayCount = count($businessTripDays);

            // công bộ phận đề xuất
            $proposedWorkDays = $this->calculateProposedWorkDays($record['month'], $record['year'], $record['total_holiday_days'], $record['total_power_outage_days'], $record['total_compensated_days'], $businessTripDayCount);

            // nếu công đi công tác quá công bộ phận đề xuất thì set = công bộ phận đề xuất
            $businessTripDayCount = $businessTripDayCount > $proposedWorkDays ? $proposedWorkDays : $businessTripDayCount;

            // gom các ngày chấm công vs ngày làm bù
            $value = $this->getRealTimeSheet(json_decode($record['days_details'], true)['compensated_days'], $value->toArray());

            // tính đi muộn về sớm
            $lateEarly = $this->checkLateEarlyMultipleDays($value, $record['total_compensated_days'] > 0);

            // công hợp lệ
            $validAttendanceCount = $lateEarly['validAttendanceCount'] / 2;

            // tính trung bình muộn
            $calculateAvgLateMinute = $this->calculateAvgLateMinute($lateEarly, $validAttendanceCount, $businessTripDayCount, $userInfo['is_childcare_mode']);

            // tính lại số ngày nghỉ thực tế khi có nghỉ lễ
            $calculateLeaveRequest = $this->calculateLeaveRequest(collect($record)->toArray(), $userInfo);
            $leaveDaysWithPermission = $calculateLeaveRequest['leaveDaysWithPermission'];

            // số lần chấm công ko hợp lệ: công bộ phận đề xuất - chấm hợp lệ - công tác - nghỉ phép
            $invalidAttendanceCount = $proposedWorkDays - $validAttendanceCount - $businessTripDayCount - $leaveDaysWithPermission;
            // tỷ lệ chấm công ko hợp lệ: round(lấy số lần chênh / công bộ phận đề xuất * 100, 2)
            $invalidAttendanceRate = round($invalidAttendanceCount / $proposedWorkDays * 100, 2);

            // tính số lần ABC
            $ruleBCount = $ruleCCount = $trainingBCount = $trainingCCount = 0;
            $warningCount = count($userInfo['warning']);

            // cảnh báo trên 3 lần bị C nội quy
            if ($warningCount > 3) {
                $ruleCCount++;
            } elseif ($warningCount > 0) {
                // dưới 3 lần bị B
                $ruleBCount++;
            }

            // nghỉ 5 ngày trở lên bị C
            if ($leaveDaysWithPermission >= 5) {
                $ruleCCount++;
            } elseif ($leaveDaysWithPermission >= 3) {
                // 3 ngày bị B
                $ruleBCount++;
            }

            // top muộn bị B
            if ($calculateAvgLateMinute['isLatestArrival'])
                $ruleBCount++;

            // chênh lệch quá 20% công bộ phận đề xuất bị B
            if ($invalidAttendanceCount > ($proposedWorkDays * 0.2))
                $ruleBCount++;

            // tính trừ tiền
            $violationPenalty = $userInfo['violation_penalty'] ?? 0;
            $deductionAmount = 0;

            if ($userInfo['position_id'] == 6) {  // cộng tác viên
                $minusValue = ($ruleBCount * 100000) + ($ruleCCount * 200000) + ($trainingBCount * 100000) + ($trainingCCount * 200000);
                if ($minusValue <= $violationPenalty) {
                    $deductionAmount = $minusValue;
                } else {
                    $deductionAmount = $violationPenalty;
                }
            } else {
                $rate = (25 * $ruleBCount) + ($ruleCCount * 75) + ($trainingBCount * 25) + ($trainingCCount * 75);
                if ($rate == 0) {
                    $minusValue = 0;
                } elseif ($rate > 0 && $rate < 100) {
                    $minusValue = $violationPenalty * ($rate / 100);
                } else {
                    $minusValue = $violationPenalty;
                }
                $deductionAmount = $minusValue;
            }

            // bảo hiểm
            $allowanceContact = $userInfo['allowance_contact'] ?? 0;
            $allowanceMeal = $userInfo['allowance_meal'] ?? 0;
            $allowancePosition = $userInfo['allowance_position'] ?? 0;
            $allowanceFuel = $userInfo['allowance_fuel'] ?? 0;
            $allowanceTransport = $userInfo['allowance_transport'] ?? 0;
            $socialInsuranceDeduction = $healthInsuranceDeduction = $unemploymentInsuranceDeduction = $totalTaxDeduction = 0;
            $salaryLevel = $userInfo['salary_level'] ?? 0;
            if ($userInfo['position_id'] != 6) {  // nếu ko phải cộng tác viên
                $socialInsuranceDeduction = ($salaryLevel + $allowancePosition) * 0.08;  // (mức lương + chức vụ) * 8%
                $healthInsuranceDeduction = ($salaryLevel + $allowancePosition) * 0.015;  // (mức lương + chức vụ) * 1.5%
                $unemploymentInsuranceDeduction = ($salaryLevel + $allowancePosition) * 0.01;  // (mức lương + chức vụ) * 1%
                $totalTaxDeduction = ($salaryLevel + $allowancePosition) * 0.105;  // (mức lương + chức vụ) * 10.5%;
            }

            $detail = [
                'user_id' => $userInfo['id'],
                'name' => $userInfo['name'] ?? 0,
                'department' => $userInfo['department']['name'] ?? 0,
                'position_id' => $userInfo['position_id'] ?? 0,
                'salary_level' => $salaryLevel,  // Mức lương
                'violation_penalty' => $violationPenalty,  // Mức tiêu chí
                'social_insurance_deduction' => $socialInsuranceDeduction,  // Tiền BHXH (8%)
                'health_insurance_deduction' => $healthInsuranceDeduction,  // Tiền BHYT (1.5%)
                'unemployment_insurance_deduction' => $unemploymentInsuranceDeduction,  // Tiền BHTN (1%)
                'total_tax_deduction' => $totalTaxDeduction,  // Tổng tiền khấu trừ bảo hiểm
                'allowance_contact' => $allowanceContact,  // Phụ cấp liên lạc
                'allowance_meal' => $allowanceMeal,  // Phụ cấp ăn ca
                'allowance_position' => $allowancePosition,  // Phụ cấp chức vụ
                'allowance_fuel' => $allowanceFuel,  // Phụ cấp xăng xe
                'allowance_transport' => $allowanceTransport,  // Phụ cấp đi lại
                'proposed_work_days' => $proposedWorkDays,  // công bộ phận đề xuất
                'valid_attendance_count' => $validAttendanceCount,  // Số lần chấm công hợp lệ
                'invalid_attendance_count' => $invalidAttendanceCount,  // Số lần chấm công không hợp lệ
                'invalid_attendance_rate' => $invalidAttendanceRate,  // Tỷ lệ chấm công không hợp lệ
                'late_morning_count' => $lateEarly['lateMorningCount'],  // Số lần chấm công muộn buổi sáng
                'early_morning_count' => $lateEarly['earlyMorningCount'],  // Số lần chấm công sớm buổi sáng
                'late_afternoon_count' => $lateEarly['lateAfternoonCount'],  // Số lần chấm công muộn buổi chiều
                'early_afternoon_count' => $lateEarly['earlyAfternoonCount'],  // Số lần chấm công sớm buổi chiều
                'avg_late_minutes' => $calculateAvgLateMinute['avgLateMinutes'],  // Trung bình phút chấm công muộn
                'overtime_salary_rate' => 0,  // Mức lương ngoài giờ
                'overtime_evening_count' => 0,  // Số công ngoài giờ buổi tối ===============> đợi các phòng đẩy công
                'overtime_weekend_count' => 0,  // Số công ngoài giờ T7 CN ===============> đợi các phòng đẩy công
                'overtime_total_count' => 0,  // Tổng số công ngoài giờ ===============> đợi các phòng đẩy công
                'overtime_total_amount' => 0,  // Tổng tiền công ngoài giờ ===============> đợi các phòng đẩy công
                'business_trip_system_count' => $businessTripDayCount,  // Tổng số công đi công tác - hệ thống tính
                'business_trip_manual_count' => 0,  // Tổng số công đi công tác ==>>>>>>>>>>>>>>>> Rà soát đẩy thủ công
                'leave_days_with_permission' => $calculateLeaveRequest['leaveDaysWithPermission'],  // Tổng số ngày nghỉ phép
                'total_leave_days_in_year' => $calculateLeaveRequest['totalLeaveDaysInYear'],  // Tổng số ngày đã nghỉ phép trong năm
                'max_paid_leave_days_per_year' => $calculateLeaveRequest['maxPaidLeaveDaysPerYear'],  // số ngày nghỉ có lương tối đa của trong năm
                'leave_days_without_permission' => 0,  // Tổng số ngày nghỉ không phép ===============> đợi các phòng đẩy công
                'warning_count' => $warningCount,  // Tổng số lần bị cảnh báo
                'department_rating' => null,  // Đánh giá của phòng ===============> đợi các phòng đẩy công
                'council_rating' => null,  // Đánh giá của hội đồng ==>>>>>>>>>>>>>>>> Rà soát đẩy thủ công
                'is_latest_arrival' => $calculateAvgLateMinute['isLatestArrival'],  // Top muộn
                'rule_b_count' => $ruleBCount,  // Số lần bị đánh giá nội quy B
                'rule_c_count' => $ruleCCount,  // Số lần bị đánh giá nội quy C
                'training_a_count' => 0,  // Số lần đánh giá đào tạo A
                'training_b_count' => $trainingBCount,  // Số lần bị đánh giá đào tạo B
                'training_c_count' => $trainingCCount,  // Số lần bị đánh giá đào tạo đào tạo C
                'deduction_amount' => $deductionAmount,  // Số tiền trừ
                'total_received_salary' => 0,  // Tổng lương nhận
                'detail_business_trip_and_leave_days' => json_encode([
                    'business_trip_days' => $businessTripDays,
                    'leave_days' => $calculateLeaveRequest['leaveDays'],
                ]),  // Mảng các ngày công tác và nghỉ trong tháng
                'note' => $calculateAvgLateMinute['note'],  // Ghi chú ==>>>>>>>>>>>>>>>> Rà soát đẩy thủ công
            ];

            $detailRecord = $record->details()->create($detail);
            $this->calculateSalary($detailRecord);
            return $detailRecord;
        })->filter()->values()->toArray();

        return $details;
    }

    private function createCaculatedExcel(WorkTimesheet $record)
    {
        $headerExcel = array_map(fn($i) =>
                [
                    'name' => $i,
                    'rowspan' => 1,
                    'colspan' => 1,
                ],
            [
                'STT',
                'Họ tên',
                'Công BPDX',
                'Công chấm máy(Hợp lệ)',
                'Ngoài giờ',
                'Công tác (Đăng ký Hệ thống)',
                'Công tác (Không đăng ký Hệ thống)',
                'Đã nghỉ phép',
                'Nghỉ phép',
                'Chấm công không hợp lệ',
                'Tỷ lệ không hợp lệ',
                'Thời gian trung bình muộn',
                'Cảnh báo',
                'Phòng Đánh giá ABC',
                'Hội đồng đánh giá',
                'Ghi chú',
            ]);

        $details = collect($record['details'])->groupBy('department')->sortBy('position_id')->toArray();

        $dataExcel = [];
        $centerRows = [];
        $boldRows = [1];
        $romanIndex = $intIndex = 1;

        foreach ($details as $key => $value) {
            if (is_string($key)) {
                $dataExcel[] = [
                    'color' => 'red',
                    'record' => [
                        $this->getRomanIndex($romanIndex),
                        $key,
                        ...array_map(fn($i) => '', range(1, count($headerExcel) - 2)),
                    ],
                ];
                $currentRow = count($dataExcel) + 1;  // +1 vì có header
                $centerRows[] = $currentRow;
                $boldRows[] = $currentRow;
                $romanIndex++;
            }

            foreach ($value as $item) {
                $dataExcel[] = [
                    $intIndex,
                    $item['name'],
                    $item['proposed_work_days'],
                    $item['valid_attendance_count'],
                    $item['overtime_total_amount'],
                    $item['business_trip_system_count'],
                    $item['business_trip_manual_count'],
                    $item['total_leave_days_in_year'],
                    $item['leave_days_with_permission'],
                    $item['invalid_attendance_count'],
                    $item['invalid_attendance_rate'],
                    $item['avg_late_minutes'],
                    $item['warning_count'],
                    $item['department_rating'],
                    $item['council_rating'],
                    $item['note'],
                ];
                $intIndex++;
            }
        }

        $record->update([
            'calculated_path' => $this->excelService->createExcel(
                [
                    (object) [
                        'name' => 'Sheet1',
                        'header' => [$headerExcel],
                        'data' => $dataExcel,
                        'boldRows' => $boldRows,
                        'boldItalicRows' => [],
                        'italicRows' => [],
                        'centerColumns' => array_values(array_diff(range(1, count($headerExcel)), [2])),  // Loại cột 2 (Họ tên)
                        'centerRows' => $centerRows,
                        'filterStartRow' => 1,
                        'freezePane' => 'custom',
                        'freezeCell' => 'C2',
                    ],
                ],
                "uploads/render/work-timesheets/{$record['year']}/{$record['month']}",
                'calculated.xlsx'
            ),
        ]);

        return $record;
    }

    private function getRomanIndex(int $num)
    {
        $map = [
            'M' => 1000,
            'CM' => 900,
            'D' => 500,
            'CD' => 400,
            'C' => 100,
            'XC' => 90,
            'L' => 50,
            'XL' => 40,
            'X' => 10,
            'IX' => 9,
            'V' => 5,
            'IV' => 4,
            'I' => 1,
        ];

        $returnValue = '';
        while ($num > 0) {
            foreach ($map as $roman => $int) {
                if ($num >= $int) {
                    $num -= $int;
                    $returnValue .= $roman;
                    break;
                }
            }
        }
        return $returnValue;
    }
}
