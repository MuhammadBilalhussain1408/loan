<?php

namespace App\Listeners\Admin;

use App\Events\Admin\InvoicePaymentCreated;
use App\Models\Setting;
use App\Notifications\Admin\InvoicePaymentReceived;
use App\Notifications\Admin\InvoicePaymentReceivedAdminNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class SendInvoicePaymentNotifications implements ShouldQueue
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
        $invoicePayment = $event->invoicePayment;
        if (!empty($invoicePayment->tenant)) {
            Notification::route('mail', $invoicePayment->tenant->email)->notify(new InvoicePaymentReceived($invoicePayment));
        }
        Notification::route('mail', DB::connection('mysql')->table('settings')->where('setting_key', 'sales_email')->first()->setting_value)->route('slack', config('slack.channels.sales'))->notify(new InvoicePaymentReceivedAdminNotification($invoicePayment));

    }
}
