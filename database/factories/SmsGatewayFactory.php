<?php

namespace Database\Factories;

use App\Models\SmsGateway;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class SmsGatewayFactory extends Factory
{
    protected $model = SmsGateway::class;

    public function definition(): array
    {
        return [
            'created_by_id' => $this->faker->randomNumber(),
            'name' => $this->faker->word(),
            'system_name' => $this->faker->word(),
            'to_name' => $this->faker->word(),
            'url' => $this->faker->url(),
            'msg_name' => $this->faker->word(),
            'active' => $this->faker->boolean(),
            'is_current' => $this->faker->boolean(),
            'http_api' => $this->faker->randomNumber(),
            'options' => $this->faker->word(),
            'description' => $this->faker->text(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'is_system' => $this->faker->boolean(),
        ];
    }
}
