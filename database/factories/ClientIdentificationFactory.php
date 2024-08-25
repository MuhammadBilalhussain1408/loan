<?php

namespace Database\Factories;

use App\Models\ClientIdentification;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ClientIdentificationFactory extends Factory
{
    protected $model = ClientIdentification::class;

    public function definition(): array
    {
        return [
            'created_by_id' => $this->faker->randomNumber(),
            'client_id' => $this->faker->randomNumber(),
            'client_identification_type_id' => $this->faker->randomNumber(),
            'identification_value' => $this->faker->word(),
            'description' => $this->faker->text(),
            'size' => $this->faker->randomNumber(),
            'link' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'status' => $this->faker->word(),
        ];
    }
}
