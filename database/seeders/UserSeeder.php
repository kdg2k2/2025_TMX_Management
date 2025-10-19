<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\JobTitle;
use App\Models\Position;
use App\Models\User;
use App\Traits\CheckLocalTraits;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    use CheckLocalTraits;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::truncate();
        $arr = array_map(function ($item) {
            $item['department_id'] = Department::inRandomOrder()->first()->id;
            $item['position_id'] = Position::inRandomOrder()->first()->id;
            $item['job_title_id'] = JobTitle::inRandomOrder()->first()->id;
            $item['created_at'] = now();
            $item['updated_at'] = now();
            if (!isset($item['email']))
                $item['email'] = strtolower(str_replace(' ', '', app(\App\Services\StringHandlerService::class)->stringToSlug($item['name'])) . '@tanmaixanh.vn');
            if (!isset($item['password']))
                $item['password'] = bcrypt('123456');
            return $item;
        }, [
            [
                'name' => 'Super Admin',
                'email' => 'xmg@ifee.edu.vn',
                'password' => bcrypt('Xmg@@2025'),
            ],
            ...array_map(function ($item) {
                return [
                    'name' => $item,
                ];
            }, [
                'Lê Sỹ Doanh',
                'Phạm Văn Huân',
                'Vũ Thị Kim Oanh',
            ])
        ]);
        User::insert($arr);

        if ($this->isLocal())
            User::factory()->count(10)->create();
    }
}
