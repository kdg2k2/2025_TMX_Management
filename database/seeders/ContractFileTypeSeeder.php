<?php

namespace Database\Seeders;

use App\Models\ContractFileType;
use App\Models\FileExtension;
use Illuminate\Database\Seeder;

class ContractFileTypeSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            [
                'name' => 'Link Hồ Sơ Thầu',
                'type' => 'url',
            ],
            [
                'name' => 'Link Sản Phẩm Hđ',
                'type' => 'url',
            ],
            [
                'name' => 'Phương Pháp Luận',
                'type' => 'file',
                'extensions' => ['doc', 'docx'],
            ],
            [
                'name' => 'Thuyết Minh',
                'type' => 'file',
                'extensions' => ['doc', 'docx'],
            ],
            [
                'name' => 'Dự Toán',
                'type' => 'file',
                'extensions' => ['xls', 'xlsx'],
            ],
            [
                'name' => 'Hợp Đồng',
                'type' => 'file',
                'extensions' => ['doc', 'docx'],
            ],
            [
                'name' => 'Phục Lục Hợp Đồng',
                'type' => 'file',
                'extensions' => ['xls', 'xlsx', 'doc', 'docx'],
            ],
            [
                'name' => 'Bb Thương Thảo',
                'type' => 'file',
                'extensions' => ['doc', 'docx'],
            ],
            [
                'name' => 'Phụ Lục Bb Thương Thảo',
                'type' => 'file',
                'extensions' => ['xls', 'xlsx', 'doc', 'docx'],
            ],
            [
                'name' => 'Biên Bản Nghiệm Thu',
                'type' => 'file',
                'extensions' => ['doc', 'docx', 'rar', 'zip'],
            ],
            [
                'name' => 'Phụ Lục Biên Bản Nghiệm Thu',
                'type' => 'file',
                'extensions' => ['xls', 'xlsx', 'doc', 'docx', 'rar', 'zip'],
            ],
            [
                'name' => 'Biên Bản Thanh Lý',
                'type' => 'file',
                'extensions' => ['doc', 'docx'],
            ],
            [
                'name' => 'Phụ Lục Biên Bản Thanh Lý',
                'type' => 'file',
                'extensions' => ['xls', 'xlsx', 'doc', 'docx'],
            ],
            [
                'name' => '8a',
                'type' => 'file',
                'extensions' => ['xls', 'xlsx', 'doc', 'docx'],
            ],
            [
                'name' => '8b',
                'type' => 'file',
                'extensions' => ['xls', 'xlsx', 'doc', 'docx'],
            ],
            [
                'name' => '3a',
                'type' => 'file',
                'extensions' => ['xls', 'xlsx', 'doc', 'docx'],
            ],
            [
                'name' => 'Đối Chiếu Công Nợ',
                'type' => 'file',
                'extensions' => ['xls', 'xlsx', 'doc', 'docx', 'pdf'],
            ],
            [
                'name' => 'Khác',
                'type' => 'file',
                'extensions' => ['zip', 'rar', 'pdf'],
            ],
            [
                'name' => 'Phân bổ',
                'type' => 'file',
                'extensions' => ['xls', 'xlsx', 'xlsm'],
            ],
            [
                'name' => 'Hợp đồng nhân công',
                'type' => 'file',
                'extensions' => ['doc', 'docx'],
            ],
            [
                'name' => 'Chi khác',
                'type' => 'file',
                'extensions' => ['doc', 'docx', 'xls', 'xlsx', 'zip', 'rar'],
            ],
        ] as $item) {
            $type = ContractFileType::updateOrCreate(
                ['name' => $item['name']],
                ['type' => $item['type']]
            );

            if (!empty($item['extensions'])) {
                $extensionIds = FileExtension::whereIn('extension', $item['extensions'])
                    ->pluck('id')
                    ->toArray();

                // Xóa cũ
                $type->extensions()->delete();

                // Tạo mới
                $type->extensions()->createMany(
                    array_map(fn($id) => [
                        'extension_id' => $id,
                    ], $extensionIds)
                );
            }
        }
    }
}
