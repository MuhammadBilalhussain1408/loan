<?php

namespace App\Listeners;

use App\Events\LoanApplicationApprovalStageNowCurrent;
use App\Events\LoanApplicationApprovalStageStatusChanged;
use App\Events\LoanApplicationStatusChanged;
use App\Models\LoanApplicationHistory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;

class LoanApplicationApprovalStageStatusChangedActions  implements ShouldQueue
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
        //move application to next stage
        if ($event->linkedApprovalStage->status === 'sent_back') {
            $previousStage = $event->linkedApprovalStage->application->stages->where('id', '<', $event->linkedApprovalStage->id)->last();
            if ($previousStage) {
                $previousStage->status = 'pending';
                $previousStage->save();
                event(new LoanApplicationApprovalStageStatusChanged($previousStage));
                $event->linkedApprovalStage->application->current_loan_application_linked_approval_stage_id = $previousStage->id;
                $event->linkedApprovalStage->application->next_loan_application_linked_approval_stage_id = $event->linkedApprovalStage->id;
                $event->linkedApprovalStage->application->save();
                event(new LoanApplicationApprovalStageNowCurrent($previousStage));
            }
        }
        if ($event->linkedApprovalStage->status === 'approved') {
            $nextStage = $event->linkedApprovalStage->application->stages->where('id', '>', $event->linkedApprovalStage->id)->first();
            if ($nextStage) {
                $nextStage->status = 'pending';
                $nextStage->save();
                $event->linkedApprovalStage->application->current_loan_application_linked_approval_stage_id = $nextStage->id;
                if ($nextOfNextStage = $event->linkedApprovalStage->application->stages->where('id', '>', $nextStage->id)->first()) {
                    $event->linkedApprovalStage->application->next_loan_application_linked_approval_stage_id = $nextOfNextStage->id;
                } else {
                    $event->linkedApprovalStage->application->next_loan_application_linked_approval_stage_id = null;
                }
                $event->linkedApprovalStage->application->save();
                event(new LoanApplicationApprovalStageNowCurrent($nextStage));
            } else {
                //this is the last step
                $oldStatus=$event->linkedApprovalStage->application->status;
                $event->linkedApprovalStage->application->status = 'approved';
                $event->linkedApprovalStage->application->save();
                event(new LoanApplicationStatusChanged($event->linkedApprovalStage->application,$oldStatus));
            }
        }
        if ($event->linkedApprovalStage->status === 'rejected') {
            //reject whole application
            $oldStatus=$event->linkedApprovalStage->application->status;
            $event->linkedApprovalStage->application->status = 'rejected';
            $event->linkedApprovalStage->application->save();
            event(new LoanApplicationStatusChanged($event->linkedApprovalStage->application,$oldStatus));
        }
    }
}
