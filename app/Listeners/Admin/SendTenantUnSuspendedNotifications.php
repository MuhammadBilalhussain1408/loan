<?php

namespace App\Listeners\Admin;

use App\Events\Admin\TenantUnSuspended;
use App\Notifications\Admin\AccountUnSuspensionNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendTenantUnSuspendedNotifications
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
     * @param  TenantUnSuspended  $event
     * @return void
     */
    public function handle($event)
    {
        if (!empty($event->tenant)) {
            Notification::route('mail', $event->tenant->email)->notify(new AccountUnSuspensionNotification($event->tenant));
        }
    }
}
