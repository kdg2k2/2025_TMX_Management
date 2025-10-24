<?php

namespace Database\Seeders;

use App\Models\Bidding;
use App\Models\Eligibility;
use App\Models\Personnel;
use App\Models\PersonnelCustomField;
use App\Models\PersonnelFile;
use App\Models\PersonnelUnit;
use App\Models\ProofContract;
use App\Models\SoftwareOwnership;
use App\Models\User;
use Illuminate\Database\Seeder;

class PersonnelSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo users
        $users = User::factory(5)->create();

        // Tạo các bảng đơn giản
        Bidding::factory(10)->create(['created_by' => $users->random()->id]);
        Eligibility::factory(10)->create(['created_by' => $users->random()->id]);
        ProofContract::factory(10)->create(['created_by' => $users->random()->id]);
        SoftwareOwnership::factory(10)->create(['created_by' => $users->random()->id]);

        // Tạo đơn vị nhân sự
        $units = PersonnelUnit::factory(5)->create(['created_by' => $users->random()->id]);

        // Tạo nhân sự
        $personnels = Personnel::factory(20)->create([
            'created_by' => $users->random()->id,
            'personnel_unit_id' => $units->random()->id,
        ]);

        // Tạo loại file nhân sự và file
        $fileTypes = collect(['Bằng cấp', 'Chứng chỉ', 'Hợp đồng', 'Hồ sơ'])->map(function ($name) {
            return \App\Models\PersonnelFileType::create(['name' => $name, 'description' => "Loại file $name"]);
        });

        PersonnelFile::factory(30)->create([
            'personnel_id' => $personnels->random()->id,
            'type_id' => $fileTypes->random()->id,
            'created_by' => $users->random()->id,
        ]);

        // Tạo custom fields
        $customFields = PersonnelCustomField::factory(5)->create(['created_by' => $users->random()->id]);

        // Tạo dữ liệu pivot
        foreach ($personnels as $personnel) {
            foreach ($customFields->random(3) as $field) {
                \DB::table('personnel_pivot_personnel_custom_fields')->insert([
                    'personnel_id' => $personnel->id,
                    'personnel_custom_field_id' => $field->id,
                    'value' => $field->type === 'date' ? now()->format('Y-m-d') : fake()->word(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
