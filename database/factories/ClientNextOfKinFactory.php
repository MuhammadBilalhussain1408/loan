<?php

namespace Database\Factories;

use App\Models\ClientNextOfKin;
use App\Models\Country;
use App\Models\Profession;
use App\Models\Title;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ClientNextOfKinFactory extends Factory
{
    protected $model = ClientNextOfKin::class;

    public function definition(): array
    {
        return [
            'created_by_id' => $this->faker->randomNumber(),
            'client_id' => $this->faker->randomNumber(),
            'client_relationship_id' => $this->faker->randomNumber(),
            'first_name' => $this->faker->firstName(),
            'middle_name' => $this->faker->name(),
            'last_name' => $this->faker->lastName(),
            'gender' => $this->faker->word(),
            'marital_status' => $this->faker->word(),
            'mobile' => $this->faker->word(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'dob' => Carbon::now(),
            'address' => $this->faker->address(),
            'city' => $this->faker->city(),
            'state' => $this->faker->word(),
            'zip' => $this->faker->postcode(),
            'employer' => $this->faker->word(),
            'photo' => $this->faker->word(),
            'notes' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'country_id' => Country::factory(),
            'title_id' => Title::factory(),
            'profession_id' => Profession::factory(),
        ];
    }
}
