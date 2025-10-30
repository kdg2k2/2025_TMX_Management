<?php

namespace Database\Seeders;

use App\Models\JobTitle;
use Illuminate\Database\Seeder;

class JobTitleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        JobTitle::truncate();
        JobTitle::insert(array_map(function ($item) {
            $item['created_at'] = now();
            $item['updated_at'] = now();
            return $item;
        }, [
            [
                'name' => 'Chủ Tịch Hội Đồng Quản Trị',
                'level' => 10,
            ],
            [
                'name' => 'Giám Đốc',
                'level' => 20,
            ],
            [
                'name' => 'Phó Giám Đốc',
                'level' => 30,
            ],
            [
                'name' => 'Trưởng Phòng',
                'level' => 40,
            ],
            [
                'name' => 'Kế Toán Trưởng',
                'level' => 50,
            ],
            [
                'name' => 'Phó Trưởng Phòng',
                'level' => 60,
            ],
            [
                'name' => 'Phó Trưởng Phòng Phụ Trách Khoa Học',
                'level' => 70,
            ],
            [
                'name' => 'Phó Trưởng Phòng Phụ Trách Hành Chính',
                'level' => 80,
            ],
            [
                'name' => 'Chuyên Gia',
                'level' => 90,
            ],
            [
                'name' => 'Kế Toán Tổng Hợp',
                'level' => 100,
            ],
            [
                'name' => 'Nghiên Cứu Viên',
                'level' => 110,
            ],
            [
                'name' => 'Kỹ Thuật Viên',
                'level' => 120,
            ],
            [
                'name' => 'Kế Toán Viên',
                'level' => 130,
            ],
            [
                'name' => 'Văn Thư',
                'level' => 140,
            ],
            [
                'name' => 'Nhân Viên',
                'level' => 150,
            ],
            [
                'name' => 'Cộng Tác Viên',
                'level' => 160,
            ],
        ]));
    }
}
