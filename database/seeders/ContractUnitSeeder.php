<?php

namespace Database\Seeders;

use App\Models\ContractUnit;
use Illuminate\Database\Seeder;

class ContractUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ContractUnit::truncate();
        ContractUnit::factory()->count(10)->create();
    }
}
