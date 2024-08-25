<?php

namespace App\Listeners\Admin;

use App\Models\Setting;
use App\Notifications\Admin\TenantCreatedNotification;
use App\Notifications\Admin\TicketAssignedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendTicketAssignedNotifications implements ShouldQueue
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

        $notification = Notification::route('mail', $event->ticket->staff->email);
        if ($event->ticket->staff->slack_webhook_url) {
            $notification->route('slack', $event->ticket->staff->slack_webhook_url);
        }
        $notification->notify(new TicketAssignedNotification($event->ticket));

    }
}
