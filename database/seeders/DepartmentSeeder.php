<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Department::truncate();
        Department::insert([
            [
                'name' => 'Ban Giám Đốc',
            ],
            [
                'name' => 'Tổng Hợp',
            ],
            [
                'name' => 'Kỹ Thuật',
            ],
            [
                'name' => 'Rnd',
            ],
            [
                'name' => 'Kinh Doanh',
            ],
            [
                'name' => 'Đào Tạo',
            ],
        ]);
    }
}
