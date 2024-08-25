<?php

namespace Database\Factories;

use App\Models\CommunicationCampaign;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class CommunicationCampaignFactory extends Factory
{
    protected $model = CommunicationCampaign::class;

    public function definition(): array
    {
        return [
            'sms_gateway_id' => $this->faker->randomNumber(),
            'communication_campaign_business_rule_id' => $this->faker->randomNumber(),
            'communication_campaign_attachment_type_id' => $this->faker->randomNumber(),
            'branch_id' => $this->faker->randomNumber(),
            'client_id' => $this->faker->randomNumber(),
            'user_id' => $this->faker->randomNumber(),
            'loan_officer_id' => $this->faker->randomNumber(),
            'loan_product_id' => $this->faker->randomNumber(),
            'savings_product_id' => $this->faker->randomNumber(),
            'share_product_id' => $this->faker->randomNumber(),
            'subject' => $this->faker->word(),
            'name' => $this->faker->name(),
            'campaign_type' => $this->faker->word(),
            'trigger_type' => $this->faker->word(),
            'scheduled_date' => Carbon::now(),
            'scheduled_time' => $this->faker->word(),
            'schedule_frequency' => $this->faker->randomNumber(),
            'schedule_frequency_type' => $this->faker->word(),
            'selected_days' => $this->faker->words(),
            'scheduled_next_run_date' => Carbon::now(),
            'scheduled_last_run_date' => Carbon::now(),
            'from_x' => $this->faker->randomNumber(),
            'to_y' => $this->faker->randomNumber(),
            'cycle_x' => $this->faker->randomNumber(),
            'cycle_y' => $this->faker->randomNumber(),
            'overdue_x' => $this->faker->randomNumber(),
            'overdue_y' => $this->faker->randomNumber(),
            'active' => $this->faker->boolean(),
            'status' => $this->faker->word(),
            'description' => $this->faker->text(),
            'notes' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'doctor_id' => $this->faker->randomNumber(),
            'nurse_id' => $this->faker->randomNumber(),
            'co_payer_id' => $this->faker->randomNumber(),
            'patient_id' => $this->faker->randomNumber(),
            'currency_id' => $this->faker->randomNumber(),

            'created_by_id' => User::factory(),
        ];
    }
}
