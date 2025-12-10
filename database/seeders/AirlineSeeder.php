<?php

namespace Database\Seeders;

use App\Models\Airline;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AirlineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $arr = [
            ['name' => 'Vietnam Airline'],
            ['name' => 'Vietjet Air'],
            ['name' => 'Jetstar Pacific Airlines'],
            ['name' => 'Bamboo Airways'],
        ];
        foreach ($arr as $item) {
            Airline::updateOrCreate([
                'name' => $item['name'],
            ], $item);
        }
    }
}
