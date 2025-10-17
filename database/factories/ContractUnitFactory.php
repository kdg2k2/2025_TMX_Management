<?php

namespace Database\Factories;

use App\Models\ContractUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ContractUnit>
 */
class ContractUnitFactory extends Factory
{
    protected $model = ContractUnit::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->company(),
            'address' => $this->faker->optional()->address(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
