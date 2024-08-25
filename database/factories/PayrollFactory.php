<?php

namespace Database\Factories;

use App\Models\Payroll;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class PayrollFactory extends Factory
{
    protected $model = Payroll::class;

    public function definition(): array
    {
        return [
            'branch_id' => $this->faker->randomNumber(),
            'currency_id' => $this->faker->randomNumber(),
            'created_by_id' => $this->faker->randomNumber(),
            'payroll_template_id' => $this->faker->randomNumber(),
            'user_id' => $this->faker->randomNumber(),
            'employee_name' => $this->faker->name(),
            'bank_name' => $this->faker->name(),
            'account_number' => $this->faker->word(),
            'description' => $this->faker->text(),
            'comments' => $this->faker->word(),
            'work_duration' => $this->faker->randomFloat(),
            'duration_unit' => $this->faker->word(),
            'amount_per_duration' => $this->faker->randomFloat(),
            'total_duration_amount' => $this->faker->randomFloat(),
            'gross_amount' => $this->faker->randomFloat(),
            'total_allowances' => $this->faker->randomFloat(),
            'total_deductions' => $this->faker->randomFloat(),
            'date' => Carbon::now(),
            'year' => $this->faker->word(),
            'month' => $this->faker->word(),
            'recurring' => $this->faker->randomNumber(),
            'recur_frequency' => $this->faker->word(),
            'recur_start_date' => Carbon::now(),
            'recur_end_date' => Carbon::now(),
            'recur_next_date' => Carbon::now(),
            'recur_type' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
