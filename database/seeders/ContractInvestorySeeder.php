<?php

namespace Database\Seeders;

use App\Models\ContractInvestor;
use App\Traits\CheckLocalTraits;
use Illuminate\Database\Seeder;

class ContractInvestorySeeder extends Seeder
{
    use CheckLocalTraits;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if ($this->isLocal())
            ContractInvestor::factory()->count(10)->create();
    }
}
