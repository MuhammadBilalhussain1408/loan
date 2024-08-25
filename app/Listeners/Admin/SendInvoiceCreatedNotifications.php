<?php

namespace App\Listeners\Admin;

use App\Events\Admin\InvoiceCreated;
use App\Notifications\Admin\InvoiceGeneratedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendInvoiceCreatedNotifications
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
     * @param InvoiceCreated $event
     * @return void
     */
    public function handle($event)
    {
        if (!empty($event->invoice->tenant)) {
            Notification::route('mail', $event->invoice->tenant->email)->notify(new InvoiceGeneratedNotification($event->invoice));
        }

    }
}
