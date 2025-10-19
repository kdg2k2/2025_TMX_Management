<?php

namespace Database\Seeders;

use App\Models\ContractScanFileType;
use App\Models\FileExtension;
use Illuminate\Database\Seeder;

class ContractScanFileTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ContractScanFileType::truncate();
        foreach ([
            [
                'name' => 'Qđpd đề cương dự toán',
                'extensions' => ['pdf']
            ],
            [
                'name' => 'Qđpd kế hoạch lựa chọn nhà thầu',
                'extensions' => ['pdf']
            ],
            [
                'name' => 'Qđpd kết quả lựa chọn nhà thầu',
                'extensions' => ['pdf']
            ],
            [
                'name' => 'Ủy quyền',
                'extensions' => ['pdf']
            ],
            [
                'name' => 'Thỏa thuận liên danh',
                'extensions' => ['pdf']
            ],
            [
                'name' => 'HĐ nguyên tắc',
                'extensions' => ['pdf']
            ],
            [
                'name' => 'Biên bản thương thảo',
                'extensions' => ['pdf']
            ],
            [
                'name' => 'Hợp đồng',
                'extensions' => ['pdf']
            ],
            [
                'name' => 'Phụ lục HĐ',
                'extensions' => ['pdf']
            ],
            [
                'name' => 'BB nghiệm thu',
                'extensions' => ['pdf']
            ],
            [
                'name' => 'BB thanh lý',
                'extensions' => ['pdf']
            ],
            [
                'name' => '8A',
                'extensions' => ['pdf']
            ],
            [
                'name' => '8B',
                'extensions' => ['pdf']
            ],
            [
                'name' => '3A',
                'extensions' => ['pdf']
            ],
            [
                'name' => 'Qđpd kết quả thực hiện',
                'extensions' => ['pdf']
            ],
            [
                'name' => 'Hóa đơn',
                'extensions' => ['pdf', 'rar', 'zip']
            ],
            [
                'name' => 'File khác',
                'extensions' => ['pdf', 'rar', 'zip']
            ],
        ] as $item) {
            $type = ContractScanFileType::create([
                'name' => $item['name'],
            ]);
            if (isset($item['extensions'])) {
                $extensions = FileExtension::whereIn('extension', $item['extensions'])->pluck('id')->filter()->toArray() ?? [];
                $type->extensions()->createMany(array_map(function ($i) use ($type) {
                    return [
                        'extension_id' => $i,
                        'type_id' => $type['id'],
                    ];
                }, $extensions));
            }
        }
    }
}
