<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class FinancialPeriodFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            
            'name' => $this->faker->name(),
            'start_date' => $this->faker->date(),
        ];
    }
}
