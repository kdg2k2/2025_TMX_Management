<?php

namespace App\Services;

use App\Models\WorkTimesheet;
use Exception;

class PayrollService extends BaseService
{
    public function __construct(
        private UserService $userService,
        private ExcelService $excelService,
        private HandlerUploadFileService $handlerUploadFileService,
        private StringHandlerService $stringHandlerService,
    ) {}

    public function baseIndexData()
    {
        return app(WorkTimesheetService::class)->baseIndexData();
    }

    public function update(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $month = $request['month'];
            $year = $request['year'];

            // Validate đã có xuất lưới và bảng lương
            $workTimesheetService = app(WorkTimesheetService::class);
            $workTimesheet = $workTimesheetService->findByMonthYear($month, $year);

            if (!$workTimesheet)
                throw new Exception("Chưa upload xuất lưới tháng {$month}/{$year}");

            if (!$workTimesheet['payroll_path'])
                throw new Exception("Chưa có bảng lương tháng {$month}/{$year}");

            // Đọc file Excel
            $excelData = $this->excelService->readExcel($request['file']);

            // Validate đúng file gốc (có đủ 6 sheets)
            $requiredSheets = ['Bank', 'Lương Cơ Hữu', 'Lương CTV', 'Thêm Giờ Cơ Hữu', 'Tiêu Chí ABC', 'Tổng Thu Nhập'];
            foreach ($requiredSheets as $sheetName) {
                if (!isset($excelData[$sheetName])) {
                    throw new Exception("File không đúng định dạng! Thiếu sheet: {$sheetName}");
                }
            }

            // Parse sheet "Tiêu Chí ABC"
            $tieuChiData = $this->parseTieuChiABCSheet($excelData['Tiêu Chí ABC']);

            // Load details để validate và update
            $workTimesheet->load('details');
            $detailsMap = $workTimesheet->details->keyBy('name');

            $errors = [];

            foreach ($tieuChiData as $data) {
                $name = $data['hoten'];

                // Validate user tồn tại
                if (!isset($detailsMap[$name])) {
                    $errors[] = "Không tìm thấy nhân viên: {$name}";
                    continue;
                }

                $detail = $detailsMap[$name];

                // Validate số tiền tổng
                $expectedTotal = ($data['tru_dg_cv'] ?? 0) + ($data['tru_dg_noiquy'] ?? 0) + ($data['tru_dg_daotao'] ?? 0);
                if ($data['tong'] != $expectedTotal) {
                    $errors[] = "Sai tổng tiền trừ cho {$name}: Tính được {$expectedTotal} nhưng tổng là {$data['tong']}";
                    continue;
                }

                // Update TẤT CẢ các cột từ 4-12
                $detail->update([
                    'job_deduction_amount' => $data['tru_dg_cv'],
                    'rule_b_count' => $data['noiquy_b'],
                    'rule_c_count' => $data['noiquy_c'],
                    'rule_d_count' => $data['noiquy_d'],
                    'rule_deduction_amount' => $data['tru_dg_noiquy'],
                    'training_b_count' => $data['training_b_count'],
                    'training_c_count' => $data['training_c_count'],
                    'training_deduction_amount' => $data['tru_dg_daotao'],
                    'deduction_amount' => $data['tong'],
                ]);

                // tính lương
                $workTimesheetService->calculateSalary($detail);
            }

            if (!empty($errors)) {
                throw new Exception("Có lỗi khi cập nhật:\n" . implode("\n", $errors));
            }

            // Tạo lại file Excel bảng lương
            $newFilePath = $this->renderExcel($workTimesheet);

            // Cập nhật path mới
            $workTimesheet->update(['payroll_path' => $newFilePath]);
        }, true);
    }

    /**
     * Parse sheet "Tiêu Chí ABC" - Lấy tất cả cột từ 4-12
     */
    private function parseTieuChiABCSheet(array $sheetData): array
    {
        $result = [];
        $headerRowCount = 2;

        for ($i = $headerRowCount; $i < count($sheetData); $i++) {
            $row = $sheetData[$i];

            if (empty($row[1]))
                continue;

            $name = trim($row[1]);

            // Lấy TẤT CẢ cột từ 4-12
            // 0: TT
            // 1: Họ tên
            // 2: Mức tính tiêu chí
            // 3: Đánh giá công việc
            // 4: Trừ tiêu chí (đánh giá CV) ✅
            // 5: Tiêu chí nội quy B ✅
            // 6: Tiêu chí nội quy C ✅
            // 7: Tiêu chí nội quy D ✅
            // 8: Trừ tiêu chí (nội quy) ✅
            // 9: Tiêu chí đào tạo B ✅
            // 10: Tiêu chí đào tạo C ✅
            // 11: Trừ tiêu chí (đào tạo) ✅
            // 12: Tổng ✅

            $result[] = [
                'hoten' => $name,
                'tru_dg_cv' => isset($row[4]) ? (int) str_replace(',', '', $row[4]) : 0,
                'noiquy_b' => isset($row[5]) ? (int) str_replace(',', '', $row[5]) : 0,
                'noiquy_c' => isset($row[6]) ? (int) str_replace(',', '', $row[6]) : 0,
                'noiquy_d' => isset($row[7]) ? (int) str_replace(',', '', $row[7]) : 0,
                'tru_dg_noiquy' => isset($row[8]) ? (int) str_replace(',', '', $row[8]) : 0,
                'training_b_count' => isset($row[9]) ? (int) str_replace(',', '', $row[9]) : 0,
                'training_c_count' => isset($row[10]) ? (int) str_replace(',', '', $row[10]) : 0,
                'tru_dg_daotao' => isset($row[11]) ? (int) str_replace(',', '', $row[11]) : 0,
                'tong' => isset($row[12]) ? (int) str_replace(',', '', $row[12]) : 0,
            ];
        }

        if (empty($result)) {
            throw new Exception('Sheet "Tiêu Chí ABC" không có dữ liệu!');
        }

        return $result;
    }

    public function renderExcel(WorkTimesheet $record)
    {
        return $this->tryThrow(function () use ($record) {
            $month = $record['month'];
            $year = $record['year'];

            // Phân loại nhân viên
            $workTimesheetDetails = collect($record->details)->sortBy('position_id')->values();
            $permanentUsers = $workTimesheetDetails->where('position_id', '!=', 6)->values();
            $collaborators = $workTimesheetDetails->where('position_id', 6)->values();

            // Chuẩn bị data cho từng sheet
            $data_sheetBank = $this->SheetBank($permanentUsers);
            $data_sheetLuongCoHuu = $this->SheetLuongCoHuu($permanentUsers);
            $data_sheetThemGioCoHuu = $this->SheetThemGioCoHuu($permanentUsers);
            $data_sheetLuongCTV = $this->SheetLuongCTV($collaborators);
            $data_TieuChiABC = $this->SheetTieuChiABC($workTimesheetDetails);
            $data_TongThuNhap = $this->SheetTongThuNhap($workTimesheetDetails);

            // Labels
            $lable_ngaythang = 'Ngày ' . date('d') . ' tháng ' . date('m') . ' năm ' . date('Y');
            $lable_luong = 'BẢNG THANH TOÁN TIỀN LƯƠNG';
            $lable_date = "Tháng {$month} năm {$year}";
            $header1 = config('company-info.name');
            $header2 = 'Đ/c: ' . config('company-info.address');

            // Tạo Excel với các sheet
            $sheets = [
                $this->buildSheetBank($data_sheetBank, $header1, $header2, $lable_luong, $lable_date, $lable_ngaythang),
                $this->buildSheetLuongCoHuu($data_sheetLuongCoHuu, $header1, $header2, $lable_luong, $lable_date, $lable_ngaythang),
                $this->buildSheetLuongCTV($data_sheetLuongCTV, $header1, $header2, $lable_luong, $lable_date, $lable_ngaythang),
                $this->buildSheetThemGioCoHuu($data_sheetThemGioCoHuu, $header1, $header2, $lable_luong, $lable_date, $lable_ngaythang),
                $this->buildSheetTieuChiABC($data_TieuChiABC),
                $this->buildSheetTongThuNhap($data_TongThuNhap),
            ];

            $fileName = "XuatLuong_T{$month}-{$year}.xlsx";
            $folder = "uploads/render/payroll/{$year}/{$month}";

            $filePath = $this->excelService->createExcel($sheets, $folder, $fileName);

            return $filePath;
        });
    }

    /**
     * Build Sheet Bank
     */
    private function buildSheetBank(array $data = [], string $header1 = null, string $header2 = null, string $lable_luong = null, string $lable_date = null, string $lable_ngaythang = null)
    {
        $lastRow = count($data) - 1;
        $tongtien = $data[$lastRow]['thanhtien'];
        $tienchu = 'Số tiền bằng chữ: ' . $this->stringHandlerService->convertNumberToVietnameseCurrency($tongtien);

        $header = [
            [['name' => $header1, 'rowspan' => 1, 'colspan' => 5]],
            [['name' => $header2, 'rowspan' => 1, 'colspan' => 5]],
            [['name' => '', 'rowspan' => 1, 'colspan' => 5]],
            [['name' => $lable_luong, 'rowspan' => 1, 'colspan' => 5]],
            [['name' => $lable_date, 'rowspan' => 1, 'colspan' => 5]],
            [['name' => '', 'rowspan' => 1, 'colspan' => 5]],
            [
                ['name' => 'TT', 'rowspan' => 1, 'colspan' => 1],
                ['name' => 'Họ và tên', 'rowspan' => 1, 'colspan' => 1],
                ['name' => 'Thực lĩnh', 'rowspan' => 1, 'colspan' => 1],
                ['name' => 'Số tài khoản', 'rowspan' => 1, 'colspan' => 1],
                ['name' => 'Ngân hàng', 'rowspan' => 1, 'colspan' => 1],
            ],
        ];

        $sheetData = [];
        foreach ($data as $item) {
            $sheetData[] = [
                $item['stt'],
                $item['hoten'],
                $item['thanhtien'],
                $item['stk'],
                $item['nganhang'],
            ];
        }

        // Thêm footer
        $footerStartRow = count($header) + count($sheetData) + 1;
        $sheetData[] = [];  // Dòng trống
        $sheetData[] = [$tienchu, '', '', '', ''];  // Dòng số tiền bằng chữ
        $sheetData[] = [];  // Dòng trống
        $sheetData[] = ['', '', '', $lable_ngaythang, ''];
        $sheetData[] = ['Kế toán trưởng', '', '', 'Thủ trưởng', ''];
        $sheetData[] = [];  // Dòng trống
        $sheetData[] = [];  // Dòng trống
        $sheetData[] = [];  // Dòng trống
        $sheetData[] = [];  // Dòng trống
        $sheetData[] = [];  // Dòng trống
        $sheetData[] = [$this->userService->getChiefAccountantUser()['name'] ?? '', '', '', $this->userService->getManagertUser()['name'] ?? '', ''];

        return (object) [
            'name' => 'Bank',
            'header' => $header,
            'data' => $sheetData,
            'boldRows' => [1, 4, 7, count($header) + $lastRow + 1, $footerStartRow + 1, $footerStartRow + 4, $footerStartRow + 10],
            'italicRows' => [$footerStartRow + 1],
            'centerColumns' => [1, 3, 4],
            'centerRows' => [1, 2, 4, 5, 7, $footerStartRow + 3, $footerStartRow + 4, $footerStartRow + 10],
            'leftRows' => [$footerStartRow + 1],  // Thêm căn trái cho dòng số tiền bằng chữ
        ];
    }

    /**
     * Build Sheet Lương Cơ Hữu
     */
    private function buildSheetLuongCoHuu(array $data = [], string $header1 = null, string $header2 = null, string $lable_luong = null, string $lable_date = null, string $lable_ngaythang = null)
    {
        $lastRow = count($data) - 1;
        $tongtien = $data[$lastRow]['tongnhan'];
        $tienchu = 'Số tiền bằng chữ: ' . $this->stringHandlerService->convertNumberToVietnameseCurrency($tongtien);

        $header = [
            [['name' => $header1, 'rowspan' => 1, 'colspan' => 18]],
            [['name' => $header2, 'rowspan' => 1, 'colspan' => 18]],
            [['name' => '', 'rowspan' => 1, 'colspan' => 18]],
            [['name' => $lable_luong, 'rowspan' => 1, 'colspan' => 18]],
            [['name' => $lable_date, 'rowspan' => 1, 'colspan' => 18]],
            [['name' => '', 'rowspan' => 1, 'colspan' => 18]],
            [
                ['name' => 'TT', 'rowspan' => 1, 'colspan' => 1],
                ['name' => 'Họ tên', 'rowspan' => 1, 'colspan' => 1],
                ['name' => 'Xếp loại', 'rowspan' => 1, 'colspan' => 1],
                ['name' => 'Mức lương', 'rowspan' => 1, 'colspan' => 1],
                ['name' => 'Ngày công thực tế', 'rowspan' => 1, 'colspan' => 1],
                ['name' => 'BHXH (8%)', 'rowspan' => 1, 'colspan' => 1],
                ['name' => 'BHYT (1.5%)', 'rowspan' => 1, 'colspan' => 1],
                ['name' => 'BHTN (1%)', 'rowspan' => 1, 'colspan' => 1],
                ['name' => 'Tổng khấu trừ', 'rowspan' => 1, 'colspan' => 1],
                ['name' => 'PC liên lạc', 'rowspan' => 1, 'colspan' => 1],
                ['name' => 'PC ăn ca', 'rowspan' => 1, 'colspan' => 1],
                ['name' => 'PC trách nhiệm', 'rowspan' => 1, 'colspan' => 1],
                ['name' => 'PC xăng xe', 'rowspan' => 1, 'colspan' => 1],
                ['name' => 'PC đi lại', 'rowspan' => 1, 'colspan' => 1],
                ['name' => 'Lương nhận', 'rowspan' => 1, 'colspan' => 1],
                ['name' => 'Trừ tiêu chí', 'rowspan' => 1, 'colspan' => 1],
                ['name' => 'Thành tiền', 'rowspan' => 1, 'colspan' => 1],
                ['name' => 'Tổng nhận', 'rowspan' => 1, 'colspan' => 1],
                ['name' => 'Ghi chú', 'rowspan' => 1, 'colspan' => 1],
            ],
        ];

        $sheetData = [];
        foreach ($data as $item) {
            $sheetData[] = [
                $item['tt'],
                $item['hoten'],
                $item['xeploai'],
                $item['mucluong'],
                $item['ngaycong'],
                $item['bhxh'],
                $item['bhyt'],
                $item['bhtn'],
                $item['tongkhautru'],
                $item['pc_lienlac'],
                $item['pc_anca'],
                $item['pc_chucvu'],
                $item['pc_xangxe'],
                $item['pc_dilai'],
                $item['luongnhan'],
                $item['trutien'],
                $item['thanhtien'],
                $item['tongnhan'],
                '',
            ];
        }

        $footerStartRow = count($header) + count($sheetData) + 1;
        $sheetData[] = [];
        $sheetData[] = [$tienchu, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''];
        $sheetData[] = [];
        $sheetData[] = ['', '', '', '', '', '', '', '', '', '', '', '', $lable_ngaythang, '', '', '', '', ''];
        $sheetData[] = ['Người lập biểu', '', '', '', '', '', 'Kế toán trưởng', '', '', '', '', 'Thủ trưởng', '', '', '', '', '', ''];
        $sheetData[] = [];
        $sheetData[] = [];
        $sheetData[] = [];
        $sheetData[] = [];
        $sheetData[] = [];
        $sheetData[] = [$this->userService->getGeneralAccountingUser()['name'] ?? '', '', '', '', '', '', $this->userService->getChiefAccountantUser()['name'] ?? '', '', '', '', '', $this->userService->getManagertUser()['name'] ?? '', '', '', '', '', '', ''];

        return (object) [
            'name' => 'Lương Cơ Hữu',
            'header' => $header,
            'data' => $sheetData,
            'boldRows' => [1, 4, 7, count($header) + $lastRow + 1, $footerStartRow + 1, $footerStartRow + 4, $footerStartRow + 10],
            'italicRows' => [$footerStartRow + 1],
            'centerColumns' => [1, 3],
            'centerRows' => [1, 2, 4, 5, 7, $footerStartRow + 3, $footerStartRow + 4, $footerStartRow + 10],
            'leftRows' => [$footerStartRow + 1],  // Căn trái dòng số tiền bằng chữ
        ];
    }

    /**
     * Build Sheet Lương CTV
     */
    private function buildSheetLuongCTV(array $data = [], string $header1 = null, string $header2 = null, string $lable_luong = null, string $lable_date = null, string $lable_ngaythang = null)
    {
        $lastRow = count($data) - 1;
        $tongtien = $data[$lastRow]['tongnhan'];
        $tienchu = 'Số tiền bằng chữ: ' . $this->stringHandlerService->convertNumberToVietnameseCurrency($tongtien);

        $header = [
            [['name' => $header1, 'rowspan' => 1, 'colspan' => 11]],
            [['name' => $header2, 'rowspan' => 1, 'colspan' => 11]],
            [['name' => '', 'rowspan' => 1, 'colspan' => 11]],
            [['name' => $lable_luong, 'rowspan' => 1, 'colspan' => 11]],
            [['name' => $lable_date, 'rowspan' => 1, 'colspan' => 11]],
            [['name' => '', 'rowspan' => 1, 'colspan' => 11]],
            [
                ['name' => 'TT', 'rowspan' => 1, 'colspan' => 1],
                ['name' => 'Họ tên', 'rowspan' => 1, 'colspan' => 1],
                ['name' => 'Xếp loại', 'rowspan' => 1, 'colspan' => 1],
                ['name' => 'Mức lương', 'rowspan' => 1, 'colspan' => 1],
                ['name' => 'Ngày công thực tế', 'rowspan' => 1, 'colspan' => 1],
                ['name' => 'Lương nhận', 'rowspan' => 1, 'colspan' => 1],
                ['name' => 'Công thêm giờ', 'rowspan' => 1, 'colspan' => 1],
                ['name' => 'Tiền lương thêm giờ', 'rowspan' => 1, 'colspan' => 1],
                ['name' => 'Trừ tiêu chí', 'rowspan' => 1, 'colspan' => 1],
                ['name' => 'Thành tiền', 'rowspan' => 1, 'colspan' => 1],
                ['name' => 'Tổng nhận', 'rowspan' => 1, 'colspan' => 1],
                ['name' => 'Ký nhận', 'rowspan' => 1, 'colspan' => 1],
            ],
        ];

        $sheetData = [];
        foreach ($data as $item) {
            $sheetData[] = [
                $item['tt'],
                $item['hoten'],
                $item['xeploai'],
                $item['mucluong'],
                $item['congthucte'],
                $item['luongnhan'],
                $item['themgio'],
                $item['luongthem'],
                $item['trutien'],
                $item['thanhtien'],
                $item['tongnhan'],
                '',
            ];
        }

        $footerStartRow = count($header) + count($sheetData) + 1;
        $sheetData[] = [];
        $sheetData[] = [$tienchu, '', '', '', '', '', '', '', '', '', ''];
        $sheetData[] = [];
        $sheetData[] = ['', '', '', '', '', '', '', '', $lable_ngaythang, '', ''];
        $sheetData[] = ['Người lập biểu', '', '', '', 'Kế toán trưởng', '', '', '', 'Thủ trưởng', '', ''];
        $sheetData[] = [];
        $sheetData[] = [];
        $sheetData[] = [];
        $sheetData[] = [];
        $sheetData[] = [];
        $sheetData[] = [$this->userService->getGeneralAccountingUser()['name'] ?? '', '', '', '', $this->userService->getChiefAccountantUser()['name'] ?? '', '', '', '', $this->userService->getManagertUser()['name'] ?? '', '', ''];

        return (object) [
            'name' => 'Lương CTV',
            'header' => $header,
            'data' => $sheetData,
            'boldRows' => [1, 4, 7, count($header) + $lastRow + 1, $footerStartRow + 1, $footerStartRow + 4, $footerStartRow + 10],
            'italicRows' => [$footerStartRow + 1],
            'centerColumns' => [1, 3],
            'centerRows' => [1, 2, 4, 5, 7, $footerStartRow + 3, $footerStartRow + 4, $footerStartRow + 10],
            'leftRows' => [$footerStartRow + 1],  // Căn trái dòng số tiền bằng chữ
        ];
    }

    /**
     * Build Sheet Thêm Giờ Cơ Hữu
     */
    private function buildSheetThemGioCoHuu(array $data = [], string $header1 = null, string $header2 = null, string $lable_luong = null, string $lable_date = null, string $lable_ngaythang = null)
    {
        $lastRow = count($data) - 1;
        $tongtien = $data[$lastRow]['thanhtien'];
        $tienchu = 'Số tiền bằng chữ: ' . $this->stringHandlerService->convertNumberToVietnameseCurrency($tongtien);

        $header = [
            [['name' => $header1, 'rowspan' => 1, 'colspan' => 8]],
            [['name' => $header2, 'rowspan' => 1, 'colspan' => 8]],
            [['name' => '', 'rowspan' => 1, 'colspan' => 8]],
            [['name' => $lable_luong, 'rowspan' => 1, 'colspan' => 8]],
            [['name' => $lable_date, 'rowspan' => 1, 'colspan' => 8]],
            [['name' => '', 'rowspan' => 1, 'colspan' => 8]],
            [
                ['name' => 'TT', 'rowspan' => 1, 'colspan' => 1],
                ['name' => 'Họ tên', 'rowspan' => 1, 'colspan' => 1],
                ['name' => 'Mức lương', 'rowspan' => 1, 'colspan' => 1],
                ['name' => 'Mức lương ngày', 'rowspan' => 1, 'colspan' => 1],
                ['name' => 'Số công thêm giờ', 'rowspan' => 1, 'colspan' => 1],
                ['name' => 'Lương thêm giờ', 'rowspan' => 1, 'colspan' => 1],
                ['name' => 'Thành tiền', 'rowspan' => 1, 'colspan' => 1],
                ['name' => 'Ký nhận', 'rowspan' => 1, 'colspan' => 1],
            ],
        ];

        $sheetData = [];
        foreach ($data as $item) {
            $sheetData[] = [
                $item['tt'],
                $item['hoten'],
                $item['mucluong'],
                $item['luongngay'],
                $item['socong'],
                $item['luongthem'],
                $item['thanhtien'],
                '',
            ];
        }

        $footerStartRow = count($header) + count($sheetData) + 1;
        $sheetData[] = [];
        $sheetData[] = [$tienchu, '', '', '', '', '', '', ''];
        $sheetData[] = [];
        $sheetData[] = ['', '', '', '', '', $lable_ngaythang, '', ''];
        $sheetData[] = ['Người lập biểu', '', '', 'Kế toán trưởng', '', 'Thủ trưởng', '', ''];
        $sheetData[] = [];
        $sheetData[] = [];
        $sheetData[] = [];
        $sheetData[] = [];
        $sheetData[] = [];
        $sheetData[] = [$this->userService->getGeneralAccountingUser()['name'] ?? '', '', '', $this->userService->getChiefAccountantUser()['name'] ?? '', '', $this->userService->getManagertUser()['name'] ?? '', '', ''];

        return (object) [
            'name' => 'Thêm Giờ Cơ Hữu',
            'header' => $header,
            'data' => $sheetData,
            'boldRows' => [1, 4, 7, count($header) + $lastRow + 1, $footerStartRow + 1, $footerStartRow + 4, $footerStartRow + 10],
            'italicRows' => [$footerStartRow + 1],
            'centerColumns' => [1],
            'centerRows' => [1, 2, 4, 5, 7, $footerStartRow + 3, $footerStartRow + 4, $footerStartRow + 10],
            'leftRows' => [$footerStartRow + 1],  // Căn trái dòng số tiền bằng chữ
        ];
    }

    /**
     * Build Sheet Tiêu Chí ABC
     */
    private function buildSheetTieuChiABC(array $data)
    {
        $header = [
            [
                ['name' => 'TT', 'rowspan' => 2, 'colspan' => 1],
                ['name' => 'Họ tên', 'rowspan' => 2, 'colspan' => 1],
                ['name' => 'Mức tính tiêu chí', 'rowspan' => 2, 'colspan' => 1],
                ['name' => 'Đánh giá công việc', 'rowspan' => 2, 'colspan' => 1],
                ['name' => 'Trừ tiêu chí', 'rowspan' => 2, 'colspan' => 1],
                ['name' => 'Tiêu chí nội quy', 'rowspan' => 1, 'colspan' => 3],
                ['name' => 'Trừ tiêu chí', 'rowspan' => 2, 'colspan' => 1],
                ['name' => 'Tiêu chí đào tạo', 'rowspan' => 1, 'colspan' => 2],
                ['name' => 'Trừ tiêu chí', 'rowspan' => 2, 'colspan' => 1],
                ['name' => 'Tổng', 'rowspan' => 2, 'colspan' => 1],
            ],
            [
                ['name' => 'B', 'rowspan' => 1, 'colspan' => 1],
                ['name' => 'C', 'rowspan' => 1, 'colspan' => 1],
                ['name' => 'D', 'rowspan' => 1, 'colspan' => 1],
                ['name' => 'B', 'rowspan' => 1, 'colspan' => 1],
                ['name' => 'C', 'rowspan' => 1, 'colspan' => 1],
            ],
        ];

        $sheetData = [];
        foreach ($data as $item) {
            $sheetData[] = [
                $item['tt'],
                $item['hoten'],
                $item['muctieuchi'],
                $item['dg_cv'],
                $item['tru_dg_cv'],
                $item['noiquy_b'],
                $item['noiquy_c'],
                $item['noiquy_d'],
                $item['tru_dg_noiquy'],
                $item['training_b_count'] ?? 0,
                $item['training_c_count'] ?? 0,
                $item['tru_dg_daotao'],
                $item['tong'],
            ];
        }

        return (object) [
            'name' => 'Tiêu Chí ABC',
            'header' => $header,
            'data' => $sheetData,
            'boldRows' => [1, 2],
            'centerColumns' => [1, 4, 6, 7, 8, 10, 11],
            'centerRows' => [1, 2],
        ];
    }

    /**
     * Build Sheet Tổng Thu Nhập
     */
    private function buildSheetTongThuNhap(array $data)
    {
        $header = [
            [
                ['name' => 'TT', 'rowspan' => 1, 'colspan' => 1],
                ['name' => 'Họ tên', 'rowspan' => 1, 'colspan' => 1],
                ['name' => 'Lương', 'rowspan' => 1, 'colspan' => 1],
                ['name' => 'Thêm giờ', 'rowspan' => 1, 'colspan' => 1],
                ['name' => 'Tổng', 'rowspan' => 1, 'colspan' => 1],
            ],
        ];

        $sheetData = [];
        foreach ($data as $item) {
            $sheetData[] = [
                $item['tt'],
                $item['hoten'],
                $item['luong'],
                $item['themgio'],
                $item['tong'],
            ];
        }

        return (object) [
            'name' => 'Tổng Thu Nhập',
            'header' => $header,
            'data' => $sheetData,
            'boldRows' => [1, count($sheetData) + 1],
            'centerColumns' => [1],
            'centerRows' => [1],
        ];
    }

    /**
     * Các method xử lý data giữ nguyên
     */
    private function SheetBank($permanentUsers)
    {
        $res = [];
        $total = 0;
        foreach ($permanentUsers as $index => $item) {
            $res[] = [
                'stt' => $index + 1,
                'hoten' => $item['name'],
                'thanhtien' => $item['salary_level'],
                'stk' => $item['user']['bank_code_number'] ?? '',
                'nganhang' => $item['user']['bank_name'] ?? '',
            ];

            $total += $item['salary_level'];
        }

        $res[] = [
            'stt' => 'Tổng',
            'hoten' => '',
            'thanhtien' => $total,
            'stk' => '',
            'nganhang' => ''
        ];
        return $res;
    }

    private function SheetLuongCoHuu($permanentUsers)
    {
        $res = [];

        $total_ml = $total_pcll = $total_pcac = $total_pccv = $total_pcxx = $total_pcdl = $total_ln = $total_trutien = $total_tt = $total_tn = $total_bhxh = $total_bhyt = $total_bhtn = $total_khautru = 0;

        foreach ($permanentUsers as $index => $item) {
            $tt = $item['salary_level'] - $item['deduction_amount'];
            $res[] = $arr = [
                'tt' => $index + 1,
                'hoten' => $item['name'],
                'xeploai' => $item['council_rating'],
                'mucluong' => $item['salary_level'],
                'ngaycong' => min($item['proposed_work_days'], $item['proposed_work_days'] + $item['max_paid_leave_days_per_year'] - $item['leave_days_with_permission'] - $item['leave_days_without_permission']),
                'bhxh' => $item['social_insurance_deduction'],
                'bhyt' => $item['health_insurance_deduction'],
                'bhtn' => $item['unemployment_insurance_deduction'],
                'tongkhautru' => $item['total_tax_deduction'],
                'pc_lienlac' => $item['allowance_contact'],
                'pc_anca' => $item['allowance_meal'],
                'pc_chucvu' => $item['allowance_position'],
                'pc_xangxe' => $item['allowance_fuel'],
                'pc_dilai' => $item['allowance_transport'],
                'luongnhan' => $item['salary_level'],
                'trutien' => $item['deduction_amount'],
                'thanhtien' => $tt,
                'tongnhan' => $tt,
            ];

            $total_ml += $arr['mucluong'];
            $total_pcll += $arr['pc_lienlac'];
            $total_pcac += $arr['pc_anca'];
            $total_pccv += $arr['pc_chucvu'];
            $total_pcxx += $arr['pc_xangxe'];
            $total_pcdl += $arr['pc_dilai'];
            $total_ln += $arr['luongnhan'];
            $total_trutien += $arr['trutien'];
            $total_tt += $arr['thanhtien'];
            $total_tn += $arr['tongnhan'];
            $total_bhxh += $arr['bhxh'];
            $total_bhyt += $arr['bhyt'];
            $total_bhtn += $arr['bhtn'];
            $total_khautru += $arr['tongkhautru'];
        }

        $res[] = [
            'tt' => 'TỔNG',
            'hoten' => '',
            'xeploai' => '',
            'mucluong' => $total_ml,
            'ngaycong' => '',
            'bhxh' => $total_bhxh,
            'bhyt' => $total_bhyt,
            'bhtn' => $total_bhtn,
            'tongkhautru' => $total_khautru,
            'pc_lienlac' => $total_pcll,
            'pc_anca' => $total_pcac,
            'pc_chucvu' => $total_pccv,
            'pc_xangxe' => $total_pcxx,
            'pc_dilai' => $total_pcdl,
            'luongnhan' => $total_ln,
            'trutien' => $total_trutien,
            'thanhtien' => $total_tt,
            'tongnhan' => $total_tn
        ];

        return $res;
    }

    private function SheetThemGioCoHuu($permanentUsers)
    {
        $res = [];

        $total_mlt = $total_mln = $total_socong = $total_ltg = $total_tn = 0;

        foreach ($permanentUsers as $index => $item) {
            $res[] = [
                'tt' => $index + 1,
                'hoten' => $item['name'],
                'mucluong' => $item['salary_level'],
                'luongngay' => round($item['overtime_salary_rate']),
                'socong' => $item['overtime_total_count'],
                'luongthem' => $item['overtime_total_amount'],
                'thanhtien' => $item['overtime_total_amount'],
            ];

            $total_mlt += $item['salary_level'];
            $total_mln += round($item['overtime_salary_rate']);
            $total_socong += $item['overtime_total_count'];
            $total_ltg += $item['overtime_total_amount'];
            $total_tn += $item['overtime_total_amount'];
        }

        $res[] = [
            'tt' => 'TỔNG',
            'hoten' => '',
            'mucluong' => $total_mlt,
            'luongngay' => $total_mln,
            'socong' => $total_socong,
            'luongthem' => $total_ltg,
            'thanhtien' => $total_tn
        ];

        return $res;
    }

    private function SheetLuongCTV($collaborators)
    {
        $res = [];
        $total_ml = $total_ln = $total_trutien = $total_tt = $total_tn = 0;

        foreach ($collaborators as $index => $item) {
            $totalWorkDayCount = $item['valid_attendance_count'] + $item['business_trip_system_count'] + $item['business_trip_manual_count'] + $item['leave_days_with_permission'];
            $luongNhan = round(($item['salary_level'] / $item['proposed_work_days']) * $totalWorkDayCount);

            $res[] = [
                'tt' => $index + 1,
                'hoten' => $item['name'],
                'xeploai' => $item['council_rating'],
                'mucluong' => $item['salary_level'],
                'congthucte' => $totalWorkDayCount,
                'luongnhan' => $luongNhan,
                'themgio' => $item['overtime_total_count'],
                'luongthem' => $item['overtime_total_amount'],
                'trutien' => $item['deduction_amount'],
                'thanhtien' => $item['total_received_salary'],
                'tongnhan' => $item['total_received_salary'],
            ];

            $total_ml += $item['salary_level'];
            $total_ln += $luongNhan;
            $total_trutien += $item['deduction_amount'];
            $total_tt += $item['total_received_salary'];
            $total_tn += $item['total_received_salary'];
        }

        $res[] = [
            'tt' => 'TỔNG',
            'hoten' => '',
            'xeploai' => '',
            'mucluong' => $total_ml,
            'congthucte' => '',
            'luongnhan' => $total_ln,
            'themgio' => '',
            'luongthem' => '',
            'trutien' => $total_trutien,
            'thanhtien' => $total_tt,
            'tongnhan' => $total_tn
        ];

        return $res;
    }

    private function SheetTieuChiABC($workTimesheetDetails)
    {
        $res = [];
        foreach ($workTimesheetDetails as $index => $item) {
            $res[] = [
                'tt' => $index + 1,
                'hoten' => $item['name'],
                'muctieuchi' => $item['violation_penalty'],
                'dg_cv' => $item['council_rating'],
                'tru_dg_cv' => $item['job_deduction_amount'] ?? 0,
                'noiquy_b' => $item['rule_b_count'],
                'noiquy_c' => $item['rule_c_count'],
                'noiquy_d' => $item['rule_d_count'] ?? 0,
                'tru_dg_noiquy' => $item['rule_deduction_amount'] ?? 0,
                'training_b_count' => $item['training_b_count'] ?? 0,
                'training_c_count' => $item['training_c_count'] ?? 0,
                'tru_dg_daotao' => $item['training_deduction_amount'] ?? 0,
                'tong' => $item['deduction_amount'],
            ];
        }
        return $res;
    }

    private function SheetTongThuNhap($workTimesheetDetails)
    {
        $res = [];
        $tl = $tng = $total = 0;

        foreach ($workTimesheetDetails as $index => $item) {
            $luong = $item['total_received_salary'] - $item['overtime_total_amount'];
            $res[] = [
                'tt' => $index + 1,
                'hoten' => $item['name'],
                'luong' => $luong,
                'themgio' => $item['overtime_total_amount'],
                'tong' => $item['total_received_salary'],
            ];

            $tl += $luong;
            $tng += $item['overtime_total_amount'];
            $total += $item['total_received_salary'];
        }

        $res[] = [
            'tt' => '',
            'hoten' => 'Tổng cộng',
            'luong' => $tl,
            'themgio' => $tng,
            'tong' => $total
        ];
        return $res;
    }
}
