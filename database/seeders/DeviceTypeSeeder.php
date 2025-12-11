<?php

namespace Database\Seeders;

use App\Models\DeviceType;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DeviceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $arr = [
            ['name' => 'Camera 360'],
            ['name' => 'Flycam'],
            ['name' => 'Khác'],
            ['name' => 'Máy in'],
            ['name' => 'Máy tính'],
        ];
        foreach ($arr as $item) {
            DeviceType::updateOrCreate([
                'name' => $item['name'],
            ], $item);
        }
    }
}
