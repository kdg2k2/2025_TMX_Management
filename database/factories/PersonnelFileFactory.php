<?php

namespace Database\Factories;

use App\Models\Personnel;
use App\Models\PersonnelFileType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PersonnelFileFactory extends Factory
{
    public function definition(): array
    {
        return [
            'personnel_id' => Personnel::factory(),
            'type_id' => PersonnelFileType::factory(),
            'path' => fake()->optional()->filePath(),
            'created_by' => User::inRandomOrder()->first()->id,
        ];
    }
}
