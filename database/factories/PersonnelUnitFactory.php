<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PersonnelUnitFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->company();
        return [
            'created_by' => User::inRandomOrder()->first()->id,
            'name' => $name,
            'short_name' => strtoupper(substr($name, 0, 3)),
        ];
    }
}
