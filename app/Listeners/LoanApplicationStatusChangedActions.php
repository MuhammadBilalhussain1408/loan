<?php

namespace App\Listeners;

use App\Events\LoanApplicationStatusChanged;
use App\Events\LoanCreated;
use App\Events\LoanStatusChanged;
use App\Models\Loan;
use App\Models\LoanLinkedCharge;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LoanApplicationStatusChangedActions  implements ShouldQueue
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
    public function handle(LoanApplicationStatusChanged $event): void
    {
        $application = $event->application;
        $product = $application->product;
        if ($event->application->status === 'disbursed') {
            //create actual loan here
            $loan = new Loan();
            $loan->loan_application_id = $application->id;
            $loan->currency_id = $application->currency_id;
            $loan->loan_product_id = $application->loan_product_id;
            $loan->member_id = $application->member_id;
            $loan->branch_id = $application->branch_id;
            $loan->member_category_id = $application->member_category_id;
            $loan->member_designation_id = $application->member_designation_id;
            $loan->loan_transaction_processing_strategy_id = $product->loan_transaction_processing_strategy_id;
            $loan->loan_purpose_id = $application->loan_purpose_id;
            $loan->loan_officer_id = $application->loan_officer_id;
            $loan->first_payment_date = $application->first_payment_date;
            $loan->payment_type_id = $application->payment_type_id;
            $loan->created_by_id = $application->disbursed_by_id;
            $loan->disbursed_on_date = $application->disbursed_on_date;
            $loan->xrate = $application->xrate;
            $loan->principal = $application->approved_amount;
            $loan->loan_term = $application->loan_term;
            $loan->repayment_frequency = $application->repayment_frequency;
            $loan->repayment_frequency_type = $application->repayment_frequency_type;
            $loan->interest_rate = $application->interest_rate;
            $loan->interest_rate_type = $application->interest_rate_type;
            $loan->grace_on_principal_paid = $application->grace_on_principal_paid;
            $loan->grace_on_interest_paid = $application->grace_on_interest_paid;
            $loan->grace_on_interest_charged = $application->grace_on_interest_charged;
            $loan->interest_methodology = $application->interest_methodology;
            $loan->amortization_method = $application->amortization_method;
            $loan->auto_disburse = $application->auto_disburse;
            $loan->deduct_interest_from_principal = $product->deduct_interest_from_principal;
            $loan->status = 'active';
            $loan->save();
            //copy charges
            $application->charges->each(function ($charge) use ($loan) {
                $linkedCharge = new LoanLinkedCharge();
                $linkedCharge->loan_id = $loan->id;
                $linkedCharge->name = $charge->name;
                $linkedCharge->loan_charge_id = $charge->loan_charge_id;
                $linkedCharge->amount = $charge->amount;
                $linkedCharge->loan_charge_type_id = $charge->loan_charge_type_id;
                $linkedCharge->loan_charge_option_id = $charge->loan_charge_option_id;
                $linkedCharge->is_penalty = $charge->is_penalty;
                $linkedCharge->save();
            });
            event(new LoanCreated($loan));
            event(new LoanStatusChanged($loan));
        }
    }
}
