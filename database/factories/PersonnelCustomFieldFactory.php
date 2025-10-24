<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PersonnelCustomFieldFactory extends Factory
{
    public function definition(): array
    {
        return [
            'z_index' => fake()->numberBetween(1, 10),
            'name' => fake()->unique()->words(2, true),
            'field' => fake()->unique()->slug(2),
            'type' => fake()->randomElement(['text', 'date', 'number']),
            'created_by' => User::inRandomOrder()->first()->id,
        ];
    }
}
