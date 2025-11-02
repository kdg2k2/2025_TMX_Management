<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\{Fill, Border, Alignment};
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Exception;

class ExcelService
{
    public function __construct(
        private HandlerUploadFileService $handlerUploadFileService
    ) {}

    /*
     * tạo sheet như sau để sử dụng
     *  $headerExcel = [
     *      [
     *          [
     *              'name' => 'Thông tin chung',
     *              'rowspan' => 1,
     *              'colspan' => 3,
     *          ],
     *          [
     *              'name' => 'Môn học',
     *              'rowspan' => 1,
     *              'colspan' => 34,
     *          ],
     *      ],
     *  ];
     *
     *  $dataExcel phải là mảng có dạng (index của mảng bắt buộc là số nguyên, value của mảng có thể là value hoặc phải là dạng như dưới)
     *  $dataExcel = [
     *      'background' => 'red',      // Màu nền: 'red', 'green', 'blue'... hoặc mã hex 'FF0000'
     *      'color' => 'white',         // Màu chữ: 'white', 'black'... hoặc mã hex 'FFFFFF'
     *      'record' => [
     *          1,
     *          'Bùi Trung Hiếu',
     *          [
     *              'value' => 8.0,
     *              'background' => 'yellow',   // Hỗ trợ tên màu tiếng Anh
     *              'color' => 'red',           // hoặc mã hex 6 ký tự
     *          ],
     *      ],
     *  ];
     *
     *  $sheets = [
     *      (object) [
     *          'name' => 'Sheet1',
     *          'header' => $headerExcel,
     *          'data' => $dataExcel,
     *          'boldRows' => [1, 2],
     *          'boldItalicRows' => [],
     *          'italicRows' => [3],
     *          'centerColumns' => [1],
     *          'centerRows' => [],
     *          'filterStartRow' => 3,
     *          'legend' => [
     *              [
     *                  'color' => 'yellow',
     *                  'note' => 'Chưa thi',
     *              ],
     *              [
     *                  'color' => 'red',
     *                  'note' => 'Trượt',
     *              ],
     *          ],
     *          'freezePane' => 'freezeTopRow',
     *          'freezePane' => 'freezeFirstColumn',
     *          'freezePane' => 'freezeTopRowAndFirstColumn',
     *          'freezePane' => 'freezeHeaderRows',
     *          'freezePane' => 'custom',  // khi dùng custom thì truyền thêm tọa độ freezeCell
     *          'freezeCell' => 'C4',
     *      ],
     *  ];
     */

    public function createExcel($sheets, $folder, $name, $autoWidth = false, $maxWidthCell = 25, $wrapText = false, $formatNumber = true)
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);

        foreach ($sheets as $i => $item) {
            $spreadsheet->createSheet($i)->setTitle($item->name);
            $sheet = $spreadsheet->getSheetByName($item->name);

            $sheet = $this->processHeaderMerge($sheet, $item->header);

            foreach ($item->data as $index => $dt) {
                $rowHasBackground = false;
                $rowBackgroundColor = '';
                $rowHasColor = false;
                $rowTextColor = '';

                if (is_array($dt) && isset($dt['record'])) {
                    if (isset($dt['background'])) {
                        $rowHasBackground = true;
                        $rowBackgroundColor = $this->convertColorNameToARGB($dt['background']);
                    }
                    if (isset($dt['color'])) {
                        $rowHasColor = true;
                        $rowTextColor = $this->convertColorNameToARGB($dt['color']);
                    }
                    $dt = $dt['record'];
                }

                foreach ($dt as $colIndex => $cellData) {
                    $cellCoordinate = Coordinate::stringFromColumnIndex($colIndex + 1)
                        . (count($item->header) + $index + 1);

                    if (is_array($cellData)) {
                        $sheet->setCellValue($cellCoordinate, $cellData['value']);
                        if (isset($cellData['background'])) {
                            $sheet
                                ->getStyle($cellCoordinate)
                                ->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()
                                ->setARGB($this->convertColorNameToARGB($cellData['background']));
                        }
                        if (isset($cellData['color'])) {
                            $sheet
                                ->getStyle($cellCoordinate)
                                ->getFont()
                                ->getColor()
                                ->setARGB($this->convertColorNameToARGB($cellData['color']));
                        }
                    } else {
                        $sheet->setCellValue($cellCoordinate, $cellData);
                    }
                }

                $rowNumber = count($item->header) + $index + 1;
                $lastColumnLetter = Coordinate::stringFromColumnIndex(count($dt));

                if ($rowHasBackground) {
                    $sheet
                        ->getStyle("A{$rowNumber}:{$lastColumnLetter}{$rowNumber}")
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB($rowBackgroundColor);
                }

                if ($rowHasColor) {
                    $sheet
                        ->getStyle("A{$rowNumber}:{$lastColumnLetter}{$rowNumber}")
                        ->getFont()
                        ->getColor()
                        ->setARGB($rowTextColor);
                }
            }

            $sheet = $this->autoWidthExcel($sheet, $autoWidth, isset($item->filterStartRow) ? $item->filterStartRow : null, $maxWidthCell, $wrapText, $formatNumber);

            // Xử lý freeze panes nếu được cấu hình
            if (isset($item->freezePane)) {
                // Các lựa chọn freeze panes
                switch ($item->freezePane) {
                    case 'freezeTopRow':
                        // Freeze dòng đầu tiên
                        $sheet->freezePane('A2');
                        break;
                    case 'freezeFirstColumn':
                        // Freeze cột đầu tiên
                        $sheet->freezePane('B1');
                        break;
                    case 'freezeTopRowAndFirstColumn':
                        // Freeze dòng đầu và cột đầu
                        $sheet->freezePane('B2');
                        break;
                    case 'custom':
                        // Freeze tùy chỉnh nếu có thông số cell và row
                        if (isset($item->freezeCell)) {
                            $sheet->freezePane($item->freezeCell);
                        }
                        break;
                    case 'freezeHeaderRows':
                        // Freeze tất cả các dòng header
                        $headerRowCount = count($item->header);
                        $freezeCell = 'A' . ($headerRowCount + 1);
                        $sheet->freezePane($freezeCell);
                        break;
                    default:
                        // Không freeze nếu không xác định
                        break;
                }
            }

            if (isset($item->centerColumns)) {
                foreach ($item->centerColumns as $col) {
                    $colLetter = Coordinate::stringFromColumnIndex($col);
                    $highestRow = $sheet->getHighestRow();
                    $sheet
                        ->getStyle("{$colLetter}1:$colLetter$highestRow")
                        ->getAlignment()
                        ->setVertical('center')
                        ->setHorizontal('center');
                }
            }
            $lastColumnIndexSheet = $sheet->getHighestDataColumn();
            if (isset($item->centerRows)) {
                foreach ($item->centerRows as $row) {
                    $sheet
                        ->getStyle("A{$row}:$lastColumnIndexSheet{$row}")
                        ->getAlignment()
                        ->setVertical('center')
                        ->setHorizontal('center');
                }
            }
            if (isset($item->boldRows)) {
                foreach ($item->boldRows as $row) {
                    $sheet
                        ->getStyle("A{$row}:$lastColumnIndexSheet{$row}")
                        ->getFont()
                        ->setBold(true);
                }
            }
            if (isset($item->boldItalicRows)) {
                foreach ($item->boldItalicRows as $row) {
                    $sheet
                        ->getStyle("A{$row}:$lastColumnIndexSheet{$row}")
                        ->getFont()
                        ->setBold(true);
                    $sheet
                        ->getStyle("A{$row}:$lastColumnIndexSheet{$row}")
                        ->getFont()
                        ->setItalic(true);
                }
            }
            if (isset($item->italicRows)) {
                foreach ($item->italicRows as $row) {
                    $sheet
                        ->getStyle("A{$row}:$lastColumnIndexSheet{$row}")
                        ->getFont()
                        ->setItalic(true);
                }
            }

            if (isset($item->legend) && is_array($item->legend)) {
                $legendStartColumn = Coordinate::stringFromColumnIndex(
                    Coordinate::columnIndexFromString($lastColumnIndexSheet) + 2
                );
                $legendStartRow = 1;

                // Tiêu đề Legend
                $sheet->setCellValue("{$legendStartColumn}{$legendStartRow}", 'Màu');
                $legendNoteColumn = Coordinate::stringFromColumnIndex(
                    Coordinate::columnIndexFromString($lastColumnIndexSheet) + 3
                );
                $sheet->setCellValue("{$legendNoteColumn}{$legendStartRow}", 'Ghi chú');

                $sheet
                    ->getStyle("{$legendStartColumn}{$legendStartRow}")
                    ->getFont()
                    ->setBold(true);
                $sheet
                    ->getStyle("{$legendNoteColumn}{$legendStartRow}")
                    ->getFont()
                    ->setBold(true);

                // Duyệt các mục legend
                foreach ($item->legend as $legendIndex => $legendItem) {
                    $legendRow = $legendStartRow + $legendIndex + 1;
                    // Ô màu
                    $sheet->setCellValue("{$legendStartColumn}{$legendRow}", '');
                    $sheet
                        ->getStyle("{$legendStartColumn}{$legendRow}")
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB($legendItem['color']);
                    // Ô ghi chú
                    $sheet->setCellValue("{$legendNoteColumn}{$legendRow}", $legendItem['note']);
                }

                $legendEndColumn = $legendNoteColumn;
                $legendEndRow = $legendStartRow + count($item->legend);

                // Set font cho vùng legend
                $sheet
                    ->getStyle("{$legendStartColumn}{$legendStartRow}:{$legendEndColumn}{$legendEndRow}")
                    ->getFont()
                    ->setName('Times New Roman')
                    ->setSize(13);

                // Set border cho vùng legend
                $sheet
                    ->getStyle("{$legendStartColumn}{$legendStartRow}:{$legendEndColumn}{$legendEndRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);

                // Thay vì dùng auto width, set width cố định theo giá trị $maxWidthCell
                $sheet->getColumnDimension($legendStartColumn)->setWidth($maxWidthCell);
                $sheet->getColumnDimension($legendNoteColumn)->setWidth($maxWidthCell);
            }
        }

        $writer = new Xlsx($spreadsheet);
        $publicFolder = $this->handlerUploadFileService->getAbsolutePublicPath($folder);

        $writer->save("$publicFolder/$name");

        return "$folder/$name";
    }

    public function readExcel($file)
    {
        $filePath = null;
        $extension = null;

        // Xử lý theo loại file
        if ($file instanceof UploadedFile) {
            // File từ HTTP request
            if (!$file->isValid()) {
                throw new Exception('File upload không hợp lệ');
            }

            $filePath = $file->getRealPath();
            $extension = $file->getClientOriginalExtension();
        } elseif (is_string($file)) {
            // File từ đường dẫn local

            // Thử resolve đường dẫn
            if (!file_exists($file)) {
                $possiblePaths = [
                    public_path($file),
                    storage_path('app/' . $file),
                    storage_path('app/public/' . $file)
                ];

                foreach ($possiblePaths as $path) {
                    if (file_exists($path)) {
                        $file = $path;
                        break;
                    }
                }
            }

            if (!file_exists($file)) {
                throw new Exception("File không tồn tại: {$file}");
            }

            $filePath = $file;
            $extension = pathinfo($file, PATHINFO_EXTENSION);
        } else {
            throw new Exception('File phải là UploadedFile hoặc string path');
        }

        // Validate extension
        if (!in_array(strtolower($extension), ['xls', 'xlsx'])) {
            throw new Exception('Chỉ hỗ trợ định dạng xls, xlsx');
        }

        // Load spreadsheet
        $spreadsheet = IOFactory::load($filePath);

        // Lấy tất cả sheet names
        $sheetNames = $spreadsheet->getSheetNames();

        $allSheetsData = [];

        // Iterate qua từng sheet
        foreach ($sheetNames as $sheetName) {
            $worksheet = $spreadsheet->getSheetByName($sheetName);

            // Convert sheet thành array
            $sheetData = $worksheet->toArray(
                null,  // nullValue - giá trị cho cell null
                true,  // calculateFormulas - tính toán formulas
                true,  // formatData - format data theo cell format
                false  // returnCellRef - không return cell references
            );

            // Thêm vào result array với sheet name làm key
            $allSheetsData[$sheetName] = $sheetData;
        }

        return $allSheetsData;
    }

    protected function processHeaderMerge($sheet, $headerData)
    {
        // Kiểm tra tính hợp lệ của dữ liệu header
        if (empty($headerData) || !is_array($headerData))
            return $sheet;

        // Mảng để theo dõi vị trí các ô đã sử dụng
        $occupiedCells = array_fill(0, count($headerData), array_fill(0, 100, false));

        // Duyệt qua từng dòng header
        for ($rowIndex = 0; $rowIndex < count($headerData); $rowIndex++) {
            $currentRowData = $headerData[$rowIndex];
            $currentCol = 0;

            foreach ($currentRowData as $headerCell) {
                // Tìm vị trí cột trống đầu tiên
                while (isset($occupiedCells[$rowIndex][$currentCol]) && $occupiedCells[$rowIndex][$currentCol])
                    $currentCol++;

                // Tạo tọa độ ô
                $columnLetter = Coordinate::stringFromColumnIndex($currentCol + 1);
                $cellCoordinate = $columnLetter . ($rowIndex + 1);

                // Đưa giá trị vào ô
                $sheet->setCellValue($cellCoordinate, $headerCell['name']);

                // Xử lý rowspan và colspan
                $rowspan = $headerCell['rowspan'] ?? 1;
                $colspan = $headerCell['colspan'] ?? 1;

                // Đánh dấu các ô đã sử dụng
                for ($r = 0; $r < $rowspan; $r++)
                    for ($c = 0; $c < $colspan; $c++)
                        $occupiedCells[$rowIndex + $r][$currentCol + $c] = true;

                // Thực hiện merge nếu cần
                if ($rowspan > 1 || $colspan > 1) {
                    $endColumnIndex = $currentCol + $colspan - 1;
                    $endRowIndex = $rowIndex + $rowspan - 1;

                    $startCell = Coordinate::stringFromColumnIndex($currentCol + 1) . ($rowIndex + 1);
                    $endCell = Coordinate::stringFromColumnIndex($endColumnIndex + 1) . ($endRowIndex + 1);

                    // Merge ô
                    $sheet->mergeCells("{$startCell}:{$endCell}");
                }

                // Tăng cột
                $currentCol += $colspan;

                // Căn giữa dọc và ngang
                $sheet
                    ->getStyle("$cellCoordinate")
                    ->getAlignment()
                    ->setVertical(Alignment::VERTICAL_CENTER)
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setWrapText(true);  // Tự động xuống dòng nếu văn bản dài
            }
        }

        return $sheet;
    }

    protected function autoWidthExcel($sheet, $autoWidth, $filterStartRow = null, $maxWidthCell, $wrapText, $formatNumber)
    {
        $nb_sheet = $sheet->getHighestRow();
        $lastColumnIndexSheet = $sheet->getHighestDataColumn();
        $last_col_sheet = Coordinate::columnIndexFromString($lastColumnIndexSheet);
        $rangeLastCol = $lastColumnIndexSheet . $nb_sheet;

        // Thiết lập kiểu font và căn giữa
        $sheet->getStyle("A1:$rangeLastCol")->getFont()->setSize(13)->setName('Times New Roman');
        $sheet->getStyle("A1:$rangeLastCol")->getAlignment()->setVertical('center');

        // filter
        if ($filterStartRow !== null) {
            $hasDataFromStartRow = false;
            for ($row = $filterStartRow; $row <= $nb_sheet; $row++) {
                $cellValue = $sheet->getCell('A' . $row)->getValue();
                if (!empty($cellValue)) {
                    $hasDataFromStartRow = true;
                    break;
                }
            }

            if ($hasDataFromStartRow) {
                $filterRange = 'A' . $filterStartRow . ':' . $lastColumnIndexSheet . $nb_sheet;
                $sheet->setAutoFilter($filterRange);
            }
        }

        // Duyệt từng cột
        for ($col = 1; $col <= $last_col_sheet; $col++) {
            $colLetter = Coordinate::stringFromColumnIndex($col);
            $width = 0;

            // Duyệt từng hàng
            for ($row = 1; $row <= $nb_sheet; $row++) {
                $cell = $sheet->getCell($colLetter . $row);
                $cellValue = $cell->getValue();
                // wrap text
                if ($wrapText == true)
                    $cell
                        ->getStyle()
                        ->getAlignment()
                        ->setWrapText(true);
                $cellWidth = strlen($cellValue);
                if ($cellWidth > $width)
                    $width = $cellWidth + 3;

                // Kiểm tra và định dạng giá trị số
                if (is_numeric($cellValue)) {
                    $cellCoordinate = $colLetter . $row;  // Địa chỉ ô, ví dụ: A1, B2

                    // Format căn phải nếu là số
                    $sheet->getStyle($cellCoordinate)->getAlignment()->setHorizontal('right');
                    if ($formatNumber == true)
                        if (strpos((string) $cellValue, '.') !== false) {
                            // Tách phần nguyên và phần thập phân
                            $parts = explode('.', (string) $cellValue);
                            $decimalCount = isset($parts[1]) ? strlen($parts[1]) : 0;  // Đếm số chữ số sau dấu thập phân

                            if ($decimalCount > 3)
                                $decimalCount = 2;
                            // Tạo định dạng số thực với số lượng chữ số thập phân tương ứng
                            $formatCode = '#,##0.' . str_repeat('0', $decimalCount);
                            $sheet
                                ->getStyle($cellCoordinate)
                                ->getNumberFormat()
                                ->setFormatCode($formatCode);
                        } else {
                            // Format số nguyên
                            $sheet
                                ->getStyle($cellCoordinate)
                                ->getNumberFormat()
                                ->setFormatCode('#,##0');
                        }
                }
            }

            // Thiết lập chiều rộng cột
            $finalWidth = $autoWidth ? $width : min($width, $maxWidthCell);
            $sheet->getColumnDimension($colLetter)->setWidth($finalWidth);
        }

        // Thiết lập đường viền
        $sheet->getStyle("A1:$rangeLastCol")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        return $sheet;
    }

    /**
     * Convert tên màu thông dụng sang mã ARGB
     */
    protected function convertColorNameToARGB($colorName)
    {
        $colorMap = [
            'red' => 'FF0000',
            'green' => '00FF00',
            'blue' => '0000FF',
            'yellow' => 'FFFF00',
            'orange' => 'FFA500',
            'purple' => '800080',
            'pink' => 'FFC0CB',
            'brown' => 'A52A2A',
            'gray' => '808080',
            'grey' => '808080',
            'black' => '000000',
            'white' => 'FFFFFF',
            'cyan' => '00FFFF',
            'magenta' => 'FF00FF',
            'lime' => '00FF00',
            'navy' => '000080',
            'teal' => '008080',
            'olive' => '808000',
            'maroon' => '800000',
            'aqua' => '00FFFF',
            'silver' => 'C0C0C0',
            'gold' => 'FFD700',
        ];

        $colorName = strtolower(trim($colorName));

        // Nếu đã là mã hex (6 ký tự), return luôn
        if (preg_match('/^[0-9A-Fa-f]{6}$/', $colorName)) {
            return strtoupper($colorName);
        }

        // Convert tên màu sang mã
        return $colorMap[$colorName] ?? 'FF0000';  // Default: red
    }
}
