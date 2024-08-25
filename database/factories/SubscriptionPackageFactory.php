<?php

namespace Database\Factories;

use App\Models\SubscriptionPackage;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class SubscriptionPackageFactory extends Factory
{
    protected $model = SubscriptionPackage::class;

    public function definition(): array
    {
        return [
            'created_by_id' => $this->faker->randomNumber(),
            'name' => $this->faker->word(),
            'days' => $this->faker->randomNumber(),
            'trial_days' => $this->faker->randomNumber(),
            'users' => $this->faker->randomNumber(),
            'patients' => $this->faker->randomNumber(),
            'appointments' => $this->faker->randomNumber(),
            'tariffs' => $this->faker->randomNumber(),
            'branches' => $this->faker->randomNumber(),
            'price' => $this->faker->randomFloat(),
            'annual_price' => $this->faker->randomFloat(),
            'monthly_price' => $this->faker->randomFloat(),
            'quarterly_price' => $this->faker->randomFloat(),
            'semi_annual_price' => $this->faker->randomFloat(),
            'is_trial' => $this->faker->boolean(),
            'modules' => [],
            'description' => $this->faker->text(),
            'icon' => $this->faker->word(),
            'active' => $this->faker->boolean(),
            'featured' => $this->faker->boolean(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
