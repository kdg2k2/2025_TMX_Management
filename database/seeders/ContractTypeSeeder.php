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
        ContractType::truncate();
        ContractType::insert(array_map(function ($item) {
            $item['created_at'] = now();
            $item['updated_at'] = now();
            return $item;
        }, [
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
        ]));
    }
}
