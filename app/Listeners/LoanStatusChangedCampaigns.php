<?php

namespace App\Listeners;

use App\Events\LoanStatusChanged;
use App\Models\CommunicationCampaign;
use App\Models\CommunicationCampaignBusinessRule;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;


class LoanStatusChangedCampaigns implements ShouldQueue
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
     * @param LoanStatusChanged $event
     * @return void
     */
    public function handle(LoanStatusChanged $event)
    {
        $loan = $event->loan;
        $previousStatus = $event->oldStatus;
        $communicationCampaignBusinessRuleID = '';

        if ($loan->status == 'submitted' && empty($previousStatus)) {
            $communicationCampaignBusinessRuleID =CommunicationCampaignBusinessRule::where('name','Loan Submitted')->first()->id;
        }
        if ($loan->status == 'pending' && empty($previousStatus)) {
            $communicationCampaignBusinessRuleID = CommunicationCampaignBusinessRule::where('name','Loan Submitted')->first()->id;
        }
        if ($loan->status == 'rejected' && $previousStatus == 'submitted') {
            $communicationCampaignBusinessRuleID = CommunicationCampaignBusinessRule::where('name','Loan Rejected')->first()->id;
        }
        if ($loan->status == 'approved' && $previousStatus == 'submitted') {
            $communicationCampaignBusinessRuleID = CommunicationCampaignBusinessRule::where('name','Loan Approved')->first()->id;
        }
        if ($loan->status == 'active' && $previousStatus == 'approved') {
            $communicationCampaignBusinessRuleID = CommunicationCampaignBusinessRule::where('name','Loan Disbursed')->first()->id;
        }
        if ($loan->status == 'rescheduled') {
            $communicationCampaignBusinessRuleID = CommunicationCampaignBusinessRule::where('name','Loan Rescheduled')->first()->id;
        }
        if ($loan->status == 'closed' && $previousStatus == 'active') {
            $communicationCampaignBusinessRuleID = CommunicationCampaignBusinessRule::where('name','Loan Closed')->first()->id;
        }

        $campaigns = CommunicationCampaign::where('trigger_type', 'triggered')->where('status', 'active')->where('loan_product_id', $loan->loan_product_id)->where('communication_campaign_business_rule_id', $communicationCampaignBusinessRuleID)->get();
        foreach ($campaigns as $key) {
            if ($key->campaign_type == 'sms') {
                if (!empty($loan->member->contact_number)) {
                    $description = template_replace_tags(["body" => $key->description, "member_id" => $loan->member_id, "loan_id" => $loan->id]);
                    send_sms($loan->member->contact_number, $description, $key->sms_gateway_id);
                    //log sms
                    log_campaign([
                        'created_by_id' => Auth::id(),
                        'member_id' => $loan->member_id,
                        'communication_campaign_id' => $key->id,
                        'campaign_type' => $key->campaign_type,
                        'description' => $description,
                        'send_to' => $loan->member->contact_number,
                        'status' => 'sent',
                        'campaign_name' => $key->name
                    ]);
                }
            }
            if ($key->campaign_type == 'email') {
                if (!empty($loan->member->email)) {
                    $description = template_replace_tags(["body" => $key->description, "member_id" => $loan->member_id, "loan_id" => $loan->id]);
                    $email = $loan->member->email;
                    $subject = $key->subject;
                    $attachment_type = $key->communication_campaign_attachment_type_id;
                    Mail::send([], [], function ($message) use ($email, $description, $subject, $attachment_type, $loan) {
                        $message->subject($subject);
                        $message->from(Setting::where('setting_key', 'company_email')->first()->setting_value, Setting::where('setting_key', 'company_name')->first()->setting_value);
                        $message->to($email);
                        if ($attachment_type == '1') {
                            //loan schedule
                            $pdf = Pdf::loadView('loan_schedule.pdf', compact('loan'))->setPaper('a4', 'landscape');
                            $message->attachData($pdf->output(),
                                  "schedule.pdf",
                                ['mime' => 'application/pdf']);
                        }
                    });
                    //log sms
                    log_campaign([
                        'created_by_id' => Auth::id(),
                        'member_id' => $loan->member_id,
                        'communication_campaign_id' => $key->id,
                        'campaign_type' => $key->campaign_type,
                        'description' => $description,
                        'send_to' => $loan->member->email,
                        'status' => 'sent',
                        'campaign_name' => $key->name
                    ]);
                }
            }
        }
    }
}
