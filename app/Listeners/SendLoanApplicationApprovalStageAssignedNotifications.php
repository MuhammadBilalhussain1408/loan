<?php

namespace App\Listeners;

use App\Events\LoanApplicationApprovalStageAssigned;
use App\Notifications\LoanApplicationApprovalStageAssignedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendLoanApplicationApprovalStageAssignedNotifications implements ShouldQueue
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
    public function handle(LoanApplicationApprovalStageAssigned $event): void
    {
        if ($event->linkedApprovalStage->assignedTo) {
            $event->linkedApprovalStage->assignedTo->notify(new LoanApplicationApprovalStageAssignedNotification($event->linkedApprovalStage));
        }
    }
}
