<?php

namespace App\Jobs;

use App\Mail\SendBasicEmail;
use App\Models\Member;
use App\Models\CommunicationCampaign;
use App\Models\Loan;
use App\Models\LoanRepaymentSchedule;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;


class ProcessCampaign implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public CommunicationCampaign $communicationCampaign;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(CommunicationCampaign $communicationCampaign)
    {
        $this->communicationCampaign = $communicationCampaign;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $communicationCampaign = $this->communicationCampaign;
        $branch_id = $communicationCampaign->branch_id;
        $loan_officer_id = $communicationCampaign->loan_officer_id;
        $loan_product_id = $communicationCampaign->loan_product_id;
        $attachment_type = $communicationCampaign->communicationAttachmentType;
        $from_x = $communicationCampaign->from_x;
        $to_y = $communicationCampaign->to_y;
        $cycle_x = $communicationCampaign->cycle_x;
        $cycle_y = $communicationCampaign->cycle_y;
        $overdue_x = $communicationCampaign->overdue_x;
        $overdue_y = $communicationCampaign->overdue_y;
        //single member
        if ($communicationCampaign->communicationCampaignBusinessRule->name === 'Single Member') {
            $member = Member::when($branch_id, function ($query) use ($branch_id) {
                $query->where('branch_id', $branch_id);
            })->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                $query->where('loan_officer_id', $loan_officer_id);
            })->find($communicationCampaign->member_id);
            if (!empty($member)) {
                if ($communicationCampaign->campaign_type == 'sms') {
                    if (!empty($member->contact_number)) {
                        $description = template_replace_tags(["body" => $communicationCampaign->description, "member_id" => $member->id]);
                        send_sms($member->contact_number, $description, $communicationCampaign->sms_gateway_id);
                        //log sms
                        log_campaign([
                            'member_id' => $member->id,
                            'communicationCampaign_id' => $communicationCampaign->id,
                            'campaign_type' => $communicationCampaign->campaign_type,
                            'description' => $description,
                            'send_to' => $member->contact_number,
                            'status' => 'sent',
                            'campaign_name' => $communicationCampaign->name
                        ]);
                    }
                }
                if ($communicationCampaign->campaign_type == 'email') {
                    if (!empty($member->email)) {
                        $description = template_replace_tags(["body" => $communicationCampaign->description, "member_id" => $member->id]);
                        $email = $member->email;
                        $subject = $communicationCampaign->subject;
                        Mail::to($email)->send(new SendBasicEmail($subject, $description));
                        //log sms
                        log_campaign([
                            'member_id' => $member->id,
                            'communicationCampaign_id' => $communicationCampaign->id,
                            'campaign_type' => $communicationCampaign->campaign_type,
                            'description' => $description,
                            'send_to' => $member->email,
                            'status' => 'sent',
                            'campaign_name' => $communicationCampaign->name
                        ]);
                    }
                }
            }
        }
        //single user
        if ($communicationCampaign->communicationCampaignBusinessRule->name === 'Single User') {
            $user = User::when($branch_id, function ($query) use ($branch_id) {
                $query->where('branch_id', $branch_id);
            })->find($communicationCampaign->user_id);
            if (!empty($user)) {
                if ($communicationCampaign->campaign_type == 'sms') {
                    if (!empty($user->contact_number)) {
                        $description = template_replace_tags(["body" => $communicationCampaign->description, "user_id" => $user->id]);
                        send_sms($user->contact_number, $description, $communicationCampaign->sms_gateway_id);
                        //log sms
                        log_campaign([
                            'member_id' => $user->id,
                            'communicationCampaign_id' => $communicationCampaign->id,
                            'campaign_type' => $communicationCampaign->campaign_type,
                            'description' => $description,
                            'send_to' => $user->contact_number,
                            'status' => 'sent',
                            'campaign_name' => $communicationCampaign->name
                        ]);
                    }
                }
                if ($communicationCampaign->campaign_type == 'email') {
                    if (!empty($user->email)) {
                        $description = template_replace_tags(["body" => $communicationCampaign->description, "user_id" => $user->id]);
                        $email = $user->email;
                        $subject = $communicationCampaign->subject;
                        Mail::to($email)->send(new SendBasicEmail($subject, $description));
                        //log sms
                        log_campaign([
                            'member_id' => $user->id,
                            'communicationCampaign_id' => $communicationCampaign->id,
                            'campaign_type' => $communicationCampaign->campaign_type,
                            'description' => $description,
                            'send_to' => $user->email,
                            'status' => 'sent',
                            'campaign_name' => $communicationCampaign->name
                        ]);
                    }
                }
            }
        }
        //active members
        if ($communicationCampaign->communicationCampaignBusinessRule->name === 'Active Members') {
            $members = Member::where('status', 'active')->when($branch_id, function ($query) use ($branch_id) {
                $query->where('branch_id', $branch_id);
            })->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                $query->where('loan_officer_id', $loan_officer_id);
            })->get();
            foreach ($members as $member) {
                if ($communicationCampaign->campaign_type == 'sms') {
                    if (!empty($member->contact_number)) {
                        $description = template_replace_tags(["body" => $communicationCampaign->description, "member_id" => $member->id]);
                        send_sms($member->contact_number, $description, $communicationCampaign->sms_gateway_id);
                        //log sms
                        log_campaign([
                            'member_id' => $member->id,
                            'communicationCampaign_id' => $communicationCampaign->id,
                            'campaign_type' => $communicationCampaign->campaign_type,
                            'description' => $description,
                            'send_to' => $member->contact_number,
                            'status' => 'sent',
                            'campaign_name' => $communicationCampaign->name
                        ]);
                    }
                }
                if ($communicationCampaign->campaign_type == 'email') {
                    if (!empty($member->email)) {
                        $description = template_replace_tags(["body" => $communicationCampaign->description, "member_id" => $member->id]);
                        $email = $member->email;
                        $subject = $communicationCampaign->subject;
                        Mail::to($email)->send(new SendBasicEmail($subject, $description));
                        //log sms
                        log_campaign([
                            'member_id' => $member->id,
                            'communicationCampaign_id' => $communicationCampaign->id,
                            'campaign_type' => $communicationCampaign->campaign_type,
                            'description' => $description,
                            'send_to' => $member->email,
                            'status' => 'sent',
                            'campaign_name' => $communicationCampaign->name
                        ]);
                    }
                }
            }
        }
        //active members who have never had a loan
        if ($communicationCampaign->communicationCampaignBusinessRule->name === 'Prospective Members') {
            $members = Member::leftJoin("loans", "members.id", "loans.member_id")->where('members.status', 'active')->when($branch_id, function ($query) use ($branch_id) {
                $query->where('members.branch_id', $branch_id);
            })->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                $query->where('members.loan_officer_id', $loan_officer_id);
            })->selectRaw("members.*,count(loans.id) loan_count")->having('loan_count', 0)->get();
            foreach ($members as $member) {
                if ($communicationCampaign->campaign_type == 'sms') {
                    if (!empty($member->contact_number)) {
                        $description = template_replace_tags(["body" => $communicationCampaign->description, "member_id" => $member->id]);
                        send_sms($member->contact_number, $description, $communicationCampaign->sms_gateway_id);
                        //log sms
                        log_campaign([
                            'member_id' => $member->id,
                            'communicationCampaign_id' => $communicationCampaign->id,
                            'campaign_type' => $communicationCampaign->campaign_type,
                            'description' => $description,
                            'send_to' => $member->contact_number,
                            'status' => 'sent',
                            'campaign_name' => $communicationCampaign->name
                        ]);
                    }
                }
                if ($communicationCampaign->campaign_type == 'email') {
                    if (!empty($member->email)) {
                        $description = template_replace_tags(["body" => $communicationCampaign->description, "member_id" => $member->id]);
                        $email = $member->email;
                        $subject = $communicationCampaign->subject;
                        Mail::to($email)->send(new SendBasicEmail($subject, $description));
                        //log sms
                        log_campaign([
                            'member_id' => $member->id,
                            'communicationCampaign_id' => $communicationCampaign->id,
                            'campaign_type' => $communicationCampaign->campaign_type,
                            'description' => $description,
                            'send_to' => $member->email,
                            'status' => 'sent',
                            'campaign_name' => $communicationCampaign->name
                        ]);
                    }
                }
            }
        }
        //all members with an outstanding loan
        if ($communicationCampaign->communicationCampaignBusinessRule->name === 'Active Loan Members') {
            $members = Member::leftJoin("loans", "members.id", "loans.member_id")->where('members.status', 'active')->where('loans.status', 'active')->when($branch_id, function ($query) use ($branch_id) {
                $query->where('members.branch_id', $branch_id);
            })->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                $query->where('members.loan_officer_id', $loan_officer_id);
            })->when($loan_product_id, function ($query) use ($loan_product_id) {
                $query->where('loans.loan_product_id', $loan_product_id);
            })->selectRaw("members.*,count(loans.id) loan_count")->having('loan_count', '>', 0)->get();
            foreach ($members as $member) {
                if ($communicationCampaign->campaign_type == 'sms') {
                    if (!empty($member->contact_number)) {
                        $description = template_replace_tags(["body" => $communicationCampaign->description, "member_id" => $member->id]);
                        send_sms($member->contact_number, $description, $communicationCampaign->sms_gateway_id);
                        //log sms
                        log_campaign([
                            'member_id' => $member->id,
                            'communicationCampaign_id' => $communicationCampaign->id,
                            'campaign_type' => $communicationCampaign->campaign_type,
                            'description' => $description,
                            'send_to' => $member->contact_number,
                            'status' => 'sent',
                            'campaign_name' => $communicationCampaign->name
                        ]);
                    }
                }
                if ($communicationCampaign->campaign_type == 'email') {
                    if (!empty($member->email)) {
                        $description = template_replace_tags(["body" => $communicationCampaign->description, "member_id" => $member->id]);
                        $email = $member->email;
                        $subject = $communicationCampaign->subject;
                        Mail::to($email)->send(new SendBasicEmail($subject, $description));
                        //log sms
                        log_campaign([
                            'member_id' => $member->id,
                            'communicationCampaign_id' => $communicationCampaign->id,
                            'campaign_type' => $communicationCampaign->campaign_type,
                            'description' => $description,
                            'send_to' => $member->email,
                            'status' => 'sent',
                            'campaign_name' => $communicationCampaign->name
                        ]);
                    }
                }
            }
        }
        //all members with loans in arrears
        if ($communicationCampaign->communicationCampaignBusinessRule->name === 'Loans in arrears') {
            $loans = LoanRepaymentSchedule::join("loans", "loans.id", "loan_repayment_schedules.loan_id")->leftJoin("members", "members.id", "loans.member_id")->where('loans.status', 'active')->when($branch_id, function ($query) use ($branch_id) {
                $query->where('loans.branch_id', $branch_id);
            })->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                $query->where('loans.loan_officer_id', $loan_officer_id);
            })->when($loan_product_id, function ($query) use ($loan_product_id) {
                $query->where('loans.loan_product_id', $loan_product_id);
            })->when($from_x, function ($query) use ($from_x, $to_y) {
                $query->havingRaw("days_in_arrears between $from_x AND $to_y");
            })->whereRaw("loan_repayment_schedules.id =(select lrs.id from loan_repayment_schedules as lrs where lrs.due_date<now() AND lrs.loan_id=loan_repayment_schedules.loan_id AND lrs.total_due > 0 order by due_date desc limit 1)")->selectRaw("loans.member_id,loans.id,members.contact_number,members.email,datediff(now(),loan_repayment_schedules.due_date) days_in_arrears")->get();
            foreach ($loans as $loan) {
                if ($communicationCampaign->campaign_type == 'sms') {
                    if (!empty($loan->contact_number)) {
                        $description = template_replace_tags(["body" => $communicationCampaign->description, "loan_id" => $loan->id, "member_id" => $loan->member_id]);
                        send_sms($loan->contact_number, $description, $communicationCampaign->sms_gateway_id);
                        //log sms
                        log_campaign([
                            'member_id' => $loan->member_id,
                            'communicationCampaign_id' => $communicationCampaign->id,
                            'campaign_type' => $communicationCampaign->campaign_type,
                            'description' => $description,
                            'send_to' => $loan->contact_number,
                            'status' => 'sent',
                            'campaign_name' => $communicationCampaign->name
                        ]);
                    }
                }
                if ($communicationCampaign->campaign_type == 'email') {
                    if (!empty($loan->email)) {
                        $description = template_replace_tags(["body" => $communicationCampaign->description, "loan_id" => $loan->id, "member_id" => $loan->member_id]);
                        $email = $loan->email;
                        $subject = $communicationCampaign->subject;
                        $attachments = [];
                        if ($communicationCampaign->communicationAttachmentType === 'Loan Schedule') {
                            //loan schedule
                            $loan = Loan::find($loan->id);
                            $pdf = Pdf::loadView('loan_schedule.pdf', compact('loan'))->setPaper('a4', 'landscape');
                            $attachments[] =
                                [
                                    "file_path" => $pdf->output(),
                                    "file_name" => "schedule.pdf",
                                    "extra_args" => ['mime' => 'application/pdf']
                                ];
                        }
                        Mail::to($email)->send(new SendBasicEmail($subject, $description, $attachments));
                        //log sms
                        log_campaign([
                            'member_id' => $loan->member_id,
                            'communicationCampaign_id' => $communicationCampaign->id,
                            'campaign_type' => $communicationCampaign->campaign_type,
                            'description' => $description,
                            'send_to' => $loan->email,
                            'status' => 'sent',
                            'campaign_name' => $communicationCampaign->name
                        ]);
                    }
                }
            }
        }
        //loans disbursed to members
        if ($communicationCampaign->communicationCampaignBusinessRule->name === 'Loans disbursed to members') {
            $loans = Loan::join("members", "members.id", "loans.member_id")->where('loans.status', 'active')->when($branch_id, function ($query) use ($branch_id) {
                $query->where('loans.branch_id', $branch_id);
            })->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                $query->where('loans.loan_officer_id', $loan_officer_id);
            })->when($loan_product_id, function ($query) use ($loan_product_id) {
                $query->where('loans.loan_product_id', $loan_product_id);
            })->whereBetween('disbursed_on_date', [Carbon::today()->subDays($to_y)->format("Y-m-d"), Carbon::today()->subDays($from_x)->format("Y-m-d")])->selectRaw("loans.member_id,loans.id,members.contact_number,members.email")->get();
            foreach ($loans as $loan) {
                if ($communicationCampaign->campaign_type == 'sms') {
                    if (!empty($loan->contact_number)) {
                        $description = template_replace_tags(["body" => $communicationCampaign->description, "loan_id" => $loan->id, "member_id" => $loan->member_id]);
                        send_sms($loan->contact_number, $description, $communicationCampaign->sms_gateway_id);
                        //log sms
                        log_campaign([
                            'member_id' => $loan->member_id,
                            'communicationCampaign_id' => $communicationCampaign->id,
                            'campaign_type' => $communicationCampaign->campaign_type,
                            'description' => $description,
                            'send_to' => $loan->contact_number,
                            'status' => 'sent',
                            'campaign_name' => $communicationCampaign->name
                        ]);
                    }
                }
                if ($communicationCampaign->campaign_type == 'email') {
                    if (!empty($loan->email)) {
                        $description = template_replace_tags(["body" => $communicationCampaign->description, "loan_id" => $loan->id, "member_id" => $loan->member_id]);
                        $email = $loan->email;
                        $subject = $communicationCampaign->subject;
                        $attachments = [];
                        if ($communicationCampaign->communicationAttachmentType === 'Loan Schedule') {
                            //loan schedule
                            $loan = Loan::find($loan->id);
                            $pdf = Pdf::loadView('loan_schedule.pdf', compact('loan'))->setPaper('a4', 'landscape');
                            $attachments[] =
                                [
                                    "file_path" => $pdf->output(),
                                    "file_name" => "schedule.pdf",
                                    "extra_args" => ['mime' => 'application/pdf']
                                ];
                        }
                        Mail::to($email)->send(new SendBasicEmail($subject, $description, $attachments));

                        //log sms
                        log_campaign([
                            'member_id' => $loan->member_id,
                            'communicationCampaign_id' => $communicationCampaign->id,
                            'campaign_type' => $communicationCampaign->campaign_type,
                            'description' => $description,
                            'send_to' => $loan->email,
                            'status' => 'sent',
                            'campaign_name' => $communicationCampaign->name
                        ]);
                    }
                }
            }
        }
        //loan payments due
        if ($communicationCampaign->communicationCampaignBusinessRule->name === 'Loan payments due') {
            $loans = LoanRepaymentSchedule::join("loans", "loans.id", "loan_repayment_schedules.loan_id")->join("members", "members.id", "loans.member_id")->where('loans.status', 'active')->when($branch_id, function ($query) use ($branch_id) {
                $query->where('loans.branch_id', $branch_id);
            })->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                $query->where('loans.loan_officer_id', $loan_officer_id);
            })->when($loan_product_id, function ($query) use ($loan_product_id) {
                $query->where('loans.loan_product_id', $loan_product_id);
            })->where('loan_repayment_schedules.total_due', '>', 0)->whereBetween('loan_repayment_schedules.due_date', [Carbon::today()->addDays($from_x)->format("Y-m-d"), Carbon::today()->addDays($to_y)->format("Y-m-d")])->selectRaw("loans.member_id,loans.id,members.contact_number,members.email,loan_repayment_schedules.id loan_repayment_schedule_id")->get();
            foreach ($loans as $loan) {
                if ($communicationCampaign->campaign_type == 'sms') {
                    if (!empty($loan->contact_number)) {
                        $description = template_replace_tags(["body" => $communicationCampaign->description, "loan_id" => $loan->id, "member_id" => $loan->member_id, "loan_repayment_schedule_id" => $loan->loan_repayment_schedule_id]);
                        send_sms($loan->contact_number, $description, $communicationCampaign->sms_gateway_id);
                        //log sms
                        log_campaign([
                            'member_id' => $loan->member_id,
                            'communicationCampaign_id' => $communicationCampaign->id,
                            'campaign_type' => $communicationCampaign->campaign_type,
                            'description' => $description,
                            'send_to' => $loan->contact_number,
                            'status' => 'sent',
                            'campaign_name' => $communicationCampaign->name
                        ]);
                    }
                }
                if ($communicationCampaign->campaign_type == 'email') {
                    if (!empty($loan->email)) {
                        $description = template_replace_tags(["body" => $communicationCampaign->description, "loan_id" => $loan->id, "member_id" => $loan->member_id]);
                        $email = $loan->email;
                        $subject = $communicationCampaign->subject;
                        $attachments = [];
                        if ($communicationCampaign->communicationAttachmentType === 'Loan Schedule') {
                            //loan schedule
                            $loan = Loan::find($loan->id);
                            $pdf = PDF::loadView('loan_schedule.pdf', compact('loan'))->setPaper('a4', 'landscape');
                            $attachments[] =
                                [
                                    "file_path" => $pdf->output(),
                                    "file_name" => "schedule.pdf",
                                    "extra_args" => ['mime' => 'application/pdf']
                                ];
                        }
                        Mail::to($email)->send(new SendBasicEmail($subject, $description, $attachments));
                        //log sms
                        log_campaign([
                            'member_id' => $loan->member_id,
                            'communicationCampaign_id' => $communicationCampaign->id,
                            'campaign_type' => $communicationCampaign->campaign_type,
                            'description' => $description,
                            'send_to' => $loan->email,
                            'status' => 'sent',
                            'campaign_name' => $communicationCampaign->name
                        ]);
                    }
                }
            }
        }
        if ($communicationCampaign->trigger_type !== 'scheduled') {
            $communicationCampaign->status = 'done';
            $communicationCampaign->save();
        }
    }
}
