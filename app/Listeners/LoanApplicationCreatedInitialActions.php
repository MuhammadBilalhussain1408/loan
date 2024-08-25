<?php

namespace App\Listeners;

use App\Events\LoanApplicationApprovalStageAssigned;
use App\Events\LoanApplicationCreated;
use App\Events\LoanApplicationCurrentApprovalStageChanged;
use App\Models\LoanApplicationApprovalStage;
use App\Models\LoanApplicationHistory;
use App\Models\LoanApplicationLinkedApprovalStage;
use App\Models\LoanApplicationLinkedChecklistItem;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;

class LoanApplicationCreatedInitialActions  implements ShouldQueue
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
        $application = $event->application;
        //save approval stages
        $approvalStages = LoanApplicationApprovalStage::orderBy('field_position')->get();
        $count = 0;
        $approvalStages->each(function ($stage) use ($application, &$count) {
            $linkedStage = new LoanApplicationLinkedApprovalStage();
            $linkedStage->loan_application_id = $application->id;
            $linkedStage->loan_application_approval_stage_id = $stage->id;
            $linkedStage->name = $stage->name;
            $linkedStage->status = 'pending';
            if ($stage->assigned_to_id) {
                $linkedStage->assigned_to_id = $stage->assigned_to_id;
            }
            $linkedStage->save();
            if ($stage->assigned_to_id) {
                event(new LoanApplicationApprovalStageAssigned($linkedStage));
            }
            if ($count === 0) {
                $application->current_loan_application_linked_approval_stage_id = $linkedStage->id;
                $application->save();
                event(new LoanApplicationCurrentApprovalStageChanged($application));
            }
            if ($count === 1) {
                $application->next_loan_application_linked_approval_stage_id = $linkedStage->id;
                $application->save();
                event(new LoanApplicationCurrentApprovalStageChanged($application));
            }
            $count++;
        });
        //save checklist items
        if ($application->checklist) {
            $application->checklist->items->each(function ($item) use ($application) {
                $linkedItem = new LoanApplicationLinkedChecklistItem();
                $linkedItem->loan_application_id = $application->id;
                $linkedItem->loan_application_checklist_item_id = $item->id;
                $linkedItem->name = $item->name;
                $linkedItem->save();
            });
        }
        $applicationHistory = new LoanApplicationHistory();
        $applicationHistory->loan_application_id = $application->id;
        if ($application->source === 'admin') {
            $applicationHistory->created_by_id = $application->created_by_id;
            $applicationHistory->user = $application->createdBy->name;
            $applicationHistory->action = 'Loan Application Created by admin';
        } else {
            $applicationHistory->action = 'Loan Application Created by member';
        }
        $applicationHistory->save();
    }
}
