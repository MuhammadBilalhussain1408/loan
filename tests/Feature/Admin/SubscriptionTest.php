<?php

namespace Tests\Feature\Admin;

use App\Models\TenantSubscriptionPackageHistory;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class SubscriptionTest extends TestCase
{

    public function test_can_view_subscriptions()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $response = $this->actingAs($user)->get('/subscription');
        $response->assertInertia(fn(Assert $page) => $page
            ->component('Admin/Subscriptions/Index')
            ->has('subscriptions', fn(Assert $page) => $page
                ->has('data')
                ->has('links')
            )
        );
    }
    public function test_subscription_can_be_updated()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $subscription = TenantSubscriptionPackageHistory::factory()->create([
            'from_subscription_package_id' => 1,
            'subscription_package_id' => 1,
            'invoice_id' => 1,
            'tenant_id' => 1,
            'active' => 0,
        ]);
        $response = $this->actingAs($user)
            ->put('/subscription/' . $subscription->id . '/update', [
                'start_date' =>date('Y-m-d'),
                'end_date' =>date('Y-m-d'),
                'active' => 1,
            ]);
        $subscription->refresh();
        $this->assertTrue($subscription->active);
    }

    public function test_subscription_can_be_deleted()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $subscription = TenantSubscriptionPackageHistory::factory()->create([
            'from_subscription_package_id' => 1,
            'subscription_package_id' => 1,
            'invoice_id' => 1,
            'tenant_id' => 1,
            'active' => 0,
        ]);
        $response = $this->actingAs($user)
            ->delete('/subscription/' . $subscription->id . '/destroy');
        $this->assertNull($subscription->fresh());
    }
}
