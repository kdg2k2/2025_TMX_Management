<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\JobTitle;
use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => bcrypt('password'),
            'phone' => fake()->unique()->numerify('##########'),
            'citizen_identification_number' => fake()->unique()->numerify('############'),
            'path' => null,
            'path_signature' => null,
            'department_id' => optional(Department::inRandomOrder()->first())->id,
            'position_id' => optional(Position::inRandomOrder()->first())->id,
            'job_title_id' => optional(JobTitle::inRandomOrder()->first())->id,
            'is_banned' => false,
            'retired' => false,
            'jwt_version' => 1,
            'remember_token' => null,
        ];
    }
}
