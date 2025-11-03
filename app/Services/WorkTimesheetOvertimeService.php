<?php

namespace App\Services;

use App\Repositories\WorkTimesheetOvertimeRepository;
use Carbon\Carbon;
use Exception;

class WorkTimesheetOvertimeService extends BaseService
{
    public function __construct(
        private HandlerUploadFileService $handlerUploadFileService,
        private ExcelService $excelService,
        private UserService $userService,
        private DateService $dateService,
        private ArchiveService $archiveService,
        private WorkTimesheetOvertimeDetailService $workTimesheetOvertimeDetailService
    ) {
        $this->repository = app(WorkTimesheetOvertimeRepository::class);
    }

    public function baseOvertimeUpload()
    {
        $nowMonthYear = date('Y-n', strtotime('first day of last month'));
        $currentYear = (int) date('Y', strtotime($nowMonthYear));
        $currentMonth = (int) date('n', strtotime($nowMonthYear));
        return [
            'currentYear' => $currentYear,
            'currentMonth' => $currentMonth,
        ];
    }

    public function template(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $data = $this->repository->findByMonthYear($request['month'], $request['year']);

            // Lấy department_id từ auth
            $list_nhansu = $this->userService->list([
                'sort_by' => 'asc',
                'order_by' => 'position_id',
                'load_relations' => false,
                'is_salary_counted' => true,
                'department_id' => auth()->user()->department_id,
                'columns' => 'name',
            ]);

            $Y = $request['year'];
            $m = $request['month'];

            $array_day = $this->dateService->getDaysInMonth($m, $Y, [], 'd')->toArray();
            $countDay = count($array_day);

            // Tạo mảng thứ trong tuần
            $array_weekday = $this->buildWeekdayArray($array_day, $Y, $m);

            // Chuẩn bị data cho từng sheet
            $lable_month = "Tháng {$m} Năm {$Y}";

            // SHEET 1: TỐI
            $headerToi = $this->buildHeaderOvertime($array_day, $array_weekday, $countDay, true);
            $dataToi = $this->buildDataOvertime($list_nhansu, $countDay);

            // SHEET 2: T7 + CN
            $headerT7CN = $this->buildHeaderOvertime($array_day, $array_weekday, $countDay, true, true);
            $dataT7CN = $this->buildDataOvertimeT7CN($list_nhansu, $countDay);

            // SHEET 3: THEO DÕI
            $headerTheoDoi = $this->buildHeaderOvertime($array_day, $array_weekday, $countDay, false);
            $dataTheoDoi = $this->buildDataOvertime($list_nhansu, $countDay);

            // SHEET 4: ĐÁNH GIÁ CHUNG
            $headerDanhGia = [
                [
                    ['name' => 'TT', 'rowspan' => 1, 'colspan' => 1],
                    ['name' => 'Họ và tên', 'rowspan' => 1, 'colspan' => 1],
                    ['name' => 'Đánh giá', 'rowspan' => 1, 'colspan' => 1],
                ]
            ];
            $dataDanhGia = $this->buildDataRating($list_nhansu);

            // Tạo Excel bằng ExcelService
            $baseFileName = "BCC_T{$m}-{$Y}";
            $fileName = "$baseFileName.xlsx";
            $folder = "uploads/render/work-timesheets/overtime/{$Y}/{$m}";

            $sheets = $this->buildExcelSheets($lable_month, $countDay, $headerToi, $dataToi, $headerT7CN, $dataT7CN, $headerTheoDoi, $dataTheoDoi, $headerDanhGia, $dataDanhGia);

            $filePath = $this->excelService->createExcel($sheets, $folder, $fileName);

            $zipPath = $folder . "/$baseFileName.zip";
            $filesToZip = [];

            // File xuất lưới gốc (nếu có)
            if (isset($data['workTimesheet']['original_path'])) {
                $originalPath = $data['workTimesheet']['original_path'];
                $absolutePath = $this->handlerUploadFileService->getAbsolutePublicPath($originalPath);
                if (file_exists($absolutePath)) {
                    $filesToZip['xuat-luoi.xlsx'] = $originalPath;
                }
            }

            // File template vừa tạo
            $filesToZip[$baseFileName . '.xlsx'] = $filePath;

            $this->archiveService->compress($filesToZip, $zipPath);

            return asset($zipPath);
        });
    }

    public function upload(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            // Validate work_timesheet đã tồn tại chưa
            $workTimesheetService = app(WorkTimesheetService::class);
            $workTimesheet = $workTimesheetService->findByMonthYear($request['month'], $request['year']);

            if (!$workTimesheet)
                throw new Exception("Tháng {$request['month']}/{$request['year']} chưa có dữ liệu xuất lưới!");

            // Lấy department_id của auth user
            $authDepartmentId = auth()->user()->department_id;
            if (!$authDepartmentId) {
                throw new Exception('Tài khoản của bạn chưa được gán phòng ban!');
            }

            // Đọc file Excel
            $excelData = $this->excelService->readExcel($request['file']);

            // Validate cấu trúc file
            $this->validateRequiredSheets($excelData);

            // Parse dữ liệu từng sheet
            $eveningData = $this->parseOvertimeSheet($excelData['TỐI'], 'TỐI', $authDepartmentId);
            $weekendData = $this->parseWeekendSheet($excelData['T7 + CN'], $authDepartmentId);
            $trackingData = $this->parseTrackingSheet($excelData['THEO DÕI'], $request['month'], $request['year'], $authDepartmentId);
            $ratingData = $this->parseRatingSheet($excelData['Đánh Giá Chung'], $authDepartmentId);

            // Validate tổng hợp
            $this->validateOvertimeData($eveningData, $weekendData, $trackingData, $ratingData);

            // Kiểm tra và lưu path file cũ để xóa sau
            $overtime = $this->repository->findByMonthYear($request['month'], $request['year']);
            $oldFilePath = $overtime ? $overtime['path'] : null;

            // Lưu file mới
            $filePath = $this->handlerUploadFileService->storeAndRemoveOld(
                $request['file'],
                'render/work-timesheets/overtime',
                "{$request['year']}/{$request['month']}",
                null
            );

            // Tạo hoặc update WorkTimesheetOvertime
            if ($overtime) {
                $this->update([
                    'id' => $overtime['id'],
                    'path' => $filePath,
                ]);
            } else {
                $overtime = $this->store([
                    'work_timesheet_id' => $workTimesheet['id'],
                    'path' => $filePath,
                ]);
            }

            // Merge và sync selective (chỉ xóa user trong file)
            $mergedData = $this->mergeOvertimeData($eveningData, $weekendData, $trackingData, $ratingData);
            $uploadedUserIds = array_keys($mergedData);
            $this->syncOvertimeDetailsSelective($overtime, $mergedData, $uploadedUserIds);

            // Sau khi xử lý xong, xóa file cũ (nếu có và khác file mới)
            if ($oldFilePath && $oldFilePath !== $filePath) {
                $this->handlerUploadFileService->safeDeleteFile($oldFilePath);
            }

            return [
                'message' => 'Upload và xử lý dữ liệu thành công',
                'overtime_id' => $overtime['id'],
                'total_users' => count($mergedData),
            ];
        }, true);
    }

    /**
     * Tạo mảng thứ trong tuần
     */
    private function buildWeekdayArray(array $array_day, int $year, int $month): array
    {
        $array_weekday = [];
        foreach ($array_day as $day) {
            $date = Carbon::create($year, $month, $day);
            $weekday = $date->dayOfWeek;
            $array_weekday[] = $weekday == 0 ? 'CN' : ($weekday + 1);
        }
        return $array_weekday;
    }

    /**
     * Tìm user và validate department (logic chung)
     */
    private function findAndValidateUser(string $name, int $authDepartmentId, int $rowIndex, string $sheetName): array
    {
        $user = $this->userService->findByKey($name, 'name', false);

        if (!$user) {
            throw new Exception("Không tìm thấy user: {$name} ở dòng " . ($rowIndex + 1) . " sheet {$sheetName}");
        }

        // Validate department
        if ($user['department_id'] != $authDepartmentId) {
            throw new Exception(
                "User '{$name}' không thuộc phòng ban của bạn! (dòng " . ($rowIndex + 1) . " sheet {$sheetName})"
            );
        }

        return $user->toArray();
    }

    /**
     * Validate sheets required
     */
    private function validateRequiredSheets(array $excelData): void
    {
        $requiredSheets = ['TỐI', 'T7 + CN', 'THEO DÕI', 'Đánh Giá Chung'];
        foreach ($requiredSheets as $sheetName) {
            if (!isset($excelData[$sheetName]))
                throw new Exception("File thiếu sheet: {$sheetName}");
        }
    }

    /**
     * Validate tổng số công
     */
    private function validateTotalCount(int $counted, int $declared, string $userName, int $rowIndex, string $context = ''): void
    {
        if ($counted != $declared) {
            throw new Exception(
                "Sai lệch {$context} cho {$userName}: Đếm được {$counted} nhưng tổng là {$declared} (dòng " . ($rowIndex + 1) . ')'
            );
        }
    }

    /**
     * Đếm số ô có giá trị trong 1 dòng
     */
    private function countNonEmptyCells(array $row, int $startCol, int $endCol): int
    {
        $count = 0;
        for ($col = $startCol; $col < $endCol; $col++) {
            if (isset($row[$col]) && !empty($row[$col]) && trim($row[$col]) !== '') {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Prepare files to zip
     */
    private function prepareFilesToZip(array $files): array
    {
        $filesToZip = [];
        foreach ($files as $file) {
            $fileName = basename($file);
            $filesToZip[$fileName] = $file;
        }
        return $filesToZip;
    }

    /**
     * Build excel sheets config
     */
    private function buildExcelSheets(string $lable_month, int $countDay, array $headerToi, array $dataToi, array $headerT7CN, array $dataT7CN, array $headerTheoDoi, array $dataTheoDoi, array $headerDanhGia, array $dataDanhGia): array
    {
        return [
            // Sheet 1: TỐI
            (object) [
                'name' => 'TỐI',
                'header' => array_merge(
                    [
                        [['name' => 'BẢNG CHẤM CÔNG LÀM THÊM BUỔI TỐI', 'rowspan' => 1, 'colspan' => $countDay + 3]],
                        [['name' => $lable_month, 'rowspan' => 1, 'colspan' => $countDay + 3]],
                        [['name' => '', 'rowspan' => 1, 'colspan' => $countDay + 3]],
                    ],
                    $headerToi
                ),
                'data' => $dataToi,
                'boldRows' => [1, 2, 4, 5, 6],
                'centerColumns' => array_values(array_diff(range(1, $countDay + 3), [2])),
                'centerRows' => [1, 2, 4, 5, 6],
            ],
            // Sheet 2: T7 + CN
            (object) [
                'name' => 'T7 + CN',
                'header' => array_merge(
                    [
                        [['name' => 'BẢNG CHẤM CÔNG LÀM THÊM THỨ 7, CHỦ NHẬT', 'rowspan' => 1, 'colspan' => $countDay + 4]],
                        [['name' => $lable_month, 'rowspan' => 1, 'colspan' => $countDay + 4]],
                        [['name' => '', 'rowspan' => 1, 'colspan' => $countDay + 4]],
                    ],
                    $headerT7CN
                ),
                'data' => $dataT7CN,
                'boldRows' => [1, 2, 4, 5, 6],
                'centerColumns' => array_values(array_diff(range(1, $countDay + 4), [2])),
                'centerRows' => [1, 2, 4, 5, 6],
            ],
            // Sheet 3: THEO DÕI
            (object) [
                'name' => 'THEO DÕI',
                'header' => array_merge(
                    [
                        [['name' => 'BẢNG THEO DÕI GIỜ LÀM VIỆC CỦA NGƯỜI LAO ĐỘNG', 'rowspan' => 1, 'colspan' => $countDay + 3]],
                        [['name' => $lable_month, 'rowspan' => 1, 'colspan' => $countDay + 3]],
                        [['name' => '', 'rowspan' => 1, 'colspan' => $countDay + 3]],
                    ],
                    $headerTheoDoi
                ),
                'data' => $dataTheoDoi,
                'boldRows' => [1, 2, 4, 5, 6],
                'centerColumns' => array_values(array_diff(range(1, $countDay + 3), [2])),
                'centerRows' => [1, 2, 4, 5, 6],
            ],
            // Sheet 4: ĐÁNH GIÁ CHUNG
            (object) [
                'name' => 'Đánh Giá Chung',
                'header' => array_merge(
                    [
                        [['name' => 'ĐÁNH GIÁ CHUNG', 'rowspan' => 1, 'colspan' => 3]],
                        [['name' => $lable_month, 'rowspan' => 1, 'colspan' => 3]],
                        [['name' => '', 'rowspan' => 1, 'colspan' => 3]],
                    ],
                    $headerDanhGia
                ),
                'data' => $dataDanhGia,
                'boldRows' => [1, 2, 4],
                'centerColumns' => [1, 3],
                'centerRows' => [1, 2, 4],
            ],
        ];
    }

    // ============== BUILD DATA METHODS ==============

    /**
     * Build header cho các sheet overtime
     */
    private function buildHeaderOvertime($array_day, $array_weekday, $countDay, $withTotal = true, $withBuoi = false)
    {
        $header = [];
        $row1 = [];
        $row2 = [];
        $row3 = [];

        // TT
        $row1[] = ['name' => 'TT', 'rowspan' => 2, 'colspan' => 1];
        $row3[] = ['name' => 'A', 'rowspan' => 1, 'colspan' => 1];

        // Họ và tên
        $row1[] = ['name' => 'Họ và tên', 'rowspan' => 2, 'colspan' => 1];
        $row3[] = ['name' => 'B', 'rowspan' => 1, 'colspan' => 1];

        // Buổi (chỉ cho sheet T7+CN)
        if ($withBuoi) {
            $row1[] = ['name' => 'Buổi', 'rowspan' => 2, 'colspan' => 1];
            $row3[] = ['name' => 'C', 'rowspan' => 1, 'colspan' => 1];
        }

        // Ngày trong tháng
        $row1[] = ['name' => 'Ngày trong tháng', 'rowspan' => 1, 'colspan' => $countDay];

        foreach ($array_day as $index => $day) {
            $row2[] = ['name' => $array_weekday[$index], 'rowspan' => 1, 'colspan' => 1];
            $row3[] = ['name' => $day, 'rowspan' => 1, 'colspan' => 1];
        }

        // Tổng số công
        if ($withTotal) {
            $row1[] = ['name' => 'Số công làm thêm', 'rowspan' => 3, 'colspan' => 1];
        } else {
            $row1[] = ['name' => 'Tổng số ngày nghỉ phép không đăng ký', 'rowspan' => 3, 'colspan' => 1];
        }

        $header[] = $row1;
        $header[] = $row2;
        $header[] = $row3;

        return $header;
    }

    /**
     * Gộp buildDataOvertime
     */
    private function buildDataOvertime($list_nhansu, $countDay)
    {
        $data = [];
        foreach ($list_nhansu as $index => $ns) {
            $row = [$index + 1, $ns['name']];

            // Thêm các cột ngày trống + cột tổng
            for ($i = 0; $i <= $countDay; $i++) {
                $row[] = '';
            }

            $data[] = $row;
        }
        return $data;
    }

    /**
     * Build data cho sheet T7+CN
     */
    private function buildDataOvertimeT7CN($list_nhansu, $countDay)
    {
        $data = [];
        foreach ($list_nhansu as $index => $ns) {
            // Dòng Sáng
            $rowSang = [$index + 1, $ns['name'], 'Sáng'];
            for ($i = 0; $i <= $countDay; $i++) {
                $rowSang[] = '';
            }
            $data[] = $rowSang;

            // Dòng Chiều
            $rowChieu = [$index + 1, $ns['name'], 'Chiều'];
            for ($i = 0; $i <= $countDay; $i++) {
                $rowChieu[] = '';
            }
            $data[] = $rowChieu;
        }
        return $data;
    }

    /**
     * Tách riêng build data rating
     */
    private function buildDataRating($list_nhansu): array
    {
        $data = [];
        foreach ($list_nhansu as $index => $ns) {
            $data[] = [$index + 1, $ns['name'], ''];
        }
        return $data;
    }

    /**
     * Parse sheet chấm công tối
     */
    private function parseOvertimeSheet(array $sheetData, string $sheetName, int $authDepartmentId)
    {
        $result = [];
        $headerRowCount = 6;
        $countDay = count($sheetData[5]) - 3;

        for ($i = $headerRowCount; $i < count($sheetData); $i++) {
            $row = $sheetData[$i];
            if (empty($row[1]))
                continue;

            $name = trim($row[1]);
            $user = $this->findAndValidateUser($name, $authDepartmentId, $i, $sheetName);

            // Đếm số công
            $workDayCount = $this->countNonEmptyCells($row, 2, 2 + $countDay);

            // Validate tổng
            $totalColumn = 2 + $countDay;
            $declaredTotal = isset($row[$totalColumn]) ? (int) $row[$totalColumn] : 0;
            $this->validateTotalCount($workDayCount, $declaredTotal, $name, $i, 'dữ liệu chấm công');

            $result[$user['id']] = [
                'user_id' => $user['id'],
                'name' => $name,
                'count' => $workDayCount,
            ];
        }

        return $result;
    }

    /**
     * Parse sheet T7 + CN
     */
    private function parseWeekendSheet(array $sheetData, int $authDepartmentId)
    {
        $result = [];
        $headerRowCount = 6;
        $countDay = count($sheetData[5]) - 4;

        for ($i = $headerRowCount; $i < count($sheetData); $i += 2) {
            $rowSang = $sheetData[$i];
            $rowChieu = isset($sheetData[$i + 1]) ? $sheetData[$i + 1] : [];

            if (empty($rowSang[1]))
                continue;

            $name = trim($rowSang[1]);
            $user = $this->findAndValidateUser($name, $authDepartmentId, $i, 'T7 + CN');

            // Đếm công Sáng và Chiều
            $sangCount = $this->countNonEmptyCells($rowSang, 3, 3 + $countDay);
            $chieuCount = $this->countNonEmptyCells($rowChieu, 3, 3 + $countDay);
            $totalCount = $sangCount + $chieuCount;

            // Validate tổng
            $totalColumn = 3 + $countDay;
            $declaredTotalSang = isset($rowSang[$totalColumn]) ? (int) $rowSang[$totalColumn] : 0;
            $declaredTotalChieu = isset($rowChieu[$totalColumn]) ? (int) $rowChieu[$totalColumn] : 0;
            $declaredTotal = $declaredTotalSang + $declaredTotalChieu;

            $this->validateTotalCount($totalCount, $declaredTotal, $name, $i, "T7+CN (Sáng: {$sangCount}, Chiều: {$chieuCount})");

            $result[$user['id']] = [
                'user_id' => $user['id'],
                'name' => $name,
                'count' => $totalCount,
            ];
        }

        return $result;
    }

    /**
     * Parse sheet Theo dõi
     */
    private function parseTrackingSheet(array $sheetData, int $month, int $year, int $authDepartmentId)
    {
        $result = [];
        $headerRowCount = 6;
        $countDay = count($sheetData[5]) - 3;

        for ($i = $headerRowCount; $i < count($sheetData); $i++) {
            $row = $sheetData[$i];
            if (empty($row[1]))
                continue;

            $name = trim($row[1]);
            $user = $this->findAndValidateUser($name, $authDepartmentId, $i, 'THEO DÕI');

            // Đếm số ngày nghỉ không phép và format thành Y-m-d
            $leaveDays = [];
            for ($col = 2; $col < 2 + $countDay; $col++) {
                $cellValue = isset($row[$col]) ? trim($row[$col]) : '';
                if ($cellValue !== '' && $cellValue !== null) {
                    $dayNumber = $col - 1;
                    $leaveDays[] = Carbon::create($year, $month, $dayNumber)->format('Y-m-d');
                }
            }

            // Validate tổng
            $totalColumn = 2 + $countDay;
            $declaredTotal = isset($row[$totalColumn]) ? (int) $row[$totalColumn] : 0;
            $this->validateTotalCount(count($leaveDays), $declaredTotal, $name, $i, 'nghỉ không phép');

            $result[$user['id']] = [
                'user_id' => $user['id'],
                'name' => $name,
                'leave_count' => count($leaveDays),
                'leave_days' => $leaveDays,
            ];
        }

        return $result;
    }

    /**
     * Parse sheet Đánh giá chung
     */
    private function parseRatingSheet(array $sheetData, int $authDepartmentId)
    {
        $result = [];
        $headerRowCount = 4;
        $validRatings = ['A', 'B', 'C', 'D'];

        for ($i = $headerRowCount; $i < count($sheetData); $i++) {
            $row = $sheetData[$i];
            if (empty($row[1]))
                continue;

            $name = trim($row[1]);
            $user = $this->findAndValidateUser($name, $authDepartmentId, $i, 'Đánh Giá');

            $rating = isset($row[2]) ? strtoupper(trim($row[2])) : null;

            if (empty($rating)) {
                throw new Exception("Đánh giá không được để trống cho {$name} (dòng " . ($i + 1) . ')');
            }

            if (!in_array($rating, $validRatings)) {
                throw new Exception("Đánh giá không hợp lệ cho {$name}: '{$rating}'. Chỉ chấp nhận: A, B, C, D (dòng " . ($i + 1) . ')');
            }

            $result[$user['id']] = [
                'user_id' => $user['id'],
                'name' => $name,
                'rating' => $rating,
            ];
        }

        if (empty($result)) {
            throw new Exception('Sheet Đánh Giá không được để trống!');
        }

        return $result;
    }

    /**
     * Validate tổng hợp các sheet
     */
    private function validateOvertimeData($eveningData, $weekendData, $trackingData, $ratingData)
    {
        $ratingUserIds = array_keys($ratingData);

        if (empty($ratingUserIds)) {
            throw new Exception('Sheet Đánh Giá không có dữ liệu!');
        }

        // Tất cả user trong sheet Đánh Giá phải có mặt ở các sheet khác
        foreach ($ratingUserIds as $userId) {
            $userName = $ratingData[$userId]['name'];

            if (!isset($eveningData[$userId])) {
                throw new Exception("User '{$userName}' có trong sheet Đánh Giá nhưng thiếu ở sheet TỐI");
            }
            if (!isset($weekendData[$userId])) {
                throw new Exception("User '{$userName}' có trong sheet Đánh Giá nhưng thiếu ở sheet T7 + CN");
            }
            if (!isset($trackingData[$userId])) {
                throw new Exception("User '{$userName}' có trong sheet Đánh Giá nhưng thiếu ở sheet THEO DÕI");
            }
        }

        // Kiểm tra ngược lại
        $allOtherUserIds = array_unique(array_merge(
            array_keys($eveningData),
            array_keys($weekendData),
            array_keys($trackingData)
        ));

        foreach ($allOtherUserIds as $userId) {
            if (!isset($ratingData[$userId])) {
                $userName = $eveningData[$userId]['name'] ?? $weekendData[$userId]['name'] ?? $trackingData[$userId]['name'] ?? "ID: {$userId}";
                throw new Exception("User '{$userName}' có trong các sheet chấm công nhưng thiếu ở sheet Đánh Giá");
            }
        }
    }

    /**
     * Merge dữ liệu từ các sheet
     */
    private function mergeOvertimeData($eveningData, $weekendData, $trackingData, $ratingData)
    {
        $result = [];

        foreach ($ratingData as $userId => $ratingInfo) {
            $result[$userId] = [
                'user_id' => $userId,
                'overtime_evening_count' => $eveningData[$userId]['count'] ?? 0,
                'overtime_weekend_count' => $weekendData[$userId]['count'] ?? 0,
                'overtime_total_count' => ($eveningData[$userId]['count'] ?? 0) + ($weekendData[$userId]['count'] ?? 0),
                'leave_days_without_permission' => $trackingData[$userId]['leave_count'] ?? 0,
                'detail_leave_days_without_permission' => json_encode($trackingData[$userId]['leave_days'] ?? []),
                'department_rating' => $ratingInfo['rating'],
            ];
        }

        return $result;
    }

    /**
     * Sync chi tiết overtime - CHỈ xóa và cập nhật user có trong file upload
     */
    private function syncOvertimeDetailsSelective($overtime, array $mergedData, array $uploadedUserIds)
    {
        // Chỉ xóa những detail có user_id nằm trong file upload
        $existingDetails = $overtime->details ?? [];
        foreach ($existingDetails as $detail) {
            if (in_array($detail['user_id'], $uploadedUserIds)) {
                $this->workTimesheetOvertimeDetailService->delete($detail['id']);
            }
        }

        // Thêm details mới
        $details = [];
        foreach ($mergedData as $data) {
            $details[] = array_merge($data, [
                'work_timesheet_overtime_id' => $overtime['id'],
            ]);
        }

        if (!empty($details)) {
            $this->workTimesheetOvertimeDetailService->insert($details);
        }
    }
}
