<?php

namespace Tests\Feature\Admin;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Inertia\Testing\AssertableInertia as Assert;
use Laravel\Jetstream\Jetstream;
use Stancl\Tenancy\Events\TenantCreated;
use Tests\TestCase;

class TenantsTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_can_render_signup_page(): void
    {
        $response = $this->get('/signup');
        $response->assertStatus(200);
    }

    public function test_can_signup()
    {
        Event::fake();
        $initialCount = Tenant::count();
        $response = $this->post('/signup', [
            'name' => fake()->domainName(),
            'subdomain' => fake()->domainName(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->email(),
            'mobile' => fake()->phoneNumber(),
            'country_id' => 1,
            'phone_code' => 1,
            'gender' => 'male',
            'password' => 'password',
            'password_confirmation' => 'password',
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature(),
        ]);
        $this->assertDatabaseCount(Tenant::class, $initialCount + 1);
        $response->assertSeeText('impersonate');
        Event::assertDispatched(TenantCreated::class);

    }

    public function test_can_view_tenants_list()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $response = $this->actingAs($user)->get('/tenant');
        $response->assertOk();
        $response->assertInertia(fn(Assert $page) => $page
            ->component('Admin/Tenants/Index')
            ->has('tenants', fn(Assert $page) => $page
                ->has('data')
                ->has('links')
            )
        );
    }
}
