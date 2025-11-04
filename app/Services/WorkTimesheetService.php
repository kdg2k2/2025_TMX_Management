<?php

namespace App\Services;

use App\Models\WorkTimesheet;
use App\Models\WorkTimesheetDetail;
use App\Repositories\WorkTimesheetRepository;
use Arr;
use DB;
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
        private WorkTimesheetDetailService $workTimesheetDetailService,
        private WorkTimesheetOvertimeService $workTimesheetOvertimeService,
        private PayrollService $payrollService,
    ) {
        $this->repository = app(WorkTimesheetRepository::class);
    }

    public function baseIndexData()
    {
        $baseOvertimeUpload = $this->workTimesheetOvertimeService->baseOvertimeUpload();
        $currentYear = $baseOvertimeUpload['currentYear'];
        $currentMonth = $baseOvertimeUpload['currentMonth'];

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
        if (isset($array['payroll_path']))
            $array['payroll_path'] = $this->getAssetUrl($array['payroll_path']);
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

            // cập nhật lại top 3 muộn
            $this->setTop3LatestArrival($record);

            // tạo file excel hiển thị kết quả tính
            $record = $this->createCaculatedExcel($record);
            // tạo file excel bảng lương
            $this->createPayrollExcel($record);

            // gửi mail
            app(TaskScheduleService::class)->run('WORK_TIMESHEET_REPORT');

            // xóa file cũ
            $this->handlerUploadFileService->removeFiles(array_map(fn($i) => !in_array($i, [$record['original_path'], $record['calculated_path']]), [$oldOriginalPath, $oldCalculatedPath]));
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

    // Tính công bộ phận đề xuất dựa trên ngày làm việc thực tế của user
    private function calculateProposedWorkDays(
        int $month,
        int $year,
        array $holidayDays = [],  // Array các ngày nghỉ lễ
        array $powerOutageDays = [],  // Array các ngày mất điện
        array $compensatedDays = [],  // Array các ngày làm bù
        int $totalBusinessTripDays = 0,
        ?string $workStartDate = null,
        ?string $workEndDate = null
    ): float {
        // Ngày đầu và cuối tháng
        $monthStart = $this->dateService->getFirstDayOfMonth($month, $year);
        $monthEnd = $this->dateService->getLastDayOfMonth($month, $year);

        // Xác định khoảng thời gian làm việc thực tế
        $actualStartDate = $workStartDate && $workStartDate >= $monthStart && $workStartDate <= $monthEnd
            ? $workStartDate  // work_start_date TRONG tháng → dùng work_start_date
            : $monthStart;  // work_start_date < đầu tháng hoặc null → dùng đầu tháng

        $actualEndDate = $workEndDate && $workEndDate >= $monthStart && $workEndDate <= $monthEnd
            ? $workEndDate  // work_end_date TRONG tháng → dùng work_end_date
            : $monthEnd;  // work_end_date > cuối tháng hoặc null → dùng cuối tháng

        // Nếu user không làm việc trong tháng này
        if (($workStartDate && $workStartDate > $monthEnd) || ($workEndDate && $workEndDate < $monthStart)) {
            return 0;
        }

        // Tính số ngày làm việc (không tính CN) trong khoảng thời gian thực tế
        $workingDaysInRange = $this->dateService->getWorkingDays($actualStartDate, $actualEndDate, $month, $year);

        // CHỈ TRỪ nghỉ lễ/mất điện NẰM TRONG khoảng làm việc
        $actualHolidayDays = $this->filterDaysInRange($holidayDays, $actualStartDate, $actualEndDate);
        $actualPowerOutageDays = $this->filterDaysInRange($powerOutageDays, $actualStartDate, $actualEndDate);

        // CHỈ CỘNG ngày làm bù NẰM TRONG khoảng làm việc
        $actualCompensatedDays = $this->filterDaysInRange($compensatedDays, $actualStartDate, $actualEndDate);

        // Công thức: ngày làm việc - nghỉ lễ - mất điện + làm bù + công tác
        return $workingDaysInRange - $actualHolidayDays - $actualPowerOutageDays + $actualCompensatedDays + $totalBusinessTripDays;
    }

    // Lọc các ngày nằm trong khoảng thời gian
    private function filterDaysInRange(array $days, string $startDate, string $endDate): int
    {
        if (empty($days)) {
            return 0;
        }

        $daysInRange = array_filter($days, function ($day) use ($startDate, $endDate) {
            return $day >= $startDate && $day <= $endDate;
        });

        return count($daysInRange);
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

        // tính trung bình muộn: round(tổng 4 tiêu chí chấm muộn / (công hợp lệ + công tác), 2)
        $avgLateMinutes = round(collect($lateEarly)->except('validAttendanceCount')->sum() / ($validAttendanceCount + $businessTripDayCount), 2);

        // nếu có con nhỏ cho đi muộn về sớm 30p
        if ($isChildcareMode == 1)
            $avgLateMinutes -= 60;

        return $avgLateMinutes;
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

    // tính tiêu chí và mức trừ
    private function calculateDeductionCriteria(WorkTimesheetDetail $detail, bool $updateNote = true)
    {
        // tính số lần ABC
        $ruleBCount = $ruleCCount = $ruleDCount = $trainingBCount = $trainingCCount = 0;
        $warningCount = $detail['warning_count'];
        $leaveDaysWithPermission = $detail['leave_days_with_permission'];
        $invalidAttendanceCount = $detail['invalid_attendance_count'];
        $proposedWorkDays = $detail['proposed_work_days'];
        $note = null;
        $deductionAmount = $jobDeductionAmount = $ruleDeductionAmount = $trainingDeductionAmount = 0;
        $violationPenalty = $detail['violation_penalty'] ?? 0;

        // cảnh báo trên 3 lần bị C nội quy
        if ($warningCount > 3) {
            $ruleCCount++;
            $note = ($note ? $note . '; ' : '') . 'Cảnh báo trên 3 lần => C';
        } elseif ($warningCount > 0) {
            // dưới 3 lần bị B
            $ruleBCount++;
            $note = ($note ? $note . '; ' : '') . 'Cảnh báo dưới 3 lần => B';
        }

        // nghỉ 5 ngày trở lên bị C
        if ($leaveDaysWithPermission >= 5) {
            $ruleCCount++;
            $note = ($note ? $note . '; ' : '') . 'Nghỉ phép 5 ngày trở lên => C';
        } elseif ($leaveDaysWithPermission >= 3) {
            // 3 ngày bị B
            $ruleBCount++;
            $note = ($note ? $note . '; ' : '') . 'Nghỉ phép 3 ngày => B';
        }

        // chênh lệch quá 20% công bộ phận đề xuất bị B
        if ($invalidAttendanceCount > ($proposedWorkDays * 0.2)) {
            $ruleBCount++;
            $note = ($note ? $note . '; ' : '') . 'Chênh lệch công bộ phận đề xuất trên 20% => B';
        }

        // đánh giá của phòng ban
        if ($detail['department_rating'] == 'D') {
            $ruleDCount++;
            $note = ($note ? $note . '; ' : '') . 'Phòng đánh giá => D';
        } elseif ($detail['department_rating'] == 'C') {
            $ruleCCount++;
            $note = ($note ? $note . '; ' : '') . 'Phòng đánh giá => C';
        } elseif ($detail['department_rating'] == 'B') {
            $ruleBCount++;
            $note = ($note ? $note . '; ' : '') . 'Phòng đánh giá => B';
        }

        // đánh giá của hội đồng
        if ($detail['council_rating'] == 'D') {
            $ruleDCount++;
            $note = ($note ? $note . '; ' : '') . 'Hội đồng đánh giá => D';
            $jobDeductionAmount = $detail['postion_id'] == 6 ? 100000 : $violationPenalty;
        } elseif ($detail['council_rating'] == 'C') {
            $ruleCCount++;
            $note = ($note ? $note . '; ' : '') . 'Hội đồng đánh giá => C';
            $jobDeductionAmount = $detail['postion_id'] == 6 ? 200000 : $violationPenalty * 0.75;
        } elseif ($detail['council_rating'] == 'B') {
            $ruleBCount++;
            $note = ($note ? $note . '; ' : '') . 'Hội đồng đánh giá => B';
            $jobDeductionAmount = $detail['postion_id'] == 6 ? 300000 : $violationPenalty * 0.25;
        }

        // công tác ko đăng ký
        if ($detail['business_trip_manual_count'] >= 5) {
            $ruleDCount++;
            $note = ($note ? $note . '; ' : '') . 'Công tác ko đăng ký 5 ngày trở lên => D';
        } elseif ($detail['business_trip_manual_count'] >= 3) {
            $ruleCCount++;
            $note = ($note ? $note . '; ' : '') . 'Công tác ko đăng ký 3 ngày trở lên => C';
        } elseif ($detail['business_trip_manual_count'] > 0) {
            $ruleBCount++;
            $note = ($note ? $note . '; ' : '') . 'Công tác ko đăng ký dưới 3 ngày => B';
        }

        // tính trừ tiền (dính tiêu chí D thì trừ tối đa)
        if ($detail['position_id'] == 6) {  // Cộng tác viên
            $minusValue = $jobDeductionAmount
                + 100000 * ($ruleBCount + $trainingBCount)
                + 200000 * ($ruleCCount + $trainingCCount);

            $deductionAmount = ($ruleDCount > 0 || $minusValue > $violationPenalty)
                ? $violationPenalty
                : $minusValue;
        } else {
            $ruleDeductionAmount = $violationPenalty * (min(100, 25 * $ruleBCount + 75 * $ruleCCount + 100 * $ruleDCount) / 100);
            $trainingDeductionAmount = $violationPenalty * (min(100, 25 * $trainingBCount + 75 * $trainingCCount) / 100);

            $deductionAmount = min($violationPenalty, $jobDeductionAmount + $ruleDeductionAmount + $trainingDeductionAmount);
        }

        $updates = [
            'rule_b_count' => $ruleBCount,
            'rule_c_count' => $ruleCCount,
            'rule_d_count' => $ruleDCount,
            'training_b_count' => $trainingBCount,
            'training_c_count' => $trainingCCount,
            'job_deduction_amount' => $jobDeductionAmount,
            'rule_deduction_amount' => $ruleDeductionAmount,
            'training_deduction_amount' => $trainingDeductionAmount,
            'deduction_amount' => $deductionAmount,
        ];

        if ($updateNote)
            $updates['note'] = $note;

        $detail->update($updates);
    }

    // tính lương
    public function calculateSalary(WorkTimesheetDetail $detail)
    {
        // max công bpdx
        $maxProposedWorkDays = $this->workTimesheetDetailService->getMaxProposedWorkDayInMonth($detail['user_id'], $detail['workTimeSheet']['month'], $detail['workTimeSheet']['year']);

        // mức lương ngoài giờ
        $overtimeSalaryRate = round(($detail['salary_level'] / $maxProposedWorkDays) / 2, 0);
        // tổng nhận lương ngoài giờ
        $overtimeTotalAmount = $detail['overtime_total_count'] * $overtimeSalaryRate;

        // số công
        $totalWorkDayCount = min($maxProposedWorkDays, $maxProposedWorkDays + ($detail['position_id'] == 6 ? $detail['max_paid_leave_days_per_year'] : 0) - $detail['leave_days_with_permission'] - $detail['leave_days_without_permission']);

        // tổng lương: tổng phụ cấp + (mức lương/max công bpdx) - tổng trừ tiêu chí + tổng công thêm
        $totalReceivedSalary = $detail['allowance_contact'] + $detail['allowance_meal'] + $detail['allowance_position'] + $detail['allowance_fuel'] + $detail['allowance_transport'] + (($detail['salary_level'] / $maxProposedWorkDays) * $totalWorkDayCount) - $detail['deduction_amount'] + $overtimeTotalAmount;
        if ($detail['position_id'] != 6)
            $totalReceivedSalary -= (($detail['salary_level'] + $detail['allowance_position']) * 0.105);
        $totalReceivedSalary = round($totalReceivedSalary, 0);

        $detail->update([
            'total_work_day_count' => $totalWorkDayCount,
            'overtime_salary_rate' => $overtimeSalaryRate,
            'overtime_total_amount' => $overtimeTotalAmount,
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
        array_splice($sheetData, 0, length: 2);

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

            // Tính công bộ phận đề xuất với work_start_date và work_end_date
            $daysDetails = json_decode($record['days_details'], true);
            $proposedWorkDays = $this->calculateProposedWorkDays(
                $record['month'],
                $record['year'],
                $daysDetails['holiday_days'],
                $daysDetails['power_outage_days'],
                $daysDetails['compensated_days'],
                $businessTripDayCount,
                $userInfo['work_start_date'] ?? null,  // Thêm tham số
                $userInfo['work_end_date'] ?? null  // Thêm tham số
            );

            // Nếu user không làm việc trong tháng này, bỏ qua
            if ($proposedWorkDays <= 0)
                return null;

            // nếu công đi công tác quá công bộ phận đề xuất thì set = công bộ phận đề xuất
            $businessTripDayCount = $businessTripDayCount > $proposedWorkDays ? $proposedWorkDays : $businessTripDayCount;

            // gom các ngày chấm công vs ngày làm bù
            $value = $this->getRealTimeSheet(json_decode($record['days_details'], true)['compensated_days'], $value->toArray());

            // tính đi muộn về sớm
            $lateEarly = $this->checkLateEarlyMultipleDays($value, $record['total_compensated_days'] > 0);

            // công hợp lệ
            $validAttendanceCount = $lateEarly['validAttendanceCount'] / 2;

            // tính trung bình muộn
            $avgLateMinutes = $this->calculateAvgLateMinute($lateEarly, $validAttendanceCount, $businessTripDayCount, $userInfo['is_childcare_mode']);

            // tính lại số ngày nghỉ thực tế khi có nghỉ lễ
            $calculateLeaveRequest = $this->calculateLeaveRequest(collect($record)->toArray(), $userInfo);
            $leaveDaysWithPermission = $calculateLeaveRequest['leaveDaysWithPermission'];

            // số lần chấm công ko hợp lệ: công bộ phận đề xuất - chấm hợp lệ - công tác - nghỉ phép
            $invalidAttendanceCount = $proposedWorkDays - $validAttendanceCount - $businessTripDayCount - $leaveDaysWithPermission;
            // tỷ lệ chấm công ko hợp lệ: round(lấy số lần chênh / công bộ phận đề xuất * 100, 2)
            $invalidAttendanceRate = round($invalidAttendanceCount / $proposedWorkDays * 100, 2);

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
                'violation_penalty' => $userInfo['violation_penalty'] ?? 0,  // Mức tiêu chí
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
                'avg_late_minutes' => $avgLateMinutes,  // Trung bình phút chấm công muộn
                'business_trip_system_count' => $businessTripDayCount,  // Tổng số công đi công tác - hệ thống tính
                'business_trip_manual_count' => 0,  // Tổng số công đi công tác ==>>>>>>>>>>>>>>>> Rà soát đẩy thủ công
                'leave_days_with_permission' => $calculateLeaveRequest['leaveDaysWithPermission'],  // Tổng số ngày nghỉ phép
                'total_leave_days_in_year' => $calculateLeaveRequest['totalLeaveDaysInYear'],  // Tổng số ngày đã nghỉ phép trong năm
                'max_paid_leave_days_per_year' => $calculateLeaveRequest['maxPaidLeaveDaysPerYear'],  // số ngày nghỉ có lương tối đa của trong năm
                'warning_count' => count($userInfo['warning']),  // Tổng số lần bị cảnh báo
                'council_rating' => null,  // Đánh giá của hội đồng ==>>>>>>>>>>>>>>>> Rà soát đẩy thủ công
                'detail_business_trip_and_leave_days' => json_encode([
                    'business_trip_days' => $businessTripDays,
                    'leave_days' => $calculateLeaveRequest['leaveDays'],
                ]),  // Mảng các ngày công tác và nghỉ trong tháng
            ];

            // lưu chi tiết
            $detailRecord = $record->details()->create($detail);

            // tính tiêu chí và mức trừ
            $this->calculateDeductionCriteria($detailRecord);

            // tính lương
            $this->calculateSalary($detailRecord);
            return $detailRecord;
        })->filter()->values()->toArray();

        return $details;
    }

    private function createCaculatedExcel(WorkTimesheet $record)
    {
        $record->load('details');

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

        if ($record['calculated_path'])
            $this->handlerUploadFileService->safeDeleteFile($record['calculated_path']);

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

    public function findByMonthYear(int $month, int $year)
    {
        return $this->tryThrow(function () use ($month, $year) {
            return $this->repository->findByMonthYear($month, $year);
        });
    }

    public function syncDataWorkTimesheetOvertimeDetailByDepartmentId(WorkTimesheet $workTimesheet, int $departmentId)
    {
        return $this->tryThrow(function () use ($workTimesheet, $departmentId) {
            // Load relations với filter department
            $workTimesheet->load([
                'details' => fn($q) => $q->whereHas('user', fn($q) => $q->where('department_id', $departmentId)),
                'overtimes.details' => fn($q) => $q->whereHas('user', fn($q) => $q->where('department_id', $departmentId)),
            ]);

            // Kiểm tra có overtime data không
            if ($workTimesheet->overtimes->isEmpty()) {
                throw new Exception("Không có dữ liệu overtime cho tháng {$workTimesheet->month}/{$workTimesheet->year}");
            }

            // Lấy overtime đầu tiên (vì 1 work_timesheet chỉ có 1 overtime)
            $overtime = $workTimesheet->overtimes->first();

            if ($overtime->details->isEmpty()) {
                throw new Exception('Không có dữ liệu overtime detail cho phòng ban này');
            }

            // Map overtime details theo user_id để dễ truy cập
            $overtimeDetailsMap = $overtime->details->keyBy('user_id');

            $updatedCount = 0;
            $notFoundUsers = [];

            // Sync từng user
            foreach ($workTimesheet->details as $detail) {
                $userId = $detail->user_id;

                // Kiểm tra user này có trong overtime details không
                if (!isset($overtimeDetailsMap[$userId])) {
                    $notFoundUsers[] = $detail->name;
                    continue;
                }

                $overtimeDetail = $overtimeDetailsMap[$userId];

                // Sync data từ overtime detail sang work timesheet detail
                $detail->update([
                    'overtime_evening_count' => $overtimeDetail->overtime_evening_count,
                    'overtime_weekend_count' => $overtimeDetail->overtime_weekend_count,
                    'overtime_total_count' => $overtimeDetail->overtime_total_count,
                    'leave_days_without_permission' => $overtimeDetail->leave_days_without_permission,
                    'department_rating' => $overtimeDetail->department_rating,
                ]);

                $updatedCount++;

                // tính lại tiêu chí và mức trừ
                $this->calculateDeductionCriteria($detail);

                // Tính lại lương
                $this->calculateSalary($detail);
            }

            // Thông báo kết quả
            $message = "Đã sync {$updatedCount} user thành công";

            if (!empty($notFoundUsers)) {
                $message .= '. Không tìm thấy overtime data cho: ' . implode(', ', $notFoundUsers);
            }

            // cập nhật lại excel xuất lưới
            $this->createCaculatedExcel($workTimesheet);

            // cập nhật lại bảng lương
            $this->createPayrollExcel($workTimesheet);

            return [
                'message' => $message,
                'updated_count' => $updatedCount,
                'not_found_users' => $notFoundUsers,
            ];
        }, true);
    }

    private function setTop3LatestArrival(WorkTimesheet $record)
    {
        return $this->tryThrow(function () use ($record) {
            $top3 = $record
                ->details()
                ->where('avg_late_minutes', '>', 0)
                ->orderByDesc('avg_late_minutes')
                ->limit(3)
                ->pluck('id')
                ->toArray();

            $record->details()->where('avg_late_minutes', '>', 0)->update(['is_latest_arrival' => false]);

            if (!empty($top3)) {
                $record->details()->whereIn('id', $top3)->update([
                    'is_latest_arrival' => true,
                    'note' => DB::raw("CONCAT(TRIM(COALESCE(note,'')), CASE WHEN note IS NULL OR note = '' THEN '' ELSE '; ' END, 'Top muộn -> B')"),
                ]);
            }

            return $top3;
        }, true);
    }

    public function update(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            // Tìm work_timesheet
            $workTimesheet = $this->repository->findByMonthYear(
                $request['month'],
                $request['year']
            );

            if (!$workTimesheet) {
                throw new Exception("Không tìm thấy dữ liệu xuất lưới tháng {$request['month']}/{$request['year']}");
            }

            // Đọc file Excel
            $excelData = $this->excelService->readExcel($request['file']);

            // Validate file có sheet 'Sheet1'
            if (!isset($excelData['Sheet1'])) {
                throw new Exception("File Excel không đúng cấu trúc! Thiếu sheet 'Sheet1'");
            }

            $sheetData = $excelData['Sheet1'];

            // Parse và validate data
            $parsedData = $this->parseCalculatedExcelForUpdate($sheetData);

            // Validate data
            $this->validateUpdateData($parsedData);

            // Load details
            $workTimesheet->load('details');

            // Map details theo name để dễ truy cập
            $detailsMap = $workTimesheet->details->keyBy('name');

            // Update từng user
            foreach ($parsedData as $data) {
                $name = $data['name'];

                // Kiểm tra user tồn tại
                if (!isset($detailsMap[$name]))
                    throw new Exception("Không tìm thấy nhân sự: {$name}");

                $detail = $detailsMap[$name];

                // Update 3 cột
                $detail->update([
                    'business_trip_manual_count' => $data['business_trip_manual_count'],
                    'council_rating' => $data['council_rating'],
                    'note' => $data['note'],
                ]);

                // Tính lại tiêu chí và mức trừ
                $this->calculateDeductionCriteria($detail, false);

                // Tính lại lương
                $this->calculateSalary($detail);
            }

            // Cập nhật lại file calculated Excel
            $this->createCaculatedExcel($workTimesheet);

            // cập nhật lại bảng lương
            $this->createPayrollExcel($workTimesheet);
        }, true);
    }

    /**
     * Parse file Excel calculated để lấy 3 cột cần update
     */
    private function parseCalculatedExcelForUpdate(array $sheetData): array
    {
        $result = [];

        // Bỏ qua header, bắt đầu từ dòng 1
        for ($i = 1; $i < count($sheetData); $i++) {
            $row = $sheetData[$i];

            // Bỏ qua dòng trống
            if (empty($row[1]))
                continue;

            // Kiểm tra xem có phải dòng phòng ban (La Mã) không
            $firstCol = trim($row[0] ?? '');
            if (preg_match('/^[IVX]+$/', $firstCol)) {
                // Đây là dòng phòng ban, bỏ qua
                continue;
            }

            // Lấy dữ liệu
            $name = trim($row[1] ?? '');  // Cột B: Họ tên

            // Bỏ qua nếu không có tên
            if (empty($name))
                continue;

            // Lấy 3 cột cần update theo index
            // Index trong file Excel created từ createCaculatedExcel:
            // 0: STT
            // 1: Họ tên
            // 2: Công BPDX
            // 3: Công chấm máy(Hợp lệ)
            // 4: Ngoài giờ
            // 5: Công tác (Đăng ký Hệ thống)
            // 6: Công tác (Không đăng ký Hệ thống) ✅
            // 7: Đã nghỉ phép
            // 8: Nghỉ phép
            // 9: Chấm công không hợp lệ
            // 10: Tỷ lệ không hợp lệ
            // 11: Thời gian trung bình muộn
            // 12: Cảnh báo
            // 13: Phòng Đánh giá ABC
            // 14: Hội đồng đánh giá ✅
            // 15: Ghi chú ✅

            $businessTripManualCount = isset($row[6]) ? trim($row[6]) : null;
            $councilRating = isset($row[14]) ? strtoupper(trim($row[14])) : null;
            $note = isset($row[15]) ? trim($row[15]) : null;

            $result[] = [
                'name' => $name,
                'business_trip_manual_count' => $businessTripManualCount,
                'council_rating' => $councilRating,
                'note' => $note,
            ];
        }

        return $result;
    }

    /**
     * Validate data update
     */
    private function validateUpdateData(array $data): void
    {
        $validRatings = ['A', 'B', 'C', 'D'];
        $errors = [];

        foreach ($data as $index => $item) {
            $rowNumber = $index + 2;  // +2 vì bắt đầu từ dòng 1 và có header

            // Validate business_trip_manual_count
            if ($item['business_trip_manual_count'] !== null && $item['business_trip_manual_count'] !== '') {
                if (!is_numeric($item['business_trip_manual_count'])) {
                    $errors[] = "Dòng {$rowNumber} ({$item['name']}): 'Công tác (Không đăng ký)' phải là số";
                } elseif ($item['business_trip_manual_count'] < 0) {
                    $errors[] = "Dòng {$rowNumber} ({$item['name']}): 'Công tác (Không đăng ký)' phải >= 0";
                }
            }

            // Validate council_rating - REQUIRED
            if (empty($item['council_rating'])) {
                $errors[] = "Dòng {$rowNumber} ({$item['name']}): 'Hội đồng đánh giá' không được để trống";
            } elseif (!in_array(strtoupper($item['council_rating']), $validRatings)) {
                $errors[] = "Dòng {$rowNumber} ({$item['name']}): 'Hội đồng đánh giá' chỉ chấp nhận: A, B, C, D";
            }

            // Validate note - REQUIRED
            if (mb_strlen($item['note']) > 255) {
                $errors[] = "Dòng {$rowNumber} ({$item['name']}): 'Ghi chú' không được vượt quá 255 ký tự";
            }
        }

        if (!empty($errors)) {
            throw new Exception("Dữ liệu không hợp lệ:\n" . implode("\n", $errors));
        }
    }

    private function createPayrollExcel(WorkTimesheet $record)
    {
        return $this->tryThrow(function () use ($record) {
            $record->load('details');  // Load lại details để đảm bảo có dữ liệu mới nhất
            if ($record['payroll_path'])
                $this->handlerUploadFileService->safeDeleteFile($record['payroll_path']);
            $record->update([
                'payroll_path' => $this->payrollService->renderExcel($record),
            ]);
            return $record;
        });
    }

    public function emailSchedule(bool $isActive, string $type, string $subject, array $emails, array $data)
    {
        if (!$isActive)
            return;

        $time = $this->workTimesheetOvertimeService->baseOvertimeUpload();
        $workTimesheet = $this->findByMonthYear($time['currentMonth'], $time['currentYear']);
        if (!$workTimesheet)
            throw new Exception("Tháng {$time['currentMonth']}/{$time['currentYear']} chưa có dữ liệu xuất lưới!");

        switch ($type) {
            case 'WORK_TIMESHEET_REPORT':
                $files = [
                    $this->handlerUploadFileService->getAbsolutePublicPath($workTimesheet['calculated_path'])
                ];
                break;
            case 'PAYROLL_REPORT_WORK_TIMESHEET':
                $files = [
                    $this->handlerUploadFileService->getAbsolutePublicPath($workTimesheet['payroll_path'])
                ];
                break;
            default:
                $files = [];
                break;
        }

        app(TaskScheduleService::class)->sendMail($subject, $emails, $data, $files);
    }
}
