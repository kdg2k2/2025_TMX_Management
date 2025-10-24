<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SoftwareOwnershipFactory extends Factory
{
    public function definition(): array
    {
        return [
            'created_by' => User::inRandomOrder()->first()->id,
            'name' => fake()->unique()->sentence(3),
            'path' => fake()->optional()->filePath(),
        ];
    }
}
