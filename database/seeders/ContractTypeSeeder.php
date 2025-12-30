<?php

namespace Database\Seeders;

use App\Models\ContractType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContractTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ([
            [
                'name' => 'Thầu phụ',
            ],
            [
                'name' => 'Liên danh',
            ],
            [
                'name' => 'Độc lập',
            ],
            [
                'name' => 'Tư vấn cá nhân',
            ],
        ] as $item)
            ContractType::updateOrCreate(['name' => $item['name']], $item);
    }
}
