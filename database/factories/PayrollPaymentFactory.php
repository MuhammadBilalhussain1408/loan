<?php

namespace Database\Factories;

use App\Models\PayrollPayment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class PayrollPaymentFactory extends Factory
{
    protected $model = PayrollPayment::class;

    public function definition(): array
    {
        return [
            'created_by_id' => $this->faker->randomNumber(),
            'branch_id' => $this->faker->randomNumber(),
            'payroll_id' => $this->faker->randomNumber(),
            'payment_type_id' => $this->faker->randomNumber(),
            'payment_detail_id' => $this->faker->randomNumber(),
            'amount' => $this->faker->randomFloat(),
            'xrate' => $this->faker->randomFloat(),
            'submitted_on' => Carbon::now(),
            'notes' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
