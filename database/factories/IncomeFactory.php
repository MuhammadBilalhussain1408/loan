<?php

namespace Database\Factories;

use App\Models\Income;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class IncomeFactory extends Factory
{
    protected $model = Income::class;

    public function definition(): array
    {
        return [
            'created_by_id' => $this->faker->randomNumber(),
            'income_type_id' => $this->faker->randomNumber(),
            'currency_id' => $this->faker->randomNumber(),
            'branch_id' => $this->faker->randomNumber(),
            'income_chart_of_account_id' => $this->faker->randomNumber(),
            'asset_chart_of_account_id' => $this->faker->randomNumber(),
            'amount' => $this->faker->randomFloat(),
            'date' => Carbon::now(),
            'recurring' => $this->faker->randomNumber(),
            'recur_frequency' => $this->faker->word(),
            'recur_start_date' => Carbon::now(),
            'recur_end_date' => Carbon::now(),
            'recur_next_date' => Carbon::now(),
            'recur_type' => $this->faker->word(),
            'notes' => $this->faker->word(),
            'description' => $this->faker->text(),
            'files' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
