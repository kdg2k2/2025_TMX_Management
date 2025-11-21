<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProfessionalRecordTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $arr = [
            [
                'name' => 'Biểu điều tra',
                'unit' => 'Biểu',
                'created_by' => 1,
            ],
            [
                'name' => 'Biên bản làm việc',
                'unit' => 'Bản',
                'created_by' => 1,
            ],
            [
                'name' => 'Mẫu đất',
                'unit' => 'Mẫu',
                'created_by' => 1,
            ],
            [
                'name' => 'Mẫu cây',
                'unit' => 'Mẫu',
                'created_by' => 1,
            ],
            [
                'name' => 'Phiếu phỏng vấn',
                'unit' => 'Phiếu',
                'created_by' => 1,
            ],
            [
                'name' => 'Mẫu khóa ảnh',
                'unit' => 'MKA',
                'created_by' => 1,
            ],
            [
                'name' => 'Bộ dữ liệu',
                'unit' => 'Bộ dữ liệu',
                'created_by' => 1,
            ],
        ];

        \DB::table('professional_record_types')->truncate();
        app(\App\Services\ProfessionalRecordTypeService::class)->insert($arr);
    }
}
