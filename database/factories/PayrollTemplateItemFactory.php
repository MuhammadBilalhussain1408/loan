<?php

namespace Database\Factories;

use App\Models\PayrollTemplateItem;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class PayrollTemplateItemFactory extends Factory
{
    protected $model = PayrollTemplateItem::class;

    public function definition(): array
    {
        return [
            'payroll_template_id' => $this->faker->randomNumber(),
            'payroll_item_id' => $this->faker->randomNumber(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
