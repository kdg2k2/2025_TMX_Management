<?php

namespace Database\Seeders;

use App\Models\ContractType;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ContractTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ContractType::truncate();
        ContractType::insert([
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
        ]);
    }
}
