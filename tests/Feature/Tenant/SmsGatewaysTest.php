<?php

namespace Tests\Feature\Tenant;

use App\Models\User;
use App\Models\SmsGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class SmsGatewaysTest extends TestCase
{
    protected $tenancy = true;

    public function test_sms_gateways_page_can_be_rendered()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $response = $this->actingAs($user)->get('/communication/sms_gateway');
        $response->assertStatus(200);
        $response->assertInertia(fn(Assert $page) => $page
            ->component('SmsGateways/Index')
            ->has('smsGateways', fn(Assert $page) => $page
                ->has('data')
                ->has('links')
            )
        );

    }

    public function test_sms_gateway_can_be_created()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $initialCount = SmsGateway::count();
        $response = $this->actingAs($user)
            ->post('/communication/sms_gateway/store', [
                'name' => 'econet',
            ]);
        $this->assertDatabaseCount(SmsGateway::class, $initialCount + 1);
        $response->assertRedirect('/communication/sms_gateway');
    }

    public function test_sms_gateway_can_be_updated()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $smsGateway = SmsGateway::factory()->create([
            'name' => 'econet'
        ]);
        $response = $this->actingAs($user)
            ->withSession(['banned' => false])
            ->put('/communication/sms_gateway/' . $smsGateway->id . '/update', [
                'name' => 'netone',
            ]);
        $smsGateway->refresh();
        $this->assertEquals('netone', $smsGateway->name);
    }

    public function test_sms_gateway_can_be_deleted()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $smsGateway = SmsGateway::factory()->create([
            'is_system' => 0
        ]);
        $this->actingAs($user)
            ->withSession(['banned' => false])
            ->delete('/communication/sms_gateway/' . $smsGateway->id . '/destroy');
        $this->assertNull($smsGateway->fresh());

    }

    public function test_system_sms_gateway_can_not_be_deleted()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $smsGateway = SmsGateway::factory()->create([
            'is_system' => 1
        ]);
        $this->actingAs($user)
            ->withSession(['banned' => false])
            ->delete('/communication/sms_gateway/' . $smsGateway->id . '/destroy');
        $this->assertNotNull($smsGateway->fresh());

    }
}
