<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RenderTreeMapGoogleDriveFolders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:render-tree-map-google-drive-folders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hiển thị sơ đồ cấu trúc thư mục Google Drive';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Tạo thư mục nếu chưa tồn tại
        $outputPath = public_path('uploads/render');
        if (!file_exists($outputPath)) {
            mkdir($outputPath, 0755, true);
        }

        // Tên file với timestamp
        $fileName = 'tree_structure_' . date('Y-m-d_H-i-s') . '.txt';
        $filePath = $outputPath . '/' . $fileName;

        // Bắt đầu output buffering để lưu kết quả
        ob_start();

        /**
         * Sơ đồ cấu trúc thư mục hệ thống
         * Mô tả: Cấu trúc tổ chức thư mục cho hệ thống quản lý hợp đồng, nhân sự và ấn phẩm khoa học
         */
        $cauTrucThuMuc = [
            'Drive' => [
                'description' => 'Thư mục gốc của hệ thống',
                'children' => [
                    'Project' => [
                        'description' => 'Thư mục dự án',
                        'children' => [
                            '{YYYY}' => [
                                'description' => 'Thư mục năm (ví dụ: 2024, 2025)',
                                'children' => [
                                    '{TenTomTatHD}' => [
                                        'description' => 'Thư mục hợp đồng (tên tóm tắt không dấu, viết liền)',
                                        'note' => 'Tự động tạo khi thêm hợp đồng mới',
                                        'children' => [
                                            'Contracts' => [
                                                'description' => 'Lưu file scan/word hợp đồng, BBTT, BBNT, BBTL',
                                                'note' => 'Quản trị file ScanHD + phần HD/BBNT/BBTL',
                                                'children' => []
                                            ],
                                            'ProposalEstimateBudget' => [
                                                'description' => 'Hồ sơ thầu',
                                                'note' => 'HSMT (Hồ sơ mời thầu), HSCB (Hồ sơ chào giá), HSNT (Hồ sơ nhà thầu)',
                                                'children' => []
                                            ],
                                            // 'Disbursement' => [
                                            //     'description' => 'File excel phân bổ',
                                            //     'children' => []
                                            // ],
                                            'Products' => [
                                                'description' => 'Sản phẩm dự án',
                                                'children' => [
                                                    '1.MainProducts' => [
                                                        'description' => 'Sản phẩm chính',
                                                        'children' => []
                                                    ],
                                                    '2.IntermediaProducts' => [
                                                        'description' => 'Sản phẩm trung gian',
                                                        'children' => []
                                                    ],
                                                    '3.Documentary_Decisions' => [
                                                        'description' => 'Văn bản quyết định',
                                                        'children' => []
                                                    ],
                                                    'z.DataReferences' => [
                                                        'description' => 'Dữ liệu tham khảo',
                                                        'children' => []
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'NhanSu' => [
                        'description' => 'Thư mục quản lý thông tin nhân sự (phục vụ đấu thầu)',
                        'note' => 'Sử dụng chung dữ liệu với hệ thống IFEE',
                        'children' => [
                            'NhanSu_VST' => [
                                'description' => 'Nhân sự VST',
                                'children' => [
                                    '{HoVaTen}' => [
                                        'description' => 'Thư mục cá nhân (tên nhân sự)',
                                        'note' => 'Tự động tạo khi bổ sung nhân sự từ hệ thống IFEE',
                                        'children' => [
                                            'FileScan' => [
                                                'description' => 'Các file scan tài liệu cá nhân',
                                                'children' => []
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            'NhanSu_DHLN' => [
                                'description' => 'Nhân sự ĐHLN',
                                'children' => [
                                    '{HoVaTen}' => [
                                        'description' => 'Thư mục cá nhân (tên nhân sự)',
                                        'note' => 'Tự động tạo khi bổ sung nhân sự từ hệ thống IFEE',
                                        'children' => [
                                            'FileScan' => [
                                                'description' => 'Các file scan tài liệu cá nhân',
                                                'children' => []
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            'NhanSu_Khac' => [
                                'description' => 'Nhân sự khác',
                                'children' => [
                                    '{HoVaTen}' => [
                                        'description' => 'Thư mục cá nhân (tên nhân sự)',
                                        'note' => 'Tự động tạo khi bổ sung nhân sự từ hệ thống IFEE',
                                        'children' => [
                                            'FileScan' => [
                                                'description' => 'Các file scan tài liệu cá nhân',
                                                'children' => []
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'AnPhamKHCN' => [
                        'description' => 'Ấn phẩm khoa học công nghệ',
                        'children' => [
                            '{YYYY}' => [
                                'description' => 'Thư mục năm (ví dụ: 2024, 2025)',
                                'children' => [
                                    'FileScanAnPham' => [
                                        'description' => 'File scan ấn phẩm cụ thể',
                                        'children' => []
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        /**
         * Hàm in sơ đồ cây (dạng text) với đường kẻ rõ ràng
         */
        function inSoDoThumbuc($array, $level = 0, $prefix = '', $isLast = true)
        {
            $items = [];
            foreach ($array as $key => $value) {
                // Bỏ qua key 'description', 'note', 'children'
                if (in_array($key, ['description', 'note', 'children'])) {
                    continue;
                }
                $items[$key] = $value;
            }

            $totalItems = count($items);
            $currentIndex = 0;

            foreach ($items as $key => $value) {
                $currentIndex++;
                $isLastItem = ($currentIndex === $totalItems);

                // Tạo connector và prefix cho dòng hiện tại
                if ($level === 0) {
                    $connector = '📁 ';
                    $childPrefix = '';
                } else {
                    $connector = ($isLastItem ? '└── ' : '├── ') . '📁 ';
                    $childPrefix = $prefix . ($isLastItem ? '    ' : '│   ');
                }

                // In dòng hiện tại
                echo $prefix . $connector . $key;

                // In mô tả nếu có
                if (isset($value['description'])) {
                    echo ' (' . $value['description'] . ')';
                }

                // In ghi chú nếu có
                if (isset($value['note'])) {
                    echo ' [' . $value['note'] . ']';
                }

                echo PHP_EOL;

                // Đệ quy in các thư mục con
                if (isset($value['children']) && is_array($value['children']) && !empty($value['children'])) {
                    inSoDoThumbuc($value['children'], $level + 1, $childPrefix, $isLastItem);
                }
            }
        }

        /**
         * Hàm lấy đường dẫn đầy đủ của một thư mục
         */
        function layDuongDan($array, $target, $currentPath = '')
        {
            foreach ($array as $key => $value) {
                if (in_array($key, ['description', 'note', 'children'])) {
                    continue;
                }

                $newPath = $currentPath . '/' . $key;

                if ($key === $target) {
                    return $newPath;
                }

                if (isset($value['children']) && is_array($value['children'])) {
                    $result = layDuongDan($value['children'], $target, $newPath);
                    if ($result) {
                        return $result;
                    }
                }
            }

            return null;
        }

        // Xuất cấu trúc
        echo '=== SƠ ĐỒ CẤU TRÚC THƯ MỤC HỆ THỐNG ===' . PHP_EOL . PHP_EOL;
        inSoDoThumbuc($cauTrucThuMuc);

        echo PHP_EOL . '=== VÍ DỤ ĐƯỜNG DẪN ===' . PHP_EOL;
        echo 'Hợp đồng: ' . layDuongDan($cauTrucThuMuc, 'Contracts') . PHP_EOL;
        echo 'Nhân sự VST: ' . layDuongDan($cauTrucThuMuc, 'NhanSu_VST') . PHP_EOL;
        echo 'Ấn phẩm KHCN: ' . layDuongDan($cauTrucThuMuc, 'AnPhamKHCN') . PHP_EOL;

        // Lấy nội dung từ output buffer
        $content = ob_get_clean();

        // Lưu vào file
        file_put_contents($filePath, $content);

        // Hiển thị nội dung ra console
        echo $content;

        // Thông báo cho user
        echo PHP_EOL . '=== THÔNG BÁO ===' . PHP_EOL;
        echo '✅ Đã lưu sơ đồ vào file: ' . $filePath . PHP_EOL;
        echo '📂 Đường dẫn tương đối: /uploads/render/' . $fileName . PHP_EOL;
        echo '🌐 URL: ' . url('uploads/render/' . $fileName) . PHP_EOL;

        $this->info('Command executed successfully!');
    }
}
