<?php

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class TenantFactory extends Factory
{
    protected $model = Tenant::class;

    public function definition(): array
    {
        return [
            'created_by_id' => $this->faker->randomNumber(),
            'country_id' => $this->faker->randomNumber(),
            'billing_country_id' => $this->faker->randomNumber(),
            'timezone_id' => $this->faker->randomNumber(),
            'currency_id' => $this->faker->randomNumber(),
            'staff_id' => $this->faker->randomNumber(),
            'subscription_package_id' => $this->faker->randomNumber(),
            'name' => $this->faker->name(),
            'reference' => $this->faker->word(),
            'mobile' => $this->faker->word(),
            'phone' => $this->faker->phoneNumber(),
            'vat' => $this->faker->word(),
            'website' => $this->faker->word(),
            'email' => $this->faker->unique()->safeEmail(),
            'external_id' => $this->faker->word(),
            'address' => $this->faker->address(),
            'billing_address' => $this->faker->address(),
            'city' => $this->faker->city(),
            'billing_city' => $this->faker->city(),
            'state' => $this->faker->word(),
            'billing_state' => $this->faker->word(),
            'zip' => $this->faker->postcode(),
            'billing_zip' => $this->faker->postcode(),
            'trial_start_date' => Carbon::now(),
            'trial_end_date' => Carbon::now(),
            'subscription_start_date' => Carbon::now(),
            'subscription_end_date' => Carbon::now(),
            'logo' => $this->faker->word(),
            'description' => $this->faker->text(),
            'joined_date' => Carbon::now(),
            'activation_date' => Carbon::now(),
            'active' => $this->faker->randomNumber(),
            'never_suspend' => $this->faker->randomNumber(),
            'is_system' => $this->faker->randomNumber(),
            'billing_cycle' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'data' => $this->faker->words(),
            'is_trial' => $this->faker->randomNumber(),
        ];
    }
}
