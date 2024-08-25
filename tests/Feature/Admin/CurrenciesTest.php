<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Currency;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class CurrenciesTest extends TestCase
{

    public function test_can_view_currencies()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        Currency::factory(3)->create();
        $response = $this->actingAs($user)->get('/currency');
        $response->assertInertia(fn(Assert $page) => $page
            ->component('Admin/Currencies/Index')
            ->has('currencies', fn(Assert $page) => $page
                ->has('data')
                ->has('links')
            )
        );
    }

    public function test_currency_can_be_created()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $initialCount = Currency::count();
        $response = $this->actingAs($user)
            ->post('/currency/store', [
                'name' => 'dollar',
                'decimals' => '2',
            ]);
        $this->assertDatabaseCount(Currency::class, $initialCount + 1);
        $response->assertRedirect('/currency');
    }

    public function test_currency_can_be_updated()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $currency = Currency::factory()->create();
        $response = $this->actingAs($user)
            ->put('/currency/' . $currency->id . '/update', [
                'name' => 'Updated Name',
            ]);
        $currency->refresh();
        $this->assertEquals('Updated Name', $currency->name);
    }

    public function test_currency_can_be_deleted()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $currency = Currency::factory()->create();
        $response = $this->actingAs($user)
            ->delete('/currency/' . $currency->id . '/destroy');
        $this->assertNull($currency->fresh());
    }
}
