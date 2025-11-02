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
        private ArchiveService $archiveService
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
            $files = [];
            if ($data)
                $files[] = $this->handlerUploadFileService->getAbsolutePublicPath($data['calculated_path']);

            $list_nhansu = $this->userService->list([
                'sort_by' => 'asc',
                'order_by' => 'position_id',
                'load_relations' => false,
                'is_salary_counted' => true,
                'columns' => 'name',
            ]);

            $Y = $request['year'];
            $m = $request['month'];

            $array_day = $this->dateService->getDaysInMonth($m, $Y, [], 'd')->toArray();
            $countDay = count($array_day);

            // Tạo mảng thứ trong tuần
            $array_weekday = [];
            foreach ($array_day as $day) {
                $date = Carbon::create($Y, $m, $day);
                $weekday = $date->dayOfWeek;
                $array_weekday[] = $weekday == 0 ? 'CN' : ($weekday + 1);
            }

            // Chuẩn bị data cho từng sheet
            $lable_month = "Tháng {$m} Năm {$Y}";

            // SHEET 1: TỐI
            $headerToi = $this->buildHeaderOvertime($array_day, $array_weekday, $countDay, true);
            $dataToi = $this->buildDataOvertime($list_nhansu, $countDay, true);

            // SHEET 2: T7 + CN
            $headerT7CN = $this->buildHeaderOvertime($array_day, $array_weekday, $countDay, true, true);
            $dataT7CN = $this->buildDataOvertimeT7CN($list_nhansu, $countDay);

            // SHEET 3: THEO DÕI
            $headerTheoDoi = $this->buildHeaderOvertime($array_day, $array_weekday, $countDay, false);
            $dataTheoDoi = $this->buildDataOvertime($list_nhansu, $countDay, false);

            // SHEET 4: ĐÁNH GIÁ CHUNG
            $headerDanhGia = [
                [
                    ['name' => 'TT', 'rowspan' => 1, 'colspan' => 1],
                    ['name' => 'Họ và tên', 'rowspan' => 1, 'colspan' => 1],
                    ['name' => 'Đánh giá', 'rowspan' => 1, 'colspan' => 1],
                ]
            ];
            $dataDanhGia = [];
            foreach ($list_nhansu as $index => $ns) {
                $dataDanhGia[] = [
                    $index + 1,
                    $ns['name'],
                    '',
                ];
            }

            // Tạo Excel bằng ExcelService
            $baseFileName = "BCC_T{$m}-{$Y}";
            $fileName = "$baseFileName.xlsx";
            $folder = "uploads/render/work-timesheets/overtime/{$Y}/{$m}";
            $filePath = $this->excelService->createExcel(
                [
                    // Sheet 1: TỐI
                    (object) [
                        'name' => 'TỐI',
                        'header' => array_merge(
                            [
                                [['name' => 'BẢNG CHẤM CÔNG LÀM THÊM BUỔI TỐI', 'rowspan' => 1, 'colspan' => $countDay + 3]],
                                [['name' => $lable_month, 'rowspan' => 1, 'colspan' => $countDay + 3]],
                                [['name' => '', 'rowspan' => 1, 'colspan' => $countDay + 3]],  // Dòng trống
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
                ],
                $folder,
                $fileName
            );

            $files[] = $this->handlerUploadFileService->getAbsolutePublicPath($filePath);
            $zipPath = $folder . "/$baseFileName.zip";

            $filesToZip = [];
            foreach ($files as $file) {
                $fileName = basename($file);
                $filesToZip[$fileName] = str_replace($this->handlerUploadFileService->getAbsolutePublicPath(''), '', $file);
            }

            $this->archiveService->compress($filesToZip, $zipPath);

            return asset($zipPath);
        });
    }

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

        // Tổng số công (nếu có)
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
     * Build data cho các sheet overtime thông thường
     */
    private function buildDataOvertime($list_nhansu, $countDay, $withTotal = true)
    {
        $data = [];
        foreach ($list_nhansu as $index => $ns) {
            $row = [
                $index + 1,
                $ns['name'],
            ];

            // Thêm các cột ngày trống
            for ($i = 0; $i < $countDay; $i++) {
                $row[] = '';
            }

            // Cột tổng
            $row[] = '';

            $data[] = $row;
        }

        return $data;
    }

    /**
     * Build data cho sheet T7+CN (có 2 dòng cho mỗi nhân sự: Sáng + Chiều)
     */
    private function buildDataOvertimeT7CN($list_nhansu, $countDay)
    {
        $data = [];
        foreach ($list_nhansu as $index => $ns) {
            // Dòng Sáng
            $rowSang = [
                $index + 1,
                $ns['name'],
                'Sáng',
            ];
            for ($i = 0; $i < $countDay; $i++) {
                $rowSang[] = '';
            }
            $rowSang[] = '';
            $data[] = $rowSang;

            // Dòng Chiều
            $rowChieu = [
                $index + 1,
                $ns['name'],
                'Chiều',
            ];
            for ($i = 0; $i < $countDay; $i++) {
                $rowChieu[] = '';
            }
            $rowChieu[] = '';
            $data[] = $rowChieu;
        }

        return $data;
    }

    public function upload(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $data = $this->repository->findByMonthYear($request['month'], $request['year']);
            if (!$data)
                throw new Exception("Tháng {$request['month']}/{$request['year']} chưa upload xuất lưới!");
        }, true);
    }
}
