<?php

namespace Database\Factories;

use App\Models\PayrollTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class PayrollTemplateFactory extends Factory
{
    protected $model = PayrollTemplate::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'work_duration' => $this->faker->randomFloat(),
            'duration_unit' => $this->faker->word(),
            'amount_per_duration' => $this->faker->randomFloat(),
            'total_duration_amount' => $this->faker->randomFloat(),
            'picture' => $this->faker->word(),
            'description' => $this->faker->text(),
            'items' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
