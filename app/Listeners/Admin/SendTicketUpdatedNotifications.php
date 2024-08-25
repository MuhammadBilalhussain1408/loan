<?php

namespace App\Listeners\Admin;

use App\Models\Setting;
use App\Notifications\Admin\TenantCreatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendTicketUpdatedNotifications implements ShouldQueue
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
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        //send notification to admin
        Notification::route('mail', Setting::where('setting_key', 'company_email')->first()->setting_value)->notify(new TenantCreatedNotification($event->tenant));
    }
}
