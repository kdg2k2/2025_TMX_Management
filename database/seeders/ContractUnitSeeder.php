<?php

namespace Database\Seeders;

use App\Models\ContractUnit;
use App\Traits\CheckLocalTraits;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContractUnitSeeder extends Seeder
{
    use CheckLocalTraits;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if ($this->isLocal()) {
            ContractUnit::truncate();
            ContractUnit::factory()->count(10)->create();
        }
    }
}
