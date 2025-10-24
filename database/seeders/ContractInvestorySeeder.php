<?php

namespace Database\Seeders;

use App\Models\ContractInvestor;
use Illuminate\Database\Seeder;

class ContractInvestorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ContractInvestor::truncate();
        ContractInvestor::factory()->count(10)->create();
    }
}
