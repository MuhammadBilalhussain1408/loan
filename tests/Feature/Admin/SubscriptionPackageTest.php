<?php

namespace Tests\Feature\Admin;

use App\Models\SubscriptionPackage;
use App\Models\User;
use App\Models\Currency;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class SubscriptionPackageTest extends TestCase
{

    public function test_can_view_packages()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        SubscriptionPackage::factory(3)->create();
        $response = $this->actingAs($user)->get('/subscription/package');
        $response->assertInertia(fn(Assert $page) => $page
            ->component('Admin/SubscriptionPackages/Index')
            ->has('subscriptionPackages', fn(Assert $page) => $page
                ->has('data')
                ->has('links')
            )
        );
    }

    public function test_package_can_be_created()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $initialCount = SubscriptionPackage::count();
        $response = $this->actingAs($user)
            ->post('/subscription/package/store', [
                'name' => 'dollar',
                'decimals' => '2',
            ]);
        $this->assertDatabaseCount(SubscriptionPackage::class, $initialCount + 1);
    }

    public function test_package_can_be_updated()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $package = SubscriptionPackage::factory()->create();
        $response = $this->actingAs($user)
            ->put('/subscription/package/' . $package->id . '/update', [
                'name' => 'Updated Name',
            ]);
        $package->refresh();
        $this->assertEquals('Updated Name', $package->name);
    }

    public function test_package_can_be_deleted()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $package = SubscriptionPackage::factory()->create();
        $response = $this->actingAs($user)
            ->delete('/subscription/package/' . $package->id . '/destroy');
        $this->assertNull($package->fresh());
    }
}
