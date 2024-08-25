<?php

namespace App\Listeners\Admin;

use App\Models\Setting;
use App\Notifications\Admin\TenantCreatedNotification;
use App\Notifications\Admin\TicketCreatedAdminNotification;
use App\Notifications\Admin\TicketRepliedAdminNotification;
use App\Notifications\Admin\TicketRepliedTenantNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendTicketRepliedNotifications implements ShouldQueue
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
        if ($event->reply->is_tenant) {
            if ($event->reply->ticket->staff) {
                $notification = Notification::route('mail', $event->reply->ticket->staff->email);
                if ($event->reply->ticket->staff->slack_webhook_url) {
                    $notification->route('slack', $event->reply->ticket->staff->slack_webhook_url);
                }
                $notification->notify(new TicketRepliedAdminNotification($event->reply));
            } else {
                Notification::route('mail', Setting::on('mysql')->where('setting_key', 'support_email')->first()->setting_value)->notify(new TicketRepliedAdminNotification($event->reply));
            }

        } else {
            Notification::route('mail', $event->reply->ticket->tenant->email)->notify(new TicketRepliedTenantNotification($event->reply));
        }

    }
}
