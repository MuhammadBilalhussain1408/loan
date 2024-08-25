<?php

namespace Database\Factories;

use App\Models\Tenant;
use App\Models\Ticket;
use App\Models\TicketDepartment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        return [
            'status' => $this->faker->randomElement(['new','open','active','in_progress','on_hold','awaiting_reply','answered','closed']),
            'priority' => $this->faker->randomElement(['low','medium','high','critical']),
            'reference' => $this->faker->word(),
            'name' => $this->faker->name(),
            'staff_name' => $this->faker->name(),
            'cc' => $this->faker->word(),
            'subject' => $this->faker->word(),
            //'tags' => ['sales', 'php', 'error'],
            'description' => $this->faker->paragraph(),
            'date_completed' => Carbon::now(),
            'date_status_changed' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'created_by_id' => User::factory(),
            'tenant_id' => 1,
            'ticket_department_id' => $this->faker->randomElement([1, 2, 3]),
            'staff_id' => User::factory(),
        ];
    }
}
