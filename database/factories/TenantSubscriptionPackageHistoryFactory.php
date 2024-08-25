<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\SubscriptionPackage;
use App\Models\Tenant;
use App\Models\TenantSubscriptionPackageHistory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class TenantSubscriptionPackageHistoryFactory extends Factory
{
    protected $model = TenantSubscriptionPackageHistory::class;

    public function definition(): array
    {
        return [
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now(),
            'active' => $this->faker->boolean(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'from_subscription_package_id' => SubscriptionPackage::factory(),
            'subscription_package_id' => SubscriptionPackage::factory(),
            'invoice_id' => Invoice::factory(),
            'tenant_id' => Tenant::factory(),
        ];
    }
}
