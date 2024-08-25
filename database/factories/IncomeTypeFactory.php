<?php

namespace Database\Factories;

use App\Models\IncomeType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class IncomeTypeFactory extends Factory
{
    protected $model = IncomeType::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'income_chart_of_account_id' => $this->faker->randomNumber(),
            'asset_chart_of_account_id' => $this->faker->randomNumber(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
