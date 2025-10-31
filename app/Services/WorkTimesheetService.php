<?php

namespace App\Services;

use App\Models\WorkTimesheet;
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
        return $this->tryThrow(function () use ($request) {});
    }

    public function import(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            // tìm và xóa xuất lưới cũ cùng tháng
            $old = $this->findByMultipleKey([
                'month' => $request['month'],
                'year' => $request['year'],
            ]);
            $oldPath = $old['path'] ?? null;
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
                'path' => $this->handlerUploadFileService->storeAndRemoveOld($request['file'], 'work-timesheets', 'uploads'),
            ]);

            // đọc excel
            $this->readExcel($record);
            dd(0);

            $this->handlerUploadFileService->safeDeleteFile($oldPath);
        }, true);
    }

    private function readExcel(WorkTimesheet $record)
    {
        $dataExcel = $this->excelService->readExcel($record['path']);
        $sheetData = $dataExcel['Sheet1'] ?? [];
        if (empty($sheetData))
            throw new Exception("Không tìm thấy data trong 'Sheet1'");

        // cắt 2 dòng header của file excel
        array_splice($sheetData, 0, 2);

        // công bộ phận đề xuất: các ngày ko tính chủ nhật - nghỉ lễ - mất điện + làm bù + công tác (công tác ko tính chủ nhật cộng thêm trong khi map user bên dưới)
        $proposedWorkDays = count($this->dateService->getDaysInMonth($record['month'], $record['year'], [0])) - $record['total_holiday_days'] - $record['total_power_outage_days'] + $record['total_compensated_days'];

        // ngày đầu và ngày cuối của tháng
        $firstDay = $this->dateService->getFirstDayOfMonth($record['month'], $record['year']);
        $lastDay = $this->dateService->getLastDayOfMonth($record['month'], $record['year']);

        $details = collect($sheetData)->groupBy(1)->map(function ($value, $key) use ($record, $proposedWorkDays, $firstDay, $lastDay) {
            // tìm thông tin tài khoản
            $userInfo = $this->userService->findByKey($key, 'name', true, true, [
                'workSchedules' => fn($q) => $this->workScheduleService->getBaseQueryForDateRange($firstDay, $lastDay, ['approved'], $q),
                'leaveRequest' => fn($q) => $this->leaveRequestService->getBaseQueryForDateRange($firstDay, $lastDay, ['approved'], $q),
                'warning' => fn($q) => $q->where('warning_date', 'like', '%' . sprintf('%s-%02d', $record['year'], $record['month']) . '%'),
            ])->toArray();

            // báo lỗi nếu ko tìm thấy user
            if (!$userInfo)
                throw new Exception("Không tìm thấy dữ liệu tài khoản của $key");

            // ko bật tính lương thì cút
            if ($userInfo['is_salary_counted'] == 0)
                return null;

            // các ngày đi công tác trong tháng ko tính chủ nhật
            $businessTripDays = array_unique(Arr::flatten(collect($userInfo['work_schedules'])->map(fn($i) => $this->dateService->getDatesInRange($i['from_date'], $i['to_date'], [0], 'Y-m-d', $record['month'], $record['year']))->toArray()));
            $businessTripDayCount = count($businessTripDays);

            // công bộ phận đề xuất cộng thêm số ngày công tác
            $proposedWorkDays += $businessTripDayCount;

            // lấy ra mảng chấm công các ngày làm bù
            $compensatedDays = $value->filter(fn($i) => in_array($i[3], json_decode($record['days_details'], true)['compensated_days']))->toArray();

            // gom lại và lọc unique các ngày cần tính đi muộn về sớm
            $value = array_map('unserialize', array_unique(array_map('serialize', array_values(array_merge($value->toArray(), $compensatedDays)))));

            // tính đi muộn về sớm
            $lateEarly = $this->checkLateEarlyMultipleDays($value, $record['total_compensated_days'] > 0);

            // công hợp lệ
            $validAttendanceCount = $lateEarly['validAttendanceCount'] / 2;

            $avgLateMinutes = 0;
            $isLatestArrival = false;
            $note = null;

            // khi có chấm công hoặc có công tác thì tính trung binh muộn
            if ($validAttendanceCount > 0 || $businessTripDayCount > 0) {
                // tính trung bình muộn: round(tổng 4 tiêu chí chấm muộn / (công hợp lệ + công tác), 2)
                $avgLateMinutes = round(collect($lateEarly)->except('validAttendanceCount')->sum() / ($validAttendanceCount + $businessTripDayCount), 2);

                // nếu có con nhỏ cho đi muộn về sớm 30p
                if ($userInfo['is_childcare_mode'] == 1)
                    $avgLateMinutes -= 60;

                // nếu quá trung bình muộn quá 15p đánh giá top muộn
                if ($avgLateMinutes > 15) {
                    $isLatestArrival = true;
                    $note = 'Muộn quá 15 phút';
                }
            }

            // nếu công đi công tác quá công bộ phận đề xuất thì set = công bộ phận đề xuất
            $businessTripDayCount = $businessTripDayCount > $proposedWorkDays ? $proposedWorkDays : $businessTripDayCount;

            // tính lại số ngày nghỉ thực tế khi có nghỉ lễ
            $compensatedDays = json_decode($record['days_details'], true)['holiday_days'];
            $leaveDays = $userInfo['leave_request'] ?? [];
            if (count($compensatedDays) > 0)
                $leaveDays = array_map(function ($i) use ($record, $compensatedDays) {
                    $i['range'] = $this->dateService->getDatesInRange($i['from_date'], $i['to_date'], [0], 'Y-m-d', $record['month'], $record['year'])->toArray();
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

            // số lần chấm công ko hợp lệ: công bộ phận đề xuất - chấm hợp lệ - công tác - nghỉ phép
            $invalidAttendanceCount = $proposedWorkDays - $validAttendanceCount - $businessTripDayCount - $leaveDaysWithPermission;
            // tỷ lệ chấm công ko hợp lệ: round(lấy số lần chênh / công bộ phận đề xuất * 100, 2)
            $invalidAttendanceRate = round($invalidAttendanceCount / $proposedWorkDays * 100, 2);

            // mức lương ngoài giờ
            $salaryLevel = $userInfo['salary_level'] ?? 0;
            $overtimeSalaryRate = round($salaryLevel / $proposedWorkDays, 0);

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
            if ($isLatestArrival)
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

            return [
                'user_id' => $userInfo['id'],
                'name' => $userInfo['name'] ?? 0,
                'department' => $userInfo['department']['name'] ?? 0,
                'position_id' => $userInfo['position_id'] ?? 0,
                'salary_level' => $salaryLevel,  // Mức lương
                'violation_penalty' => $violationPenalty,  // Mức tiêu chí
                'allowance_contact' => $userInfo['allowance_contact'] ?? 0,  // Phụ cấp liên lạc
                'allowance_position' => $userInfo['allowance_position'] ?? 0,  // Phụ cấp chức vụ
                'allowance_fuel' => $userInfo['allowance_fuel'] ?? 0,  // Phụ cấp xăng xe
                'allowance_transport' => $userInfo['allowance_transport'] ?? 0,  // Phụ cấp đi lại
                'proposed_work_days' => $proposedWorkDays,  // công bộ phận đề xuất
                'valid_attendance_count' => $validAttendanceCount,  // Số lần chấm công hợp lệ
                'invalid_attendance_count' => $invalidAttendanceCount,  // Số lần chấm công không hợp lệ
                'invalid_attendance_rate' => $invalidAttendanceRate,  // Tỷ lệ chấm công không hợp lệ
                'late_morning_count' => $lateEarly['lateMorningCount'],  // Số lần chấm công muộn buổi sáng
                'early_morning_count' => $lateEarly['earlyMorningCount'],  // Số lần chấm công sớm buổi sáng
                'late_afternoon_count' => $lateEarly['lateAfternoonCount'],  // Số lần chấm công muộn buổi chiều
                'early_afternoon_count' => $lateEarly['earlyAfternoonCount'],  // Số lần chấm công sớm buổi chiều
                'avg_late_minutes' => $avgLateMinutes,  // Trung bình phút chấm công muộn
                'overtime_salary_rate' => $overtimeSalaryRate,  // Mức lương ngoài giờ (mức lương / công bộ phận đề xuất) / 2
                'overtime_evening_count' => 0,  // Số công ngoài giờ buổi tối ===============> đợi các phòng đẩy công
                'overtime_weekend_count' => 0,  // Số công ngoài giờ T7 CN ===============> đợi các phòng đẩy công
                'overtime_total_count' => 0,  // Tổng số công ngoài giờ ===============> đợi các phòng đẩy công
                'overtime_total_amount' => 0,  // Tổng tiền công ngoài giờ ===============> đợi các phòng đẩy công
                'business_trip_system_count' => $businessTripDayCount,  // Tổng số công đi công tác - hệ thống tính
                'business_trip_manual_count' => 0,  // Tổng số công đi công tác ==>>>>>>>>>>>>>>>> Rà soát đẩy thủ công
                'leave_days_with_permission' => $leaveDaysWithPermission,  // Tổng số ngày nghỉ phép
                'leave_days_without_permission' => 0,  // Tổng số ngày nghỉ không phép ===============> đợi các phòng đẩy công
                'warning_count' => $warningCount,  // Tổng số lần bị cảnh báo
                'department_rating' => null,  // Đánh giá của phòng ===============> đợi các phòng đẩy công
                'council_rating' => null,  // Đánh giá của hội đồng ==>>>>>>>>>>>>>>>> Rà soát đẩy thủ công
                'is_latest_arrival' => $isLatestArrival,  // Top muộn
                'rule_b_count' => $ruleBCount,  // Số lần bị đánh giá nội quy B
                'rule_c_count' => $ruleCCount,  // Số lần bị đánh giá nội quy C
                'training_a_count' => 0,  // Số lần đánh giá đào tạo A
                'training_b_count' => $trainingBCount,  // Số lần bị đánh giá đào tạo B
                'training_c_count' => $trainingCCount,  // Số lần bị đánh giá đào tạo đào tạo C
                'deduction_amount' => $deductionAmount,  // Số tiền trừ
                'total_received_salary' => 0,  // Tổng lương nhận
                'detail_business_trip_and_leave_days' => json_encode([
                    'business_trip_days' => $businessTripDays,
                    'leave_days' => Arr::flatten(collect($leaveDays)->map(fn($i) => $i['range'])->toArray()),
                ]),  // Mảng các ngày công tác và nghỉ trong tháng
                'note' => $note,  // Ghi chú ==>>>>>>>>>>>>>>>> Rà soát đẩy thủ công
            ];
        })->filter()->values()->toArray();

        $record->details()->createMany($details);

        return $details;
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
}
