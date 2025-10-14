<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use DB;

class DatabaseSeeder extends Seeder
{
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
            ContractInvestorySeeder::class,
            UserSeeder::class,
        ]);

        Artisan::call('db:fix-auto-increment');

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
