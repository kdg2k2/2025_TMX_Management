<?php

namespace Database\Factories;

use App\Models\PersonnelUnit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PersonnelFactory extends Factory
{
    public function definition(): array
    {
        return [
            'created_by' => User::inRandomOrder()->first()->id,
            'name' => fake()->name(),
            'personnel_unit_id' => PersonnelUnit::factory(),
            'educational_level' => fake()->randomElement(['Trung cấp', 'Cao đẳng', 'Đại học', 'Thạc sĩ', 'Tiến sĩ']),
        ];
    }
}
