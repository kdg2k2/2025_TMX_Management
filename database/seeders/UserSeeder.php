<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::truncate();
        User::create([
            'name' => 'Super Admin',
            'email' => 'xmg@ifee.edu.vn',
            'password' => bcrypt('Xmg@@2025'),
            'phone' => null,
            'citizen_identification_number' => null,
            'path' => null,
            'path_signature' => null,
            'department_id' => 4,
            'position_id' => 5,
            'job_title_id' => 15,
        ]);
    }
}
