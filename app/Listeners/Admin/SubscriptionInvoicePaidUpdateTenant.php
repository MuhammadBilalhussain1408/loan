<?php

namespace App\Listeners\Admin;

use App\Events\Admin\InvoicePaymentCreated;
use App\Events\Admin\TenantUnSuspended;
use App\Models\Setting;
use App\Models\TenantSubscriptionPackageHistory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SubscriptionInvoicePaidUpdateTenant implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param InvoicePaymentCreated $event
     * @return void
     */
    public function handle($event)
    {
        $invoice = $event->invoicePayment->invoice;
        $tenant = $invoice->tenant;
        //only run for subscription invoices
        if (!empty($invoice->subscriptionPackage) && !empty($tenant) && $invoice->balance <= 0) {
            if ($invoice->change_plan_on_payment == 1) {
                $subscriptionPackageHistory = new TenantSubscriptionPackageHistory();
                $subscriptionPackageHistory->tenant_id = $tenant->id;
                $subscriptionPackageHistory->active = 1;
                $subscriptionPackageHistory->from_subscription_package_id = $tenant->subscription_package_id;
                $subscriptionPackageHistory->subscription_package_id = $invoice->subscription_package_id;
                $subscriptionPackageHistory->start_date = $invoice->subscription_start_date;
                $subscriptionPackageHistory->end_date = $invoice->subscription_end_date;
                $subscriptionPackageHistory->invoice_id = $invoice->id;
                $subscriptionPackageHistory->save();
                DB::table('tenant_subscription_package_history')
                    ->where('tenant_id', $tenant->id)
                    ->where('active', 1)
                    ->where('id', '!=', $subscriptionPackageHistory->id)
                    ->update([
                        'active' => 0
                    ]);
                $tenant->subscription_package_id = $invoice->subscription_package_id;
                $tenant->is_trial = 0;
            }
            $tenant->subscription_start_date = $invoice->subscription_start_date;
            $tenant->subscription_end_date = $invoice->subscription_end_date;
            $tenant->billing_cycle = $invoice->billing_cycle;
            $tenant->save();
            if (!$tenant->active && !Carbon::today()->greaterThan(Carbon::parse($tenant->subscription_end_date))) {
                $tenant->active = 1;
                $tenant->save();
                event(new TenantUnSuspended($tenant));
            }

        }
    }
}
