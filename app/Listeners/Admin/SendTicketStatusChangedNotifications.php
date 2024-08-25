<?php

namespace App\Listeners\Admin;

use App\Models\Setting;
use App\Notifications\Admin\TenantCreatedNotification;
use App\Notifications\Admin\TicketCreatedTenantNotification;
use App\Notifications\Admin\TicketStatusChangedTenantNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendTicketStatusChangedNotifications implements ShouldQueue
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
        Notification::route('mail', $event->ticket->tenant->email)->notify(new TicketStatusChangedTenantNotification($event->ticket));
    }
}
