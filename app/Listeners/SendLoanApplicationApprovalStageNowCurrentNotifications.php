<?php

namespace App\Listeners;

use App\Events\LoanApplicationApprovalStageNowCurrent;
use App\Notifications\AssignedLoanApplicationApprovalStageNowCurrentNotification;
use App\Notifications\LoanApplicationApprovalStageAssignedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendLoanApplicationApprovalStageNowCurrentNotifications  implements ShouldQueue
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
    public function handle(LoanApplicationApprovalStageNowCurrent $event): void
    {
        if ($event->linkedApprovalStage->assignedTo) {
            $event->linkedApprovalStage->assignedTo->notify(new AssignedLoanApplicationApprovalStageNowCurrentNotification($event->linkedApprovalStage));
        }
    }
}
