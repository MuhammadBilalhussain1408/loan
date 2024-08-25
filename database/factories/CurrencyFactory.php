<?php

namespace Database\Factories;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class CurrencyFactory extends Factory
{
    protected $model = Currency::class;

    public function definition(): array
    {
        return [
            'created_by_id' => $this->faker->randomNumber(),
            'name' => $this->faker->word(),
            'code' => $this->faker->word(),
            'symbol' => $this->faker->word(),
            'decimals' => $this->faker->randomNumber(),
            'xrate' => $this->faker->randomFloat(),
            'international_code' => $this->faker->word(),
            'active' => $this->faker->boolean(),
            'is_default' => $this->faker->boolean(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
