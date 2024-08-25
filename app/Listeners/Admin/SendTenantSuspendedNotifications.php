<?php

namespace App\Listeners\Admin;

use App\Events\Admin\TenantSuspended;
use App\Notifications\Admin\AccountSuspensionNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendTenantSuspendedNotifications
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
     * @param  TenantSuspended  $event
     * @return void
     */
    public function handle($event)
    {
        if (!empty($event->tenant)) {
            Notification::route('mail', $event->tenant->email)->notify(new AccountSuspensionNotification($event->tenant));
        }
    }
}
