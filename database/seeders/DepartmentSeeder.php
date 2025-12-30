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
        foreach ([
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
        ] as $item)
            Department::updateOrCreate(['name' => $item['name']], $item);
    }
}
