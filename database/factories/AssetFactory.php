<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\AssetType;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class AssetFactory extends Factory
{
    protected $model = Asset::class;

    public function definition(): array
    {
        return [
            'created_by_id' => $this->faker->randomNumber(),
            'name' => $this->faker->name(),
            'purchase_date' => Carbon::now(),
            'purchase_price' => $this->faker->randomFloat(),
            'replacement_value' => $this->faker->randomFloat(),
            'value' => $this->faker->randomFloat(),
            'life_span' => $this->faker->randomNumber(),
            'salvage_value' => $this->faker->randomFloat(),
            'serial_number' => $this->faker->word(),
            'bought_from' => $this->faker->word(),
            'purchase_year' => $this->faker->word(),
            'notes' => $this->faker->word(),
            'status' => $this->faker->word(),
            'active' => $this->faker->randomNumber(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'asset_type_id' => AssetType::factory(),
            'branch_id' => Branch::factory(),
        ];
    }
}
