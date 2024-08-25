<?php

namespace App\Listeners;

use App\Events\LoanApplicationApprovalStageStatusChanged;
use App\Notifications\LoanApplicationApprovalStageStatusChangedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendLoanApplicationApprovalStageStatusChangedNotifications implements ShouldQueue
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
    public function handle(LoanApplicationApprovalStageStatusChanged $event): void
    {
        if (!empty($event->linkedApprovalStage->application->member)) {
            $event->linkedApprovalStage->application->member->notify(new LoanApplicationApprovalStageStatusChangedNotification($event->linkedApprovalStage));
        }
    }
}
