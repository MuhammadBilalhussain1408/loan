<?php

namespace Database\Factories;

use App\Models\File;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class FileFactory extends Factory
{
    protected $model = File::class;

    public function definition(): array
    {
        return [
            'created_by_id' => $this->faker->randomNumber(),
            'record_id' => $this->faker->randomNumber(),
            'category' => $this->faker->word(),
            'disk' => $this->faker->word(),
            'name' => $this->faker->name(),
            'description' => $this->faker->text(),
            'file_size' => $this->faker->randomNumber(),
            'link' => $this->faker->word(),
            'mime_type' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
