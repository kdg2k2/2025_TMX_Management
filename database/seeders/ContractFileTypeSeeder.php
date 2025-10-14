<?php

namespace Database\Seeders;

use App\Models\ContractFileType;
use App\Models\FileExtension;
use Illuminate\Database\Seeder;

class ContractFileTypeSeeder extends Seeder
{
    public function run(): void
    {
        ContractFileType::truncate();
        foreach ([
            [
                'name' => 'Link Hồ Sơ Thầu',
                'extensions' => [],
            ],
            [
                'name' => 'Link Sản Phẩm Hđ',
                'extensions' => [],
            ],
            [
                'name' => 'Phương Pháp Luận',
                'extensions' => ['doc', 'docx'],
            ],
            [
                'name' => 'Thuyết Minh',
                'extensions' => ['doc', 'docx'],
            ],
            [
                'name' => 'Dự Toán',
                'extensions' => ['xls', 'xlsx'],
            ],
            [
                'name' => 'Hợp Đồng',
                'extensions' => ['doc', 'docx'],
            ],
            [
                'name' => 'Phục Lục Hợp Đồng',
                'extensions' => ['xls', 'xlsx', 'doc', 'docx'],
            ],
            [
                'name' => 'Bb Thương Thảo',
                'extensions' => ['doc', 'docx'],
            ],
            [
                'name' => 'Phụ Lục Bb Thương Thảo',
                'extensions' => ['xls', 'xlsx', 'doc', 'docx'],
            ],
            [
                'name' => 'Biên Bản Nghiệm Thu',
                'extensions' => ['doc', 'docx', 'rar', 'zip'],
            ],
            [
                'name' => 'Phụ Lục Biên Bản Nghiệm Thu',
                'extensions' => ['xls', 'xlsx', 'doc', 'docx', 'rar', 'zip'],
            ],
            [
                'name' => 'Biên Bản Thanh Lý',
                'extensions' => ['doc', 'docx'],
            ],
            [
                'name' => 'Phụ Lục Biên Bản Thanh Lý',
                'extensions' => ['xls', 'xlsx', 'doc', 'docx'],
            ],
            [
                'name' => '8a',
                'extensions' => ['xls', 'xlsx', 'doc', 'docx'],
            ],
            [
                'name' => '8b',
                'extensions' => ['xls', 'xlsx', 'doc', 'docx'],
            ],
            [
                'name' => '3a',
                'extensions' => ['xls', 'xlsx', 'doc', 'docx'],
            ],
            [
                'name' => 'Đối Chiếu Công Nợ',
                'extensions' => ['xls', 'xlsx', 'doc', 'docx', 'pdf'],
            ],
            [
                'name' => 'Khác',
                'extensions' => ['zip', 'rar', 'pdf'],
            ],
        ] as $item) {
            $type = ContractFileType::create(['name' => $item['name']]);
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
