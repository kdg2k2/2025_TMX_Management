<?php

namespace Database\Seeders;

use App\Models\JobTitle;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JobTitleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        JobTitle::truncate();
        JobTitle::insert([
            [
                'name' => 'Chủ Tịch Hội Đồng Quản Trị',
                'level' => 100,
            ],
            [
                'name' => 'Giám Đốc',
                'level' => 90,
            ],
            [
                'name' => 'Phó Giám Đốc',
                'level' => 80,
            ],
            [
                'name' => 'Trưởng Phòng',
                'level' => 70,
            ],
            [
                'name' => 'Kế Toán Trưởng',
                'level' => 65,
            ],
            [
                'name' => 'Phó Trưởng Phòng',
                'level' => 60,
            ],
            [
                'name' => 'Phó Trưởng Phòng Phụ Trách Khoa Học',
                'level' => 58,
            ],
            [
                'name' => 'Phó Trưởng Phòng Phụ Trách Hành Chính',
                'level' => 57,
            ],
            [
                'name' => 'Chuyên Gia',
                'level' => 55,
            ],
            [
                'name' => 'Kế Toán Tổng Hợp',
                'level' => 50,
            ],
            [
                'name' => 'Nghiên Cứu Viên',
                'level' => 45,
            ],
            [
                'name' => 'Kỹ Thuật Viên',
                'level' => 40,
            ],
            [
                'name' => 'Kế Toán Viên',
                'level' => 35,
            ],
            [
                'name' => 'Văn Thư',
                'level' => 30,
            ],
            [
                'name' => 'Nhân Viên',
                'level' => 20,
            ],
            [
                'name' => 'Cộng Tác Viên',
                'level' => 10,
            ],
        ]);
    }
}
