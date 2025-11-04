<?php

namespace Database\Seeders;

use App\Traits\CheckLocalTraits;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use DB;

class DatabaseSeeder extends Seeder
{
    use CheckLocalTraits;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $this->call([
            FileExtensionSeeder::class,
            DepartmentSeeder::class,
            PositionSeeder::class,
            JobTitleSeeder::class,
            ProvinceSeeder::class,
            CommuneSeeder::class,
            ContractTypeSeeder::class,
            ContractFileTypeSeeder::class,
            ContractScanFileTypeSeeder::class,
            UserSeeder::class,
            TaskScheduleSeeder::class,
        ]);

        if ($this->isLocal())
            $this->call([
                ContractInvestorySeeder::class,
                ContractUnitSeeder::class,
                PersonnelSeeder::class,
            ]);

        Artisan::call('db:fix-auto-increment');

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
