<?php

namespace Database\Seeders;

use App\Models\PlaneTicketClass;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlaneTicketClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $arr = [
            ['name' => 'Hạng nhất'],
            ['name' => 'Thương gia'],
            ['name' => 'Phổ thông đặc biệt'],
            ['name' => 'Phổ thông'],
        ];
        foreach ($arr as $item) {
            PlaneTicketClass::updateOrCreate([
                'name' => $item['name'],
            ], $item);
        }
    }
}
