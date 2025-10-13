<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ContractInvestor>
 */
class ContractInvestorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name_vi' => $this->faker->unique()->company() . ' Việt Nam',
            'name_en' => $this->faker->unique()->company(),
            'address' => $this->faker->optional()->address(),
        ];
    }
}
