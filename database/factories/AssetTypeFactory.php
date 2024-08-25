<?php

namespace Database\Factories;

use App\Models\AssetType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class AssetTypeFactory extends Factory
{
    protected $model = AssetType::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'type' => $this->faker->word(),
            'chart_of_account_fixed_asset_id' => $this->faker->randomNumber(),
            'chart_of_account_asset_id' => $this->faker->randomNumber(),
            'chart_of_account_contra_asset_id' => $this->faker->randomNumber(),
            'chart_of_account_expense_id' => $this->faker->randomNumber(),
            'chart_of_account_liability_id' => $this->faker->randomNumber(),
            'chart_of_account_income_id' => $this->faker->randomNumber(),
            'notes' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
