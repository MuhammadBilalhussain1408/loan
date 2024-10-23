<?php

namespace App\Listeners;

use App\Events\LoanStatusChanged;
use App\Events\TransactionUpdated;
use App\Models\JournalEntry;
use App\Models\LoanHistory;
use App\Models\LoanLinkedCharge;
use App\Models\LoanRepaymentSchedule;
use App\Models\LoanTransaction;
use App\Models\LoanTransactionType;
use App\Models\PaymentDetail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Carbon;


class LoanStatusChangedActions implements ShouldQueue
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
    public function handle(LoanStatusChanged $event): void
    {
        $loan = $event->loan;
        $previousStatus = $event->oldStatus;
        //activate loan
        if ($loan->status === 'active' && $previousStatus === '') {
            //disburse loan
            //payment details
            $paymentDetail = new PaymentDetail();
            $paymentDetail->created_by_id = $loan->created_by_id;
            $paymentDetail->payment_type_id = $loan->payment_type_id;
            $paymentDetail->transaction_type = 'loan_transaction';
            $paymentDetail->save();
            //prepare loan schedule
            if ($loan->repayment_frequency_type != 'ballon_payment') {
                //determine interest rate
                $interest_rate = determine_period_interest_rate($loan->interest_rate, $loan->repayment_frequency_type, $loan->interest_rate_type, $loan->repayment_frequency);
                $balance = round($loan->principal, $loan->decimals);
                $period = intval(floor($loan->loan_term / $loan->repayment_frequency));
                //check if period has a remainder
                $principalBalanceAddedToLast = false;
                if (($loan->loan_term % $loan->repayment_frequency) != 0) {
                    $principalBalanceAddedToLast = true;
                }
                $payment_from_date = $loan->disbursed_on_date;
                $next_payment_date = $loan->first_payment_date;
                $total_principal = 0;
                $total_interest = 0;

                for ($i = 1; $i <= $period; $i++) {
                    $schedule = new LoanRepaymentSchedule();
                    $schedule->created_by_id = $loan->created_by_id;
                    $schedule->loan_id = $loan->id;
                    $schedule->installment = $i;
                    //adjust $next_payment_date if weekend or holiday
                    $adjusted_next_payment_date = $next_payment_date;
                    if ($loan->product->exclude_weekends) {
                        $adjusted_next_payment_date = get_next_week_day($next_payment_date);
                    }
                    if ($loan->product->exclude_holidays) {
                        $adjusted_next_payment_date = get_next_non_holiday_day($next_payment_date);
                    }
                    $schedule->due_date = $adjusted_next_payment_date;
                    $schedule->from_date = $payment_from_date;
                    $date = explode('-', $next_payment_date);
                    //determine which method to use
                    //flat  method
                    if ($loan->interest_methodology == 'flat') {
                        $principal = round($loan->principal / $period, $loan->decimals);
                        $interest = round($interest_rate * $loan->principal, $loan->decimals) / $period;
                        if ($loan->deduct_interest_from_principal) {
                            if ($i == 1) {
                                $schedule->interest = $interest;
                            } else {
                                $schedule->interest = 0;
                            }
                        } else {
                            if ($loan->grace_on_interest_charged >= $i) {
                                $schedule->interest = 0;
                            } else {
                                $schedule->interest = $interest;
                            }
                        }
                        if ($i == $period) {
                            //account for values lost during rounding
                            $schedule->principal = round($balance, $loan->decimals);
                        } else {
                            $schedule->principal = $principal;
                        }
                        //determine next balance
                        $balance = ($balance - $principal);
                    }
                    //reducing balance
                    if ($loan->interest_methodology == 'declining_balance') {
                        if ($loan->amortization_method == 'equal_installments') {
                            $amortized_payment = round(determine_amortized_payment($interest_rate, $loan->principal, $period), $loan->decimals);
                            //determine if we have grace period for interest
                            $interest = round($interest_rate * $balance, $loan->decimals);
                            $principal = round(($amortized_payment - $interest), $loan->decimals);
                            if ($loan->grace_on_interest_charged >= $i) {
                                $schedule->interest = 0;
                            } else {
                                $schedule->interest = $interest;
                            }
                            if ($i == $period) {
                                //account for values lost during rounding
                                $schedule->principal = round($balance, $loan->decimals);
                            } else {
                                $schedule->principal = $principal;
                            }
                            //determine next balance
                            $balance = ($balance - $principal);
                        }
                        if ($loan->amortization_method == 'equal_principal_payments') {
                            $principal = round($loan->principal / $period, $loan->decimals);
                            //determine if we have grace period for interest
                            $interest = round($interest_rate * $balance, $loan->decimals);
                            if ($loan->grace_on_interest_charged >= $i) {
                                $schedule->interest = 0;
                            } else {
                                $schedule->interest = $interest;
                            }
                            if ($i == $period) {
                                //account for values lost during rounding
                                $schedule->principal = round($balance, $loan->decimals);
                            } else {
                                $schedule->principal = $principal;
                            }
                            //determine next balance
                            $balance = ($balance - $principal);
                        }
                    }
                    $payment_from_date = Carbon::parse($next_payment_date)->add(1, 'day')->format("Y-m-d");
                    if ($loan->repayment_frequency_type == 'months') {
                        $next_payment_date = Carbon::parse($next_payment_date)->addMonthsNoOverflow($loan->repayment_frequency)->format("Y-m-d");
                    } else {
                        $next_payment_date = Carbon::parse($next_payment_date)->add($loan->repayment_frequency, $loan->repayment_frequency_type)->format("Y-m-d");
                    }
                    $total_principal = $total_principal + $schedule->principal;
                    $total_interest = $total_interest + $schedule->interest;
                    $schedule->total_due = $schedule->principal + $schedule->interest;
                    $schedule->save();
                }
                $loan->expected_maturity_date = $next_payment_date;
                $loan->principal_disbursed_derived = $total_principal;
                $loan->interest_disbursed_derived = $total_interest;
                //add disbursal transaction
                $transaction = new LoanTransaction();
                $transaction->created_by_id = $loan->id;
                $transaction->loan_id = $loan->id;
                $transaction->payment_detail_id = $paymentDetail->id;
                $transaction->name = 'Disbursement';
                $transaction->loan_transaction_type_id = LoanTransactionType::where('name', 'Disbursement')->first()->id;
                $transaction->submitted_on = null;
                $transaction->created_on = date("Y-m-d");
                $transaction->amount = $loan->principal;
                $transaction->debit = $loan->principal;
                $disbursal_transaction_id = $transaction->id;
                $transaction->save();
                $loanHistory = new LoanHistory();
                $loanHistory->loan_id = $loan->id;
                $loanHistory->created_by_id = $loan->created_by_id ?? 0;
                $loanHistory->user = $loan->createdBy->name ?? '';
                $loanHistory->action = 'Loan Disbursed';
                $loanHistory->save();;
                //add interest transaction
                $transaction = new LoanTransaction();
                $transaction->created_by_id = $loan->created_by_id;
                $transaction->loan_id = $loan->id;
                $transaction->name = 'Interest Applied';
                $transaction->loan_transaction_type_id = LoanTransactionType::where('name', 'Apply Interest')->first()->id;
                $transaction->submitted_on = null;
                $transaction->created_on = date("Y-m-d");
                $transaction->amount = $total_interest;
                $transaction->debit = $total_interest;
                $transaction->save();
                $installment_fees = 0;
                $disbursement_fees = 0;
                foreach ($loan->charges as $key) {
                    //disbursement
                    if ($key->charge->type->name === 'Disbursement') {
                        if ($key->loan_charge_option_id == 1) {
                            $key->calculated_amount = $key->amount;
                            $key->amount_paid_derived = $key->calculated_amount;
                            $key->is_paid = 1;
                            $disbursement_fees = $disbursement_fees + $key->calculated_amount;
                        }
                        if ($key->loan_charge_option_id == 2) {
                            $key->calculated_amount = round(($key->amount * $total_principal / 100), $loan->decimals);
                            $key->amount_paid_derived = $key->calculated_amount;
                            $key->is_paid = 1;
                            $disbursement_fees = $disbursement_fees + $key->calculated_amount;
                        }
                        if ($key->loan_charge_option_id == 3) {
                            $key->calculated_amount = round(($key->amount * ($total_interest + $total_principal) / 100), $loan->decimals);
                            $key->amount_paid_derived = $key->calculated_amount;
                            $key->is_paid = 1;
                            $disbursement_fees = $disbursement_fees + $key->calculated_amount;
                        }
                        if ($key->loan_charge_option_id == 4) {
                            $key->calculated_amount = round(($key->amount * $total_interest / 100), $loan->decimals);
                            $key->amount_paid_derived = $key->calculated_amount;
                            $key->is_paid = 1;
                            $disbursement_fees = $disbursement_fees + $key->calculated_amount;
                        }
                        if ($key->loan_charge_option_id == 5) {
                            $key->calculated_amount = round(($key->amount * $total_principal / 100), $loan->decimals);
                            $key->amount_paid_derived = $key->calculated_amount;
                            $key->is_paid = 1;
                            $disbursement_fees = $disbursement_fees + $key->calculated_amount;
                        }
                        if ($key->loan_charge_option_id == 6) {
                            $key->calculated_amount = round(($key->amount * $total_principal / 100), $loan->decimals);
                            $key->amount_paid_derived = $key->calculated_amount;
                            $key->is_paid = 1;
                            $disbursement_fees = $disbursement_fees + $key->calculated_amount;
                        }
                        if ($key->loan_charge_option_id == 7) {
                            $key->calculated_amount = round(($key->amount * $loan->principal / 100), $loan->decimals);
                            $key->amount_paid_derived = $key->calculated_amount;
                            $key->is_paid = 1;
                            $disbursement_fees = $disbursement_fees + $key->calculated_amount;
                        }
                    }
                    //installment_fee
                    if ($key->charge->type->name === 'Installment Fees') {
                        if ($key->charge->option->name === 'Flat') {
                            $key->calculated_amount = $key->amount;
                            $installment_fees = $installment_fees + $key->calculated_amount;
                        }
                        if ($key->charge->option->name === 'Principal due on installment') {
                            $key->calculated_amount = round(($key->amount * $total_principal / 100), $loan->decimals);
                            $installment_fees = $installment_fees + $key->calculated_amount;
                        }
                        if ($key->charge->option->name === 'Principal + Interest due on installment') {
                            $key->calculated_amount = round(($key->amount * ($total_interest + $total_principal) / 100), $loan->decimals);
                            $installment_fees = $installment_fees + $key->calculated_amount;
                        }
                        if ($key->charge->option->name === 'Interest due on installment') {
                            $key->calculated_amount = round(($key->amount * $total_interest / 100), $loan->decimals);
                            $installment_fees = $installment_fees + $key->calculated_amount;
                        }
                        if ($key->charge->option->name === 'Total Outstanding Loan Principal') {
                            $key->calculated_amount = round(($key->amount * $total_principal / 100), $loan->decimals);
                            $installment_fees = $installment_fees + $key->calculated_amount;
                        }
                        if ($key->charge->option->name === 'Percentage of Original Loan Principal per Installment') {
                            $key->calculated_amount = round(($key->amount * $total_principal / 100), $loan->decimals);
                            $installment_fees = $installment_fees + $key->calculated_amount;
                        }
                        if ($key->charge->option->name === 'Original Loan Principal') {
                            $key->calculated_amount = round(($key->amount * $loan->principal / 100), $loan->decimals);
                            $installment_fees = $installment_fees + $key->calculated_amount;
                        }
                        //create transaction
                        $transaction = new LoanTransaction();
                        $transaction->created_by_id = $loan->created_by_id;
                        $transaction->loan_id = $loan->id;
                        $transaction->name = 'Fee Applied';
                        $transaction->loan_transaction_type_id = LoanTransactionType::where('name', 'Apply Charges')->first()->id;
                        $transaction->submitted_on = null;
                        $transaction->created_on = date("Y-m-d");
                        $transaction->amount = $key->calculated_amount;
                        $transaction->debit = $key->calculated_amount;
                        $transaction->reversible = 1;
                        $transaction->save();
                        $key->loan_transaction_id = $transaction->id;
                        $key->save();
                        //add the charges to the schedule
                        foreach ($loan->schedules as $schedule) {
                            if ($key->loan_charge_option_id == 2) {
                                $schedule->fees = $schedule->fees + round(($key->amount * $schedule->principal / 100), $loan->decimals);
                            } elseif ($key->loan_charge_option_id == 3) {
                                $schedule->fees = $schedule->fees + round(($key->amount * ($schedule->interest + $schedule->principal) / 100), $loan->decimals);
                            } elseif ($key->loan_charge_option_id == 4) {
                                $schedule->fees = $schedule->fees + round(($key->amount * $schedule->interest / 100), $loan->decimals);
                            } else {
                                $schedule->fees = $schedule->fees + $key->calculated_amount;
                            }
                            $schedule->total_due = $schedule->principal + $schedule->interest + $schedule->fees;
                            $schedule->save();
                        }
                    }
                }
                if ($disbursement_fees > 0) {
                    $transaction = new LoanTransaction();
                    $transaction->created_by_id = $loan->created_by_id;
                    $transaction->loan_id = $loan->id;
                    $transaction->name = 'Disbursement Charges';
                    $transaction->loan_transaction_type_id = LoanTransactionType::where('name', 'Repayment At Disbursement')->first()->id;
                    $transaction->submitted_on = null;
                    $transaction->created_on = date("Y-m-d");
                    $transaction->amount = $disbursement_fees;
                    $transaction->credit = $disbursement_fees;
                    $transaction->fees_repaid_derived = $disbursement_fees;
                    $transaction->save();
                    $disbursement_fees_transaction_id = $transaction->id;
                }
                $loan->disbursement_charges = $disbursement_fees;
                $loan->save();
            } else {
                $loan_principal = $loan->principal;
                // $interest_rate = (($loan->interest_rate/100) * $loan_principal)*4;
                // dd($interest_rate)
                $balance = round($loan_principal, $loan->decimals);
                $applied_principal = $loan_principal;
                $year1_interest = round(($loan->interest_rate / 100) * $applied_principal);
                $applied_principal = $applied_principal + $year1_interest;
                $year2_interest =  round(($loan->interest_rate / 100) * $applied_principal);
                $applied_principal = $applied_principal + $year2_interest;
                $year3_interest = round(($loan->interest_rate / 100) * $applied_principal);
                $applied_principal = $applied_principal + $year3_interest;
                $year4_interest = round(($loan->interest_rate / 100) *  $applied_principal);
                $interest = $year1_interest + $year2_interest + $year3_interest + $year4_interest;
                $fee =((($loan_principal * 0.05) / 100) * 48);
                $schedule = new LoanRepaymentSchedule();
                $schedule->created_by_id = $loan->created_by_id;
                $schedule->loan_id = $loan->id;
                $schedule->fees = $fee;
                $schedule->installment = 1;
                // $schedule->days = 365 * 48;
                $schedule->interest = $interest;
                $schedule->principal = $loan_principal;
                $schedule->balance = $balance;
                $schedule->total_due = $loan_principal + $fee + $interest;
                $schedule->save();
                //
                $transaction = new LoanTransaction();
                $transaction->created_by_id = $loan->created_by_id;
                $transaction->loan_id = $loan->id;
                $transaction->payment_detail_id = $paymentDetail->id;
                $transaction->name = 'Disbursement';
                $transaction->loan_transaction_type_id = LoanTransactionType::where('name', 'Disbursement')->first()->id;
                $transaction->submitted_on = null;
                $transaction->created_on = date("Y-m-d");
                $transaction->amount = $loan->principal;
                $transaction->debit = $loan->principal;
                $disbursal_transaction_id = $transaction->id;
                $transaction->save();
                $loanHistory = new LoanHistory();
                $loanHistory->loan_id = $loan->id;
                $loanHistory->created_by_id = $loan->created_by_id ?? 0;
                $loanHistory->user = $loan->createdBy->name ?? '';
                $loanHistory->action = 'Loan Disbursed';
                $loanHistory->save();;
                //add interest transaction
                $transaction = new LoanTransaction();
                $transaction->created_by_id = $loan->created_by_id;
                $transaction->loan_id = $loan->id;
                $transaction->name = 'Interest Applied';
                $transaction->loan_transaction_type_id = LoanTransactionType::where('name', 'Apply Interest')->first()->id;
                $transaction->submitted_on = null;
                $transaction->created_on = date("Y-m-d");
                $transaction->amount = $interest;
                $transaction->debit = $interest;
                $transaction->save();
            }
            //check if accounting is enabled
            if ($loan->product->accounting_rule == "cash" || $loan->product->accounting_rule == "accrual_periodic" || $loan->product->accounting_rule == "accrual_upfront") {
                //loan disbursal
                //credit account
                $journal_entry = new JournalEntry();
                $journal_entry->created_by_id = $loan->created_by_id;
                $journal_entry->payment_detail_id = $paymentDetail->id;
                $journal_entry->transaction_number = 'L' . $disbursal_transaction_id;
                $journal_entry->branch_id = $loan->branch_id;
                $journal_entry->currency_id = $loan->currency_id;
                $journal_entry->chart_of_account_id = $loan->product->fund_source_chart_of_account_id;
                $journal_entry->transaction_type = 'loan_disbursement';
                $journal_entry->date = $loan->disbursed_on_date;
                $date = explode('-', $loan->disbursed_on_date);
                $journal_entry->month = $date[1];
                $journal_entry->year = $date[0];
                $journal_entry->credit = $loan->principal;
                $journal_entry->reference = $loan->id;
                $journal_entry->save();
                //debit account
                $journal_entry = new JournalEntry();
                $journal_entry->created_by_id = $loan->created_by_id;
                $journal_entry->transaction_number = 'L' . $disbursal_transaction_id;
                $journal_entry->payment_detail_id = $paymentDetail->id;
                $journal_entry->branch_id = $loan->branch_id;
                $journal_entry->currency_id = $loan->currency_id;
                $journal_entry->chart_of_account_id = $loan->product->loan_portfolio_chart_of_account_id;
                $journal_entry->transaction_type = 'loan_disbursement';
                $journal_entry->date = $loan->disbursed_on_date;
                $date = explode('-', $loan->disbursed_on_date);
                $journal_entry->month = $date[1];
                $journal_entry->year = $date[0];
                $journal_entry->debit = $loan->principal;
                $journal_entry->reference = $loan->id;
                $journal_entry->save();
                //
                if ($disbursement_fees > 0) {
                    //credit account
                    $journal_entry = new JournalEntry();
                    $journal_entry->created_by_id = $loan->created_by_id;
                    $journal_entry->payment_detail_id = $paymentDetail->id;
                    $journal_entry->transaction_number = 'L' . $disbursement_fees_transaction_id;
                    $journal_entry->branch_id = $loan->branch_id;
                    $journal_entry->currency_id = $loan->currency_id;
                    $journal_entry->chart_of_account_id = $loan->product->income_from_fees_chart_of_account_id;
                    $journal_entry->transaction_type = 'repayment_at_disbursement';
                    $journal_entry->date = $loan->disbursed_on_date;
                    $date = explode('-', $loan->disbursed_on_date);
                    $journal_entry->month = $date[1];
                    $journal_entry->year = $date[0];
                    $journal_entry->credit = $loan->principal;
                    $journal_entry->reference = $loan->id;
                    $journal_entry->save();
                    //debit account
                    $journal_entry = new JournalEntry();
                    $journal_entry->created_by_id = $loan->created_by_id;
                    $journal_entry->transaction_number = 'L' . $disbursement_fees_transaction_id;
                    $journal_entry->payment_detail_id = $paymentDetail->id;
                    $journal_entry->branch_id = $loan->branch_id;
                    $journal_entry->currency_id = $loan->currency_id;
                    $journal_entry->chart_of_account_id = $loan->product->fund_source_chart_of_account_id;
                    $journal_entry->transaction_type = 'repayment_at_disbursement';
                    $journal_entry->date = $loan->disbursed_on_date;
                    $date = explode('-', $loan->disbursed_on_date);
                    $journal_entry->month = $date[1];
                    $journal_entry->year = $date[0];
                    $journal_entry->debit = $loan->principal;
                    $journal_entry->reference = $loan->id;
                    $journal_entry->save();
                }
            }
            if ($loan->deduct_interest_from_principal && $loan->repayment_frequency_type != 'ballon_payment') {
                //payment details
                $paymentDetail = new PaymentDetail();
                $paymentDetail->created_by_id = $loan->created_by_id;
                $paymentDetail->payment_type_id = $loan->payment_type_id;
                $paymentDetail->transaction_type = 'loan_transaction';
                $paymentDetail->save();
                $transaction = new LoanTransaction();
                $transaction->created_by_id = $loan->created_by_id;
                $transaction->loan_id = $loan->id;
                $transaction->payment_detail_id = $paymentDetail->id;
                $transaction->name = 'Repayments';
                $transaction->loan_transaction_type_id = LoanTransactionType::where('name', 'Repayment')->first()->id;
                $transaction->submitted_on = null;
                $transaction->created_on = date("Y-m-d");
                $transaction->amount = $loan->interest_disbursed_derived;
                $transaction->credit = $loan->interest_disbursed_derived;
                $transaction->interest_repaid_derived = $loan->interest_disbursed_derived;
                $transaction->save();
                $loan->interest_repaid_derived = $loan->interest_disbursed_derived;
                $loan->save();
            }
            event(new TransactionUpdated($loan));
        }
        //undo disbursement
        if ($loan->status === 'approved' && $previousStatus === 'active') {
            LoanLinkedCharge::where('loan_id', $loan->id)->update(["loan_transaction_id" => null]);
            $loan->schedules()->delete();
            $loan->transactions()->delete();
            //reverse journal entries
            JournalEntry::whereIn('transaction_type', ['repayment_at_disbursement', 'loan_disbursement', 'loan_repayment'])->where('reference', $loan->id)->update(["reversed" => 1]);
        }
        //write off
        if ($loan->status === 'written_off' && $previousStatus === 'active' && $loan->repayment_frequency_type != 'ballon_payment') {
            $principal = $loan->schedules->sum('principal') - $loan->schedules->sum('principal_written_off_derived') - $loan->schedules->sum('principal_repaid_derived');
            $interest = $loan->schedules->sum('interest') - $loan->schedules->sum('interest_written_off_derived') - $loan->schedules->sum('interest_repaid_derived') - $loan->schedules->sum('interest_waived_derived');
            $fees = $loan->schedules->sum('fees') - $loan->schedules->sum('fees_written_off_derived') - $loan->schedules->sum('fees_repaid_derived') - $loan->schedules->sum('fees_waived_derived');
            $penalties = $loan->schedules->sum('penalties') - $loan->schedules->sum('penalties_written_off_derived') - $loan->schedules->sum('penalties_repaid_derived') - $loan->schedules->sum('penalties_waived_derived');
            $balance = $principal + $interest + $fees + $penalties;

            $loan_transaction = new LoanTransaction();
            $loan_transaction->created_by_id = $loan->written_off_by_user_id;
            $loan_transaction->loan_id = $loan->id;
            $loan_transaction->name = 'Write Off';
            $loan_transaction->loan_transaction_type_id = LoanTransactionType::where('name', 'Write Off')->first()->id;
            $loan_transaction->submitted_on = null;
            $loan_transaction->created_on = date("Y-m-d");
            $loan_transaction->amount = $balance;
            $loan_transaction->credit = $balance;
            $loan_transaction->save();
            //check if accounting is enabled
            if ($loan->product->accounting_rule == "cash" || $loan->product->accounting_rule == "accrual_periodic" || $loan->product->accounting_rule == "accrual_upfront") {
                //credit account
                $journal_entry = new JournalEntry();
                $journal_entry->created_by_id = $loan->written_off_by_user_id;
                $journal_entry->transaction_number = 'L' . $loan_transaction->id;
                $journal_entry->branch_id = $loan->branch_id;
                $journal_entry->currency_id = $loan->currency_id;
                $journal_entry->chart_of_account_id = $loan->product->loan_portfolio_chart_of_account_id;
                $journal_entry->transaction_type = 'loan_write_off';
                $journal_entry->date = $loan->written_off_on_date;
                $date = explode('-', $loan->written_off_on_date);
                $journal_entry->month = $date[1];
                $journal_entry->year = $date[0];
                $journal_entry->credit = $balance;
                $journal_entry->reference = $loan->id;
                $journal_entry->save();
                //debit account
                $journal_entry = new JournalEntry();
                $journal_entry->created_by_id = $loan->written_off_by_user_id;
                $journal_entry->transaction_number = 'L' . $loan_transaction->id;
                $journal_entry->branch_id = $loan->branch_id;
                $journal_entry->currency_id = $loan->currency_id;
                $journal_entry->chart_of_account_id = $loan->product->losses_written_off_chart_of_account_id;
                $journal_entry->transaction_type = 'loan_write_off';
                $journal_entry->date = $loan->written_off_on_date;
                $date = explode('-', $loan->written_off_on_date);
                $journal_entry->month = $date[1];
                $journal_entry->year = $date[0];
                $journal_entry->debit = $balance;
                $journal_entry->reference = $loan->id;
                $journal_entry->save();
            }
            event(new TransactionUpdated($loan));
        }
        //undo write off
        if ($loan->status === 'active' && $previousStatus === 'written_off' && $loan->repayment_frequency_type != 'ballon_payment') {
            foreach (LoanTransaction::where('loan_id', $loan->id)->where('loan_transaction_type_id', LoanTransactionType::where('name', 'Write Off')->first()->id)->where('reversed', 0)->get() as $key) {
                $key->amount = 0;
                $key->debit = $key->credit;
                $key->reversed = 1;
                $key->save();
            }
            event(new TransactionUpdated($loan));
        }
    }
}
