<?php

namespace App\Listeners\Admin;

use App\Models\Setting;
use App\Notifications\Admin\TenantCreatedNotification;
use App\Notifications\Admin\TicketCreatedAdminNotification;
use App\Notifications\Admin\TicketCreatedTenantNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendTicketCreatedNotifications implements ShouldQueue
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
        Notification::route('mail', Setting::on('mysql')->where('setting_key', 'support_email')->first()->setting_value)->notify(new TicketCreatedAdminNotification($event->ticket));
        //send to tenant
        Notification::route('mail', $event->ticket->tenant->email)->notify(new TicketCreatedTenantNotification($event->ticket));

    }
}
