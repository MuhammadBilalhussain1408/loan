<?php

namespace Tests\Feature\Admin;

use App\Events\Admin\TicketAssigned;
use App\Events\Admin\TicketCreated;
use App\Events\Admin\TicketStatusChanged;
use App\Events\Admin\TicketUpdated;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class TicketsTest extends TestCase
{

    public function test_can_view_tickets()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $response = $this->actingAs($user)->get('/ticket');
        $response->assertInertia(fn(Assert $page) => $page
            ->component('Admin/Tickets/Index')
            ->has('tickets', fn(Assert $page) => $page
                ->has('data')
                ->has('links')
            )
        );
    }

    public function test_ticket_can_be_created()
    {
        Event::fake();
        $user = User::factory()->create();
        $user->assignRole('admin');
        $initialCount = Ticket::count();
        $response = $this->actingAs($user)
            ->post('/ticket/store', [
                'tenant_id' => 1,
                'ticket_department_id' => 1,
                'status' => 'active',
                'priority' => 'low',
                'subject' => 'test',
                'tags' => ['test'],
                'description' => fake()->paragraph(),
            ]);
        $this->assertDatabaseCount(Ticket::class, $initialCount + 1);
        Event::assertDispatched(TicketCreated::class);
    }

    public function test_ticket_can_be_updated()
    {
        Event::fake();
        $user = User::factory()->create();
        $user->assignRole('admin');
        $ticket = Ticket::factory()->create([
            'ticket_department_id' => 1,
            'status' => 'new',
            'priority' => 'low',
        ]);
        $response = $this->actingAs($user)
            ->put('/ticket/' . $ticket->id . '/update', [
                'ticket_department_id' => 2,
                'status' => 'active',
                'priority' => 'high',
            ]);
        Event::assertDispatched(TicketUpdated::class);
        $ticket->refresh();
        $this->assertEquals(2, $ticket->ticket_department_id);
        $this->assertEquals('active', $ticket->status);
        $this->assertEquals('high', $ticket->priority);
    }

    public function test_ticket_can_be_deleted()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $ticket = Ticket::factory()->create();
        $response = $this->actingAs($user)
            ->delete('/ticket/' . $ticket->id . '/destroy');
        $this->assertNull($ticket->fresh());
    }

    public function test_can_view_ticket_details()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $ticket = Ticket::factory()->create([
            'subject' => 'Test Subject',
            'ticket_department_id' => 1,
            'status' => 'new',
            'priority' => 'low',
        ]);
        $response = $this->actingAs($user)->get('/ticket/' . $ticket->id . '/show');
        $response->assertInertia(fn(Assert $page) => $page
            ->component('Admin/Tickets/Show')
            ->has('ticket', fn(Assert $page) => $page
                ->where('id', $ticket->id)
                ->where('subject', 'Test Subject')
                ->where('status', 'new')
                ->where('priority', 'low')
                ->etc()
            )
        );
    }

    public function test_ticket_can_change_ticket_status()
    {
        Event::fake();
        $user = User::factory()->create();
        $user->assignRole('admin');
        $ticket = Ticket::factory()->create([
            'status' => 'new',
        ]);
        $response = $this->actingAs($user)
            ->put('/ticket/' . $ticket->id . '/change_status', [
                'status' => 'active',
            ]);
        Event::assertDispatched(TicketUpdated::class);
        Event::assertDispatched(TicketStatusChanged::class);
        $ticket->refresh();
        $this->assertEquals('active', $ticket->status);
    }

    public function test_ticket_can_assign_staff()
    {
        Event::fake();
        $user = User::factory()->create();
        $user->assignRole('admin');
        $ticket = Ticket::factory()->create([
            'status' => 'new',
        ]);
        $response = $this->actingAs($user)
            ->put('/ticket/' . $ticket->id . '/assign_staff', [
                'staff_id' => $user->id,
            ]);
        Event::assertDispatched(TicketAssigned::class);
        $ticket->refresh();
        $this->assertEquals($user->id, $ticket->staff->id);
    }
}
