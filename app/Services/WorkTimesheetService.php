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
        private UserService $userService
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

        $groupByName = collect($sheetData)->groupBy(1)->map(function ($value, $key) use ($record, $proposedWorkDays) {
            // tìm thông tin tài khoản
            $userInfo = $this->userService->findByKey($key, 'name', true, true, ['workSchedules'])->toArray();
            if (!$userInfo)
                throw new Exception("Không tìm thấy dữ liệu tài khoản của $key");

            // ko bặt tính lương thì cút
            if ($userInfo['is_salary_counted'] == 0)
                return null;

            // các ngày đi công tác trong tháng ko tính chủ nhật
            $businessTripDays = array_unique(Arr::flatten(collect($userInfo['work_schedules'])->filter(fn($i) => $i['approval_status'] == 'approved')->map(fn($i) => $this->dateService->getDatesInRange($i['from_date'], $i['to_date'], [0], 'Y-m-d', $record['month'], $record['year']))->toArray()));

            // lấy ra các ngày chấm công làm bù
            $compensatedDays = $value->filter(fn($i) => in_array($i[3], json_decode($record['days_details'], true)['compensated_days']))->toArray();

            // gom lại và lọc unique các ngày cần tính đi muộn về sớm
            $value = array_map('unserialize', array_unique(array_map('serialize', array_values(array_merge($value->toArray(), $compensatedDays)))));

            // tính đi sớm về muộn
            $lateEarly = $this->checkLateEarlyMultipleDays($value, $record['total_compensated_days'] > 0);

            dd($lateEarly);
            $detailWorkTimeSheetData = [
                'name' => $userInfo['name'] ?? 0,
                'department' => $userInfo['department']['name'] ?? 0,
                'position_id' => $userInfo['position_id'] ?? 0,
                'salary_level' => $userInfo['salary_level'] ?? 0,
                'violation_penalty' => $userInfo['violation_penalty'] ?? 0,
                'allowance_contact' => $userInfo['allowance_contact'] ?? 0,
                'allowance_position' => $userInfo['allowance_position'] ?? 0,
                'allowance_fuel' => $userInfo['allowance_fuel'] ?? 0,
                'allowance_transport' => $userInfo['allowance_transport'] ?? 0,
                'proposed_work_days' => $proposedWorkDays + count($businessTripDays),  // công bộ phận đề xuất cộng thêm số ngày công tác
                'valid_attendance_count' => $lateEarly['validAttendanceCount'] / 2, // Số lần chấm công hợp lệ
                'invalid_attendance_count' => 0, // Số lần chấm công không hợp lệ
                'invalid_attendance_rate' => 0, // Tỷ lệ chấm công không hợp lệ
                'late_morning_count' => $lateEarly['lateMorningCount'], // Số lần chấm công muộn buổi sáng
                'early_morning_count' => $lateEarly['earlyMorningCount'], // Số lần chấm công sớm buổi sáng
                'late_afternoon_count' => $lateEarly['lateAfternoonCount'], // Số lần chấm công muộn buổi chiều
                'early_afternoon_count' => $lateEarly['earlyAfternoonCount'], // Số lần chấm công sớm buổi chiều
                'avg_late_minutes' => collect($lateEarly)->except('validAttendanceCount')->avg(), // Trung bình phút chấm công muộn
            ];

            dd($detailWorkTimeSheetData);
        })->filter()->values()->toArray();
        dd($groupByName);
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
