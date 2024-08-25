<?php

namespace App\Listeners;

use App\Events\LoanApplicationCreated;
use App\Models\Setting;
use App\Notifications\LoanApplicationCreatedAdminNotification;
use App\Notifications\LoanApplicationCreatedMemberNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;
use Stripe\Util\Set;

class SendLoanApplicationCreatedNotifications implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(LoanApplicationCreated $event): void
    {
        //admin notification
        Notification::route('mail', Setting::where('setting_key', 'company_email')->first()->setting_value)->notify(new LoanApplicationCreatedAdminNotification($event->application));
        $event->application->member->notify(new LoanApplicationCreatedMemberNotification($event->application));
    }
}
