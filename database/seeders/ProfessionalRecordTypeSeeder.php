<?php

namespace Database\Seeders;

use App\Models\ProfessionalRecordType;
use Illuminate\Database\Seeder;

class ProfessionalRecordTypeSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['name' => 'Biểu điều tra', 'unit' => 'Biểu'],
            ['name' => 'Biên bản làm việc', 'unit' => 'Bản'],
            ['name' => 'Mẫu đất', 'unit' => 'Mẫu'],
            ['name' => 'Mẫu cây', 'unit' => 'Mẫu'],
            ['name' => 'Phiếu phỏng vấn', 'unit' => 'Phiếu'],
            ['name' => 'Mẫu khóa ảnh', 'unit' => 'MKA'],
            ['name' => 'Bộ dữ liệu', 'unit' => 'Bộ dữ liệu'],
        ];

        foreach ($items as $item) {
            ProfessionalRecordType::updateOrCreate(
                ['name' => $item['name']],
                [
                    'unit' => $item['unit'],
                    'created_by' => 1,
                ]
            );
        }
    }
}
