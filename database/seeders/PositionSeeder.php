<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Position::truncate();
        Position::insert([
            [
                'name' => 'Giám Đốc',
                'level' => 1,
            ],
            [
                'name' => 'Phó Giám Đốc',
                'level' => 2,
            ],
            [
                'name' => 'Trưởng Phòng',
                'level' => 3,
            ],
            [
                'name' => 'Phó Trưởng Phòng',
                'level' => 4,
            ],
            [
                'name' => 'Nhân Viên',
                'level' => 5,
            ],
            [
                'name' => 'Cộng Tác Viên',
                'level' => 6,
            ],
        ]);
    }
}
