<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Patient;
use App\Models\ReferringPractitioner;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition(): array
    {
        return [
            'created_by_id' => $this->faker->randomNumber(),
            'referring_practitioner_id' => ReferringPractitioner::factory(),
            'type' => 'appointment',
            'created_by_type' => 'user',
            'user_id' => $this->faker->randomNumber(),
            'appointment_category_id' => $this->faker->randomNumber(),
            'appointment_type' => 'appointment',
            'name' => $this->faker->name(),
            'patient_first_name' => $this->faker->firstName(),
            'patient_last_name' => $this->faker->lastName(),
            'patient_mobile' => $this->faker->word(),
            'patient_email' => $this->faker->unique()->safeEmail(),
            'reason' => $this->faker->paragraph(),
            'start_date' => Carbon::now(),
            'start_time' => Carbon::now(),
            'appointment_start_date' => Carbon::now(),
            'end_date' => Carbon::now(),
            'end_time' => Carbon::now(),
            'appointment_end_date' => Carbon::now(),
            'status' => $this->faker->randomElement(['new', 'pending', 'approved', 'cancelled', 'declined', 'completed', 'in_progress', 'missed']),
            'reminder' => $this->faker->boolean(),
            'all_day' => $this->faker->boolean(),
            'missed' => $this->faker->boolean(),
            'remind' => $this->faker->randomElement(['patient', 'doctor', 'both']),
            'remind_via' => $this->faker->randomElement(['sms', 'email', 'both']),
            'remind_time' => $this->faker->randomElement(['0_minutes', '5_minutes', '10_minutes', '15_minutes', '30_minutes', '1_hour', '2_hours', '4_hours', '1_day', '2_days', '4_days', '1_week']),
            'description' => $this->faker->text(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'branch_id' => Branch::factory(),
            'patient_id' => Patient::factory(),
            'doctor_id' => User::factory(),
        ];
    }
}
